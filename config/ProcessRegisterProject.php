<?php
// Manda o projeto para o banco de dados
session_start();
include("../Config/db.php");

// 1️⃣ Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não autenticado.");
}

$criador_id = (int) $_SESSION['usuario_id'];

// 2️⃣ Verifica se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../Shared/ViewListProject.php");
    exit;
}

// 3️⃣ Recebe dados do formulário
$nome        = trim($_POST['nome'] ?? '');
$descricao   = trim($_POST['descricao'] ?? '');
$categoria   = !empty($_POST['categoria']) ? $_POST['categoria'] : NULL;
$data_inicio = $_POST['data_inicio'] ?? null;
$data_fim    = $_POST['data_fim'] ?? null;

$alunos      = $_POST['aluno'] ?? [];
$professores = $_POST['professor'] ?? [];

// 4️⃣ Inicia transação
$conn->begin_transaction();

try {
    // 5️⃣ INSERE PROJETO
    $sqlProjeto = "
        INSERT INTO projetos 
        (nome, descricao, categoria, data_inicio, data_fim, criador_id)
        VALUES (?, ?, ?, ?, ?, ?)
    ";

    $stmtProjeto = $conn->prepare($sqlProjeto);
    $stmtProjeto->bind_param(
        "sssssi",
        $nome,
        $descricao,
        $categoria,
        $data_inicio,
        $data_fim,
        $criador_id
    );
    $stmtProjeto->execute();

    $projeto_id = $stmtProjeto->insert_id;
    $stmtProjeto->close();

    // 🔹 VINCULA CRIADOR AO PROJETO
    $sqlCriador = "
        INSERT INTO projeto_usuario 
        (projeto_id, usuario_id, papel, prioridade, arquivado)
        VALUES (?, ?, 'Criador', 'Baixa', 0)
    ";
    $stmtCriador = $conn->prepare($sqlCriador);
    $stmtCriador->bind_param("ii", $projeto_id, $criador_id);
    $stmtCriador->execute();
    $stmtCriador->close();

    // 🔹 VINCULA ALUNOS (controle de acesso)
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

    // 🔹 VINCULA PROFESSORES (controle de acesso)
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

    // 8️⃣ COMMIT
    $conn->commit();
    header("Location: ../Shared/ViewListProject.php?msg=sucesso");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    echo "Erro ao salvar projeto: " . $e->getMessage();
}

$conn->close();
