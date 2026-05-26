<?php
// Processa o envio da resposta do relatório
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

$usuario_id     = (int) $_SESSION['usuario_id'];
$tipo           = $_SESSION['usuario_tipo'] ?? 'Aluno';
$relatorio_id   = (int) ($_POST['relatorio_id'] ?? 0);
$resposta       = trim($_POST['resposta'] ?? '');
$respondente_id = $usuario_id;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../Public/Login.php");
    exit;
}

if ($relatorio_id <= 0 || empty($resposta)) {
    header("Location: ../Shared/RespondReport.php?id=" . $relatorio_id . "&erro=1");
    exit;
}

// ==========================
// VERIFICAR PERMISSÃO E STATUS
// ==========================
$sqlCheck = "SELECT aluno_id, professor_id, status, titulo FROM relatorios WHERE id = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("i", $relatorio_id);
$stmtCheck->execute();
$resCheck = $stmtCheck->get_result()->fetch_assoc();

if (!$resCheck) {
    die("Relatório não encontrado.");
}

$temPermissao = false;
if ($tipo === 'Aluno' && (int)$resCheck['aluno_id'] === $respondente_id) {
    $temPermissao = true;
} elseif (($tipo === 'Professor' || $tipo === 'Admin' || $tipo === 'Coordenador') && (int)$resCheck['professor_id'] === $respondente_id) {
    $temPermissao = true;
} elseif (in_array($tipo, ['Admin', 'Coordenador'])) {
    $temPermissao = true;
}

if (!$temPermissao) {
    die("Acesso negado.");
}

if ($resCheck['status'] === 'Concluído') {
    header("Location: ../Shared/RespondReport.php?id=" . $relatorio_id . "&concluido=1");
    exit;
}

// ==========================
// INSERIR RESPOSTA
// ==========================
$sql = "
    INSERT INTO resposta_relatorio
    (relatorio_id, respondente_id, resposta, respondido_em)
    VALUES (?, ?, ?, NOW())
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $relatorio_id, $respondente_id, $resposta);

if ($stmt->execute()) {
    $novo_status = 'Respondido';
    $sqlUpdate = "UPDATE relatorios SET status = ? WHERE id = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("si", $novo_status, $relatorio_id);
    $stmtUpdate->execute();

    // ============================================================
    // LOG
    // ============================================================
    $titulo = $resCheck['titulo'];
    registrarLog($conn, $usuario_id, 'Relatório respondido', 'relatorio', "Resposta adicionada ao relatório \"$titulo\" (ID #$relatorio_id)", $relatorio_id, 'relatorio');

    header("Location: ../Shared/RespondReport.php?id=" . $relatorio_id . "&sucesso=1");
    exit;
} else {
    header("Location: ../Shared/RespondReport.php?id=" . $relatorio_id . "&erro=1");
    exit;
}
