// ======================================================
// ViewListTasks.js - JS completo adaptado para Tarefas
// ======================================================

console.log("JS de tarefas carregado!");

// ======================================================
// CONFIGURAÇÕES E VARIÁVEIS GLOBAIS
// ======================================================
let tarefas = [];
let editandoId = null;

const CONFIG = {
    endpoints: {
        prioridade: "../Config/UpdatePrioridade.php",
        status: "../Config/UpdateStatus.php",
        arquivar: "../Config/UpdateArquivado.php"
    }
};

// ======================================================
// INICIALIZAÇÃO DE DADOS
// ======================================================
function inicializarTarefas() {
    tarefas = [];
    document.querySelectorAll("#dados-tarefas tr").forEach(tr => {
        tarefas.push({
            id: tr.dataset.id,
            nome: tr.dataset.nome,
            tarefa: tr.dataset.tarefa,
            prioridade: tr.dataset.prioridade,
            status: tr.dataset.status,
            prazo: tr.dataset.prazo,
            descricao: tr.dataset.descricao,
            arquivado: Number(tr.dataset.arquivado)
        });
    });
    console.log(`Tarefas carregadas: ${tarefas.length}`);
}

function atualizarTarefasDoDOM() {
    tarefas = [];
    document.querySelectorAll("#dados-tarefas tr").forEach(tr => {
        tarefas.push({
            id: tr.dataset.id,
            nome: tr.dataset.nome,
            tarefa: tr.dataset.tarefa,
            prioridade: tr.dataset.prioridade,
            status: tr.dataset.status,
            prazo: tr.dataset.prazo,
            descricao: tr.dataset.descricao,
            arquivado: Number(tr.dataset.arquivado)
        });
    });
}

// ======================================================
// RENDERIZAÇÃO DE TABELAS
// ======================================================
function renderizarTabelas() {
    const tbodyTarefas = document.querySelector("#tabela-tarefas tbody");
    const tbodyArquivadas = document.querySelector("#tabela-ocultos tbody");

    tbodyTarefas.innerHTML = "";
    tbodyArquivadas.innerHTML = "";

    const ativos = tarefas.filter(t => t.arquivado === 0);
    const arquivados = tarefas.filter(t => t.arquivado === 1);

    if (ativos.length === 0) {
        tbodyTarefas.innerHTML = `
            <tr class="mensagem-tr">
                <td colspan="6" class="mensagem-central">
                    Você ainda não possui nenhuma tarefa cadastrada.
                </td>
            </tr>`;
    }

    if (arquivados.length === 0) {
        tbodyArquivadas.innerHTML = `
            <tr class="mensagem-tr">
                <td colspan="6" class="mensagem-central">
                    Seus arquivos estão vazios.
                </td>
            </tr>`;
    }

    ativos.forEach(t => tbodyTarefas.appendChild(criarLinha(t)));
    arquivados.forEach(t => tbodyArquivadas.appendChild(criarLinha(t)));

    setTimeout(filtrarTabela, 50);
}

function criarLinha(t) {
    const tr = document.createElement("tr");
    tr.dataset.id = t.id;
    tr.dataset.prioridade = t.prioridade;
    tr.dataset.status = t.status;
    tr.dataset.descricao = t.descricao;

    tr.innerHTML = `
        <td>${t.nome}</td>
        <td>${t.tarefa}</td>
        <td class="prioridade-cell">
            <span class="prioridade-display"></span>
            <select class="select-prioridade hidden" data-id="${t.id}" data-field="prioridade">
                <option value="">Selecionar</option>
                <option value="Baixa">Baixa</option>
                <option value="Média">Média</option>
                <option value="Alta">Alta</option>
            </select>
        </td>
        <td class="status-cell">
            <span class="status-display"></span>
            <select class="select-status hidden" data-id="${t.id}" data-field="status">
                <option value="">Selecionar</option>
                <option value="Planejamento">Planejamento</option>
                <option value="Em Andamento">Em Andamento</option>
                <option value="Concluído">Concluído</option>
            </select>
        </td>
        <td>${t.prazo}</td>
        <td>
            <button class="botao-visualizar" title="Ver detalhes">👁️</button>
            <button class="botao-editar-prioridade" title="Editar Prioridade">📋</button>
            <button class="botao-editar-status" title="Editar Status">📈</button>
            <button class="botao-ocultar">${t.arquivado ? "♻️" : "📂"}</button>
        </td>
    `;

    aplicarVisual(tr);

    const selectPrioridade = tr.querySelector(".select-prioridade");
    if (selectPrioridade && t.prioridade && t.prioridade !== "Não definido") selectPrioridade.value = t.prioridade;

    const selectStatus = tr.querySelector(".select-status");
    if (selectStatus && t.status && t.status !== "Não definido") selectStatus.value = t.status;

    return tr;
}

function aplicarVisual(tr) {
    if (!tr) return;
    const prioridadeDisplay = tr.querySelector(".prioridade-display");
    const statusDisplay = tr.querySelector(".status-display");

    atualizarDisplay(prioridadeDisplay, tr.dataset.prioridade, 'prioridade');
    atualizarDisplay(statusDisplay, tr.dataset.status, 'status');
}

// ======================================================
// EDIÇÃO DE PRIORIDADE E STATUS
// ======================================================
function iniciarEdicao(linha, campo = 'ambos') {
    if (editandoId && editandoId !== linha.dataset.id) cancelarEdicaoAnterior();
    editandoId = linha.dataset.id;

    if (campo === 'prioridade' || campo === 'ambos') {
        const display = linha.querySelector('.prioridade-display');
        const select = linha.querySelector('.select-prioridade');
        display.classList.add('hidden');
        select.classList.remove('hidden');
        select.value = linha.dataset.prioridade;
        select.focus();
    }

    if (campo === 'status' || campo === 'ambos') {
        const display = linha.querySelector('.status-display');
        const select = linha.querySelector('.select-status');
        display.classList.add('hidden');
        select.classList.remove('hidden');
        select.value = linha.dataset.status;
        if (campo === 'status') select.focus();
    }
}

function cancelarEdicaoAnterior() {
    if (!editandoId) return;
    const linha = document.querySelector(`tr[data-id="${editandoId}"]`);
    if (!linha) return;
    linha.querySelectorAll('.prioridade-display, .status-display').forEach(d => d.classList.remove('hidden'));
    linha.querySelectorAll('.select-prioridade, .select-status').forEach(s => s.classList.add('hidden'));
    editandoId = null;
}

function salvarCampo(select) {
    const campo = select.dataset.field;
    const valor = select.value;
    const id = select.dataset.id;
    const linha = select.closest('tr');
    const display = linha.querySelector(`.${campo}-display`);

    if (valor === "") {
        select.classList.add('hidden');
        display.classList.remove('hidden');
        editandoId = null;
        return;
    }

    const endpoint = campo === 'prioridade' ? CONFIG.endpoints.prioridade : CONFIG.endpoints.status;

    fetch(endpoint, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${id}&${campo}=${encodeURIComponent(valor)}`
    })
    .then(res => res.text())
    .then(res => {
        if (res.trim() === "ok") {
            atualizarDisplay(display, valor, campo);
            linha.dataset[campo] = valor;
            const trOriginal = document.querySelector(`#dados-tarefas tr[data-id="${id}"]`);
            if (trOriginal) trOriginal.dataset[campo] = valor;
            atualizarTarefasDoDOM();
            select.classList.add('hidden');
            display.classList.remove('hidden');
            editandoId = null;
        } else {
            alert(`Erro ao salvar ${campo}`);
            select.classList.add('hidden');
            display.classList.remove('hidden');
            editandoId = null;
        }
    })
    .catch(() => {
        alert("Erro de conexão");
        select.classList.add('hidden');
        display.classList.remove('hidden');
        editandoId = null;
    });
}

function atualizarDisplay(display, valor, tipo) {
    if (!display) return;
    display.textContent = valor || (tipo === 'prioridade' ? "Não definida" : "Não definido");
    display.className = `${tipo}-display`;
    if (valor && valor !== "Não definido") {
        if (tipo === 'prioridade') display.classList.add(`prioridade-${valor.toLowerCase()}`);
        else display.classList.add(`status-${valor.toLowerCase().replace(' ', '').replace('í', 'i')}`);
    } else {
        display.classList.add(`${tipo}-indefinid${tipo === 'prioridade' ? 'a' : 'o'}`);
    }
}

// ======================================================
// ARQUIVAMENTO
// ======================================================
function atualizarArquivado(linha, arquivado) {
    const id = linha.dataset.id;

    fetch(CONFIG.endpoints.arquivar, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${id}&arquivado=${arquivado}`
    })
    .then(res => res.text())
    .then(res => {
        if (res.trim() === "ok") {
            const trOriginal = document.querySelector(`#dados-tarefas tr[data-id="${id}"]`);
            if (trOriginal) trOriginal.dataset.arquivado = arquivado;
            atualizarTarefasDoDOM();
            renderizarTabelas();
        } else {
            alert("Erro ao atualizar arquivamento");
        }
    })
    .catch(() => alert("Erro de conexão"));
}

// ======================================================
// MODAL DE DETALHES
// ======================================================
function abrirModal(linha) {
    document.getElementById("detalhe-nome").textContent = linha.cells[0].innerText;
    document.getElementById("detalhe-tarefa").textContent = linha.cells[1].innerText;
    document.getElementById("detalhe-prazo").textContent = linha.cells[4].innerText;
    document.getElementById("detalhe-prioridade").textContent = linha.dataset.prioridade || "Não definida";
    document.getElementById("detalhe-status").textContent = linha.dataset.status || "Não definido";
    document.getElementById("detalhe-descricao").textContent = linha.dataset.descricao || "Sem descrição";
    document.getElementById("modalDetalhes").style.display = "block";
}

// ======================================================
// FILTROS
// ======================================================
function filtrarTabela() {
    const linhas = document.querySelectorAll("#tabela-tarefas tbody tr");
    let linhasVisiveis = 0;

    linhas.forEach(linha => {
        if (linha.classList.contains("mensagem-tr")) return;

        const status = linha.dataset.status;
        const prioridade = linha.dataset.prioridade;
        const filtroStatus = document.getElementById("filtro-status").value;
        const filtroPrioridade = document.getElementById("filtro-prioridade").value;

        const statusOK = !filtroStatus || filtroStatus === status;
        const prioridadeOK = !filtroPrioridade || filtroPrioridade === prioridade;

        if (statusOK && prioridadeOK) {
            linha.style.display = "";
            linhasVisiveis++;
        } else linha.style.display = "none";
    });

    atualizarMensagensFiltro(linhasVisiveis);
}

function atualizarMensagensFiltro(linhasVisiveis) {
    const tbody = document.querySelector("#tabela-tarefas tbody");
    const filtroStatus = document.getElementById("filtro-status").value;
    const filtroPrioridade = document.getElementById("filtro-prioridade").value;

    if ((filtroStatus || filtroPrioridade) && linhasVisiveis === 0) {
        const mensagemTr = tbody.querySelector(".mensagem-tr");
        if (!mensagemTr) {
            const tr = document.createElement("tr");
            tr.className = "mensagem-tr";
            tr.innerHTML = `<td colspan="6" class="mensagem-central">Nenhuma tarefa encontrada com os filtros selecionados.</td>`;
            tbody.appendChild(tr);
        }
    } else {
        const mensagemFiltro = tbody.querySelector(".mensagem-tr");
        if (mensagemFiltro && !mensagemFiltro.textContent.includes("nenhuma tarefa cadastrada")) mensagemFiltro.remove();
    }
}

// ======================================================
// EVENT LISTENERS
// ======================================================
document.addEventListener("DOMContentLoaded", () => {
    inicializarTarefas();
    renderizarTabelas();

    const modal = document.getElementById("modalDetalhes");
    const fechar = document.querySelector(".fechar");
    const btnToggle = document.getElementById("btnToggleArquivar");
    const containerArquivar = document.getElementById("containerArquivar");
    const filtroStatus = document.getElementById("filtro-status");
    const filtroPrioridade = document.getElementById("filtro-prioridade");

    document.addEventListener("click", e => {
        const btnEditarPrioridade = e.target.closest(".botao-editar-prioridade");
        if (btnEditarPrioridade) { iniciarEdicao(btnEditarPrioridade.closest("tr"), 'prioridade'); return; }

        const btnEditarStatus = e.target.closest(".botao-editar-status");
        if (btnEditarStatus) { iniciarEdicao(btnEditarStatus.closest("tr"), 'status'); return; }

        const btnVisualizar = e.target.closest(".botao-visualizar");
        if (btnVisualizar) { abrirModal(btnVisualizar.closest("tr")); return; }

        const btnOcultar = e.target.closest(".botao-ocultar");
        if (btnOcultar) {
            const linha = btnOcultar.closest("tr");
            const estaNaTabelaPrincipal = linha.closest("#tabela-tarefas");
            atualizarArquivado(linha, estaNaTabelaPrincipal ? 1 : 0);
            return;
        }
    });

    document.addEventListener("change", e => {
        const select = e.target.closest(".select-prioridade, .select-status");
        if (select) salvarCampo(select);
    });

    if (fechar) fechar.addEventListener("click", () => modal.style.display = "none");
    window.addEventListener("click", e => { if (e.target === modal) modal.style.display = "none"; });

    if (btnToggle && containerArquivar) btnToggle.addEventListener("click", () => {
        const aberto = containerArquivar.style.display === "block";
        containerArquivar.style.display = aberto ? "none" : "block";
        btnToggle.textContent = aberto ? "📂 Ver Tarefas Arquivadas" : "🔙 Ocultar Arquivados";
    });

    if (filtroStatus) filtroStatus.addEventListener("change", filtrarTabela);
    if (filtroPrioridade) filtroPrioridade.addEventListener("change", filtrarTabela);

    atualizarMensagensFiltro(document.querySelectorAll("#tabela-tarefas tbody tr:not(.mensagem-tr)").length);
});

document.addEventListener("keydown", e => { if (e.key === "Escape") cancelarEdicaoAnterior(); });
