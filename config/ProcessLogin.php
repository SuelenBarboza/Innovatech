<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../Public/Login.php");
    exit;
}

$email = trim($_POST['email']);
$senha = $_POST['password'];

$database = new Database();
$db = $database->getConnection();

// Buscar usuário
$sql = "SELECT id, nome, email, senha, tipo_solicitado, aprovado, ativo
        FROM usuarios
        WHERE email = :email
        LIMIT 1";

$stmt = $db->prepare($sql);
$stmt->bindParam(':email', $email);
$stmt->execute();

if ($stmt->rowCount() !== 1) {
    header("Location: ../Public/Login.php?error=invalid_credentials");
    exit;
}

$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Conta inativa
if ($user['ativo'] != 1) {
    header("Location: ../Public/Login.php?error=inactive");
    exit;
}

// Conta não aprovada
if ($user['aprovado'] != 1) {
    header("Location: ../Public/Login.php?error=not_approved");
    exit;
}

// Verificar senha
if (!password_verify($senha, $user['senha'])) {
    header("Location: ../Public/Login.php?error=invalid_credentials");
    exit;
}

// LOGIN OK
$_SESSION['user_id']    = $user['id'];
$_SESSION['user_name']  = $user['nome'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_tipo']  = $user['tipo_solicitado'];

// Redirecionamento por perfil
switch ($user['tipo_solicitado']) {
    case 'admin':
        header("Location: ../Users/Admin/dashboard.php");
        break;

    case 'coordinator':
        header("Location: ../Users/Coordinator/dashboard.php");
        break;

    default:
        header("Location: ../Public/Home.php");
}

exit;
