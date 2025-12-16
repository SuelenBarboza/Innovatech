<?php
include("../Config/db.php");

$nome = $_POST['nome'];
$descricao = $_POST['descricao'];
$data_inicio = $_POST['data_inicio'];
$data_fim = $_POST['data_fim'];

$sql = "INSERT INTO projetos (nome, descricao, data_inicio, data_fim) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $nome, $descricao, $data_inicio, $data_fim);

if ($stmt->execute()) {
    header("Location: ../ViewListProject.php?msg=sucesso");
} else {
    echo "Erro: " . $conn->error;
}

$stmt->close();
$conn->close();
