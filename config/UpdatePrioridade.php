<?php
// Prioridade da lista de projetos
include("../Config/db.php");
session_start();

if (!isset($_POST['id'], $_SESSION['usuario_id'])) {
    echo "erro_sessao";
    exit;
}

$projeto_id = intval($_POST['id']);
$usuario_id = intval($_SESSION['usuario_id']);
$prioridade = $_POST['prioridade'] ?? null;

// Prioridades permitidas
$permitidas = ['Baixa', 'Média', 'Alta'];

// Verificar se usuário tem acesso ao projeto
$sql_verifica = "
    SELECT id FROM projeto_usuario 
    WHERE projeto_id = ? AND usuario_id = ?
    UNION
    SELECT id FROM projetos WHERE id = ? AND criador_id = ?
";
$stmt_verifica = $conn->prepare($sql_verifica);
$stmt_verifica->bind_param("iiii", $projeto_id, $usuario_id, $projeto_id, $usuario_id);
$stmt_verifica->execute();
$stmt_verifica->store_result();

if ($stmt_verifica->num_rows == 0) {
    echo "sem_permissao";
    exit;
}

// Se vier vazio, salva como NULL
if ($prioridade === "" || $prioridade === null) {
    // Tenta atualizar projeto_usuario
    $sql = "UPDATE projeto_usuario SET prioridade = NULL WHERE projeto_id = ? AND usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $projeto_id, $usuario_id);
    
    if ($stmt->execute()) {
        echo "ok";
    } else {
        // Se não existe registro, cria
        $sql_insert = "INSERT INTO projeto_usuario (projeto_id, usuario_id, prioridade, papel) 
                      VALUES (?, ?, NULL, 'Aluno')";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ii", $projeto_id, $usuario_id);
        echo $stmt_insert->execute() ? "ok" : "erro";
    }
}
// Se vier uma prioridade válida
elseif (in_array($prioridade, $permitidas)) {
    // Tenta atualizar
    $sql = "UPDATE projeto_usuario SET prioridade = ? WHERE projeto_id = ? AND usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $prioridade, $projeto_id, $usuario_id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo "ok";
    } else {
        // Se não existe registro, cria
        $sql_insert = "INSERT INTO projeto_usuario (projeto_id, usuario_id, prioridade, papel) 
                      VALUES (?, ?, ?, 'Aluno')";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iis", $projeto_id, $usuario_id, $prioridade);
        echo $stmt_insert->execute() ? "ok" : "erro";
    }
}
// Qualquer outra coisa é erro
else {
    echo "erro_dados";
}