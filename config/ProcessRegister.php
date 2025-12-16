<?php
require_once 'db.php'; 

// Recebendo dados do formulário
$nome  = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['password'] ?? '';
$confirmar = $_POST['confirmPassword'] ?? '';
$tipo  = $_POST['tipo_solicitado'] ?? '';

// Validações básicas
if (!$nome || !$email || !$senha || !$confirmar || !$tipo) {
    echo "Preencha todos os campos.";
    exit;
}

if ($senha !== $confirmar) {
    echo "As senhas não coincidem.";
    exit;
}

// Verifica se email já existe
$check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "Este e-mail já está cadastrado.";
    exit;
}

// Criptografa senha
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// Insere usuário como NÃO aprovado
$sql = $conn->prepare("
    INSERT INTO usuarios 
    (nome, email, senha, tipo_solicitado, aprovado, ativo)
    VALUES (?, ?, ?, ?, 0, 1)
");

$sql->bind_param("ssss", $nome, $email, $senhaHash, $tipo);

if ($sql->execute()) {
    // Redireciona para a página de login
    header("Location: ../Public/Login.php");
    exit;
} else {
    echo "Erro ao cadastrar usuário.";
}

