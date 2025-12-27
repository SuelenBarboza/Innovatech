<?php
include("db.php");

if (!isset($_POST['id_projeto'])) {
    header("Location: ../Shared/ViewListProject.php");
    exit;
}

$id = intval($_POST['id_projeto']);
$nome = $_POST['nome'];
$descricao = $_POST['descricao'];
$categoria = $_POST['categoria'] ?: null;
$data_inicio = $_POST['data_inicio'];
$data_fim = $_POST['data_fim'];

// ================= UPDATE DO PROJETO =================
$sql = "UPDATE projetos 
        SET nome = ?, descricao = ?, categoria = ?, data_inicio = ?, data_fim = ? 
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssi", $nome, $descricao, $categoria, $data_inicio, $data_fim, $id);
$stmt->execute();

// ================= LIMPAR RELACIONAMENTOS =================
// Alunos
$sqlDelAlunos = "DELETE FROM projeto_aluno WHERE projeto_id = ?";
$stmt = $conn->prepare($sqlDelAlunos);
$stmt->bind_param("i", $id);
$stmt->execute();

// Professores
$sqlDelProf = "DELETE FROM projeto_orientador WHERE projeto_id = ?";
$stmt = $conn->prepare($sqlDelProf);
$stmt->bind_param("i", $id);
$stmt->execute();

// ================= INSERIR NOVOS RELACIONAMENTOS =================
// Alunos
if (!empty($_POST['aluno'])) {
    $sqlInsAluno = "INSERT INTO projeto_aluno (projeto_id, usuario_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sqlInsAluno);
    foreach ($_POST['aluno'] as $aluno_id) {
        $stmt->bind_param("ii", $id, $aluno_id);
        $stmt->execute();
    }
}

// Professores
if (!empty($_POST['professor'])) {
    $sqlInsProf = "INSERT INTO projeto_orientador (projeto_id, professor_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sqlInsProf);
    foreach ($_POST['professor'] as $prof_id) {
        $stmt->bind_param("ii", $id, $prof_id);
        $stmt->execute();
    }
}

header("Location: ../Shared/ViewProject.php?id=$id");
exit;
