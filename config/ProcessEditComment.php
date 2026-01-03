<?php
// Edita o comentario para o banco de dados
session_start();
include("../Config/db.php");

if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não logado.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$id = (int)($_POST['id'] ?? 0);
$projeto_id = (int)($_POST['projeto_id'] ?? 0);
$comentario = trim($_POST['comentario'] ?? '');

if ($id <= 0 || empty($comentario)) {
    die("Dados inválidos.");
}

$sql = "
    UPDATE comentarios 
    SET comentario = ?
    WHERE id = ? AND usuario_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $comentario, $id, $usuario_id);
$stmt->execute();

header("Location: ../Shared/ViewComments.php?projeto_id=$projeto_id");
exit;
