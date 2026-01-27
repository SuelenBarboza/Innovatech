<?php
//Página de visualização de todos os projetos (Admin)
include("../Config/db.php");
session_start();


if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'Admin') {
    die("Acesso negado");
}



$nome       = trim($_GET['nome'] ?? '');
$status     = $_GET['status'] ?? '';
$prioridade = $_GET['prioridade'] ?? '';
$criador    = intval($_GET['criador'] ?? 0);



$sql = "
    SELECT 
        p.id,
        p.nome,
        p.categoria,
        p.prioridade,
        p.status,
        p.data_fim,
        p.criado_em,
        u.nome AS criador_nome
    FROM projetos p
    INNER JOIN usuarios u ON u.id = p.criador_id
    WHERE 1 = 1
";

$params = [];
$types  = "";


if ($nome !== '') {
    $sql .= " AND p.nome LIKE ?";
    $params[] = "%$nome%";
    $types   .= "s";
}

if ($status !== '') {
    $sql .= " AND p.status = ?";
    $params[] = $status;
    $types   .= "s";
}

if ($prioridade !== '') {
    $sql .= " AND p.prioridade = ?";
    $params[] = $prioridade;
    $types   .= "s";
}

if ($criador > 0) {
    $sql .= " AND p.criador_id = ?";
    $params[] = $criador;
    $types   .= "i";
}

$sql .= " ORDER BY p.criado_em DESC";



$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();



$sqlUsuarios = "SELECT id, nome FROM usuarios ORDER BY nome";
$usuarios = $conn->query($sqlUsuarios);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Admin • Todos os Projetos</title>

    <link rel="stylesheet" href="../Assets/css/Header.css">
    <link rel="stylesheet" href="../Assets/css/Footer.css">
    <link rel="stylesheet" href="../Assets/css/AdminViewAllProjects.css">
</head>
<body>

<?php include("../Includes/Header.php"); ?>

<h1 style="text-align:center;">📊 Todos os Projetos (Admin)</h1>



<form method="GET" class="filtros" style="margin:20px auto; max-width:1100px;">
    <input 
        type="text" 
        name="nome" 
        placeholder="Buscar por nome"
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
        <option value="Baixa"  <?= $prioridade === 'Baixa' ? 'selected' : '' ?>>Baixa</option>
        <option value="Média"  <?= $prioridade === 'Média' ? 'selected' : '' ?>>Média</option>
        <option value="Alta"   <?= $prioridade === 'Alta'  ? 'selected' : '' ?>>Alta</option>
    </select>

    <select name="criador">
        <option value="">Todos os usuários</option>
        <?php while ($u = $usuarios->fetch_assoc()): ?>
            <option value="<?= $u['id'] ?>" <?= $criador == $u['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($u['nome']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <button type="submit">Filtrar</button>
    <a href="AdminViewAllProjects.php" class="btn-limpar">Limpar</a>
</form>



<table id="tabela-projetos">
    <thead>
        <tr>
            <th>Projeto</th>
            <th>Categoria</th>
            <th>Prioridade</th>
            <th>Status</th>
            <th>Prazo</th>
            <th>Criador</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows === 0): ?>
            <tr>
                <td colspan="6" style="text-align:center;">
                    Nenhum projeto encontrado
                </td>
            </tr>
        <?php endif; ?>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['nome']) ?></td>
                <td><?= htmlspecialchars($row['categoria'] ?? '—') ?></td>
                <td><?= htmlspecialchars($row['prioridade'] ?? '—') ?></td>
                <td><?= htmlspecialchars($row['status'] ?? '—') ?></td>
                <td>
                    <?= $row['data_fim'] 
                        ? date("d/m/Y", strtotime($row['data_fim'])) 
                        : '—' ?>
                </td>
                <td><?= htmlspecialchars($row['criador_nome']) ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include("../Includes/Footer.php"); ?>
<script src="../Assets/js/AdminViewAllProjects.js"></script>

</body>
</html>
