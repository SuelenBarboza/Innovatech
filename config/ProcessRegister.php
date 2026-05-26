<?php
// Registra o usuario no sistema
require_once 'db.php';

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

// Recebendo dados
$nome      = trim($_POST['username'] ?? '');
$email     = trim($_POST['email'] ?? '');
$senha     = $_POST['password'] ?? '';
$confirmar = $_POST['confirmPassword'] ?? '';
$tipo      = $_POST['tipo_solicitado'] ?? '';

// Validações
if (!$nome || !$email || !$senha || !$confirmar || !$tipo) {
    die("Preencha todos os campos.");
}

if ($senha !== $confirmar) {
    die("As senhas não coincidem.");
}

// Verifica email
$check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    die("Este e-mail já está cadastrado.");
}

// Criptografia
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// Inserção
$sql = $conn->prepare("
    INSERT INTO usuarios 
    (nome, email, senha, tipo_solicitado, aprovado, ativo)
    VALUES (?, ?, ?, ?, 0, 1)
");

$sql->bind_param("ssss", $nome, $email, $senhaHash, $tipo);

if ($sql->execute()) {
    $novo_id = $conn->insert_id;

    // ============================================================
    // LOG
    // ============================================================
    registrarLog($conn, $novo_id, 'Cadastro realizado', 'cadastro', "Novo usuário \"$nome\" ($email) cadastrado como $tipo", $novo_id, 'usuario');

    header("Location: ../Public/Login.php");
    exit;
} else {
    die("Erro ao cadastrar usuário.");
}
