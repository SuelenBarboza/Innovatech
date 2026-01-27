<?php
// Atualiza STATUS do projeto (GLOBAL)

include("../Config/db.php");
session_start();

/* =========================
   VALIDAÇÕES BÁSICAS
========================= */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "metodo_invalido";
    exit;
}

if (!isset($_SESSION['usuario_id'])) {
    echo "sessao_invalida";
    exit;
}

$usuario_id = (int) $_SESSION['usuario_id'];
$projeto_id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$status     = $_POST['status'] ?? '';

if ($projeto_id <= 0 || $status === '') {
    echo "dados_invalidos";
    exit;
}

/* =========================
   STATUS PERMITIDOS (ENUM)
========================= */

$permitidos = ['Planejamento', 'Andamento', 'Pendente', 'Concluído'];

if (!in_array($status, $permitidos)) {
    echo "status_invalido";
    exit;
}

/* =========================
   PERMISSÃO: CRIADOR OU VINCULADO
========================= */

$sql = "
    SELECT 1
    FROM projetos p
    WHERE p.id = ?
      AND (
          p.criador_id = ?
          OR EXISTS (
              SELECT 1
              FROM projeto_usuario pu
              WHERE pu.projeto_id = p.id
                AND pu.usuario_id = ?
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

$stmt->close();

/* =========================
   ATUALIZA STATUS (TABELA CORRETA)
========================= */

$sql = "UPDATE projetos SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $projeto_id);

if ($stmt->execute()) {
    echo "ok";
} else {
    echo "erro";
}

$stmt->close();
$conn->close();
