<?php
// Envia ação de formulário para registrar uma nova tarefa no banco de dados.
include("db.php");
session_start();

if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não logado.");
}

$usuario_id = (int) $_SESSION['usuario_id'];

// ==========================
// VALIDAÇÃO DOS DADOS
// ==========================
$projeto_id   = isset($_POST['projeto']) ? (int) $_POST['projeto'] : 0;
$responsavel  = isset($_POST['aluno']) ? (int) $_POST['aluno'] : 0;
$nome_tarefa  = isset($_POST['nome_tarefa']) ? trim($_POST['nome_tarefa']) : '';
$descricao    = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';
$data_inicio  = !empty($_POST['data_inicio']) ? $_POST['data_inicio'] : null;
$data_fim     = !empty($_POST['data_fim']) ? $_POST['data_fim'] : null;

// Verifica campos obrigatórios
if (!$projeto_id) {
    die("O projeto é obrigatório.");
}

if (!$responsavel) {
    die("O responsável é obrigatório.");
}

if (!$nome_tarefa) {
    die("O nome da tarefa é obrigatório.");
}

// ==========================
// INSERÇÃO NO BANCO
// ==========================
$sql = "
INSERT INTO tarefas 
(projeto_id, nome, descricao, responsavel_id, data_inicio, data_fim) 
VALUES (?, ?, ?, ?, ?, ?)
";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ississ", 
    $projeto_id, 
    $nome_tarefa, 
    $descricao, 
    $responsavel, 
    $data_inicio, 
    $data_fim
);

if ($stmt->execute()) {
    header("Location: ../Shared/ViewListTasks.php?success=1");
    exit;
} else {
    die("Erro ao cadastrar tarefa: " . $stmt->error);
}
?>
