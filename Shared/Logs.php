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
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input
                type="text"
                class="search-input"
                id="searchInput"
                placeholder="Buscar logs por ação, descrição ou usuário..."
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
    <!-- TABELA COM PAGINAÇÃO -->
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
            <!-- Os logs serão inseridos via JavaScript -->
            <div class="empty-state" style="display:block">
                <div>📋</div>
                <h3>Carregando logs...</h3>
                <p>Aguarde um momento</p>
            </div>
        </div>

        <!-- PAGINAÇÃO -->
        <div class="pagination" id="pagination">
            <div class="items-per-page">
                <label>Itens por página:</label>
                <select id="itemsPerPage">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="pagination-info" id="paginationInfo">
                Mostrando 0 de 0 registros
            </div>
            <div class="pagination-btns" id="paginationButtons">
                <button class="pg-btn" disabled>«</button>
                <button class="pg-btn" disabled>‹</button>
                <button class="pg-btn active">1</button>
                <button class="pg-btn" disabled>›</button>
                <button class="pg-btn" disabled>»</button>
            </div>
        </div>

    </div>

</div>

<!-- ===================================================== -->
<!-- MODAL -->
<!-- ===================================================== -->

<div class="modal-overlay" id="modalOverlay">

    <div class="modal-detail">

        <div class="modal-header">

            <div class="modal-header-left">
                <span class="modal-cat-badge" id="modalCatBadge">Log</span>
                <h2>Detalhes do Log</h2>
            </div>

            <button class="modal-close" onclick="closeModal()">
                ✕
            </button>

        </div>

        <div class="modal-body">

            <div class="modal-field">
                <span class="mf-label">Ação</span>
                <span class="mf-value" id="mAcao"></span>
            </div>

            <div class="modal-field full-width">
                <span class="mf-label">Descrição</span>
                <span class="mf-value" id="mDescricao"></span>
            </div>

            <div class="modal-field">
                <span class="mf-label">Data/Hora</span>
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
// =====================================================
// SISTEMA DE LOGS COM PAGINAÇÃO
// =====================================================

(function() {
    'use strict';

    // =====================================================
    // DADOS
    // =====================================================
    let allLogs = [];      
    let filteredLogs = []; 
    let currentPage = 1;
    let itemsPerPage = 25;
    
    // Elementos DOM
    const logsBody = document.getElementById("logsBody");
    const searchInput = document.getElementById("searchInput");
    const chips = document.querySelectorAll(".chip");
    const itemsPerPageSelect = document.getElementById("itemsPerPage");
    const paginationInfo = document.getElementById("paginationInfo");
    const paginationButtons = document.getElementById("paginationButtons");
    const modal = document.getElementById("modalOverlay");
    
    // Categorias para label
    const categoriasLabel = <?php echo json_encode($categoriasLabel); ?>;
    
    // =====================================================
    // CARREGAR LOGS DO PHP
    // =====================================================
    function loadLogsData() {
        // Pegar os logs que vieram do PHP
        const phpLogs = <?php 
            $logsArray = [];
            foreach ($todosLogs as $log) {
                $data = new DateTime($log['criado_em']);
                $nomeUsuario = $log['usuario_nome'] ?? 'Sistema';
                $iniciais = '';
                $nomes = explode(' ', $nomeUsuario);
                foreach ($nomes as $nome) {
                    $iniciais .= strtoupper(substr($nome, 0, 1));
                }
                $iniciais = substr($iniciais, 0, 2);
                
                $logsArray[] = [
                    'id' => $log['id'],
                    'criado_em' => $log['criado_em'],
                    'data_formatada' => $data->format('d/m/Y'),
                    'hora_formatada' => $data->format('H:i:s'),
                    'categoria' => strtolower($log['categoria']),
                    'categoria_label' => $categoriasLabel[$log['categoria']] ?? ucfirst($log['categoria']),
                    'acao' => htmlspecialchars($log['acao']),
                    'descricao' => htmlspecialchars($log['descricao']),
                    'usuario_nome' => htmlspecialchars($nomeUsuario),
                    'usuario_papel' => htmlspecialchars($log['usuario_papel'] ?? 'Sistema'),
                    'usuario_email' => htmlspecialchars($log['usuario_email'] ?? '—'),
                    'iniciais' => $iniciais ?: 'S',
                    'ip_usuario' => htmlspecialchars($log['ip_usuario'] ?? '—'),
                    'referencia_tipo' => htmlspecialchars($log['referencia_tipo'] ?? ''),
                    'referencia_id' => htmlspecialchars($log['referencia_id'] ?? ''),
                    'search_text' => strtolower($log['acao'] . ' ' . $log['descricao'] . ' ' . $nomeUsuario . ' ' . ($log['usuario_email'] ?? ''))
                ];
            }
            echo json_encode($logsArray);
        ?>;
        
        allLogs = phpLogs;
        filteredLogs = [...allLogs];
        renderCurrentView();
    }
    
    // =====================================================
    // RENDERIZAR VISÃO ATUAL (com paginação)
    // =====================================================
    function renderCurrentView() {
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const pageLogs = filteredLogs.slice(startIndex, endIndex);
        
        renderLogs(pageLogs);
        renderPagination();
    }
    
    // =====================================================
    // RENDERIZAR LOGS
    // =====================================================
    function renderLogs(logs) {
        if (!logsBody) return;
        
        if (logs.length === 0) {
            logsBody.innerHTML = `
                <div class="empty-state" style="display:block">
                    <div style="font-size:3rem; margin-bottom:1rem;">📋</div>
                    <h3 style="margin-bottom:0.5rem;">Nenhum log encontrado</h3>
                    <p>Tente ajustar os filtros ou realizar uma nova busca</p>
                </div>
            `;
            return;
        }
        
        logsBody.innerHTML = logs.map(log => {
            // Determinar classe do papel para o avatar
            let roleClass = 'role-Sistema';
            if (log.usuario_papel === 'Admin') roleClass = 'role-Admin';
            else if (log.usuario_papel === 'Professor') roleClass = 'role-Professor';
            else if (log.usuario_papel === 'Aluno') roleClass = 'role-Aluno';
            else if (log.usuario_papel === 'Coordenador') roleClass = 'role-Coordenador';
            
            const refTexto = (log.referencia_tipo && log.referencia_tipo !== '' && log.referencia_id && log.referencia_id !== '') 
                ? `<span class="log-status status-concluido">${log.referencia_tipo} #${log.referencia_id}</span>` 
                : '<span class="log-status">—</span>';
            
            return `
                <div class="log-row" data-cat="${log.categoria}" data-search="${log.search_text}">
                    <div class="log-ts">
                        <div class="date">${log.data_formatada}</div>
                        <div class="time">${log.hora_formatada}</div>
                    </div>
                    <div>
                        <span class="log-cat cat-${log.categoria}">${log.categoria_label}</span>
                    </div>
                    <div class="log-desc">
                        <strong>${log.acao}</strong><br>
                        <small>${log.descricao}</small>
                    </div>
                    <div class="log-actor">
                        <div class="actor-avatar ${roleClass}">${log.iniciais}</div>
                        <div class="actor-info">
                            <div class="actor-name">${log.usuario_nome}</div>
                            <div class="actor-role">${log.usuario_papel}</div>
                        </div>
                    </div>
                    <div>
                        ${refTexto}
                    </div>
                    <div class="log-detail-btn-wrap">
                        <button class="btn-detail" onclick='openModal(${JSON.stringify(log).replace(/'/g, "\\'")})'>
                            Detalhes
                        </button>
                    </div>
                </div>
            `;
        }).join('');
    }
    
    // =====================================================
    // RENDERIZAR PAGINAÇÃO
    // =====================================================
    function renderPagination() {
        const totalItems = filteredLogs.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const startItem = (currentPage - 1) * itemsPerPage + 1;
        const endItem = Math.min(currentPage * itemsPerPage, totalItems);
        
        // Atualizar info
        if (totalItems > 0) {
            paginationInfo.innerHTML = `Mostrando ${startItem} - ${endItem} de ${totalItems} registros`;
        } else {
            paginationInfo.innerHTML = `Mostrando 0 de 0 registros`;
        }
        
        // Gerar botões
        let buttonsHtml = '';
        
        // Botão Primeira
        buttonsHtml += `<button class="pg-btn" onclick="goToPage(1)" ${currentPage === 1 || totalPages === 0 ? 'disabled' : ''}>«</button>`;
        
        // Botão Anterior
        buttonsHtml += `<button class="pg-btn" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 || totalPages === 0 ? 'disabled' : ''}>‹</button>`;
        
        if (totalPages > 0) {
            // Números das páginas
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, currentPage + 2);
            
            if (startPage > 1) {
                buttonsHtml += `<button class="pg-btn" onclick="goToPage(1)">1</button>`;
                if (startPage > 2) buttonsHtml += `<span class="pg-dots">...</span>`;
            }
            
            for (let i = startPage; i <= endPage; i++) {
                buttonsHtml += `<button class="pg-btn ${currentPage === i ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
            }
            
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) buttonsHtml += `<span class="pg-dots">...</span>`;
                buttonsHtml += `<button class="pg-btn" onclick="goToPage(${totalPages})">${totalPages}</button>`;
            }
        } else {
            buttonsHtml += `<button class="pg-btn active">1</button>`;
        }
        
        // Botão Próximo
        buttonsHtml += `<button class="pg-btn" onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}>›</button>`;
        
        // Botão Última
        buttonsHtml += `<button class="pg-btn" onclick="goToPage(${totalPages})" ${currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}>»</button>`;
        
        paginationButtons.innerHTML = buttonsHtml;
    }
    
    // =====================================================
    // FUNÇÃO PARA MUDAR DE PÁGINA (global para os botões)
    // =====================================================
    window.goToPage = function(page) {
        const totalPages = Math.ceil(filteredLogs.length / itemsPerPage);
        if (page < 1 || page > totalPages || totalPages === 0) return;
        currentPage = page;
        renderCurrentView();
        // Scroll suave para o topo da lista
        const container = document.querySelector('.logs-container');
        if (container) {
            container.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };
    
    // =====================================================
    // FILTRAR LOGS
    // =====================================================
    function filterLogs() {
        const searchTerm = searchInput?.value.toLowerCase().trim() || '';
        const activeChip = document.querySelector(".chip.active");
        const categoryFilter = activeChip?.dataset.cat || "all";
        
        filteredLogs = allLogs.filter(log => {
            // Filtro por categoria
            if (categoryFilter !== "all" && log.categoria !== categoryFilter) {
                return false;
            }
            // Filtro por pesquisa
            if (searchTerm && !log.search_text.includes(searchTerm)) {
                return false;
            }
            return true;
        });
        
        // Resetar para primeira página ao filtrar
        currentPage = 1;
        renderCurrentView();
    }
    
    // =====================================================
    // MODAL FUNCTIONS
    // =====================================================
    window.openModal = function(log) {
        const refTexto = (log.referencia_tipo && log.referencia_tipo !== '' && log.referencia_id && log.referencia_id !== '')
            ? `${log.referencia_tipo} #${log.referencia_id}`
            : '—';
        
        document.getElementById("mAcao").innerText = log.acao;
        document.getElementById("mDescricao").innerText = log.descricao;
        document.getElementById("mData").innerText = `${log.data_formatada} ${log.hora_formatada}`;
        document.getElementById("mCategoria").innerText = log.categoria_label;
        document.getElementById("mUsuario").innerText = log.usuario_nome;
        document.getElementById("mEmail").innerText = log.usuario_email;
        document.getElementById("mPapel").innerText = log.usuario_papel;
        document.getElementById("mIp").innerText = log.ip_usuario;
        document.getElementById("mReferencia").innerText = refTexto;
        
        // Atualizar badge da categoria no modal
        const catBadge = document.getElementById("modalCatBadge");
        if (catBadge) {
            catBadge.innerText = log.categoria_label;
            catBadge.style.background = `var(--cat-${log.categoria})`;
        }
        
        modal.style.display = "flex";
        modal.classList.add("open");
        document.body.style.overflow = "hidden";
    };
    
    window.closeModal = function() {
        modal.style.display = "none";
        modal.classList.remove("open");
        document.body.style.overflow = "";
    };
    
    // Fechar modal ao clicar no overlay
    modal?.addEventListener("click", function(e) {
        if (e.target === modal) closeModal();
    });
    
    // Fechar modal com ESC
    document.addEventListener("keydown", function(e) {
        if (e.key === "Escape" && modal.style.display === "flex") closeModal();
    });
    
    // =====================================================
    // EVENT LISTENERS
    // =====================================================
    
    // Pesquisa com debounce
    let searchTimeout;
    searchInput?.addEventListener("input", function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(filterLogs, 300);
    });
    
    // Chips de categoria
    chips.forEach(chip => {
        chip.addEventListener("click", function() {
            chips.forEach(c => c.classList.remove("active"));
            this.classList.add("active");
            filterLogs();
        });
    });
    
    // Items por página
    itemsPerPageSelect?.addEventListener("change", function() {
        itemsPerPage = parseInt(this.value);
        currentPage = 1;
        filterLogs();
    });
    
    // =====================================================
    // INICIALIZAR
    // =====================================================
    loadLogsData();
    
})();
</script>

<?php include("../Includes/Footer.php"); ?>

</body>
</html>