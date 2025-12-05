<?php
include("../config/conexao.php");

$nome  = $_POST['user'];
$email = $_POST['email'];
$senha = $_POST['password'];
$confirmaSenha = $_POST['confirmPassword'];

if ($senha !== $confirmaSenha) {
    die("As senhas não coincidem. <a href='cadastro.html'>Tente novamente</a>");
}


$senhaHash = hash("sha256", $senha);


$sqlCheck = "SELECT id FROM usuarios WHERE email = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("s", $email);
$stmtCheck->execute();
$stmtCheck->store_result();

if ($stmtCheck->num_rows > 0) {
    die("E-mail já cadastrado. <a href='Register.html'>Tente novamente</a>");
}
$stmtCheck->close();

$sql = "INSERT INTO usuarios (nome, email, senha, tipo_solicitado, aprovado, ativo) VALUES (?, ?, ?, 'Aluno', 0, 1)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $nome, $email, $senhaHash);

if ($stmt->execute()) {
    header("Location: ../Public/Login.php?success=registered");
    exit;
} else {
    header("Location: ../Public/Login.php?error=register_failed");
    exit;
}

$stmt->close();
$conn->close();
?>