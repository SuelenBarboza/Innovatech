<?php
// Mostra os relatórios enviados pelo aluno
session_start();
include("../Config/db.php");

// ==========================
// VERIFICAÇÃO DE LOGIN
// ==========================
if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não logado.");
}

// ==========================
// VERIFICAÇÃO DE PERMISSÃO
// ==========================
if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'Aluno') {
    die("Acesso negado. Apenas alunos podem acessar esta página.");
}

$aluno_id = $_SESSION['usuario_id'];

// ==========================
// FILTROS
// ==========================
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';

// Query base
$sql = "
    SELECT r.*, p.nome AS projeto
    FROM relatorios r
    JOIN projetos p ON p.id = r.projeto_id
    WHERE r.aluno_id = ?
";

$params = [$aluno_id];
$types = "i";

// Filtro de busca (título ou descrição)
if (!empty($search)) {
    $sql .= " AND (r.titulo LIKE ? OR r.descricao LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

// Filtro de status
if (!empty($status)) {
    $sql .= " AND r.status = ?";
    $params[] = $status;
    $types .= "s";
}

// Filtro de data início
if (!empty($data_inicio)) {
    $sql .= " AND DATE(r.criado_em) >= ?";
    $params[] = $data_inicio;
    $types .= "s";
}

// Filtro de data fim
if (!empty($data_fim)) {
    $sql .= " AND DATE(r.criado_em) <= ?";
    $params[] = $data_fim;
    $types .= "s";
}

$sql .= " ORDER BY r.criado_em DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Função para traduzir status
function traduzirStatus($status) {
    $statusMap = [
        'novo' => 'Novo',
        'esperando' => 'Esperando Resposta',
        'aguardando' => 'Aguardando Aluno',
        'concluido' => 'Concluído'
    ];
    return isset($statusMap[$status]) ? $statusMap[$status] : ucfirst($status);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Relatórios</title>
    <link rel="stylesheet" href="../Assets/css/Header.css">
    <link rel="stylesheet" href="../Assets/css/Footer.css">
    <link rel="stylesheet" href="../Assets/css/ViewReportsTeacher.css">
    <style>
        /* Ajustes específicos para aluno */
        .btn-ver {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }
        .btn-ver:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16,185,129,0.3);
        }
    </style>
</head>
<body>

<?php include("../Includes/Header.php"); ?>

<div class="container">
    <h2>
        📄 Meus Relatórios
    </h2>

    <!-- FILTROS -->
    <div class="filtros-container">
        <form method="GET" class="filtros-form">
            <div class="filtros-grid">
                <div class="filtro-group">
                    <label>🔍 Buscar</label>
                    <input type="text" name="search" placeholder="Título ou descrição..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="filtro-group">
                    <label>📌 Status</label>
                    <select name="status">
                        <option value="">Todos</option>
                        <option value="novo" <?= $status == 'novo' ? 'selected' : '' ?>>Novo</option>
                        <option value="esperando" <?= $status == 'esperando' ? 'selected' : '' ?>>Esperando Resposta</option>
                        <option value="aguardando" <?= $status == 'aguardando' ? 'selected' : '' ?>>Aguardando Aluno</option>
                        <option value="concluido" <?= $status == 'concluido' ? 'selected' : '' ?>>Concluído</option>
                    </select>
                </div>
                <div class="filtro-group">
                    <label>📅 Data Início</label>
                    <input type="date" name="data_inicio" value="<?= htmlspecialchars($data_inicio) ?>">
                </div>
                <div class="filtro-group">
                    <label>📅 Data Fim</label>
                    <input type="date" name="data_fim" value="<?= htmlspecialchars($data_fim) ?>">
                </div>
            </div>
            <div class="filtros-botoes">
                <button type="submit" class="btn-filtrar">
                    🔍 Filtrar
                </button>
                <a href="MyReports.php" class="btn-limpar">
                    🗑️ Limpar Filtros
                </a>
            </div>
        </form>
    </div>

    <!-- TOTAL DE REGISTROS -->
    <div class="total-registros">
        📊 Total: <?= $result->num_rows ?> relatório(s) encontrado(s)
    </div>

    <!-- TABELA DE RELATÓRIOS -->
    <?php if ($result->num_rows === 0): ?>
        <div class="nenhum-registro">
            📭 Nenhum relatório encontrado.
        </div>
    <?php else: ?>
        <div class="tabela-wrapper">
            <table class="tabela">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Projeto</th>
                        <th>Descrição</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($r = $result->fetch_assoc()): ?>
                        <tr>
                            <td data-label="Título">
                                <strong><?= htmlspecialchars($r['titulo']) ?></strong>
                            </td>
                            <td data-label="Projeto">
                                📁 <?= htmlspecialchars($r['projeto']) ?>
                            </td>
                            <td data-label="Descrição">
                                <?= htmlspecialchars(substr($r['descricao'], 0, 80)) ?>
                                <?= strlen($r['descricao']) > 80 ? '...' : '' ?>
                            </td>
                            <td data-label="Data">
                                📅 <?= date("d/m/Y", strtotime($r['criado_em'])) ?>
                            </td>
                            <td data-label="Status">
                                <span class="status <?= $r['status'] ?>">
                                    <?php
                                    $icone = match($r['status']) {
                                        'novo' => '🔔',
                                        'esperando' => '💬',
                                        'aguardando' => '⏳',
                                        'concluido' => '✅',
                                        default => '📌'
                                    };
                                    echo $icone . " " . traduzirStatus($r['status']);
                                    ?>
                                </span>
                            </td>
                            <td data-label="Ações">
                                <a href="ViewReport.php?id=<?= $r['id'] ?>" class="btn btn-ver">
                                    👁️ Ver Resposta
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include("../Includes/Footer.php"); ?>
</body>
</html>