<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../Config/db.php");

// ============================================================
// VERIFICA LOGIN
// ============================================================

if (!isset($_SESSION['usuario_id'])) {
    die("Acesso negado.");
}

// ============================================================
// BUSCAR LOGS
// ============================================================

try {

    $sql = "
        SELECT 
            l.id,
            l.usuario_id,
            l.acao,
            l.categoria,
            l.descricao,
            l.referencia_id,
            l.referencia_tipo,
            l.ip_usuario,
            l.criado_em,

            u.nome        AS usuario_nome,
            u.email       AS usuario_email,
            u.tipo_usuario AS usuario_papel

        FROM logs l

        LEFT JOIN usuarios u 
            ON l.usuario_id = u.id

        ORDER BY l.criado_em DESC
    ";

    $result = $conn->query($sql);

    if (!$result) {
        die("Erro SQL: " . $conn->error);
    }

    $todosLogs = [];

    while ($row = $result->fetch_assoc()) {
        $todosLogs[] = $row;
    }

    // ========================================================
    // ESTATÍSTICAS
    // ========================================================

    $stats = [
        'total'      => count($todosLogs),
        'projetos'   => 0,
        'tarefas'    => 0,
        'relatorios' => 0,
        'usuarios'   => 0,
        'suporte'    => 0
    ];

    foreach ($todosLogs as $log) {

        switch ($log['categoria']) {

            case 'projeto':
                $stats['projetos']++;
                break;

            case 'tarefa':
                $stats['tarefas']++;
                break;

            case 'relatorio':
                $stats['relatorios']++;
                break;

            case 'usuario':
                $stats['usuarios']++;
                break;

            case 'suporte':
                $stats['suporte']++;
                break;
        }
    }

} catch (Exception $e) {

    die("Erro ao carregar logs: " . $e->getMessage());

}

// ============================================================
// LABELS
// ============================================================

$categoriasLabel = [

    'login'       => 'Login',
    'logout'      => 'Logout',
    'cadastro'    => 'Cadastro',
    'usuario'     => 'Usuário',
    'projeto'     => 'Projeto',
    'tarefa'      => 'Tarefa',
    'relatorio'   => 'Relatório',
    'comentario'  => 'Comentário',
    'resposta'    => 'Resposta',
    'suporte'     => 'Suporte',
    'sistema'     => 'Sistema'

];

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Logs do Sistema | InnovaTech</title>

    <link rel="stylesheet" href="../Assets/css/Header.css">
    <link rel="stylesheet" href="../Assets/css/Footer.css">
    <link rel="stylesheet" href="../Assets/css/Logs.css">

</head>

<body>

<?php include("../Includes/Header.php"); ?>

<div class="logs-page">

    <!-- ================================================= -->
    <!-- HEADER -->
    <!-- ================================================= -->

    <div class="page-header">

        <div class="page-header-left">

            <h1>
                <span class="dot"></span>
                Logs do Sistema
            </h1>

            <p>
                Registro completo das ações realizadas na plataforma.
            </p>

        </div>

        <div class="header-stats">

            <div class="stat-pill">
                <div class="val"><?= $stats['total'] ?></div>
                <div class="lbl">Total</div>
            </div>

            <div class="stat-pill">
                <div class="val"><?= $stats['projetos'] ?></div>
                <div class="lbl">Projetos</div>
            </div>

            <div class="stat-pill">
                <div class="val"><?= $stats['tarefas'] ?></div>
                <div class="lbl">Tarefas</div>
            </div>

            <div class="stat-pill">
                <div class="val"><?= $stats['relatorios'] ?></div>
                <div class="lbl">Relatórios</div>
            </div>

            <div class="stat-pill">
                <div class="val"><?= $stats['usuarios'] ?></div>
                <div class="lbl">Usuários</div>
            </div>

            <div class="stat-pill">
                <div class="val"><?= $stats['suporte'] ?></div>
                <div class="lbl">Suporte</div>
            </div>

        </div>

    </div>

    <!-- ================================================= -->
    <!-- PESQUISA -->
    <!-- ================================================= -->

    <div class="controls-bar">

        <div class="search-wrap">

            <input
                type="text"
                class="search-input"
                id="searchInput"
                placeholder="Buscar logs..."
            >

        </div>

    </div>

    <!-- ================================================= -->
    <!-- CATEGORIAS -->
    <!-- ================================================= -->

    <div class="category-chips">

        <div class="chip active" data-cat="all">Todos</div>
        <div class="chip" data-cat="login">Login</div>
        <div class="chip" data-cat="cadastro">Cadastro</div>
        <div class="chip" data-cat="usuario">Usuários</div>
        <div class="chip" data-cat="projeto">Projetos</div>
        <div class="chip" data-cat="tarefa">Tarefas</div>
        <div class="chip" data-cat="relatorio">Relatórios</div>
        <div class="chip" data-cat="comentario">Comentários</div>
        <div class="chip" data-cat="resposta">Respostas</div>
        <div class="chip" data-cat="suporte">Suporte</div>

    </div>

    <!-- ================================================= -->
    <!-- TABELA -->
    <!-- ================================================= -->

    <div class="logs-container">

        <div class="logs-table-header">

            <span>Data/Hora</span>
            <span>Categoria</span>
            <span>Ação / Descrição</span>
            <span>Usuário</span>
            <span>Referência</span>
            <span>Detalhes</span>

        </div>

        <div id="logsBody">

            <?php if (empty($todosLogs)): ?>

                <div class="empty-state">
                    Nenhum log encontrado.
                </div>

            <?php else: ?>

                <?php foreach ($todosLogs as $log): ?>

                    <?php

                    $data = new DateTime($log['criado_em']);

                    $nomeUsuario = $log['usuario_nome'] ?? 'Sistema';

                    $papelUsuario = $log['usuario_papel'] ?? 'Sistema';

                    $emailUsuario = $log['usuario_email'] ?? '—';

                    $iniciais = '';

                    $nomes = explode(' ', $nomeUsuario);

                    foreach ($nomes as $nome) {

                        $iniciais .= strtoupper(substr($nome, 0, 1));

                    }

                    $iniciais = substr($iniciais, 0, 2);

                    $refTexto = '';
                    if (!empty($log['referencia_tipo']) && !empty($log['referencia_id'])) {
                        $refTexto = ucfirst($log['referencia_tipo']) . ' #' . $log['referencia_id'];
                    }

                    ?>

                    <div
                        class="log-row"
                        data-cat="<?= strtolower($log['categoria']) ?>"
                        data-search="<?= strtolower($log['acao'] . ' ' . $log['descricao'] . ' ' . $nomeUsuario . ' ' . $emailUsuario) ?>"
                    >

                        <!-- DATA -->

                        <div class="log-ts">

                            <div class="date">
                                <?= $data->format('d/m/Y') ?>
                            </div>

                            <div class="time">
                                <?= $data->format('H:i:s') ?>
                            </div>

                        </div>

                        <!-- CATEGORIA -->

                        <div>

                            <span class="log-cat cat-<?= strtolower($log['categoria']) ?>">

                                <?= $categoriasLabel[$log['categoria']] ?? ucfirst($log['categoria']) ?>

                            </span>

                        </div>

                        <!-- AÇÃO / DESCRIÇÃO -->

                        <div class="log-desc">

                            <strong><?= htmlspecialchars($log['acao']) ?></strong><br>
                            <small><?= htmlspecialchars($log['descricao']) ?></small>

                        </div>

                        <!-- USUÁRIO -->

                        <div class="log-actor">

                            <div class="actor-avatar">

                                <?= $iniciais ?>

                            </div>

                            <div class="actor-info">

                                <div class="actor-name">

                                    <?= htmlspecialchars($nomeUsuario) ?>

                                </div>

                                <div class="actor-role">

                                    <?= htmlspecialchars($papelUsuario) ?>

                                </div>

                            </div>

                        </div>

                        <!-- REFERÊNCIA -->

                        <div>

                            <span class="log-status">

                                <?= $refTexto !== '' ? htmlspecialchars($refTexto) : '—' ?>

                            </span>

                        </div>

                        <!-- DETALHES -->

                        <div class="log-detail-btn-wrap">

                            <button
                                class="btn-detail"
                                onclick="openModal(this)"

                                data-acao="<?= htmlspecialchars($log['acao']) ?>"
                                data-descricao="<?= htmlspecialchars($log['descricao']) ?>"
                                data-data="<?= $data->format('d/m/Y H:i:s') ?>"
                                data-categoria="<?= htmlspecialchars($log['categoria']) ?>"
                                data-usuario="<?= htmlspecialchars($nomeUsuario) ?>"
                                data-email="<?= htmlspecialchars($emailUsuario) ?>"
                                data-papel="<?= htmlspecialchars($papelUsuario) ?>"
                                data-ip="<?= htmlspecialchars($log['ip_usuario'] ?? '—') ?>"
                                data-referencia-tipo="<?= htmlspecialchars($log['referencia_tipo'] ?? '—') ?>"
                                data-referencia-id="<?= htmlspecialchars($log['referencia_id'] ?? '—') ?>"
                            >

                                Detalhes

                            </button>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

    </div>

</div>

<!-- ===================================================== -->
<!-- MODAL -->
<!-- ===================================================== -->

<div class="modal-overlay" id="modalOverlay">

    <div class="modal-detail">

        <div class="modal-header">

            <h2>Detalhes do Log</h2>

            <button class="modal-close" onclick="closeModal()">
                X
            </button>

        </div>

        <div class="modal-body">

            <div class="modal-field">

                <span class="mf-label">Ação</span>

                <span class="mf-value" id="mAcao"></span>

            </div>

            <div class="modal-field">

                <span class="mf-label">Descrição</span>

                <span class="mf-value" id="mDescricao"></span>

            </div>

            <div class="modal-field">

                <span class="mf-label">Data</span>

                <span class="mf-value" id="mData"></span>

            </div>

            <div class="modal-field">

                <span class="mf-label">Categoria</span>

                <span class="mf-value" id="mCategoria"></span>

            </div>

            <div class="modal-field">

                <span class="mf-label">Usuário</span>

                <span class="mf-value" id="mUsuario"></span>

            </div>

            <div class="modal-field">

                <span class="mf-label">E-mail</span>

                <span class="mf-value" id="mEmail"></span>

            </div>

            <div class="modal-field">

                <span class="mf-label">Papel</span>

                <span class="mf-value" id="mPapel"></span>

            </div>

            <div class="modal-field">

                <span class="mf-label">IP</span>

                <span class="mf-value" id="mIp"></span>

            </div>

            <div class="modal-field">

                <span class="mf-label">Referência</span>

                <span class="mf-value" id="mReferencia"></span>

            </div>

        </div>

    </div>

</div>

<script src="../Assets/js/Header.js"></script>

<script>

const modal = document.getElementById("modalOverlay");

// =====================================================
// ABRIR MODAL
// =====================================================

function openModal(button) {

    document.getElementById("mAcao").innerText      = button.dataset.acao;
    document.getElementById("mDescricao").innerText = button.dataset.descricao;
    document.getElementById("mData").innerText      = button.dataset.data;
    document.getElementById("mCategoria").innerText = button.dataset.categoria;
    document.getElementById("mUsuario").innerText   = button.dataset.usuario;
    document.getElementById("mEmail").innerText     = button.dataset.email;
    document.getElementById("mPapel").innerText     = button.dataset.papel;
    document.getElementById("mIp").innerText        = button.dataset.ip;

    const refTipo = button.dataset.referenciaTipo;
    const refId   = button.dataset.referenciaId;
    document.getElementById("mReferencia").innerText =
        (refTipo && refTipo !== '—' && refId && refId !== '—')
        ? refTipo + ' #' + refId
        : '—';

    modal.style.display = "flex";
}

// =====================================================
// FECHAR MODAL
// =====================================================

function closeModal() {

    modal.style.display = "none";
}

// =====================================================
// PESQUISA
// =====================================================

const searchInput = document.getElementById("searchInput");

searchInput.addEventListener("keyup", function () {

    const termo = this.value.toLowerCase();

    document.querySelectorAll(".log-row").forEach(row => {

        const texto = row.dataset.search;

        if (texto.includes(termo)) {

            row.style.display = "grid";

        } else {

            row.style.display = "none";

        }

    });

});

// =====================================================
// FILTRO CATEGORIA
// =====================================================

document.querySelectorAll(".chip").forEach(chip => {

    chip.addEventListener("click", function () {

        document.querySelectorAll(".chip").forEach(c => {
            c.classList.remove("active");
        });

        this.classList.add("active");

        const categoria = this.dataset.cat;

        document.querySelectorAll(".log-row").forEach(row => {

            if (categoria === "all") {

                row.style.display = "grid";

            } else {

                row.style.display =
                    row.dataset.cat === categoria
                    ? "grid"
                    : "none";
            }

        });

    });

});

</script>

<?php include("../Includes/Footer.php"); ?>

</body>
</html>