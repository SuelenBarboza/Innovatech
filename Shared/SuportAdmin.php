<?php
// Admin gerencia chamados de suporte
session_start();
include("../Config/db.php");

// Verifica admin
if (!isset($_SESSION['usuario_tipo']) || strtolower(trim($_SESSION['usuario_tipo'])) !== 'admin') {
    die("Acesso negado.");
}

// ========================
// VARIÁVEIS PARA MENSAGENS
// ========================
$mensagem = '';
$tipo_mensagem = '';

// ========================
// CONCLUIR CHAMADO
// ========================
if (isset($_GET['concluir'])) {
    $chamado_id = (int) $_GET['concluir'];
    
    $sql = "UPDATE suporte_chamados SET status = 'concluido' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $chamado_id);
    
    if ($stmt->execute()) {
        $mensagem = "Chamado concluído com sucesso!";
        $tipo_mensagem = "sucesso";
    } else {
        $mensagem = "Erro ao concluir chamado.";
        $tipo_mensagem = "erro";
    }
    $stmt->close();
}

// ========================
// EXCLUIR CHAMADO
// ========================
if (isset($_GET['excluir'])) {
    $chamado_id = (int) $_GET['excluir'];
    
    $sql = "DELETE FROM suporte_chamados WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $chamado_id);
    
    if ($stmt->execute()) {
        $mensagem = "Chamado excluído com sucesso!";
        $tipo_mensagem = "sucesso";
    } else {
        $mensagem = "Erro ao excluir chamado.";
        $tipo_mensagem = "erro";
    }
    $stmt->close();
}

// ========================
// RESPONDER CHAMADO
// ========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['chamado_id'])) {
    $chamado_id = (int) $_POST['chamado_id'];
    $resposta = trim($_POST['resposta']);
    
    if (!empty($resposta)) {
        $sql = "UPDATE suporte_chamados 
                SET resposta = ?, 
                    status = 'respondido', 
                    data_resposta = NOW() 
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $resposta, $chamado_id);
        
        if ($stmt->execute()) {
            $mensagem = "Resposta enviada com sucesso!";
            $tipo_mensagem = "sucesso";
        } else {
            $mensagem = "Erro ao enviar resposta.";
            $tipo_mensagem = "erro";
        }
        $stmt->close();
    } else {
        $mensagem = "A resposta não pode estar vazia.";
        $tipo_mensagem = "erro";
    }
}

// ========================
// BUSCAR CHAMADOS COM FILTRO
// ========================
$filtro_status = isset($_GET['status']) ? $_GET['status'] : 'todos';
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

$sql = "SELECT sc.*, u.nome as usuario_nome, u.email as usuario_email 
        FROM suporte_chamados sc
        JOIN usuarios u ON sc.usuario_id = u.id";

$where = [];
$params = [];
$types = "";

if ($filtro_status !== 'todos') {
    $where[] = "sc.status = ?";
    $params[] = $filtro_status;
    $types .= "s";
}

if (!empty($busca)) {
    $where[] = "(u.nome LIKE ? OR u.email LIKE ? OR sc.assunto LIKE ?)";
    $busca_param = "%$busca%";
    $params[] = $busca_param;
    $params[] = $busca_param;
    $params[] = $busca_param;
    $types .= "sss";
}

if (count($where) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY 
            CASE sc.status 
                WHEN 'pendente' THEN 1 
                WHEN 'respondido' THEN 2 
                WHEN 'concluido' THEN 3 
            END,
            sc.data_abertura DESC";

$stmt = $conn->prepare($sql);
if (count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

include("../Includes/Header.php");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Chamados de Suporte</title>
    <link rel="stylesheet" href="/Innovatech/Assets/css/Header.css">
    <link rel="stylesheet" href="/Innovatech/Assets/css/Footer.css">
    <link rel="stylesheet" href="/Innovatech/Assets/css/SupportAdmin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="container">
    <h1><i class="fas fa-headset"></i> Chamados de Suporte</h1>
    
    <!-- MENSAGEM DE FEEDBACK -->
    <?php if (!empty($mensagem)): ?>
        <div class="mensagem <?= $tipo_mensagem ?>">
            <i class="fas <?= $tipo_mensagem === 'sucesso' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
            <?= htmlspecialchars($mensagem) ?>
        </div>
    <?php endif; ?>
    
    <!-- BARRA DE BUSCA E FILTROS -->
    <div class="barra-busca-container">
        <form method="GET" action="" class="barra-busca">
            <div class="grupo-busca">
                <i class="fas fa-search"></i>
                <input type="text" 
                       name="busca" 
                       class="barra-busca-input" 
                       placeholder="Buscar por usuário, email ou assunto..."
                       value="<?= htmlspecialchars($busca) ?>">
            </div>
            <div class="filtros-rapidos">
                <button type="submit" name="status" value="todos" class="filtro-btn <?= $filtro_status === 'todos' ? 'ativo' : '' ?>">
                    <i class="fas fa-list"></i> Todos
                </button>
                <button type="submit" name="status" value="pendente" class="filtro-btn <?= $filtro_status === 'pendente' ? 'ativo' : '' ?>">
                    <i class="fas fa-clock"></i> Pendentes
                </button>
                <button type="submit" name="status" value="respondido" class="filtro-btn <?= $filtro_status === 'respondido' ? 'ativo' : '' ?>">
                    <i class="fas fa-reply-all"></i> Respondidos
                </button>
                <button type="submit" name="status" value="concluido" class="filtro-btn <?= $filtro_status === 'concluido' ? 'ativo' : '' ?>">
                    <i class="fas fa-check-double"></i> Concluídos
                </button>
                <?php if (!empty($busca) || $filtro_status !== 'todos'): ?>
                    <a href="SuportAdmin.php" class="limpar-busca">
                        <i class="fas fa-times"></i> Limpar
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <!-- CONTADOR DE RESULTADOS -->
    <div class="contador-resultados">
        <i class="fas fa-ticket-alt"></i>
        <span>Total de chamados: <strong class="numero"><?= $result->num_rows ?></strong></span>
        <?php if (!empty($busca)): ?>
            <span> | Busca: "<strong><?= htmlspecialchars($busca) ?></strong>"</span>
        <?php endif; ?>
        <?php if ($filtro_status !== 'todos'): ?>
            <span> | Filtro: <strong><?= ucfirst($filtro_status) ?></strong></span>
        <?php endif; ?>
    </div>
    
    <!-- TABELA DE CHAMADOS -->
    <div class="tabela-container">
        <table class="support-table">
            <thead>
                <tr>
                    <th><i class="fas fa-user"></i> Usuário</th>
                    <th><i class="fas fa-envelope"></i> Email</th>
                    <th><i class="fas fa-tag"></i> Assunto</th>
                    <th><i class="fas fa-comment"></i> Mensagem</th>
                    <th><i class="fas fa-chart-line"></i> Status</th>
                    <th><i class="fas fa-cogs"></i> Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="col-nome">
                                <i class="fas fa-user-circle"></i>
                                <?= htmlspecialchars($row['usuario_nome']) ?>
                            </td>
                            <td class="col-email">
                                <i class="fas fa-envelope"></i>
                                <?= htmlspecialchars($row['usuario_email']) ?>
                            </td>
                            <td class="col-assunto">
                                <strong><?= htmlspecialchars($row['assunto']) ?></strong>
                            </td>
                            <td class="col-mensagem">
                                <div class="mensagem-preview" title="<?= htmlspecialchars($row['mensagem']) ?>">
                                    <i class="fas fa-quote-left"></i>
                                    <?= nl2br(htmlspecialchars(substr($row['mensagem'], 0, 100))) ?>
                                    <?= strlen($row['mensagem']) > 100 ? '...' : '' ?>
                                </div>
                            </td>
                            <td class="col-status">
                                <?php
                                $status_class = '';
                                $status_icon = '';
                                $status_text = '';
                                switch ($row['status']) {
                                    case 'pendente':
                                        $status_class = 'status-pendente';
                                        $status_icon = 'fa-clock';
                                        $status_text = 'Pendente';
                                        break;
                                    case 'respondido':
                                        $status_class = 'status-respondido';
                                        $status_icon = 'fa-reply-all';
                                        $status_text = 'Respondido';
                                        break;
                                    case 'concluido':
                                        $status_class = 'status-concluido';
                                        $status_icon = 'fa-check-circle';
                                        $status_text = 'Concluído';
                                        break;
                                    default:
                                        $status_class = 'status-pendente';
                                        $status_icon = 'fa-question';
                                        $status_text = ucfirst($row['status']);
                                }
                                ?>
                                <div class="status-wrapper">
                                    <span class="status-badge <?= $status_class ?>">
                                        <i class="fas <?= $status_icon ?>"></i>
                                        <?= $status_text ?>
                                    </span>
                                    <?php if ($row['status'] === 'respondido' && $row['resposta']): ?>
                                        <div class="resposta-preview">
                                            <i class="fas fa-reply"></i>
                                            <strong>Resposta:</strong> <?= htmlspecialchars(substr($row['resposta'], 0, 60)) ?>...
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="col-acao">
                                <div class="acoes-container">
                                    <?php if ($row['status'] !== 'concluido'): ?>
                                        <button class="btn btn-responder" onclick="openModal(<?= $row['id'] ?>, '<?= htmlspecialchars($row['usuario_nome']) ?>')">
                                            <i class="fas fa-reply"></i> Responder
                                        </button>
                                        <a href="?concluir=<?= $row['id'] ?><?= $filtro_status !== 'todos' ? '&status=' . $filtro_status : '' ?><?= !empty($busca) ? '&busca=' . urlencode($busca) : '' ?>" 
                                           class="btn btn-concluir" 
                                           onclick="return confirm('✓ Confirmar conclusão deste chamado?\n\nApós concluído, o usuário não poderá mais responder.')">
                                            <i class="fas fa-check"></i> Concluir
                                        </a>
                                    <?php else: ?>
                                        <span class="finalizado-badge">
                                            <i class="fas fa-lock"></i> Finalizado
                                        </span>
                                    <?php endif; ?>
                                    <a href="?excluir=<?= $row['id'] ?><?= $filtro_status !== 'todos' ? '&status=' . $filtro_status : '' ?><?= !empty($busca) ? '&busca=' . urlencode($busca) : '' ?>" 
                                       class="btn btn-delete" 
                                       onclick="return confirm('⚠ ATENÇÃO: Tem certeza que deseja EXCLUIR este chamado?\n\nEsta ação NÃO pode ser desfeita!')">
                                        <i class="fas fa-trash-alt"></i> Excluir
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="empty-message">
                            <i class="fas fa-inbox"></i>
                            <p>Nenhum chamado encontrado</p>
                            <?php if (!empty($busca) || $filtro_status !== 'todos'): ?>
                                <small>Tente outros filtros ou <a href="SuportAdmin.php">limpar busca</a></small>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL DE RESPOSTA -->
<div id="modal" class="modal">
    <div class="modal-content">
        <h3><i class="fas fa-reply-all"></i> Responder Chamado</h3>
        <form method="POST" action="" id="responderForm">
            <input type="hidden" name="chamado_id" id="chamado_id">
            <div class="usuario-info" id="usuario_info" style="margin-bottom: 15px; padding: 10px; background: var(--bg-light); border-radius: var(--radius-sm);">
                <i class="fas fa-user"></i> Respondendo para: <strong id="usuario_nome">...</strong>
            </div>
            <textarea 
                name="resposta" 
                id="resposta_textarea"
                placeholder="Digite sua resposta para o usuário..." 
                required
                maxlength="5000"
            ></textarea>
            <div class="modal-actions">
                <button type="button" class="btn btn-cancelar" onclick="closeModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" class="btn btn-responder">
                    <i class="fas fa-paper-plane"></i> Enviar Resposta
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id, nome) {
    document.getElementById('chamado_id').value = id;
    document.getElementById('usuario_nome').innerHTML = nome;
    document.getElementById('resposta_textarea').value = '';
    document.getElementById('modal').classList.add('active');
    document.getElementById('modal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('modal').classList.remove('active');
    document.getElementById('modal').style.display = 'none';
}

// Fechar modal clicando fora
window.onclick = function(event) {
    const modal = document.getElementById('modal');
    if (event.target === modal) {
        closeModal();
    }
}

// Tecla ESC fecha modal
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});
</script>

<?php include("../Includes/Footer.php"); ?>
<?php $conn->close(); ?>
</body>
</html>