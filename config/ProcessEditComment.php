<?php
// Processa a edição de um comentário existente
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
    die("Usuário não logado.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../Shared/ViewComments.php");
    exit;
}

$usuario_id      = (int) $_SESSION['usuario_id'];
$comentario_id   = (int) ($_POST['comentario_id'] ?? 0);
$novo_comentario = trim($_POST['comentario'] ?? '');

if ($comentario_id <= 0 || $novo_comentario === '') {
    die("Dados inválidos.");
}

// ==========================
// Verificar se o comentário pertence ao usuário
// ==========================
$sql = "SELECT usuario_id, projeto_id FROM comentarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $comentario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Comentário não encontrado.");
}

$comentario = $result->fetch_assoc();
$result->free();

if ($comentario['usuario_id'] !== $usuario_id) {
    die("Acesso negado.");
}

// ==========================
// Atualizar comentário
// ==========================
$sqlUpdate = "UPDATE comentarios SET comentario = ?, atualizado_em = NOW() WHERE id = ?";
$stmtUpdate = $conn->prepare($sqlUpdate);
$stmtUpdate->bind_param("si", $novo_comentario, $comentario_id);

if ($stmtUpdate->execute()) {

    // ============================================================
    // LOG
    // ============================================================
    registrarLog($conn, $usuario_id, 'Comentário editado', 'comentario', "Comentário #$comentario_id editado no projeto #" . $comentario['projeto_id'], $comentario_id, 'comentario');

    header("Location: ../Shared/ViewComments.php?projeto_id=" . $comentario['projeto_id'] . "&msg=editado");
    exit;
} else {
    echo "Erro ao atualizar comentário: " . $stmtUpdate->error;
}

$stmtUpdate->close();
$conn->close();
