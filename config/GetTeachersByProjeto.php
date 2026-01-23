<?php
// Busca os professores associados a um projeto específico
include("../Config/db.php");

header('Content-Type: application/json');

if (!isset($_GET['projeto_id']) || !is_numeric($_GET['projeto_id'])) {
    echo json_encode([]);
    exit;
}

$projeto_id = (int) $_GET['projeto_id'];

// Seleciona todos os professores do projeto (tabela projeto_orientador + projeto_usuario com papel Orientador)
$sql = "
SELECT u.id, u.nome
FROM projeto_orientador po
JOIN usuarios u ON u.id = po.professor_id
WHERE po.projeto_id = ?

UNION

SELECT u.id, u.nome
FROM projeto_usuario pu
JOIN usuarios u ON u.id = pu.usuario_id
WHERE pu.projeto_id = ? AND pu.papel = 'Orientador'
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $projeto_id, $projeto_id);
$stmt->execute();
$result = $stmt->get_result();

$professores = [];
while($row = $result->fetch_assoc()) {
    $professores[] = $row;
}

echo json_encode($professores);
