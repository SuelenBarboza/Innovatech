<?php
// Processa as tarefas no banco de dados.
session_start();
include("../Config/db.php");

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

$usuario_id  = (int) ($_SESSION['usuario_id'] ?? 0);
$projeto_id  = (int) $_POST['projeto'];
$nome        = $_POST['nome_tarefa'];
$descricao   = $_POST['descricao'];
$data_inicio = $_POST['data_inicio'];
$data_fim    = $_POST['data_fim'];
$aluno_id    = (int) $_POST['aluno'];
$prioridade  = $_POST['prioridade'] ?? null;

$sql = "INSERT INTO tarefas (projeto_id, aluno_id, nome, descricao, data_inicio, data_fim, prioridade) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iisssss", $projeto_id, $aluno_id, $nome, $descricao, $data_inicio, $data_fim, $prioridade);

if ($stmt->execute()) {
    $tarefa_id = $conn->insert_id;

    // ============================================================
    // LOG
    // ============================================================
    registrarLog($conn, $usuario_id, 'Tarefa criada', 'tarefa', "Tarefa \"$nome\" criada no projeto #$projeto_id", $tarefa_id, 'tarefa');

    echo "✅ Tarefa cadastrada com sucesso! <a href='../View/ViewTasks.php'>Ver Tarefas</a>";
} else {
    echo "❌ Erro: " . $conn->error;
}

$stmt->close();
$conn->close();
