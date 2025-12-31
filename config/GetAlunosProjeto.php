<?php
// Puxa os alunos do projeto para popular o dropdown na criação de tarefas
include("../Config/db.php");
session_start();

if (!isset($_GET['projeto_id'])) {
    echo json_encode([]);
    exit;
}

$projeto_id = (int) $_GET['projeto_id'];

$sql = "
SELECT u.id, u.nome
FROM usuarios u
INNER JOIN projeto_aluno pa ON pa.usuario_id = u.id
WHERE pa.projeto_id = ?
ORDER BY u.nome ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $projeto_id);
$stmt->execute();
$result = $stmt->get_result();

$alunos = [];
while ($row = $result->fetch_assoc()) {
    $alunos[] = $row;
}

echo json_encode($alunos);
?>
