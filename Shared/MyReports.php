<?php
// Mostra os relatórios enviados pelo aluno, mas apenas para professores
session_start();
include("../Config/db.php");

// ==========================
// VERIFICAÇÃO DE LOGIN
// ==========================
if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não logado.");
}

// ==========================
// VERIFICAÇÃO DE PERMISSÃO
// ==========================
if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'Aluno') {
    die("Acesso negado. Apenas alunos podem acessar esta página.");
}


$aluno_id = $_SESSION['usuario_id'];

$sql = "
    SELECT r.*, p.nome AS projeto
    FROM relatorios r
    JOIN projetos p ON p.id = r.projeto_id
    WHERE r.aluno_id = ?
    ORDER BY r.criado_em DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Meus Relatórios</title>
<link rel="stylesheet" href="../Assets/css/Header.css">
<link rel="stylesheet" href="../Assets/css/Footer.css">
<link rel="stylesheet" href="../Assets/css/MyReports.css">
</head>
<body>

<?php include("../Includes/Header.php"); ?>

<section class="form-container">
<h2>📑 Meus Relatórios</h2>

<?php if ($result->num_rows === 0): ?>
<p>Você ainda não enviou nenhum relatório.</p>
<?php endif; ?>

<?php while ($r = $result->fetch_assoc()): ?>
<div class="report-card">
  <h3><?= htmlspecialchars($r['titulo']) ?></h3>
  <small>Projeto: <?= htmlspecialchars($r['projeto']) ?></small>
  <p><?= nl2br(htmlspecialchars($r['descricao'])) ?></p>
  <span>📅 <?= date("d/m/Y", strtotime($r['criado_em'])) ?></span>

  <a href="ViewReport.php?id=<?= $r['id'] ?>" class="btn-ver">
    👀 Ver Resposta
  </a>
</div>
<?php endwhile; ?>

</section>

<?php include("../Includes/Footer.php"); ?>
</body>
</html>
