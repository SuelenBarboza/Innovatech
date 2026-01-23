<?php
//Processa o envio da resposta do relatório
session_start();
include("db.php");

if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não logado.");
}

$tipo = $_SESSION['usuario_tipo'] ?? 'Aluno';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../Shared/Home.php");
    exit;
}

$relatorio_id   = (int) ($_POST['relatorio_id'] ?? 0);
$resposta       = trim($_POST['resposta'] ?? '');
$respondente_id = (int) $_SESSION['usuario_id'];

if ($relatorio_id <= 0 || empty($resposta)) {
    die("Dados inválidos.");
}

// Opcional: verificar se aluno está respondendo ao próprio relatório
if ($tipo === 'Aluno') {
    $sqlCheck = "SELECT aluno_id FROM relatorios WHERE id = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("i", $relatorio_id);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result()->fetch_assoc();
    
    if (!$resCheck || (int)$resCheck['aluno_id'] !== $respondente_id) {
        die("Acesso negado.");
    }
}

// Inserir resposta
$sql = "
    INSERT INTO resposta_relatorio
    (relatorio_id, respondente_id, resposta)
    VALUES (?, ?, ?)
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $relatorio_id, $respondente_id, $resposta);

if ($stmt->execute()) {
    header("Location: ../Shared/ViewReport.php?id=".$relatorio_id);
    exit;
} else {
    echo "Erro ao salvar resposta.";
}
