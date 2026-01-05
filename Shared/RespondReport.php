<?php
//Relatorios recebidos pelo professor/admin para responder
session_start();
include("../Config/db.php");

// ==========================
// VALIDAÇÃO DE LOGIN
// ==========================
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Public/Login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$tipo = $_SESSION['usuario_tipo'] ?? 'Aluno';

// Apenas Professor ou Admin
if (!in_array($tipo, ['Professor', 'Admin'])) {
    die("Acesso negado.");
}

// ==========================
// VALIDAÇÃO DO ID
// ==========================
$relatorio_id = (int) ($_GET['id'] ?? 0);
if ($relatorio_id <= 0) {
    header("Location: ViewReportsTeacher.php");
    exit;
}

// ==========================
// BUSCAR RELATÓRIO
// ==========================
$sqlRelatorio = "
    SELECT 
        r.titulo,
        r.descricao,
        r.professor_id,
        u.nome AS aluno
    FROM relatorios r
    INNER JOIN usuarios u ON u.id = r.aluno_id
    WHERE r.id = ?
";
$stmt = $conn->prepare($sqlRelatorio);
$stmt->bind_param("i", $relatorio_id);
$stmt->execute();
$relatorio = $stmt->get_result()->fetch_assoc();

if (!$relatorio) {
    die("Relatório não encontrado.");
}

// Professor só pode responder relatórios dele
if ($tipo === 'Professor' && $relatorio['professor_id'] != $usuario_id) {
    die("Acesso negado.");
}

// ==========================
// HISTÓRICO DE RESPOSTAS
// ==========================
$sqlRespostas = "
    SELECT 
        rr.resposta,
        rr.respondido_em,
        u.nome AS respondente,
        u.tipo_solicitado
    FROM resposta_relatorio rr
    INNER JOIN usuarios u ON u.id = rr.respondente_id
    WHERE rr.relatorio_id = ?
    ORDER BY rr.respondido_em ASC
";
$stmt = $conn->prepare($sqlRespostas);
$stmt->bind_param("i", $relatorio_id);
$stmt->execute();
$respostas = $stmt->get_result();
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

<h2>📄 Relatório Recebidos</h2>

<p><strong>Aluno:</strong> <?= htmlspecialchars($relatorio['aluno']) ?></p>
<p><strong>Título:</strong> <?= htmlspecialchars($relatorio['titulo']) ?></p>

<div class="relatorio-descricao">
    <?= nl2br(htmlspecialchars($relatorio['descricao'])) ?>
</div>

<hr>

<h3>💬 Histórico de Respostas</h3>

<?php if ($respostas->num_rows === 0): ?>
    <p>Nenhuma resposta ainda.</p>
<?php endif; ?>

<?php while ($r = $respostas->fetch_assoc()): ?>
    <div class="resposta-box">
        <strong>
            <?= htmlspecialchars($r['respondente']) ?>
            (<?= htmlspecialchars($r['tipo_solicitado']) ?>)
        </strong><br>
        <small><?= date("d/m/Y H:i", strtotime($r['respondido_em'])) ?></small>

        <p><?= nl2br(htmlspecialchars($r['resposta'])) ?></p>
    </div>
<?php endwhile; ?>

<hr>

<h3>✍️ Nova Resposta</h3>

<form action="../Config/ProcessRespondReport.php" method="POST">
    <input type="hidden" name="relatorio_id" value="<?= $relatorio_id ?>">

    <label>Resposta</label>
    <textarea name="resposta" rows="6" required></textarea>

    <div class="form-actions">
        <button type="submit">💾 Enviar Resposta</button>
        <a href="ViewReportsTeacher.php" class="btn-voltar">⬅ Voltar</a>
    </div>
</form>

</section>

<?php include("../Includes/Footer.php"); ?>

</body>
</html>
