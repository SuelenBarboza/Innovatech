<?php
// Visualizar relatórios recebidos pelos professores
session_start();
include("../Config/db.php");

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Public/Login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$tipo = $_SESSION['tipo'] ?? 'aluno';

// professor ou admin
if ($tipo !== 'professor' && $tipo !== 'admin') {
    die("Acesso negado.");
}

if ($tipo === 'admin') {
    $sql = "
    SELECT 
        r.id,
        r.titulo,
        r.criado_em,
        p.nome AS projeto,
        u.nome AS aluno
    FROM relatorios r
    INNER JOIN projetos p ON p.id = r.projeto_id
    INNER JOIN usuarios u ON u.id = r.aluno_id
    ORDER BY r.criado_em DESC
    ";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "
    SELECT 
        r.id,
        r.titulo,
        r.criado_em,
        p.nome AS projeto,
        u.nome AS aluno
    FROM relatorios r
    INNER JOIN projetos p ON p.id = r.projeto_id
    INNER JOIN usuarios u ON u.id = r.aluno_id
    WHERE r.professor_id = ?
    ORDER BY r.criado_em DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Relatórios Recebidos</title>
<link rel="stylesheet" href="../Assets/css/Header.css">
<link rel="stylesheet" href="../Assets/css/Footer.css">
<link rel="stylesheet" href="../Assets/css/Report.css">
</head>
<body>

<?php include("../Includes/Header.php"); ?>

<section class="form-container">
<h2>📥 Relatórios Recebidos</h2>

<?php if ($result->num_rows === 0): ?>
<p>Nenhum relatório recebido.</p>
<?php endif; ?>

<table class="tabela">
<tr>
  <th>Projeto</th>
  <th>Aluno</th>
  <th>Título</th>
  <th>Data</th>
  <th>Ação</th>
</tr>

<?php while ($r = $result->fetch_assoc()): ?>
<tr>
  <td><?= htmlspecialchars($r['projeto']) ?></td>
  <td><?= htmlspecialchars($r['aluno']) ?></td>
  <td><?= htmlspecialchars($r['titulo']) ?></td>
  <td><?= date("d/m/Y", strtotime($r['criado_em'])) ?></td>
  <td>
    <a href="RespondReport.php?id=<?= $r['id'] ?>">Responder</a>
  </td>
</tr>
<?php endwhile; ?>

</table>
</section>

<?php include("../Includes/Footer.php"); ?>
</body>
</html>
