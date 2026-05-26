<?php
// Processa a resposta do professor ao relatório enviado pelo aluno
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

$professor_id = (int) $_SESSION['usuario_id'];
$tipo         = $_SESSION['usuario_tipo'] ?? '';

if ($tipo !== 'Professor' && $tipo !== 'Admin') {
    die("Acesso negado.");
}

$relatorio_id = (int) ($_POST['relatorio_id'] ?? 0);
$resposta     = trim($_POST['resposta'] ?? '');

if ($relatorio_id <= 0 || empty($resposta)) {
    die("Dados inválidos.");
}

// Verifica se já existe resposta
$sqlCheck = "SELECT id FROM resposta_relatorio WHERE relatorio_id = ?";
$stmt = $conn->prepare($sqlCheck);
$stmt->bind_param("i", $relatorio_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $sql = "UPDATE resposta_relatorio SET resposta = ?, respondido_em = NOW() WHERE relatorio_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $resposta, $relatorio_id);
    $acao_log = 'Resposta de relatório atualizada';
} else {
    $sql = "INSERT INTO resposta_relatorio (relatorio_id, professor_id, resposta) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $relatorio_id, $professor_id, $resposta);
    $acao_log = 'Relatório respondido pelo professor';
}

if ($stmt->execute()) {

    // ============================================================
    // LOG
    // ============================================================
    registrarLog($conn, $professor_id, $acao_log, 'relatorio', "Professor respondeu o relatório #$relatorio_id", $relatorio_id, 'relatorio');

    header("Location: ../Shared/ViewReportsTeacher.php?msg=respondido");
    exit;
} else {
    echo "Erro ao salvar resposta: " . $stmt->error;
}
