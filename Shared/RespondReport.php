<?php
session_start();
include("../Config/db.php");

// VALIDAÇÃO DE LOGIN
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Public/Login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$tipo = $_SESSION['usuario_tipo'] ?? 'Aluno';

// VALIDAÇÃO DO ID DO RELATÓRIO
$relatorio_id = (int) ($_GET['id'] ?? 0);
if ($relatorio_id <= 0) {
    header("Location: MyReports.php");
    exit;
}

// BUSCAR RELATÓRIO
$sqlRelatorio = "
    SELECT 
        r.titulo,
        r.descricao,
        r.aluno_id,
        r.professor_id,
        p.nome AS projeto,
        u.nome AS aluno
    FROM relatorios r
    INNER JOIN projetos p ON p.id = r.projeto_id
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

// VERIFICAR PERMISSÃO DE ACESSO
if ($tipo === 'Aluno' && $usuario_id !== (int)$relatorio['aluno_id']) {
    die("Acesso negado.");
}
if ($tipo === 'Professor' && $usuario_id !== (int)$relatorio['professor_id']) {
    die("Acesso negado.");
}

// HISTÓRICO DE RESPOSTAS
$sqlRespostas = "
    SELECT 
        rr.resposta,
        rr.respondido_em,
        u.nome AS respondente,
        u.tipo_usuario
    FROM resposta_relatorio rr
    INNER JOIN usuarios u ON u.id = rr.respondente_id
    WHERE rr.relatorio_id = ?
    ORDER BY rr.respondido_em ASC
";
$stmt = $conn->prepare($sqlRespostas);
$stmt->bind_param("i", $relatorio_id);
$stmt->execute();
$respostas = $stmt->get_result();

// BUSCAR RESPOSTA DO USUÁRIO LOGADO (para edição, se existir)
$sqlMinhaResposta = "
    SELECT resposta 
    FROM resposta_relatorio 
    WHERE relatorio_id = ? AND respondente_id = ?
    ORDER BY respondido_em DESC
    LIMIT 1
";
$stmt = $conn->prepare($sqlMinhaResposta);
$stmt->bind_param("ii", $relatorio_id, $usuario_id);
$stmt->execute();
$minhaResposta = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Responder Relatório</title>
<link rel="stylesheet" href="../Assets/css/Header.css">
<link rel="stylesheet" href="../Assets/css/Footer.css">
<link rel="stylesheet" href="../Assets/css/RespondReport.css">
</head>
<body>

<?php include("../Includes/Header.php"); ?>

<section class="form-container">

<h2> Relatório — <?= htmlspecialchars($relatorio['titulo']) ?></h2>

<p><strong>Projeto:</strong> <?= htmlspecialchars($relatorio['projeto']) ?></p>
<p><strong>Aluno:</strong> <?= htmlspecialchars($relatorio['aluno']) ?></p>

<div class="relatorio-descricao">
    <?= nl2br(htmlspecialchars($relatorio['descricao'])) ?>
</div>

<hr>

<h3> Histórico de Respostas</h3>

<?php if ($respostas->num_rows === 0): ?>
    <p>Nenhuma resposta ainda.</p>
<?php else: ?>
    <?php while ($r = $respostas->fetch_assoc()): ?>
        <div class="resposta-box <?= $r['respondente'] === $relatorio['aluno'] ? 'aluno' : 'professor' ?>">
            <strong><?= htmlspecialchars($r['respondente']) ?> (<?= htmlspecialchars($r['tipo_usuario']) ?>)</strong><br>
            <small><?= date("d/m/Y H:i", strtotime($r['respondido_em'])) ?></small>
            <p><?= nl2br(htmlspecialchars($r['resposta'])) ?></p>
        </div>
    <?php endwhile; ?>
<?php endif; ?>

<hr>

<h3> Nova Resposta</h3>

<form action="../Config/ProcessRespondReport.php" method="POST">
    <input type="hidden" name="relatorio_id" value="<?= $relatorio_id ?>">

    <label>Resposta</label>
    <textarea name="resposta" rows="6" required><?= htmlspecialchars($minhaResposta['resposta'] ?? '') ?></textarea>

    <div class="form-actions">
        <button type="submit">Enviar Resposta</button>
        <a href="<?= $tipo === 'Aluno' ? 'MyReports.php' : 'ViewReportsTeacher.php' ?>" class="btn-voltar">⬅ Voltar</a>
    </div>
</form>

</section>

<?php include("../Includes/Footer.php"); ?>

</body>
</html>
