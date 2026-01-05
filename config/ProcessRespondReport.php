<?php
//Processa o envio da resposta do professor ao relatório do aluno
session_start();
include("db.php");

if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não logado.");
}

$tipo = $_SESSION['usuario_tipo'] ?? 'Aluno';

if (!in_array($tipo, ['Professor', 'Admin'])) {
    die("Acesso negado.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../Shared/ViewReportsTeacher.php");
    exit;
}

$relatorio_id   = (int) ($_POST['relatorio_id'] ?? 0);
$resposta       = trim($_POST['resposta'] ?? '');
$respondente_id = (int) $_SESSION['usuario_id'];

if ($relatorio_id <= 0 || empty($resposta)) {
    die("Dados inválidos.");
}

$sql = "
    INSERT INTO resposta_relatorio
    (relatorio_id, respondente_id, resposta)
    VALUES (?, ?, ?)
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $relatorio_id, $respondente_id, $resposta);

if ($stmt->execute()) {
    header("Location: ../Shared/RespondReport.php?id=".$relatorio_id);
    exit;
} else {
    echo "Erro ao salvar resposta.";
}
