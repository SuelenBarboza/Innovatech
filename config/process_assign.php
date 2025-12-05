<?php
include("../config/conexao.php");

$projeto_id = $_POST['projeto_id'];
$membro_id = $_POST['membro_id'];
$descricao = $_POST['descricaoTarefa'];

$sql = "INSERT INTO atribuicoes (projeto_id, membro_id, descricao) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $projeto_id, $membro_id, $descricao);

if ($stmt->execute()) {
    echo "✅ Tarefa atribuída com sucesso! <a href='ViewListTasks.php'>Ver Tarefas</a>";
} else {
    echo "❌ Erro: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
