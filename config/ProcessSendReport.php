<?php
//Processa o do envio de resposta ao relatório pelo professor/admin
session_start();
include("db.php");

if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não logado.");
}

$respondente_id = (int) $_SESSION['usuario_id'];
$relatorio_id   = (int) ($_POST['relatorio_id'] ?? 0);
$mensagem       = trim($_POST['resposta'] ?? '');

if ($relatorio_id <= 0 || empty($mensagem)) {
    die("Dados inválidos.");
}

// Inserir nova resposta
$sql = "
    INSERT INTO resposta_relatorio (relatorio_id, respondente_id, resposta)
    VALUES (?, ?, ?)
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $relatorio_id, $respondente_id, $mensagem);

if ($stmt->execute()) {
    header("Location: ../Shared/ViewReport.php?id=$relatorio_id&msg=sucesso");
    exit;
} else {
    echo "Erro ao enviar resposta.";
}

$stmt->close();
$conn->close();
?>
