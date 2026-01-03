<?php
// Mostra a página de visualização de um relatório enviado pelo aluno
session_start();
include("../Config/db.php");

// 🔐 valida login
if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não logado.");
}

$aluno_id = $_SESSION['usuario_id'];

// 🔎 valida id do relatório
if (!isset($_GET['id'])) {
    header("Location: MyReports.php");
    exit;
}

$relatorio_id = (int) $_GET['id'];

// 📌 busca relatório (somente se for do aluno)
$sql = "
    SELECT 
        r.*, 
        p.nome AS projeto,
        u.nome AS professor
    FROM relatorios r
    INNER JOIN projetos p ON p.id = r.projeto_id
    LEFT JOIN usuarios u ON u.id = r.professor_id
    WHERE r.id = ? AND r.aluno_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $relatorio_id, $aluno_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Relatório não encontrado ou acesso negado.");
}

$relatorio = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Relatório</title>
<link rel="stylesheet" href="../Assets/css/Header.css">
<link rel="stylesheet" href="../Assets/css/Footer.css">
<link rel="stylesheet" href="../Assets/css/Report.css">
</head>
<body>

<?php include("../Includes/Header.php"); ?>

<section class="form-container">

<h2>📄 Relatório</h2>

<div class="report-box">
  <p><strong>Projeto:</strong> <?= htmlspecialchars($relatorio['projeto']) ?></p>
  <p><strong>Título:</strong> <?= htmlspecialchars($relatorio['titulo']) ?></p>
  <p><strong>Enviado em:</strong> <?= date("d/m/Y H:i", strtotime($relatorio['criado_em'])) ?></p>

  <hr>

  <p><strong>Descrição enviada:</strong></p>
  <p><?= nl2br(htmlspecialchars($relatorio['descricao'])) ?></p>
</div>

<hr>

<h3>🧑‍🏫 Resposta do Professor</h3>

<?php if (empty($relatorio['resposta_professor'])): ?>
  <p class="status-pendente">⏳ Aguardando resposta do professor.</p>
<?php else: ?>
  <div class="resposta-box">
    <p><strong>Professor:</strong> <?= htmlspecialchars($relatorio['professor']) ?></p>
    <p><strong>Respondido em:</strong> <?= date("d/m/Y H:i", strtotime($relatorio['respondido_em'])) ?></p>

    <p><?= nl2br(htmlspecialchars($relatorio['resposta_professor'])) ?></p>
  </div>
<?php endif; ?>

<div class="form-actions">
  <a href="MyReports.php" class="btn-voltar">⬅ Voltar</a>
</div>

</section>

<?php include("../Includes/Footer.php"); ?>
</body>
</html>
