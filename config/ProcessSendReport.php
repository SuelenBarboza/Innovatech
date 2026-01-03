<?php
// Processa os relatorios enviados pelos alunos aos professores
session_start();
include("db.php");

if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não logado.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../Shared/ViewSendReport.php");
    exit;
}

$aluno_id     = (int) $_SESSION['usuario_id'];
$projeto_id   = (int) ($_POST['projeto_id'] ?? 0);
$professor_id = (int) ($_POST['professor_id'] ?? 0);
$titulo       = trim($_POST['titulo'] ?? '');
$descricao    = trim($_POST['descricao'] ?? '');

if ($projeto_id <= 0 || $professor_id <= 0 || empty($titulo) || empty($descricao)) {
    die("Dados inválidos.");
}

$sql = "
    INSERT INTO relatorios (projeto_id, aluno_id, professor_id, titulo, descricao)
    VALUES (?, ?, ?, ?, ?)
";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "iiiss",
    $projeto_id,
    $aluno_id,
    $professor_id,
    $titulo,
    $descricao
);

if ($stmt->execute()) {
    header("Location: ../Shared/ViewSendReport.php?msg=sucesso");
    exit;
} else {
    echo "Erro ao enviar relatório.";
}

$stmt->close();
$conn->close();
