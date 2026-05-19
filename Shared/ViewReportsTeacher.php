<?php
// ==========================
// VISUALIZAR RELATÓRIOS COM FILTRO
// ==========================

session_start();
include("../Config/db.php");

// ==========================
// VERIFICA LOGIN
// ==========================
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Public/Login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$tipo = $_SESSION['usuario_tipo'] ?? 'Aluno';

// ==========================
// VERIFICA PERMISSÃO
// ==========================
if ($tipo !== 'Professor' && $tipo !== 'Admin') {
    die("Acesso negado.");
}

// ==========================
// FUNÇÃO PARA CONVERTER STATUS DO BANCO PARA EXIBIÇÃO
// ==========================
function converterStatus($status_db) {
    switch ($status_db) {
        case 'Novo Relatório':
            return '🔔 Novo relatório';
        case 'Respondido':
            return '💬 Esperando resposta';
        case 'Concluído':
            return '✅ Concluído';
        default:
            return $status_db;
    }
}

// ==========================
// FILTROS (GET)
// ==========================
$filtro_status = $_GET['status'] ?? '';
$filtro_projeto = $_GET['projeto'] ?? '';
$filtro_aluno = $_GET['aluno'] ?? '';
$filtro_data_inicio = $_GET['data_inicio'] ?? '';
$filtro_data_fim = $_GET['data_fim'] ?? '';

// Mapear filtro de status para valor do banco
$mapeamento_status = [
    '🔔 Novo relatório' => 'Novo Relatório',
    '💬 Esperando resposta' => 'Respondido',
    '✅ Concluído' => 'Concluído'
];
$filtro_status_db = $mapeamento_status[$filtro_status] ?? $filtro_status;

// ==========================
// CONSULTA COM FILTROS
// ==========================
$sql = "
    SELECT
        r.id,
        r.titulo,
        r.descricao,
        r.criado_em,
        r.status,
        p.nome AS projeto,
        u.nome AS aluno
    FROM relatorios r
    INNER JOIN projetos p ON p.id = r.projeto_id
    INNER JOIN usuarios u ON u.id = r.aluno_id
";

$conditions = [];
$params = [];
$types = "";

// Filtro por professor (se não for Admin)
if ($tipo !== 'Admin') {
    $conditions[] = "r.professor_id = ?";
    $params[] = $usuario_id;
    $types .= "i";
}

// Filtro por status (usando o valor do banco)
if (!empty($filtro_status_db)) {
    $conditions[] = "r.status = ?";
    $params[] = $filtro_status_db;
    $types .= "s";
}

// Filtro por projeto
if (!empty($filtro_projeto)) {
    $conditions[] = "p.nome LIKE ?";
    $params[] = "%$filtro_projeto%";
    $types .= "s";
}

// Filtro por aluno
if (!empty($filtro_aluno)) {
    $conditions[] = "u.nome LIKE ?";
    $params[] = "%$filtro_aluno%";
    $types .= "s";
}

// Filtro por data início
if (!empty($filtro_data_inicio)) {
    $conditions[] = "DATE(r.criado_em) >= ?";
    $params[] = $filtro_data_inicio;
    $types .= "s";
}

// Filtro por data fim
if (!empty($filtro_data_fim)) {
    $conditions[] = "DATE(r.criado_em) <= ?";
    $params[] = $filtro_data_fim;
    $types .= "s";
}

// Monta WHERE se houver condições
if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// ORDER BY
$sql .= "
    ORDER BY
        CASE
            WHEN r.status = 'Novo Relatório' THEN 1
            WHEN r.status = 'Respondido' THEN 2
            WHEN r.status = 'Concluído' THEN 3
            ELSE 4
        END,
        r.criado_em DESC
";

// Prepara e executa
$stmt = $conn->prepare($sql);
if (count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// ==========================
// BUSCAR DADOS PARA OS FILTROS (Projetos e Alunos)
// ==========================
// Buscar projetos distintos para o filtro
$sqlProjetos = "
    SELECT DISTINCT p.nome 
    FROM relatorios r
    INNER JOIN projetos p ON p.id = r.projeto_id
";
if ($tipo !== 'Admin') {
    $sqlProjetos .= " WHERE r.professor_id = ?";
    $stmtProjetos = $conn->prepare($sqlProjetos);
    $stmtProjetos->bind_param("i", $usuario_id);
} else {
    $stmtProjetos = $conn->prepare($sqlProjetos);
}
$stmtProjetos->execute();
$projetos = $stmtProjetos->get_result();

// Buscar alunos distintos para o filtro
$sqlAlunos = "
    SELECT DISTINCT u.id, u.nome 
    FROM relatorios r
    INNER JOIN usuarios u ON u.id = r.aluno_id
";
if ($tipo !== 'Admin') {
    $sqlAlunos .= " WHERE r.professor_id = ?";
    $stmtAlunos = $conn->prepare($sqlAlunos);
    $stmtAlunos->bind_param("i", $usuario_id);
} else {
    $stmtAlunos = $conn->prepare($sqlAlunos);
}
$stmtAlunos->execute();
$alunos = $stmtAlunos->get_result();

// Status disponíveis para filtro (com emojis para exibição)
$status_opcoes = [
    '🔔 Novo relatório' => '🔔 Novo relatório',
    '💬 Esperando resposta' => '💬 Esperando resposta',
    '✅ Concluído' => '✅ Concluído'
];

// ==========================
// LIMPAR FILTROS (botão)
// ==========================
$limpar_filtros = isset($_GET['limpar']);
if ($limpar_filtros) {
    header("Location: ViewReportsTeacher.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatórios Recebidos</title>
    <link rel="stylesheet" href="../Assets/css/Header.css">
    <link rel="stylesheet" href="../Assets/css/Footer.css">
    <link rel="stylesheet" href="../Assets/css/ViewReportsTeacher.css">
</head>
<body>

<?php include("../Includes/Header.php"); ?>

<div class="container">
    <h2>📋 Relatórios Recebidos</h2>

    <!-- ==========================
         FILTROS
    =========================== -->
    <div class="filtros-container">
        <form method="GET" action="" class="filtros-form">
            <div class="filtros-grid">
                <div class="filtro-group">
                    <label>📌 Status</label>
                    <select name="status">
                        <option value="">Todos</option>
                        <?php foreach ($status_opcoes as $valor => $texto): ?>
                            <option value="<?= $valor ?>" <?= ($filtro_status == $valor) ? 'selected' : '' ?>>
                                <?= $texto ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filtro-group">
                    <label>📁 Projeto</label>
                    <input type="text" name="projeto" value="<?= htmlspecialchars($filtro_projeto) ?>" 
                           placeholder="Buscar projeto..." list="lista-projetos">
                    <datalist id="lista-projetos">
                        <?php while($p = $projetos->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($p['nome']) ?>">
                        <?php endwhile; ?>
                    </datalist>
                </div>

                <div class="filtro-group">
                    <label>👤 Aluno</label>
                    <input type="text" name="aluno" value="<?= htmlspecialchars($filtro_aluno) ?>" 
                           placeholder="Buscar aluno..." list="lista-alunos">
                    <datalist id="lista-alunos">
                        <?php 
                        $alunos->data_seek(0);
                        while($a = $alunos->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($a['nome']) ?>">
                        <?php endwhile; ?>
                    </datalist>
                </div>

                <div class="filtro-group">
                    <label>📅 Data Início</label>
                    <input type="date" name="data_inicio" value="<?= $filtro_data_inicio ?>">
                </div>

                <div class="filtro-group">
                    <label>📅 Data Fim</label>
                    <input type="date" name="data_fim" value="<?= $filtro_data_fim ?>">
                </div>
            </div>

            <div class="filtros-botoes">
                <button type="submit" class="btn-filtrar">🔍 Filtrar</button>
                <a href="ViewReportsTeacher.php" class="btn-limpar">🗑️ Limpar Filtros</a>
            </div>
        </form>
    </div>

    <!-- ==========================
         RESULTADOS
    =========================== -->
    <?php if ($result->num_rows === 0): ?>
        <div class="nenhum-registro">
            <p>📭 Nenhum relatório encontrado com os filtros selecionados.</p>
        </div>
    <?php else: ?>
        <div class="total-registros">
            📊 Total: <?= $result->num_rows ?> relatório(s)
        </div>

        <div class="tabela-wrapper">
            <table class="tabela">
                <thead>
                    <tr>
                        <th>Projeto</th>
                        <th>Aluno</th>
                        <th>Título</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($r = $result->fetch_assoc()): ?>
                        <?php
                            // Converte o status do banco para exibição
                            $status_banco = $r['status'] ?? 'Novo Relatório';
                            $status_exibicao = converterStatus($status_banco);
                            
                            // Define a classe CSS baseada no status do banco
                            if ($status_banco == 'Novo Relatório') {
                                $classe_status = 'novo';
                                $texto_status = '🔔 Novo relatório';
                            } elseif ($status_banco == 'Respondido') {
                                $classe_status = 'esperando';
                                $texto_status = '💬 Esperando resposta';
                            } elseif ($status_banco == 'Concluído') {
                                $classe_status = 'concluido';
                                $texto_status = '✅ Concluído';
                            } else {
                                $classe_status = 'novo';
                                $texto_status = '🔔 Novo relatório';
                            }
                        ?>
                        <tr>
                            <td data-label="Projeto"><?= htmlspecialchars($r['projeto']) ?></td>
                            <td data-label="Aluno"><?= htmlspecialchars($r['aluno']) ?></td>
                            <td data-label="Título"><?= htmlspecialchars($r['titulo']) ?></td>
                            <td data-label="Data"><?= date("d/m/Y H:i", strtotime($r['criado_em'])) ?></td>
                            <td data-label="Status">
                                <span class="status <?= $classe_status ?>"><?= $texto_status ?></span>
                            </td>
                            <td data-label="Ações">
                                <a href="RespondReport.php?id=<?= $r['id'] ?>" class="btn btn-responder">
                                    📄 Abrir
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

<script>
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (modal.style.display === 'flex') {
                modal.style.display = 'none';
            }
        });
    }
});
</script>

</body>
</html>