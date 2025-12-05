<?php
include("../config/conexao.php");

$nome = $_POST['nome'];
$email = $_POST['email'];
$funcao = $_POST['funcao'];

$sql = "INSERT INTO membros (nome, email, funcao) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $nome, $email, $funcao);

if ($stmt->execute()) {
    header("Location: ../ViewListMembers.php?msg=sucesso");
} else {
    echo "Erro: " . $conn->error;
}

$stmt->close();
$conn->close();
