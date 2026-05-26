<?php
session_start();
include("db.php");

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
$stmt->bind_param("iiiss", $projeto_id, $aluno_id, $professor_id, $titulo, $descricao);

if ($stmt->execute()) {
    $relatorio_id = $conn->insert_id;

    // ============================================================
    // LOG
    // ============================================================
    registrarLog($conn, $aluno_id, 'Relatório enviado', 'relatorio', "Relatório \"$titulo\" enviado no projeto #$projeto_id", $relatorio_id, 'relatorio');

    header("Location: ../Shared/SendReport.php?msg=sucesso");
    exit;
} else {
    echo "Erro ao enviar relatório.";
}

$stmt->close();
$conn->close();
