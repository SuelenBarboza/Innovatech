<?php
// Status da lista de projetos
include("../Config/db.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "metodo_invalido";
    exit;
}

if (!isset($_SESSION['usuario_id'])) {
    echo "sessao_invalida";
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$status = $_POST['status'] ?? '';
$usuario_id = intval($_SESSION['usuario_id']);

if ($id <= 0 || $status === '') {
    echo "dados_invalidos";
    exit;
}

// Verificar se usuário tem permissão para editar este projeto
$sql_verifica = "
    SELECT p.id FROM projetos p
    LEFT JOIN projeto_usuario pu ON p.id = pu.projeto_id AND pu.usuario_id = ?
    WHERE p.id = ? AND (p.criador_id = ? OR pu.usuario_id = ?)
";
$stmt_verifica = $conn->prepare($sql_verifica);
$stmt_verifica->bind_param("iiii", $usuario_id, $id, $usuario_id, $usuario_id);
$stmt_verifica->execute();
$stmt_verifica->store_result();

if ($stmt_verifica->num_rows == 0) {
    echo "sem_permissao";
    exit;
}

// 🔥 MAPEAMENTO FRONT → BANCO (ENUM alinhado)
$mapaStatus = [
    'Planejamento' => 'Planejamento',
    'Andamento'    => 'Andamento',
    'Pendente'     => 'Pendente',
    'Concluído'    => 'Concluído'
];


if (!isset($mapaStatus[$status])) {
    echo "status_invalido";
    exit;
}

$statusBanco = $mapaStatus[$status];

$stmt = $conn->prepare("UPDATE projetos SET status = ? WHERE id = ?");
$stmt->bind_param("si", $statusBanco, $id);

if ($stmt->execute()) {
    echo "ok";
} else {
    echo "erro";
}

$stmt->close();
$conn->close();