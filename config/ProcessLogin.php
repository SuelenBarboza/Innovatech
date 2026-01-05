<?php
// Usuarios fazem Login no sistema e são redirecionados conforme o tipo de usuário.
session_start();
require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../Public/Login.php");
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['password'] ?? '';

// Buscar usuário
$sql = "SELECT id, nome, email, senha, tipo_solicitado, aprovado, ativo
        FROM usuarios
        WHERE email = ? LIMIT 1";

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

// LOGIN OK
$_SESSION['usuario_id']    = $user['id'];
$_SESSION['usuario_nome']  = $user['nome'];
$_SESSION['usuario_email'] = $user['email'];
$tipoNormalizado = ucfirst(strtolower(trim($user['tipo_solicitado'])));
$_SESSION['usuario_tipo'] = $tipoNormalizado;


header("Location: ../Public/Home.php");
exit;


