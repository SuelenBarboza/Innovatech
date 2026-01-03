<?php
// Edita o comentario existente
session_start();
include("../Config/db.php");

if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não logado.");
}

$usuario_id = $_SESSION['usuario_id'];

$id = (int)($_GET['id'] ?? 0);
$projeto_id = (int)($_GET['projeto_id'] ?? 0);

$sql = "SELECT comentario FROM comentarios WHERE id = ? AND usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Comentário não encontrado ou sem permissão.");
}

$comentario = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Comentário</title>
<link rel="stylesheet" href="../Assets/css/Header.css">
<link rel="stylesheet" href="../Assets/css/Footer.css">
<link rel="stylesheet" href="../Assets/css/Comments.css">
</head>
<body>

<?php include("../Includes/Header.php"); ?>

<section class="form-container">
<h2>✏️ Editar Comentário</h2>

<form method="POST" action="../Config/ProcessEditComment.php">
  <input type="hidden" name="id" value="<?= $id ?>">
  <input type="hidden" name="projeto_id" value="<?= $projeto_id ?>">

  <textarea name="comentario" required><?= htmlspecialchars($comentario['comentario']) ?></textarea>

  <div class="form-actions">
    <button type="submit">Salvar Alterações</button>
    <a href="ViewComments.php?projeto_id=<?= $projeto_id ?>" class="btn-voltar">Cancelar</a>
  </div>
</form>

</section>

<?php include("../Includes/Footer.php"); ?>
</body>
</html>
