<?php
header('Content-Type: application/json');
include("../Config/db.php");

// Pega todos os horários
$sql = "SELECT id, projeto_id, versao, tipo, data, diaSemana, hora, atividade 
        FROM horarios ORDER BY data, hora";
$result = $conn->query($sql);

$slots = [];

// Agrupa por data
while ($row = $result->fetch_assoc()) {
    $date = $row['data'];
    if (!isset($slots[$date])) {
        $slots[$date] = [
            'id' => $row['id'],
            'versao' => $row['versao'],
            'tipo' => $row['tipo'],
            'data' => $row['data'],
            'diaSemana' => $row['diaSemana'],
            'horarios' => []
        ];
    }

    $slots[$date]['horarios'][] = [
        'hora' => $row['hora'],
        'atividade' => $row['atividade']
    ];
}

// Retorna apenas os valores (sem as chaves de data)
echo json_encode(array_values($slots));
