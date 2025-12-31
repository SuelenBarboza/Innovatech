<?php
// Editar uma tarefa existente
include("../Config/db.php");

$id = $_POST['id'];
$nome = $_POST['nome'];
$descricao = $_POST['descricao'];
$data_inicio = $_POST['data_inicio'];
$data_fim = $_POST['data_fim'];
$prioridade = $_POST['prioridade'];
$status = $_POST['status'];

$sql = "UPDATE projetos SET nome=?, descricao=?, data_inicio=?, data_fim=?, prioridade=?, status=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssi", $nome, $descricao, $data_inicio, $data_fim, $prioridade, $status, $id);

if ($stmt->execute()) {
    header("Location: ../ViewListProject.php?msg=atualizado");
} else {
    echo "Erro: " . $conn->error;
}

$stmt->close();
$conn->close();
