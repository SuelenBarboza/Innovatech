<?php
// Manda o projeto para o banco de dados
session_start();
include("../Config/db.php");

// ============================================================
// HELPER LOG
// ============================================================
function registrarLog($conn, $usuario_id, $acao, $categoria, $descricao, $referencia_id = null, $referencia_tipo = null) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $sql = "INSERT INTO logs (usuario_id, acao, categoria, descricao, referencia_id, referencia_tipo, ip_usuario)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssiss", $usuario_id, $acao, $categoria, $descricao, $referencia_id, $referencia_tipo, $ip);
    $stmt->execute();
}

if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não autenticado.");
}

$criador_id = (int) $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../Shared/ViewListProject.php");
    exit;
}

$nome        = trim($_POST['nome'] ?? '');
$descricao   = trim($_POST['descricao'] ?? '');
$categoria   = !empty($_POST['categoria']) ? $_POST['categoria'] : NULL;
$data_inicio = $_POST['data_inicio'] ?? null;
$data_fim    = $_POST['data_fim'] ?? null;

$alunos      = $_POST['aluno'] ?? [];
$professores = $_POST['professor'] ?? [];

$conn->begin_transaction();

try {
    $sqlProjeto = "
        INSERT INTO projetos 
        (nome, descricao, categoria, data_inicio, data_fim, criador_id)
        VALUES (?, ?, ?, ?, ?, ?)
    ";

    $stmtProjeto = $conn->prepare($sqlProjeto);
    $stmtProjeto->bind_param("sssssi", $nome, $descricao, $categoria, $data_inicio, $data_fim, $criador_id);
    $stmtProjeto->execute();

    $projeto_id = $stmtProjeto->insert_id;
    $stmtProjeto->close();

    $sqlCriador = "
        INSERT INTO projeto_usuario 
        (projeto_id, usuario_id, papel, prioridade, arquivado)
        VALUES (?, ?, 'Criador', 'Baixa', 0)
    ";
    $stmtCriador = $conn->prepare($sqlCriador);
    $stmtCriador->bind_param("ii", $projeto_id, $criador_id);
    $stmtCriador->execute();
    $stmtCriador->close();

    if (!empty($alunos)) {
        $stmtAlunoPU = $conn->prepare("
            INSERT INTO projeto_usuario (projeto_id, usuario_id, papel)
            VALUES (?, ?, 'Aluno')
        ");
        foreach ($alunos as $aluno_id) {
            $aluno_id = (int) $aluno_id;
            if ($aluno_id > 0) {
                $stmtAlunoPU->bind_param("ii", $projeto_id, $aluno_id);
                $stmtAlunoPU->execute();
            }
        }
        $stmtAlunoPU->close();
    }

    if (!empty($professores)) {
        $stmtProfPU = $conn->prepare("
            INSERT INTO projeto_usuario (projeto_id, usuario_id, papel)
            VALUES (?, ?, 'Orientador')
        ");
        foreach ($professores as $prof_id) {
            $prof_id = (int) $prof_id;
            if ($prof_id > 0) {
                $stmtProfPU->bind_param("ii", $projeto_id, $prof_id);
                $stmtProfPU->execute();
            }
        }
        $stmtProfPU->close();
    }

    $conn->commit();

    // ============================================================
    // LOG
    // ============================================================
    registrarLog($conn, $criador_id, 'Projeto criado', 'projeto', "Projeto \"$nome\" criado", $projeto_id, 'projeto');

    header("Location: ../Shared/ViewListProject.php?msg=sucesso");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    echo "Erro ao salvar projeto: " . $e->getMessage();
}

$conn->close();
