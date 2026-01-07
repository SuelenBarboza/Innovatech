<?php
header('Content-Type: application/json');
include("db.php"); // seu arquivo de conexão

$sql = "SELECT t.id, t.titulo, t.data, c.comentario, u.nome as autor
        FROM tarefas t
        LEFT JOIN comentarios c ON c.tarefa_id = t.id
        LEFT JOIN usuarios u ON u.id = c.usuario_id";
$result = $conn->query($sql);

$tasks = [];
while ($row = $result->fetch_assoc()) {
    $tasks[] = [
        'id' => $row['id'],
        'titulo' => $row['titulo'],
        'data' => $row['data'],
        'comentario' => $row['comentario'],
        'autor' => $row['autor']
    ];
}

echo json_encode($tasks);
?>
