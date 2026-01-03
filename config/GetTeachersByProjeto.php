<?php
// Busca os professores associados a um projeto específico
include("../Config/db.php");

if (!isset($_GET['projeto_id'])) {
    echo json_encode([]);
    exit;
}

$projeto_id = (int) $_GET['projeto_id'];

$sql = "
    SELECT u.id, u.nome
    FROM projeto_orientador po
    INNER JOIN usuarios u ON u.id = po.professor_id
    WHERE po.projeto_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $projeto_id);
$stmt->execute();

$result = $stmt->get_result();
$professores = [];

while ($row = $result->fetch_assoc()) {
    $professores[] = $row;
}

echo json_encode($professores);
