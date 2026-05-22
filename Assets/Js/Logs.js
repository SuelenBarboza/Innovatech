/**
 * Logs.js — InnovaTech
 * Filtragem, paginação e modal de detalhes completo do log.
 */

(function () {
    'use strict';

    /* ── ELEMENTOS ──────────────────────────────────────── */
    const rows          = document.querySelectorAll('.log-row');
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationBtns = document.getElementById('paginationBtns');
    const searchInput   = document.getElementById('searchInput');
    const roleFilter    = document.getElementById('roleFilter');
    const sortFilter    = document.getElementById('sortFilter');
    const chips         = document.querySelectorAll('.chip');
    const modalOverlay  = document.getElementById('modalOverlay');

    const PAGE_SIZE = 25;
    let currentPage  = 1;
    let filteredRows = [];

    /* ── FILTRAR & ORDENAR ──────────────────────────────── */
    function filterRows() {
        const term      = searchInput.value.toLowerCase().trim();
        const role      = roleFilter.value;
        const activeCat = document.querySelector('.chip.active').dataset.cat;

        filteredRows = Array.from(rows).filter(row => {
            if (activeCat !== 'all' && row.dataset.cat !== activeCat) return false;
            if (role && row.dataset.role !== role)                      return false;
            if (term && !row.dataset.search.includes(term))            return false;
            return true;
        });

        const dir = sortFilter.value;
        filteredRows.sort((a, b) => {
            const ta = new Date(a.dataset.ts.replace(' ', 'T'));
            const tb = new Date(b.dataset.ts.replace(' ', 'T'));
            return dir === 'desc' ? tb - ta : ta - tb;
        });

        currentPage = 1;
        renderPage();
    }

    /* ── RENDERIZAR PÁGINA ──────────────────────────────── */
    function renderPage() {
        const total      = filteredRows.length;
        const totalPages = Math.ceil(total / PAGE_SIZE) || 1;
        const start      = (currentPage - 1) * PAGE_SIZE;
        const end        = Math.min(start + PAGE_SIZE, total);

        rows.forEach(r => (r.style.display = 'none'));
        for (let i = start; i < end; i++) {
            if (filteredRows[i]) filteredRows[i].style.display = 'grid';
        }

        const empty = document.querySelector('.empty-state');
        if (total === 0) {
            if (empty) empty.style.display = 'block';
            paginationInfo.textContent = 'Nenhum resultado encontrado';
            paginationBtns.innerHTML   = '';
        } else {
            if (empty) empty.style.display = 'none';
            paginationInfo.textContent = `Exibindo ${start + 1}–${end} de ${total} registros`;
            renderPaginationButtons(totalPages);
        }
    }

    /* ── BOTÕES DE PAGINAÇÃO ────────────────────────────── */
    function renderPaginationButtons(totalPages) {
        let html = '';
        html += btn('← Anterior', currentPage - 1, currentPage === 1);

        const s = Math.max(1, currentPage - 2);
        const e = Math.min(totalPages, currentPage + 2);

        if (s > 1) {
            html += btn('1', 1);
            if (s > 2) html += `<button class="pg-btn" disabled>…</button>`;
        }
        for (let i = s; i <= e; i++) {
            html += btn(i, i, false, i === currentPage);
        }
        if (e < totalPages) {
            if (e < totalPages - 1) html += `<button class="pg-btn" disabled>…</button>`;
            html += btn(totalPages, totalPages);
        }

        html += btn('Próximo →', currentPage + 1, currentPage === totalPages);
        paginationBtns.innerHTML = html;
    }

    function btn(label, page, disabled = false, active = false) {
        return `<button class="pg-btn${active ? ' active' : ''}"
                    onclick="window._logsGoPage(${page})"
                    ${disabled ? 'disabled' : ''}>${label}</button>`;
    }

    window._logsGoPage = function (page) {
        const totalPages = Math.ceil(filteredRows.length / PAGE_SIZE) || 1;
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        renderPage();
        document.querySelector('.logs-container').scrollIntoView({ behavior: 'smooth', block: 'start' });
    };

    /* ── EVENTOS DE FILTRO ──────────────────────────────── */
    let searchTimer;
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(filterRows, 280);
    });
    roleFilter.addEventListener('change', filterRows);
    sortFilter.addEventListener('change', filterRows);
    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            chips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            filterRows();
        });
    });

    /* ================================================================
       MODAL DE DETALHES
       ================================================================ */

    /**
     * Abre o modal preenchendo todos os campos com os dados do log.
     * @param {HTMLElement} btn — botão clicado dentro do .log-row
     */
    window.openModal = function (btnEl) {
        const row  = btnEl.closest('.log-row');
        const data = JSON.parse(row.getAttribute('data-modal'));

        /* Cabeçalho */
        document.getElementById('modalCatBadge').textContent = data.categoria;
        document.getElementById('modalTitle').textContent    = data.descricao;

        /* Cor do header conforme categoria */
        const catColors = {
            'Projeto'   : ['#3b82f6','#2563eb'],
            'Aluno'     : ['#22c55e','#16a34a'],
            'Relatório' : ['#f59e0b','#d97706'],
            'Resposta'  : ['#a855f7','#7e22ce'],
            'Comentário': ['#06b6d4','#0891b2'],
            'Tarefa'    : ['#f97316','#ea580c'],
            'Suporte'   : ['#ef4444','#dc2626'],
        };
        const [c1, c2] = catColors[data.categoria] || ['#3b82f6','#2563eb'];
        document.getElementById('modalHeader').style.background =
            `linear-gradient(135deg, ${c1}, ${c2})`;

        /* Quando */
        set('mDataHora',   data.data_hora);
        set('mDiaSemana',  data.dia_semana);

        /* Quem */
        set('mUsuarioNome',  data.usuario_nome);
        set('mUsuarioPapel', data.usuario_papel);
        set('mUsuarioEmail', data.usuario_email);

        /* Onde — Projeto */
        const projSection = document.getElementById('mProjetoSection');
        if (data.projeto_nome && data.projeto_nome !== '—') {
            projSection.style.display = '';
            set('mProjNome',   data.projeto_nome);
            set('mProjId',     data.projeto_id ? '#' + data.projeto_id : '—');
            set('mProjCat',    data.projeto_cat   || '—');
            set('mProjStatus', data.projeto_status || '—');
            set('mProjPrior',  data.projeto_prior  || '—');
            set('mProjPrazo',
                (data.projeto_inicio && data.projeto_inicio !== '—')
                    ? `${data.projeto_inicio} → ${data.projeto_fim}`
                    : '—');
        } else {
            projSection.style.display = 'none';
        }

        /* O que foi feito */
        set('mDescricao', data.descricao);

        const itemWrap = document.getElementById('mItemWrap');
        if (data.item_nome && data.item_nome !== '—') {
            itemWrap.style.display = '';
            // Rótulo dinâmico conforme categoria
            const itemLabels = {
                'Tarefa'    : 'Nome da tarefa',
                'Relatório' : 'Título do relatório',
                'Resposta'  : 'Relatório relacionado',
                'Suporte'   : 'Assunto do chamado',
                'Aluno'     : 'Aluno adicionado',
            };
            document.getElementById('mItemLabel').textContent =
                itemLabels[data.categoria] || 'Item relacionado';
            set('mItemNome', data.item_nome);
        } else {
            itemWrap.style.display = 'none';
        }

        const detalheWrap = document.getElementById('mDetalheWrap');
        if (data.detalhe_extra && data.detalhe_extra.trim()) {
            detalheWrap.style.display = '';
            document.getElementById('mDetalheExtra').textContent =
                data.detalhe_extra.length > 600
                    ? data.detalhe_extra.substring(0, 600) + '…'
                    : data.detalhe_extra;
        } else {
            detalheWrap.style.display = 'none';
        }

        /* Status */
        set('mStatus', data.status);

        /* Exibir */
        modalOverlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    };

    /* Fecha o modal */
    window.closeModal = function (evt) {
        if (evt && evt.target !== modalOverlay) return; // clique no conteúdo: não fecha
        modalOverlay.classList.remove('open');
        document.body.style.overflow = '';
    };

    /* ESC fecha o modal */
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            modalOverlay.classList.remove('open');
            document.body.style.overflow = '';
        }
    });

    /* Utilitário */
    function set(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value || '—';
    }

    /* ── INICIALIZAR ────────────────────────────────────── */
    filterRows();
})();
