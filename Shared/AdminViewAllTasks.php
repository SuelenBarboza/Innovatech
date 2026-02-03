<?php
// Página de visualização de todas as tarefas (Admin)
include("../Config/db.php");
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'Admin') {
    die("Acesso negado");
}

// ===== FILTROS =====
$nome       = trim($_GET['nome'] ?? '');
$status     = $_GET['status'] ?? '';
$prioridade = $_GET['prioridade'] ?? '';
$responsavel = intval($_GET['responsavel'] ?? 0);

// ===== QUERY =====
$sql = "
    SELECT
        t.id,
        t.nome,
        t.prioridade,
        t.status,
        t.data_fim,
        t.criado_em,
        u.nome AS responsavel_nome,
        p.nome AS projeto_nome
    FROM tarefas t
    INNER JOIN usuarios u ON u.id = t.responsavel_id
    INNER JOIN projetos p ON p.id = t.projeto_id
    WHERE 1 = 1
";

$params = [];
$types  = "";

// ===== APLICA FILTROS =====
if ($nome !== '') {
    $sql .= " AND t.nome LIKE ?";
    $params[] = "%$nome%";
    $types   .= "s";
}

if ($status !== '') {
    $sql .= " AND t.status = ?";
    $params[] = $status;
    $types   .= "s";
}

if ($prioridade !== '') {
    $sql .= " AND t.prioridade = ?";
    $params[] = $prioridade;
    $types   .= "s";
}

if ($responsavel > 0) {
    $sql .= " AND t.responsavel_id = ?";
    $params[] = $responsavel;
    $types   .= "i";
}

$sql .= " ORDER BY t.criado_em DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// ===== LISTA DE USUÁRIOS =====
$sqlUsuarios = "SELECT id, nome FROM usuarios ORDER BY nome";
$usuarios = $conn->query($sqlUsuarios);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Admin • Todas as Tarefas</title>

    <link rel="stylesheet" href="../Assets/css/Header.css">
    <link rel="stylesheet" href="../Assets/css/Footer.css">
    <link rel="stylesheet" href="../Assets/css/AdminViewAllTasks.css">
</head>
<body>

<?php include("../Includes/Header.php"); ?>

<h1 style="text-align:center;">📝 Todas as Tarefas (Admin)</h1>

<!-- ===== FILTROS ===== -->
<form method="GET" class="filtros">
    <input
        type="text"
        name="nome"
        placeholder="Buscar por tarefa"
        value="<?= htmlspecialchars($nome) ?>"
    >

    <select name="status">
        <option value="">Todos os status</option>
        <?php
        $statusList = ['Planejamento','Andamento','Pendente','Concluído'];
        foreach ($statusList as $s):
        ?>
            <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>>
                <?= $s ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="prioridade">
        <option value="">Todas prioridades</option>
        <option value="Baixa" <?= $prioridade === 'Baixa' ? 'selected' : '' ?>>Baixa</option>
        <option value="Média" <?= $prioridade === 'Média' ? 'selected' : '' ?>>Média</option>
        <option value="Alta"  <?= $prioridade === 'Alta'  ? 'selected' : '' ?>>Alta</option>
    </select>

    <select name="responsavel">
        <option value="">Todos os responsáveis</option>
        <?php while ($u = $usuarios->fetch_assoc()): ?>
            <option value="<?= $u['id'] ?>" <?= $responsavel == $u['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($u['nome']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <button type="submit">Filtrar</button>
    <a href="AdminViewAllTasks.php" class="btn-limpar">Limpar</a>
</form>

<!-- ===== TABELA ===== -->
<table>
    <thead>
        <tr>
            <th>Tarefa</th>
            <th>Projeto</th>
            <th>Prioridade</th>
            <th>Status</th>
            <th>Prazo</th>
            <th>Responsável</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows === 0): ?>
            <tr>
                <td colspan="6" style="text-align:center;">
                    Nenhuma tarefa encontrada
                </td>
            </tr>
        <?php endif; ?>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['nome']) ?></td>
                <td><?= htmlspecialchars($row['projeto_nome']) ?></td>
                <td><?= htmlspecialchars($row['prioridade'] ?? '—') ?></td>
                <td><?= htmlspecialchars($row['status'] ?? '—') ?></td>
                <td>
                    <?= $row['data_fim']
                        ? date("d/m/Y", strtotime($row['data_fim']))
                        : '—' ?>
                </td>
                <td><?= htmlspecialchars($row['responsavel_nome']) ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include("../Includes/Footer.php"); ?>
<script src="../Assets/js/AdminViewAllTasks.js"></script>

</body>
</html>
