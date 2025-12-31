<?php
// Status da tarefa 
include("db.php");
session_start();

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    exit("nao_logado");
}

if (!isset($_POST['id'], $_POST['status'])) {
    http_response_code(400);
    exit("dados_invalidos");
}

$usuario_id = (int) $_SESSION['usuario_id'];
$tarefa_id  = (int) $_POST['id'];
$status     = $_POST['status'];

// 🔎 Verifica se o usuário tem acesso à tarefa
$sql = "
SELECT t.id
FROM tarefas t
INNER JOIN projetos p ON p.id = t.projeto_id
WHERE t.id = ?
AND (
    p.criador_id = ?
    OR EXISTS (
        SELECT 1 FROM projeto_aluno pa 
        WHERE pa.projeto_id = p.id AND pa.usuario_id = ?
    )
    OR EXISTS (
        SELECT 1 FROM projeto_orientador po 
        WHERE po.projeto_id = p.id AND po.professor_id = ?
    )
)
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $tarefa_id, $usuario_id, $usuario_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(403);
    exit("acesso_negado");
}

// ✅ status é GLOBAL → UPDATE em tarefas
$sql = "UPDATE tarefas SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $tarefa_id);

if ($stmt->execute()) {
    echo "ok";
} else {
    http_response_code(500);
    echo "erro";
}

$conn->close();
