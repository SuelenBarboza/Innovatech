<?php
// Visualizar e responder a um relatório enviado por um aluno
session_start();
include("../Config/db.php");

if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não logado.");
}

$usuario_id = $_SESSION['usuario_id'];
$tipo = $_SESSION['tipo'] ?? '';

if ($tipo !== 'professor' && $tipo !== 'admin') {
    die("Acesso negado.");
}

if (!isset($_GET['id'])) {
    header("Location: ViewReportsTeacher.php");
    exit;
}

$relatorio_id = (int) $_GET['id'];

$sql = "
SELECT 
    r.*,
    p.nome AS projeto,
    u.nome AS aluno
FROM relatorios r
INNER JOIN projetos p ON p.id = r.projeto_id
INNER JOIN usuarios u ON u.id = r.aluno_id
WHERE r.id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $relatorio_id);
$stmt->execute();
$relatorio = $stmt->get_result()->fetch_assoc();

if (!$relatorio) {
    die("Relatório não encontrado.");
}

// verifica se já respondeu
$sqlResp = "SELECT resposta FROM resposta_relatorio WHERE relatorio_id = ?";
$stmt = $conn->prepare($sqlResp);
$stmt->bind_param("i", $relatorio_id);
$stmt->execute();
$respostaExistente = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Responder Relatório</title>
<link rel="stylesheet" href="../Assets/css/Header.css">
<link rel="stylesheet" href="../Assets/css/Footer.css">
<link rel="stylesheet" href="../Assets/css/Report.css">
</head>
<body>

<?php include("../Includes/Header.php"); ?>

<section class="form-container">
<h2>📄 Relatório — <?= htmlspecialchars($relatorio['titulo']) ?></h2>

<p><strong>Projeto:</strong> <?= htmlspecialchars($relatorio['projeto']) ?></p>
<p><strong>Aluno:</strong> <?= htmlspecialchars($relatorio['aluno']) ?></p>

<hr>

<p><?= nl2br(htmlspecialchars($relatorio['descricao'])) ?></p>

<hr>

<form method="POST" action="../Config/ProcessRespondReport.php">
<input type="hidden" name="relatorio_id" value="<?= $relatorio_id ?>">

<label>Resposta do Professor</label>
<textarea name="resposta" rows="6" required><?= $respostaExistente['resposta'] ?? '' ?></textarea>

<div class="form-actions">
  <button type="submit">💾 Salvar Resposta</button>
  <a href="ViewReportsTeacher.php" class="btn-voltar">⬅ Voltar</a>
</div>
</form>

</section>

<?php include("../Includes/Footer.php"); ?>
</body>
</html>
