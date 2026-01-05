<?php
session_start();
include("../Config/db.php");

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Public/Login.php");
    exit;
}

$usuario_id = (int) $_SESSION['usuario_id'];
$tipo = $_SESSION['usuario_tipo'] ?? 'Aluno';

// ==========================
// Determinar projeto selecionado
// ==========================
$projeto_id = isset($_GET['projeto_id']) ? (int) $_GET['projeto_id'] : 0;

// ==========================
// Buscar projetos do usuário (ou todos se Admin)
// ==========================
if ($tipo !== 'Admin') {
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
} else {
    $sqlProjetos = "SELECT id, nome FROM projetos ORDER BY nome";
    $stmt = $conn->prepare($sqlProjetos);
}

$stmt->execute();
$resultProjetos = $stmt->get_result();
$projetos = $resultProjetos->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if ($projeto_id <= 0 || ($tipo !== 'Admin' && !in_array($projeto_id, array_column($projetos, 'id')))) {
    $projeto_id = $projetos[0]['id'] ?? 0;
}

// ==========================
// Buscar comentários do projeto
// ==========================
if ($projeto_id > 0) {
    $sqlComentarios = "
        SELECT c.id AS comentario_id, c.comentario, c.criado_em, u.nome AS usuario_nome, u.tipo_solicitado, c.usuario_id, p.nome AS projeto_nome
        FROM comentarios c
        INNER JOIN usuarios u ON u.id = c.usuario_id
        INNER JOIN projetos p ON p.id = c.projeto_id
        WHERE c.projeto_id = ?
        ORDER BY c.criado_em ASC
    ";
    $stmt = $conn->prepare($sqlComentarios);
    $stmt->bind_param("i", $projeto_id);
    $stmt->execute();
    $resultComentarios = $stmt->get_result();
    $stmt->close();

    $nomeProjeto = $resultComentarios->fetch_assoc()['projeto_nome'] ?? '';
    $resultComentarios->data_seek(0);
} else {
    $resultComentarios = null;
    $nomeProjeto = '';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Comentários do Projeto</title>
<link rel="stylesheet" href="../Assets/css/Header.css">
<link rel="stylesheet" href="../Assets/css/Footer.css">
<link rel="stylesheet" href="../Assets/css/Comments.css">
<style>
.comentario-box { border:1px solid #ccc; padding:10px; margin-bottom:10px; border-radius:5px; }
.editar-comentario { margin-top:5px; }
textarea { width:100%; min-height:60px; }
</style>
</head>
<body>

<?php include("../Includes/Header.php"); ?>

<section class="form-container">
    <h1>Comentários do Projeto</h1>

    <!-- Seleção de Projeto -->
    <form method="GET" action="ViewComments.php">
        <label for="projeto_id">Selecionar Projeto:</label>
        <select name="projeto_id" onchange="this.form.submit()">
            <?php foreach ($projetos as $p): ?>
                <option value="<?= $p['id'] ?>" <?= $p['id'] == $projeto_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <h2><?= htmlspecialchars($nomeProjeto) ?></h2>

    <?php if (!$resultComentarios || $resultComentarios->num_rows === 0): ?>
        <p>Não há comentários para este projeto.</p>
    <?php else: ?>
        <?php while ($c = $resultComentarios->fetch_assoc()): ?>
            <div class="comentario-box">
                <strong><?= htmlspecialchars($c['usuario_nome']) ?> (<?= htmlspecialchars($c['tipo_solicitado']) ?>)</strong>
                <small><?= date("d/m/Y H:i", strtotime($c['criado_em'])) ?></small>
                <p id="texto-comentario-<?= $c['comentario_id'] ?>"><?= nl2br(htmlspecialchars($c['comentario'])) ?></p>

                <?php if ($c['usuario_id'] == $usuario_id): ?>
                    <!-- Formulário de edição -->
                    <div class="editar-comentario">
                        <button type="button" onclick="mostrarEditar(<?= $c['comentario_id'] ?>)">Editar</button>
                        <form id="form-editar-<?= $c['comentario_id'] ?>" action="../Config/ProcessEditComment.php" method="POST" style="display:none; margin-top:5px;">
                            <input type="hidden" name="comentario_id" value="<?= $c['comentario_id'] ?>">
                            <textarea name="comentario" required><?= htmlspecialchars($c['comentario']) ?></textarea>
                            <button type="submit">Salvar</button>
                            <button type="button" onclick="fecharEditar(<?= $c['comentario_id'] ?>)">Cancelar</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>

    <!-- Adicionar comentário -->
    <hr>
    <h3>Adicionar novo comentário</h3>
    <form action="../Config/ProcessComments.php" method="POST">
        <input type="hidden" name="projeto_id" value="<?= $projeto_id ?>">
        <textarea name="comentario" required placeholder="Escreva seu comentário..."></textarea>
        <button type="submit">Enviar comentário</button>
    </form>
</section>

<?php include("../Includes/Footer.php"); ?>

<script>
function mostrarEditar(id) {
    document.getElementById('form-editar-' + id).style.display = 'block';
}
function fecharEditar(id) {
    document.getElementById('form-editar-' + id).style.display = 'none';
}
</script>
</body>
</html>
