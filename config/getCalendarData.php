<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("db.php");
session_start();

/*
====================================
 IDENTIFICAÇÃO DO USUÁRIO
====================================
*/
$usuario_id   = $_SESSION['usuario_id']   ?? null;
$usuario_tipo = $_SESSION['usuario_tipo'] ?? 'comum';

// 🔴 APENAS PARA TESTE LOCAL
if (!$usuario_id) {
    $usuario_id   = 1;
    $usuario_tipo = 'admin';
}

/*
====================================
 PROJETO SELECIONADO
====================================
*/
$projeto_id = (isset($_GET['projeto_id']) && $_GET['projeto_id'] !== 'all')
    ? (int) $_GET['projeto_id']
    : null;

/*
====================================
 PROJETOS PARA O SELECT
====================================
*/
$allProjects = [];

if ($usuario_tipo === 'admin') {

    $sqlAllProjects = "
        SELECT DISTINCT p.id, p.nome
        FROM projetos p
        LEFT JOIN projeto_usuario pu ON pu.projeto_id = p.id
        WHERE COALESCE(pu.arquivado, 0) = 0
        ORDER BY p.nome
    ";

    $stmt = $conn->prepare($sqlAllProjects);

} else {

    $sqlAllProjects = "
        SELECT DISTINCT p.id, p.nome
        FROM projetos p
        JOIN projeto_usuario pu ON pu.projeto_id = p.id
        WHERE pu.usuario_id = ?
          AND COALESCE(pu.arquivado, 0) = 0
        ORDER BY p.nome
    ";

    $stmt = $conn->prepare($sqlAllProjects);
    $stmt->bind_param("i", $usuario_id);
}

$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $allProjects[] = $row;
}

/*
====================================
 PROJETOS + DADOS DO CALENDÁRIO
====================================
*/
$projects = [];

if ($usuario_tipo === 'admin') {

    $sqlProjetos = "
        SELECT DISTINCT p.*
        FROM projetos p
        LEFT JOIN projeto_usuario pu ON pu.projeto_id = p.id
        WHERE COALESCE(pu.arquivado, 0) = 0
        " . ($projeto_id ? "AND p.id = ?" : "") . "
        ORDER BY p.nome
    ";

    $stmt = $conn->prepare($sqlProjetos);

    if ($projeto_id) {
        $stmt->bind_param("i", $projeto_id);
    }

} else {

    $sqlProjetos = "
        SELECT DISTINCT p.*
        FROM projetos p
        JOIN projeto_usuario pu ON pu.projeto_id = p.id
        WHERE pu.usuario_id = ?
          AND COALESCE(pu.arquivado, 0) = 0
          " . ($projeto_id ? "AND p.id = ?" : "") . "
        ORDER BY p.nome
    ";

    $stmt = $conn->prepare($sqlProjetos);

    if ($projeto_id) {
        $stmt->bind_param("ii", $usuario_id, $projeto_id);
    } else {
        $stmt->bind_param("i", $usuario_id);
    }
}

$stmt->execute();
$res = $stmt->get_result();

while ($p = $res->fetch_assoc()) {
    $projetoId = (int) $p['id'];

    // Alunos
    $alunos = [];
    $r = $conn->query("
        SELECT u.nome
        FROM projeto_usuario pu
        JOIN usuarios u ON u.id = pu.usuario_id
        WHERE pu.projeto_id = $projetoId
          AND pu.papel = 'Aluno'
    ");
    while ($a = $r->fetch_assoc()) $alunos[] = $a['nome'];

    // Orientadores
    $orientadores = [];
    $r = $conn->query("
        SELECT u.nome
        FROM projeto_usuario pu
        JOIN usuarios u ON u.id = pu.usuario_id
        WHERE pu.projeto_id = $projetoId
          AND pu.papel = 'Orientador'
    ");
    while ($o = $r->fetch_assoc()) $orientadores[] = $o['nome'];

    
    // Tarefas
    $tarefas = [];
    $r = $conn->query("
        SELECT 
            id,
            nome,
            descricao,
            data_inicio,
            data_fim,
            created_at,
            status,
            prioridade
        FROM tarefas
        WHERE projeto_id = $projetoId
        AND arquivado = 0
        ORDER BY created_at
    ");
    while ($t = $r->fetch_assoc()) $tarefas[] = $t;

   // Comentários
// Comentários
$comentarios = [];
$r = $conn->query("
    SELECT 
        c.id,
        c.comentario,
        c.usuario_id,
        u.nome AS usuario_nome,
        c.created_at
    FROM comentarios c
    JOIN usuarios u ON u.id = c.usuario_id
    WHERE c.projeto_id = $projetoId
    ORDER BY c.created_at
");

while ($c = $r->fetch_assoc()) {
    $comentarios[] = $c;
}







   $projects[] = [
    "id"           => $p['id'],
    "nome"         => $p['nome'],
    "descricao"    => $p['descricao'],
    "data_inicio"  => $p['data_inicio'],
    "data_fim"     => $p['data_fim'],
    "categoria"    => $p['categoria'],
    "status"       => $p['status'],
    "prioridade"   => $p['prioridade'],
    "alunos"       => $alunos,
    "orientadores" => $orientadores,
    "tarefas"      => $tarefas,
    "comentarios"  => $comentarios
    ];

}

/*
====================================
 RETORNO
====================================
*/
echo json_encode([
    "allProjects" => $allProjects,
    "projects"    => $projects,
    "timeSlots"   => []
]);
