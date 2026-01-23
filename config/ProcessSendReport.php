<?php
session_start();
include("db.php");

if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não logado.");
}

$aluno_id      = (int) $_SESSION['usuario_id'];
$projeto_id    = (int) ($_POST['projeto_id'] ?? 0);
$professor_id  = (int) ($_POST['professor_id'] ?? 0);
$titulo        = trim($_POST['titulo'] ?? '');
$descricao     = trim($_POST['descricao'] ?? '');

if ($projeto_id <= 0 || $professor_id <= 0 || empty($titulo) || empty($descricao)) {
    die("Dados inválidos.");
}

// Inserir relatório
$sql = "
    INSERT INTO relatorios (projeto_id, aluno_id, professor_id, titulo, descricao)
    VALUES (?, ?, ?, ?, ?)
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiss", $projeto_id, $aluno_id, $professor_id, $titulo, $descricao);

if ($stmt->execute()) {
    header("Location: ../Shared/SendReport.php?msg=sucesso");
    exit;
} else {
    echo "Erro ao enviar relatório.";
}

$stmt->close();
$conn->close();
