<?php
// Processa a resposta do professor ao relatório enviado pelo aluno
session_start();
include("../Config/db.php");

if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não logado.");
}

$professor_id = $_SESSION['usuario_id'];
$tipo = $_SESSION['tipo'] ?? '';

if ($tipo !== 'professor' && $tipo !== 'admin') {
    die("Acesso negado.");
}

$relatorio_id = (int) ($_POST['relatorio_id'] ?? 0);
$resposta = trim($_POST['resposta'] ?? '');

if ($relatorio_id <= 0 || empty($resposta)) {
    die("Dados inválidos.");
}

// verifica se já existe resposta
$sqlCheck = "SELECT id FROM resposta_relatorio WHERE relatorio_id = ?";
$stmt = $conn->prepare($sqlCheck);
$stmt->bind_param("i", $relatorio_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $sql = "
    UPDATE resposta_relatorio
    SET resposta = ?, respondido_em = NOW()
    WHERE relatorio_id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $resposta, $relatorio_id);
} else {
    $sql = "
    INSERT INTO resposta_relatorio (relatorio_id, professor_id, resposta)
    VALUES (?, ?, ?)
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $relatorio_id, $professor_id, $resposta);
}

$stmt->execute();

header("Location: ../Shared/ViewReportsTeacher.php?msg=respondido");
exit;
