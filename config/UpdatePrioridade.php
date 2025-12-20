<?php
include("db.php");

if (!isset($_POST['id'])) {
    echo "erro";
    exit;
}

$id = intval($_POST['id']);
$prioridade = $_POST['prioridade'] ?? null;

// Prioridades permitidas
$permitidas = ['Baixa', 'Média', 'Alta'];

// Se vier vazio, salva como NULL
if ($prioridade === "" || $prioridade === null) {
    $sql = "UPDATE projetos SET prioridade = NULL WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
} 
// Se vier uma prioridade válida
elseif (in_array($prioridade, $permitidas)) {
    $sql = "UPDATE projetos SET prioridade = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $prioridade, $id);
} 
// Qualquer outra coisa é erro
else {
    echo "erro";
    exit;
}

if ($stmt->execute()) {
    echo "ok";
} else {
    echo "erro";
}
