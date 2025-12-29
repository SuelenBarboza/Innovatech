<?php
require_once __DIR__ . "/db.php";
session_start();

header("Content-Type: text/plain");

// usuário logado
$usuario_id = $_SESSION['usuario_id'] ?? 0;

// dados recebidos
$projeto_id = intval($_POST['id'] ?? 0);
$arquivado  = intval($_POST['arquivado'] ?? 0);

if ($usuario_id <= 0 || $projeto_id <= 0) {
    echo "erro";
    exit;
}

// atualiza APENAS o registro do usuário
$stmt = $conn->prepare("
    UPDATE projeto_usuario 
    SET arquivado = ? 
    WHERE projeto_id = ? AND usuario_id = ?
");

if (!$stmt) {
    echo "erro";
    exit;
}

$stmt->bind_param("iii", $arquivado, $projeto_id, $usuario_id);

if ($stmt->execute()) {
    echo "ok";
} else {
    echo "erro";
}

$stmt->close();
$conn->close();
