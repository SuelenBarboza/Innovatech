<?php
include("../Config/db.php");
session_start();

if (!isset($_POST['id'], $_SESSION['usuario_id'])) {
    echo "erro_sessao";
    exit;
}

$projeto_id = (int) $_POST['id'];
$usuario_id = (int) $_SESSION['usuario_id'];
$prioridade = $_POST['prioridade'] ?? null;

$permitidas = ['Baixa', 'Média', 'Alta'];

/*
|--------------------------------------------------------------------------
| Permissão: só quem participa ou criou
|--------------------------------------------------------------------------
*/
$sql = "
SELECT 1
FROM projetos p
WHERE p.id = ?
AND (
    p.criador_id = ?
    OR EXISTS (
        SELECT 1 FROM projeto_usuario pu
        WHERE pu.projeto_id = p.id AND pu.usuario_id = ?
    )
)
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $projeto_id, $usuario_id, $usuario_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo "sem_permissao";
    exit;
}

/*
|--------------------------------------------------------------------------
| Prioridade vazia → default
|--------------------------------------------------------------------------
*/
if ($prioridade === "" || $prioridade === null) {
    $sql = "UPDATE projetos SET prioridade = DEFAULT WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $projeto_id);
    echo $stmt->execute() ? "ok" : "erro";
    exit;
}

/*
|--------------------------------------------------------------------------
| Prioridade inválida
|--------------------------------------------------------------------------
*/
if (!in_array($prioridade, $permitidas)) {
    echo "erro_dados";
    exit;
}

/*
|--------------------------------------------------------------------------
| Atualiza prioridade
|--------------------------------------------------------------------------
*/
$sql = "UPDATE projetos SET prioridade = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $prioridade, $projeto_id);

echo $stmt->execute() ? "ok" : "erro";
