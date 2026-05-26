<?php
session_start();
require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../Public/Login.php");
    exit;
}

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

$email = trim($_POST['email'] ?? '');
$senha = $_POST['password'] ?? '';

// Buscar usuário
$sql = "
    SELECT 
        id, nome, email, senha, 
        tipo_usuario, tipo_solicitado,
        aprovado, ativo, foto
    FROM usuarios
    WHERE email = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: ../Public/Login.php?error=invalid_credentials");
    exit;
}

$user = $result->fetch_assoc();

// Conta inativa
if ((int)$user['ativo'] !== 1) {
    header("Location: ../Public/Login.php?error=inactive");
    exit;
}

// Conta não aprovada
if ((int)$user['aprovado'] !== 1) {
    header("Location: ../Public/Login.php?error=not_approved");
    exit;
}

// Verificar senha
if (!password_verify($senha, $user['senha'])) {
    header("Location: ../Public/Login.php?error=invalid_credentials");
    exit;
}

// Define tipo corretamente
$tipoFinal = !empty($user['tipo_usuario'])
    ? $user['tipo_usuario']
    : $user['tipo_solicitado'];

// Sessão
$_SESSION['usuario_id']    = $user['id'];
$_SESSION['usuario_nome']  = $user['nome'];
$_SESSION['usuario_email'] = $user['email'];
$_SESSION['usuario_tipo']  = $tipoFinal;
$_SESSION['usuario_foto']  = $user['foto'];

// ============================================================
// LOG
// ============================================================
registrarLog($conn, $user['id'], 'Login realizado', 'login', "Usuário {$user['nome']} fez login no sistema");

// Redireciona
header("Location: ../Public/Home.php");
exit;
