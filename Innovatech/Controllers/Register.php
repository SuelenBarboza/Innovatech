<?php
require_once "../Config/db.php";

// PEGAR DADOS DO FORMULÁRIO
$nome = $_POST['user'];
$email = $_POST['email'];
$senha = $_POST['password'];
$confirmSenha = $_POST['confirmPassword'];

// VERIFICAR SENHA
if ($senha !== $confirmSenha) {
    die("As senhas não coincidem.");
}

// HASH DA SENHA
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// IDENTIFICAR TIPO PELO DOMÍNIO DO EMAIL
if (str_ends_with($email, "@admin.sp.gov.br")) {
    $tipo = "administrador";
} elseif (str_ends_with($email, "@coord.sp.gov.br")) {
    $tipo = "coordenador";
} elseif (str_ends_with($email, "@professor.sp.gov.br")) {
    $tipo = "professor";
} elseif (str_ends_with($email, "@aluno.sp.gov.br")) {
    $tipo = "aluno";
} else {
    die("Domínio de e-mail inválido. Use um e-mail institucional.");
}

// INSERIR NO BANCO
$sql = "INSERT INTO registers (nome, email, senha, tipo) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $nome, $email, $senhaHash, $tipo);

if ($stmt->execute()) {
    header("Location: ../Public/Login.html?cadastro=sucesso");
    exit;
} else {
    echo "Erro ao cadastrar: " . $stmt->error;
}

$stmt->close();
$conn->close();
