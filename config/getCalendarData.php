<?php
header('Content-Type: application/json');
include("db.php"); // sua conexão com o banco

// Buscar projetos
$projects = [];
$projectQuery = "SELECT id, nome, descricao, data_inicio, data_fim, tipo FROM projetos";
$result = $conn->query($projectQuery);
while ($row = $result->fetch_assoc()) {
    // Buscar alunos e professores de cada projeto
    $projId = $row['id'];

    $alunos = [];
    $resAlunos = $conn->query("SELECT nome FROM alunos WHERE projeto_id = $projId");
    while ($al = $resAlunos->fetch_assoc()) $alunos[] = $al['nome'];

    $professores = [];
    $resProf = $conn->query("SELECT nome FROM professores WHERE projeto_id = $projId");
    while ($prof = $resProf->fetch_assoc()) $professores[] = $prof['nome'];

    $projects[] = [
        "id" => $row['id'],
        "nome" => $row['nome'],
        "descricao" => $row['descricao'],
        "data_inicio" => $row['data_inicio'],
        "data_fim" => $row['data_fim'],
        "tipo" => $row['tipo'],
        "alunos" => $alunos,
        "professores" => $professores
    ];
}

// Buscar horários (timeSlots)
$timeSlots = [];
$slotQuery = "SELECT id, versao, tipo, data, dia_semana FROM timeslots";
$resSlots = $conn->query($slotQuery);
while ($slot = $resSlots->fetch_assoc()) {
    $horarios = [];
    $slotId = $slot['id'];
    $resHorarios = $conn->query("SELECT hora, atividade FROM horarios WHERE timeslot_id = $slotId");
    while ($h = $resHorarios->fetch_assoc()) {
        $horarios[] = [
            "hora" => $h['hora'],
            "atividade" => $h['atividade']
        ];
    }

    $timeSlots[] = [
        "id" => $slot['id'],
        "versao" => $slot['versao'],
        "tipo" => $slot['tipo'],
        "data" => $slot['data'],
        "diaSemana" => $slot['dia_semana'],
        "horarios" => $horarios
    ];
}

echo json_encode([
    "projects" => $projects,
    "timeSlots" => $timeSlots
]);
