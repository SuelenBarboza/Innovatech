<?php
// Arquiva as tarefas de um projeto para o usuário logado.
include("db.php");
session_start();

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    exit("Usuário não logado");
}

if (!isset($_POST['id'], $_POST['arquivado'])) {
    http_response_code(400);
    exit("Dados inválidos");
}

$usuario_id = (int) $_SESSION['usuario_id'];
$tarefa_id  = (int) $_POST['id'];
$arquivado  = (int) $_POST['arquivado'];

// 🔎 Verifica se o usuário tem acesso à tarefa
$sql = "
SELECT tu.id 
FROM tarefa_usuario tu
INNER JOIN tarefas t ON t.id = tu.tarefa_id
INNER JOIN projetos p ON p.id = t.projeto_id
WHERE tu.tarefa_id = ?
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
    exit("Sem permissão");
}

// ✅ ATUALIZA — NÃO INSERE
$sql = "UPDATE tarefa_usuario SET arquivado = ? WHERE tarefa_id = ? AND usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $arquivado, $tarefa_id, $usuario_id);

if ($stmt->execute()) {
    echo "ok";
} else {
    http_response_code(500);
    echo "erro";
}

$conn->close();
