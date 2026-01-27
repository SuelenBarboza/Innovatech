<?php
// Arquiva ou restaura projetos para o usuário logado
require_once __DIR__ . "/db.php";
session_start();

header("Content-Type: text/plain");

$usuario_id = $_SESSION['usuario_id'] ?? 0;
$projeto_id = (int) ($_POST['id'] ?? 0);
$arquivado  = (int) ($_POST['arquivado'] ?? 0);

if ($usuario_id <= 0 || $projeto_id <= 0) {
    echo "erro";
    exit;
}

// 1️⃣ Verifica se já existe registro
$sqlCheck = "
    SELECT id 
    FROM projeto_usuario 
    WHERE projeto_id = ? AND usuario_id = ?
";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("ii", $projeto_id, $usuario_id);
$stmtCheck->execute();
$result = $stmtCheck->get_result();

if ($result->num_rows > 0) {
    // 2️⃣ UPDATE se existir
    $sql = "
        UPDATE projeto_usuario 
        SET arquivado = ?
        WHERE projeto_id = ? AND usuario_id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $arquivado, $projeto_id, $usuario_id);
} else {
    // 3️⃣ INSERT se não existir
    $sql = "
        INSERT INTO projeto_usuario (projeto_id, usuario_id, arquivado, papel)
        VALUES (?, ?, ?, 'Aluno')
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $projeto_id, $usuario_id, $arquivado);
}

if ($stmt->execute()) {
    echo "ok";
} else {
    echo "erro";
}

$stmtCheck->close();
$stmt->close();
$conn->close();
