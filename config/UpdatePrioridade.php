<?php
include("db.php");

if (!isset($_POST['id']) || !isset($_POST['prioridade'])) {
    echo "erro";
    exit;
}

$id = intval($_POST['id']);
$prioridade = $_POST['prioridade'];

// Validação de segurança
$permitidas = ['Baixa', 'Média', 'Alta'];

if (!in_array($prioridade, $permitidas)) {
    echo "erro";
    exit;
}

$sql = "UPDATE projetos SET prioridade = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $prioridade, $id);

if ($stmt->execute()) {
    echo "ok";
} else {
    echo "erro";
}
