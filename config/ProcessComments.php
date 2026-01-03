<?php
// Processa o comentario para o banco de dados
session_start();
include("../Config/db.php");

if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não logado.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../Shared/Comments.php");
    exit;
}

$usuario_id = (int) $_SESSION['usuario_id'];
$projeto_id = (int) ($_POST['projeto_id'] ?? 0);
$comentario = trim($_POST['comentario'] ?? '');

if ($projeto_id <= 0 || empty($comentario)) {
    die("Dados inválidos.");
}

$sql = "INSERT INTO comentarios (projeto_id, usuario_id, comentario)
        VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $projeto_id, $usuario_id, $comentario);

if ($stmt->execute()) {
    header("Location: ../Shared/ViewComments.php?projeto_id=$projeto_id");
    exit;

} else {
    echo "Erro ao salvar comentário.";
}

$stmt->close();
$conn->close();
