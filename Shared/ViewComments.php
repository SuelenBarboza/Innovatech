<?php
// Visualiza os comentarios do projeto
session_start();
include("../Config/db.php");

// 🔐 valida login
if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não logado.");
}

$usuario_id = $_SESSION['usuario_id'];

// 🔎 valida projeto
if (!isset($_GET['projeto_id'])) {
    header("Location: ViewListProject.php");
    exit;
}

$projeto_id = (int) $_GET['projeto_id'];

// 📌 busca dados do projeto
$sqlProjeto = "SELECT nome FROM projetos WHERE id = ?";
$stmt = $conn->prepare($sqlProjeto);
$stmt->bind_param("i", $projeto_id);
$stmt->execute();
$projeto = $stmt->get_result()->fetch_assoc();

if (!$projeto) {
    die("Projeto não encontrado.");
}

// 💬 busca comentários
$sqlComentarios = "
    SELECT 
        c.id,
        c.comentario,
        c.criado_em,
        c.usuario_id,
        u.nome
    FROM comentarios c
    INNER JOIN usuarios u ON c.usuario_id = u.id
    WHERE c.projeto_id = ?
    ORDER BY c.criado_em DESC
";


$stmt = $conn->prepare($sqlComentarios);
$stmt->bind_param("i", $projeto_id);
$stmt->execute();
$resultComentarios = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Comentários do Projeto</title>
<link rel="stylesheet" href="../Assets/css/Header.css">
<link rel="stylesheet" href="../Assets/css/Footer.css">
<link rel="stylesheet" href="../Assets/css/Comments.css">
</head>
<body>

<?php include("../Includes/Header.php"); ?>

<section class="form-container">
<h2>💬 Comentários — <?= htmlspecialchars($projeto['nome']) ?></h2>

<!-- LISTA DE COMENTÁRIOS -->
<div class="comentarios-lista">

<?php if ($resultComentarios->num_rows === 0): ?>
<p>Nenhum comentário ainda.</p>
<?php endif; ?>

<?php while ($c = $resultComentarios->fetch_assoc()): ?>
<div class="comentario-item">

  <strong><?= htmlspecialchars($c['nome']) ?></strong>
  <span><?= date("d/m/Y H:i", strtotime($c['criado_em'])) ?></span>

  <p><?= nl2br(htmlspecialchars($c['comentario'])) ?></p>

  <?php if ($c['usuario_id'] == $usuario_id): ?>
    <a 
      href="EditComment.php?id=<?= $c['id'] ?>&projeto_id=<?= $projeto_id ?>" 
      class="btn-editar-comentario"
    >
      ✏️ Editar
    </a>
  <?php endif; ?>

</div>
<?php endwhile; ?>


</div>

<hr>

<!-- NOVO COMENTÁRIO -->
<h3>✍️ Novo Comentário</h3>

<form method="POST" action="../Config/ProcessComments.php">
  <input type="hidden" name="projeto_id" value="<?= $projeto_id ?>">

  <textarea name="comentario" required placeholder="Escreva seu comentário..."></textarea>

  <div class="form-actions">
    <button type="submit">Enviar</button>
    <a href="ViewProject.php?id=<?= $projeto_id ?>" class="btn-voltar">⬅ Voltar</a>
  </div>
</form>

</section>

<?php include("../Includes/Footer.php"); ?>
</body>
</html>
