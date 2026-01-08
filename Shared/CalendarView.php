<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("db.php");

// Verificar se foi passado um projeto específico via GET
$projeto_id = isset($_GET['projeto_id']) ? (int)$_GET['projeto_id'] : null;

/*
====================================
 BUSCAR TODOS OS PROJETOS PARA O SELECTOR
====================================
*/
$allProjects = [];
$sqlAllProjects = "
SELECT 
    p.id,
    p.nome
FROM projetos p
WHERE p.arquivado = 0
ORDER BY p.nome
";

$resAllProjects = $conn->query($sqlAllProjects);
while ($proj = $resAllProjects->fetch_assoc()) {
    $allProjects[] = $proj;
}

/*
====================================
 PROJETO ESPECÍFICO OU TODOS
====================================
*/
$projects = [];

if ($projeto_id) {
    // Buscar apenas o projeto específico
    $sqlProjetos = "
    SELECT 
        p.id,
        p.nome,
        p.descricao,
        p.data_inicio,
        p.data_fim,
        p.categoria,
        p.status,
        p.prioridade
    FROM projetos p
    WHERE p.id = $projeto_id
      AND p.arquivado = 0
    ";
} else {
    // Buscar todos os projetos
    $sqlProjetos = "
    SELECT 
        p.id,
        p.nome,
        p.descricao,
        p.data_inicio,
        p.data_fim,
        p.categoria,
        p.status,
        p.prioridade
    FROM projetos p
    WHERE p.arquivado = 0
    ";
}

$resProjetos = $conn->query($sqlProjetos);

while ($p = $resProjetos->fetch_assoc()) {
    $projetoId = $p['id'];

    // Alunos do projeto
    $alunos = [];
    $sqlAlunos = "
        SELECT u.nome
        FROM projeto_usuario pu
        JOIN usuarios u ON u.id = pu.usuario_id
        WHERE pu.projeto_id = $projetoId
          AND pu.papel = 'Aluno'
    ";
    $resAlunos = $conn->query($sqlAlunos);
    while ($a = $resAlunos->fetch_assoc()) {
        $alunos[] = $a['nome'];
    }

    // Orientadores
    $orientadores = [];
    $sqlOrientadores = "
        SELECT u.nome
        FROM projeto_usuario pu
        JOIN usuarios u ON u.id = pu.usuario_id
        WHERE pu.projeto_id = $projetoId
          AND pu.papel = 'Orientador'
    ";
    $resOrient = $conn->query($sqlOrientadores);
    while ($o = $resOrient->fetch_assoc()) {
        $orientadores[] = $o['nome'];
    }

    // Buscar tarefas do projeto
    $tarefas = [];
    $sqlTarefas = "
        SELECT 
            id,
            titulo,
            descricao,
            data_prevista,
            status,
            prioridade
        FROM tarefas
        WHERE projeto_id = $projetoId
        ORDER BY data_prevista
    ";
    $resTarefas = $conn->query($sqlTarefas);
    while ($t = $resTarefas->fetch_assoc()) {
        $tarefas[] = $t;
    }

    $projects[] = [
        "id" => $p['id'],
        "nome" => $p['nome'],
        "descricao" => $p['descricao'],
        "data_inicio" => $p['data_inicio'],
        "data_fim" => $p['data_fim'],
        "tipo" => $p['categoria'],
        "status" => $p['status'],
        "prioridade" => $p['prioridade'],
        "alunos" => $alunos,
        "orientadores" => $orientadores,
        "tarefas" => $tarefas
    ];
}

/*
====================================
 TIME SLOTS
====================================
*/
$timeSlots = [];

echo json_encode([
    "allProjects" => $allProjects,
    "projects" => $projects,
    "timeSlots" => $timeSlots
]);