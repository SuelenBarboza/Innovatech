<?php
include("../Config/db.php");

$termo = $_POST['termo'] ?? '';
$tipo  = $_POST['tipo'] ?? '';

$stmt = $conn->prepare("
    SELECT nome 
    FROM usuarios 
    WHERE nome LIKE ? 
      AND tipo_solicitado = ?
      AND aprovado = 1
      AND ativo = 1
    LIMIT 10
");

$like = "%$termo%";
$stmt->bind_param("ss", $like, $tipo);
$stmt->execute();

$result = $stmt->get_result();

$nomes = [];
while ($row = $result->fetch_assoc()) {
    $nomes[] = $row['nome'];
}

header("Content-Type: application/json");
echo json_encode($nomes);
exit;
