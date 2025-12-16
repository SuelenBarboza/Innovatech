<?php
include("../Config/db.php");

$projeto_id   = $_POST['projeto'];      
$nome         = $_POST['nome_tarefa']; 
$descricao    = $_POST['descricao'];
$data_inicio  = $_POST['data_inicio'];
$data_fim     = $_POST['data_fim'];
$aluno_id     = $_POST['aluno'];       

$prioridade = isset($_POST['prioridade']) ? $_POST['prioridade'] : null;

$sql = "INSERT INTO tarefas (projeto_id, aluno_id, nome, descricao, data_inicio, data_fim, prioridade) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iisssss", $projeto_id, $aluno_id, $nome, $descricao, $data_inicio, $data_fim, $prioridade);

if ($stmt->execute()) {
    echo "✅ Tarefa cadastrada com sucesso! <a href='../View/ViewTasks.php'>Ver Tarefas</a>";
} else {
    echo "❌ Erro: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
