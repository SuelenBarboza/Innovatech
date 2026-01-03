<?php
// Adiciona comentarios para os projetos
session_start();
include("../Config/db.php");

if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não logado.");
}

$usuario_id = (int) $_SESSION['usuario_id'];

// Busca projetos que o usuário participa
$sqlProjetos = "
SELECT DISTINCT p.id, p.nome
FROM projetos p
LEFT JOIN projeto_aluno pa ON pa.projeto_id = p.id
LEFT JOIN projeto_orientador po ON po.projeto_id = p.id
WHERE p.criador_id = ? OR pa.usuario_id = ? OR po.professor_id = ?
ORDER BY p.nome
";

$stmt = $conn->prepare($sqlProjetos);
$stmt->bind_param("iii", $usuario_id, $usuario_id, $usuario_id);
$stmt->execute();
$resultProjetos = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Comentários</title>
    <link rel="stylesheet" href="../Assets/css/Header.css">
    <link rel="stylesheet" href="../Assets/css/Footer.css">
    <link rel="stylesheet" href="../Assets/css/Comments.css">
</head>
<body>

<?php include("../Includes/Header.php"); ?>

<div class="form-container">
    <h1>Comentários do Projeto</h1>

    <form action="../Config/ProcessComments.php" method="POST">

        <div class="form-group">
            <label for="projeto_id">Projeto:</label>
            <select name="projeto_id" required>
                <option value="">Selecione um projeto</option>
                <?php while ($p = $resultProjetos->fetch_assoc()) { ?>
                    <option value="<?= $p['id'] ?>">
                        <?= htmlspecialchars($p['nome']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label for="comentario">Comentário:</label>
            <textarea name="comentario" placeholder="Escreva seu comentário..." required></textarea>
        </div>

        <div class="form-actions">
            <button type="submit">Enviar</button>
            <button type="reset">Limpar</button>
        </div>

    </form>
</div>

<?php include("../Includes/Footer.php"); ?>

</body>
</html>
