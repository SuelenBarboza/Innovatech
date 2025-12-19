<?php
session_start();
include("../Config/db.php");

// 1️⃣ Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não autenticado.");
}
$criador_id = $_SESSION['usuario_id'];

// 2️⃣ Verifica se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../ViewListProject.php");
    exit;
}

// 3️⃣ Recebe dados do formulário
$nome        = trim($_POST['nome'] ?? '');
$descricao   = trim($_POST['descricao'] ?? '');
$categoria = !empty($_POST['categoria']) ? $_POST['categoria'] : NULL;

$data_inicio = $_POST['data_inicio'] ?? null;
$data_fim    = $_POST['data_fim'] ?? null;

$alunos      = $_POST['aluno'] ?? [];
$professores = $_POST['professor'] ?? [];

// 4️⃣ Inicia transação
$conn->begin_transaction();

try {
    // 5️⃣ INSERE PROJETO com prioridade NULL
    $sqlProjeto = "INSERT INTO projetos 
        (nome, descricao, categoria, data_inicio, data_fim, criador_id, prioridade)
        VALUES (?, ?, ?, ?, ?, ?, NULL)";

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

    // 6️⃣ VINCULA ALUNOS
    if (!empty($alunos)) {
        $sqlAluno = "INSERT INTO projeto_aluno (projeto_id, usuario_id) VALUES (?, ?)";
        $stmtAluno = $conn->prepare($sqlAluno);

        foreach ($alunos as $aluno_id) {
            $aluno_id = (int)$aluno_id; // garante que seja inteiro
            if ($aluno_id > 0) {
                $stmtAluno->bind_param("ii", $projeto_id, $aluno_id);
                $stmtAluno->execute();
            }
        }
        $stmtAluno->close();
    }

    // 7️⃣ VINCULA PROFESSORES
    if (!empty($professores)) {
        $sqlProf = "INSERT INTO projeto_orientador (projeto_id, professor_id) VALUES (?, ?)";
        $stmtProf = $conn->prepare($sqlProf);

        foreach ($professores as $prof_id) {
            $prof_id = (int)$prof_id; // garante que seja inteiro
            if ($prof_id > 0) {
                $stmtProf->bind_param("ii", $projeto_id, $prof_id);
                $stmtProf->execute();
            }
        }
        $stmtProf->close();
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
?>
