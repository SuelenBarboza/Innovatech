<?php
// Processa a edição de uma tarefa
include("../Config/db.php");
session_start();

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

if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não logado.");
}

$usuario_id = (int) $_SESSION['usuario_id'];

// ==========================
// VALIDAÇÃO DOS DADOS
// ==========================
if (!isset($_POST['tarefa_id'], $_POST['projeto'], $_POST['aluno'], $_POST['nome_tarefa'])) {
    die("Dados incompletos.");
}

$tarefa_id   = (int) $_POST['tarefa_id'];
$projeto_id  = (int) $_POST['projeto'];
$aluno_id    = (int) $_POST['aluno'];
$nome        = trim($_POST['nome_tarefa']);
$descricao   = trim($_POST['descricao'] ?? '');
$data_inicio = !empty($_POST['data_inicio']) ? $_POST['data_inicio'] : null;
$data_fim    = !empty($_POST['data_fim'])    ? $_POST['data_fim']    : null;

if ($nome === '') die("O nome da tarefa é obrigatório.");

if ($data_inicio && $data_fim && $data_inicio > $data_fim) {
    die("A data de início não pode ser maior que a data de fim.");
}

// ==========================
// ATUALIZAÇÃO DA TAREFA
// ==========================
$sql = "
    UPDATE tarefas SET
        projeto_id     = ?,
        responsavel_id = ?,
        nome           = ?,
        descricao      = ?,
        data_inicio    = ?,
        data_fim       = ?
    WHERE id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iissssi", $projeto_id, $aluno_id, $nome, $descricao, $data_inicio, $data_fim, $tarefa_id);

if (!$stmt->execute()) {
    die("Erro ao atualizar tarefa: " . $stmt->error);
}

// ============================================================
// LOG
// ============================================================
registrarLog($conn, $usuario_id, 'Tarefa editada', 'tarefa', "Tarefa \"$nome\" (ID #$tarefa_id) foi editada", $tarefa_id, 'tarefa');

header("Location: ../Shared/ViewTask.php?id=" . $tarefa_id);
exit;
