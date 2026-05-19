<?php
session_start();
include("../Config/db.php");

// ==========================
// VERIFICA LOGIN
// ==========================
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Public/Login.php");
    exit;
}

$usuario_id = (int)$_SESSION['usuario_id'];
$tipo = $_SESSION['usuario_tipo'] ?? 'Aluno';

// ==========================
// ID DO RELATÓRIO
// ==========================
$relatorio_id = (int)($_GET['id'] ?? 0);

if ($relatorio_id <= 0) {
    header("Location: " . ($tipo === 'Aluno' ? 'MyReports.php' : 'ViewReportsTeacher.php'));
    exit;
}

// ==========================
// BUSCAR RELATÓRIO
// ==========================
$sql = "
SELECT r.*,
       u.nome AS aluno_nome,
       p.nome AS projeto_nome,
       up.nome AS professor_nome
FROM relatorios r
INNER JOIN usuarios u ON u.id = r.aluno_id
INNER JOIN projetos p ON p.id = r.projeto_id
LEFT JOIN usuarios up ON up.id = r.professor_id
WHERE r.id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $relatorio_id);
$stmt->execute();
$relatorio = $stmt->get_result()->fetch_assoc();

if (!$relatorio) {
    die("Relatório não encontrado.");
}

// ==========================
// PERMISSÃO DE ACESSO
// ==========================
if (
    !(
        ($tipo === 'Aluno' && $usuario_id === (int)$relatorio['aluno_id']) ||
        ($tipo === 'Professor' && $usuario_id === (int)$relatorio['professor_id']) ||
        in_array($tipo, ['Admin', 'Coordenador'])
    )
) {
    die("Acesso negado.");
}

// ==========================
// FUNÇÃO PARA CONVERTER STATUS DO BANCO PARA EXIBIÇÃO
// ==========================
function getStatusDisplay($status) {
    switch ($status) {
        case 'Novo Relatório':
            return ['text' => '🔔 Novo relatório', 'class' => 'novo'];
        case 'Respondido':
            return ['text' => '💬 Esperando resposta', 'class' => 'esperando'];
        case 'Concluído':
            return ['text' => '✅ Concluído', 'class' => 'concluido'];
        default:
            return ['text' => '🔔 Novo relatório', 'class' => 'novo'];
    }
}

// ==========================
// FUNÇÃO PARA CONVERTER STATUS DO BANCO PARA VALIDAÇÃO
// ==========================
function isConcluido($status) {
    return $status === 'Concluído';
}

// ==========================
// CONCLUIR RELATÓRIO (via GET com modal)
// ==========================
if (isset($_GET['concluir']) && $tipo !== 'Aluno') {
    $update = $conn->prepare("
        UPDATE relatorios
        SET status = 'Concluído'
        WHERE id = ?
    ");
    $update->bind_param("i", $relatorio_id);
    $update->execute();
    
    header("Location: RespondReport.php?id=" . $relatorio_id);
    exit;
}

// ==========================
// BUSCAR RESPOSTAS
// ==========================
$sqlRespostas = "
SELECT rr.*,
       u.nome AS respondente_nome,
       u.tipo_usuario
FROM resposta_relatorio rr
INNER JOIN usuarios u ON u.id = rr.respondente_id
WHERE rr.relatorio_id = ?
ORDER BY rr.respondido_em ASC
";

$stmt = $conn->prepare($sqlRespostas);
$stmt->bind_param("i", $relatorio_id);
$stmt->execute();
$respostas = $stmt->get_result();
$totalRespostas = $respostas->num_rows;

// ==========================
// STATUS
// ==========================
$statusRaw = $relatorio['status'] ?? 'Novo Relatório';
$statusDisplay = getStatusDisplay($statusRaw);
$concluido = isConcluido($statusRaw);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório - <?= htmlspecialchars($relatorio['titulo']) ?></title>
    <link rel="stylesheet" href="../Assets/css/Header.css">
    <link rel="stylesheet" href="../Assets/css/Footer.css">
    <link rel="stylesheet" href="../Assets/css/RespondReport.css">
</head>
<body>

<?php include("../Includes/Header.php"); ?>

<section class="form-container">

    <!-- ==========================
         CABEÇALHO DO RELATÓRIO
    =========================== -->
    <div class="report-header">
        <div>
            <h2><?= htmlspecialchars($relatorio['titulo']) ?></h2>
            <p><strong>Projeto:</strong> <?= htmlspecialchars($relatorio['projeto_nome']) ?></p>
            <p><strong>Aluno:</strong> <?= htmlspecialchars($relatorio['aluno_nome']) ?></p>
            <p><strong>Professor:</strong> <?= htmlspecialchars($relatorio['professor_nome'] ?? 'Não definido') ?></p>
            <p><strong>Criado em:</strong> <?= date("d/m/Y H:i", strtotime($relatorio['criado_em'])) ?></p>
        </div>
        <div class="status-area">
            <span class="status <?= $statusDisplay['class'] ?>"><?= $statusDisplay['text'] ?></span>
        </div>
    </div>

    <!-- ==========================
         DESCRIÇÃO DO RELATÓRIO
    =========================== -->
    <div class="report-description">
        <?= nl2br(htmlspecialchars($relatorio['descricao'])) ?>
    </div>

    <hr>

    <!-- ==========================
         HISTÓRICO DE RESPOSTAS
    =========================== -->
    <div class="history-header">
        <h3>💬 Conversa</h3>
        <span class="response-count"><?= $totalRespostas ?> mensagem(ns)</span>
    </div>

    <div class="historico-respostas">
        <?php if ($totalRespostas === 0): ?>
            <div class="empty">Nenhuma mensagem ainda. Seja o primeiro a responder!</div>
        <?php else: ?>
            <?php while ($r = $respostas->fetch_assoc()): ?>
                <?php
                $classe = ($r['tipo_usuario'] === 'Aluno') ? 'aluno' : 'professor';
                ?>
                <div class="resposta-box <?= $classe ?>">
                    <div class="resposta-topo">
                        <strong><?= htmlspecialchars($r['respondente_nome']) ?></strong>
                        <span><?= htmlspecialchars($r['tipo_usuario']) ?></span>
                    </div>
                    <small><?= date("d/m/Y H:i", strtotime($r['respondido_em'])) ?></small>
                    <p><?= nl2br(htmlspecialchars($r['resposta'])) ?></p>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <hr>

    <!-- ==========================
         FORMULÁRIO DE RESPOSTA (SÓ SE NÃO CONCLUÍDO)
    =========================== -->
    <?php if (!$concluido): ?>

        <form action="../Config/ProcessRespondReport.php" method="POST" id="responseForm">
            <input type="hidden" name="relatorio_id" value="<?= $relatorio_id ?>">
            
            <label for="resposta">✏️ Nova Mensagem</label>
            <textarea name="resposta" id="resposta" rows="5" required placeholder="Digite sua resposta..."></textarea>

            <div class="form-actions">
                <button type="submit" class="btn-enviar">📤 Enviar Resposta</button>
                
                <?php if ($tipo !== 'Aluno'): ?>
                    <button type="button" class="btn-concluir" id="openModalBtn">✅ Concluir Relatório</button>
                <?php endif; ?>
                
                <a href="<?= ($tipo === 'Aluno') ? 'MyReports.php' : 'ViewReportsTeacher.php' ?>" class="btn-voltar">⬅ Voltar</a>
            </div>
        </form>

    <?php else: ?>

        <!-- ==========================
             MENSAGEM DE RELATÓRIO CONCLUÍDO
        =========================== -->
        <div class="report-concluded">
            <div class="concluded-icon">🏁</div>
            <h3>Relatório Finalizado</h3>
            <p>Este relatório foi <strong>concluído</strong> e não aceita mais respostas.</p>
            <div class="concluded-info">
                <p>📅 Finalizado em: <?= date("d/m/Y H:i") ?></p>
                <p>👥 Você pode visualizar todo o histórico de mensagens acima.</p>
            </div>
            <a href="<?= ($tipo === 'Aluno') ? 'MyReports.php' : 'ViewReportsTeacher.php' ?>" class="btn-voltar-concluido">⬅ Voltar para a lista</a>
        </div>

    <?php endif; ?>

</section>

<!-- ==========================
     MODAL DE CONFIRMAÇÃO (APENAS PARA CONCLUIR)
=========================== -->
<div id="confirmModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <span class="modal-icon">✅</span>
            <h3>Concluir Relatório</h3>
        </div>
        <div class="modal-body">
            <p>Tem certeza que deseja <strong>concluir este relatório</strong>?</p>
            <p>Após concluído:</p>
            <ul>
                <li>❌ Não será possível adicionar novas respostas</li>
                <li>🏁 O relatório será marcado como finalizado</li>
                <li>👁️ Todos poderão visualizar o histórico</li>
            </ul>
            <p class="warning-text">⚠️ Esta ação não pode ser desfeita!</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-cancel" id="cancelModalBtn">Cancelar</button>
            <a href="?id=<?= $relatorio_id ?>&concluir=1" class="modal-btn modal-btn-confirm" id="confirmModalBtn">Sim, Concluir</a>
        </div>
    </div>
</div>

<?php include("../Includes/Footer.php"); ?>

<!-- ==========================
     SCRIPT: ALERTA DE SAÍDA + MODAL
=========================== -->
<script>
// Alerta ao sair se houver texto não enviado
let alterou = false;
const textarea = document.getElementById('resposta');

if (textarea) {
    textarea.addEventListener('input', () => {
        alterou = true;
    });

    const form = document.getElementById('responseForm');
    if (form) {
        form.addEventListener('submit', () => {
            alterou = false;
        });
    }
}

window.addEventListener('beforeunload', function(e) {
    if (alterou) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Modal de confirmação
const modal = document.getElementById('confirmModal');
const openModalBtn = document.getElementById('openModalBtn');
const cancelModalBtn = document.getElementById('cancelModalBtn');

if (openModalBtn) {
    openModalBtn.addEventListener('click', function() {
        modal.classList.add('active');
    });
}

if (cancelModalBtn) {
    cancelModalBtn.addEventListener('click', function() {
        modal.classList.remove('active');
    });
}

// Fechar modal ao clicar fora
if (modal) {
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });
}

// Fechar modal com tecla ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && modal.classList.contains('active')) {
        modal.classList.remove('active');
    }
});
</script>

</body>
</html>