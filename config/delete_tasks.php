<?php
include("../config/conexao.php");

$id = $_GET['id'];

$sql = "DELETE FROM tarefas WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: ../ViewTasks.php?msg=deletado");
} else {
    echo "Erro: " . $conn->error;
}

$stmt->close();
$conn->close();
