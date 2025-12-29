// ======================================================
// CONFIGURAÇÕES E VARIÁVEIS GLOBAIS
// ======================================================
console.log("JS carregado!");

let projetos = [];
let editandoId = null; // Guarda qual linha está editando

const CONFIG = {
    endpoints: {
        prioridade: "../Config/UpdatePrioridade.php",
        status: "../Config/UpdateStatus.php",
        arquivar: "../Config/UpdateArquivado.php"
    }
};

// ======================================================
// FUNÇÕES DE INICIALIZAÇÃO E DADOS
// ======================================================

/**
 * Inicializa o array de projetos a partir dos dados do PHP
 */
function inicializarProjetos() {
    projetos = []; // Limpa o array para evitar duplicação
    
    document.querySelectorAll("#dados-projetos tr").forEach(tr => {
        projetos.push({
            id: tr.dataset.id,
            nome: tr.dataset.nome,
            categoria: tr.dataset.categoria,
            prioridade: tr.dataset.prioridade,
            status: tr.dataset.status,
            prazo: tr.dataset.prazo,
            descricao: tr.dataset.descricao,
            arquivado: Number(tr.dataset.arquivado)
        });
    });
    
    console.log(`Projetos carregados: ${projetos.length}`);
}

/**
 * Atualiza o array de projetos após modificações
 */
function atualizarProjetosDoDOM() {
    projetos = [];
    
    document.querySelectorAll("#dados-projetos tr").forEach(tr => {
        projetos.push({
            id: tr.dataset.id,
            nome: tr.dataset.nome,
            categoria: tr.dataset.categoria,
            prioridade: tr.dataset.prioridade,
            status: tr.dataset.status,
            prazo: tr.dataset.prazo,
            descricao: tr.dataset.descricao,
            arquivado: Number(tr.dataset.arquivado)
        });
    });
}

// ======================================================
// FUNÇÕES DE RENDERIZAÇÃO
// ======================================================

/**
 * Renderiza as tabelas de projetos ativos e arquivados
 */
function renderizarTabelas() {
    const tbodyProjetos = document.querySelector("#tabela-projetos tbody");
    const tbodyArquivados = document.querySelector("#tabela-ocultos tbody");

    tbodyProjetos.innerHTML = "";
    tbodyArquivados.innerHTML = "";

    const ativos = projetos.filter(p => p.arquivado === 0);
    const arquivados = projetos.filter(p => p.arquivado === 1);

    // Mensagem para tabela vazia (ativos)
    if (ativos.length === 0) {
        tbodyProjetos.innerHTML = `
            <tr class="mensagem-tr">
                <td colspan="6" class="mensagem-central">
                    Você ainda não possui nenhum projeto cadastrado.
                </td>
            </tr>`;
    }

    // Mensagem para tabela vazia (arquivados)
    if (arquivados.length === 0) {
        tbodyArquivados.innerHTML = `
            <tr class="mensagem-tr">
                <td colspan="6" class="mensagem-central">
                    Seus arquivos estão vazios.
                </td>
            </tr>`;
    }

    // Adiciona projetos ativos
    ativos.forEach(p => tbodyProjetos.appendChild(criarLinha(p)));
    
    // Adiciona projetos arquivados
    arquivados.forEach(p => tbodyArquivados.appendChild(criarLinha(p)));
    
    // Aplica filtros após renderizar
    setTimeout(filtrarTabela, 50);
}

/**
 * Cria uma linha HTML para um projeto
 * @param {Object} p - Objeto do projeto
 * @returns {HTMLElement} Linha da tabela
 */
function criarLinha(p) {
    const tr = document.createElement("tr");

    tr.dataset.id = p.id;
    tr.dataset.prioridade = p.prioridade;
    tr.dataset.status = p.status;
    tr.dataset.descricao = p.descricao;

    tr.innerHTML = `
        <td>${p.nome}</td>
        <td>${p.categoria}</td>

        <td class="prioridade-cell">
            <span class="prioridade-display"></span>
            <select class="select-prioridade hidden" data-id="${p.id}" data-field="prioridade">
                <option value="">Selecionar</option>
                <option value="Baixa">Baixa</option>
                <option value="Média">Média</option>
                <option value="Alta">Alta</option>
            </select>
        </td>

        <td class="status-cell">
            <span class="status-display"></span>
            <select class="select-status hidden" data-id="${p.id}" data-field="status">
                <option value="">Selecionar</option>
                <option value="Planejamento">Planejamento</option>
                <option value="Andamento">Andamento</option>
                <option value="Pendente">Pendente</option>
            </select>
        </td>

        <td>${p.prazo}</td>

        <td>
            <button class="botao-visualizar">👁️</button>
            <button class="botao-editar-prioridade" title="Editar Prioridade">📋</button>
            <button class="botao-editar-status" title="Editar Status">📈</button>
            <button class="botao-ocultar">
                ${p.arquivado ? "♻️ Restaurar" : "📂 Arquivar"}
            </button>
        </td>
    `;

    // Aplica estilos visuais
    aplicarVisual(tr);

    // Pré-seleciona valores nos selects
    const selectPrioridade = tr.querySelector(".select-prioridade");
    if (selectPrioridade && p.prioridade && p.prioridade !== "Não definido") {
        selectPrioridade.value = p.prioridade;
    }

    const selectStatus = tr.querySelector(".select-status");
    if (selectStatus && p.status && p.status !== "Não definido") {
        selectStatus.value = p.status;
    }

    return tr;
}

/**
 * Aplica estilos visuais à linha
 * @param {HTMLElement} linha - Linha da tabela
 */
function aplicarVisual(linha) {
    if (!linha) return;

    const prioridade = linha.dataset.prioridade;
    const status = linha.dataset.status;

    const prioridadeDisplay = linha.querySelector(".prioridade-display");
    const statusDisplay = linha.querySelector(".status-display");

    if (prioridadeDisplay) {
        atualizarDisplay(prioridadeDisplay, prioridade, 'prioridade');
    }

    if (statusDisplay) {
        atualizarDisplay(statusDisplay, status, 'status');
    }
}

// ======================================================
// FUNÇÕES DE EDIÇÃO (PRIORIDADE E STATUS)
// ======================================================

/**
 * Inicia a edição de prioridade ou status
 * @param {HTMLElement} linha - Linha da tabela
 * @param {string} campo - 'prioridade', 'status' ou 'ambos'
 */
function iniciarEdicao(linha, campo = 'ambos') {
    // Cancela edição anterior se houver
    if (editandoId && editandoId !== linha.dataset.id) {
        cancelarEdicaoAnterior();
    }
    
    editandoId = linha.dataset.id;
    
    // Edição de prioridade
    if (campo === 'prioridade' || campo === 'ambos') {
        const prioridadeDisplay = linha.querySelector('.prioridade-display');
        const prioridadeSelect = linha.querySelector('.select-prioridade');
        
        if (prioridadeDisplay && prioridadeSelect) {
            prioridadeDisplay.classList.add('hidden');
            prioridadeSelect.classList.remove('hidden');
            
            const valorAtual = linha.dataset.prioridade;
            if (valorAtual && valorAtual !== "Não definido") {
                prioridadeSelect.value = valorAtual;
            }
            
            prioridadeSelect.focus();
        }
    }
    
    // Edição de status
    if (campo === 'status' || campo === 'ambos') {
        const statusDisplay = linha.querySelector('.status-display');
        const statusSelect = linha.querySelector('.select-status');
        
        if (statusDisplay && statusSelect) {
            statusDisplay.classList.add('hidden');
            statusSelect.classList.remove('hidden');
            
            const valorAtual = linha.dataset.status;
            if (valorAtual && valorAtual !== "Não definido") {
                statusSelect.value = valorAtual;
            }
            
            if (campo === 'status') statusSelect.focus();
        }
    }
}

/**
 * Cancela a edição anterior
 */
function cancelarEdicaoAnterior() {
    if (!editandoId) return;
    
    const linhaAnterior = document.querySelector(`tr[data-id="${editandoId}"]`);
    if (linhaAnterior) {
        const displays = linhaAnterior.querySelectorAll('.prioridade-display, .status-display');
        const selects = linhaAnterior.querySelectorAll('.select-prioridade, .select-status');
        
        displays.forEach(display => display.classList.remove('hidden'));
        selects.forEach(select => select.classList.add('hidden'));
    }
    
    editandoId = null;
}

/**
 * Salva alterações de prioridade ou status no servidor
 * @param {HTMLSelectElement} select - Elemento select alterado
 */
function salvarCampo(select) {
    const campo = select.dataset.field; // 'prioridade' ou 'status'
    const valor = select.value;
    const id = select.dataset.id;
    const linha = select.closest('tr');
    const display = linha.querySelector(`.${campo}-display`);
    
    // Se não selecionou nada, cancela
    if (valor === "") {
        select.classList.add('hidden');
        display.classList.remove('hidden');
        editandoId = null;
        return;
    }
    
    // Determina endpoint correto
    const endpoint = campo === 'prioridade' ? CONFIG.endpoints.prioridade : CONFIG.endpoints.status;
    const paramName = campo;
    
    // Envia para o servidor
    fetch(endpoint, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${id}&${paramName}=${encodeURIComponent(valor)}`
    })
    .then(res => res.text())
    .then(res => {
        if (res.trim() === "ok") {
            // Atualiza visualmente
            atualizarDisplay(display, valor, campo);
            
            // Atualiza dataset da linha
            linha.dataset[campo] = valor;
            
            // Atualiza dados originais (tabela oculta)
            const trOriginal = document.querySelector(`#dados-projetos tr[data-id="${id}"]`);
            if (trOriginal) {
                trOriginal.dataset[campo] = valor;
            }
            
            // Atualiza array de projetos
            atualizarProjetosDoDOM();
            
            // Esconde select e mostra display
            select.classList.add('hidden');
            display.classList.remove('hidden');
            
            editandoId = null;
            console.log(`${campo} salvo com sucesso:`, valor);
        } else {
            alert(`Erro ao salvar ${campo}`);
            select.classList.add('hidden');
            display.classList.remove('hidden');
            editandoId = null;
        }
    })
    .catch(() => {
        alert("Erro de conexão com o servidor");
        select.classList.add('hidden');
        display.classList.remove('hidden');
        editandoId = null;
    });
}

/**
 * Atualiza o display de prioridade ou status com estilos
 * @param {HTMLElement} display - Elemento span do display
 * @param {string} valor - Valor a ser exibido
 * @param {string} tipo - 'prioridade' ou 'status'
 */
function atualizarDisplay(display, valor, tipo) {
    if (!display) return;
    
    display.textContent = valor || (tipo === 'prioridade' ? "Não definida" : "Não definido");
    display.className = `${tipo}-display`;
    
    // Remove classes de cor antigas
    const classesParaRemover = [];
    for (const className of display.classList) {
        if (className.startsWith(`${tipo}-`) && className !== `${tipo}-display`) {
            classesParaRemover.push(className);
        }
    }
    classesParaRemover.forEach(className => display.classList.remove(className));
    
    // Adiciona nova classe de cor
    if (valor && valor !== "Não definido" && valor !== "Não definida") {
        if (tipo === 'prioridade') {
            display.classList.add(`prioridade-${valor.toLowerCase()}`);
        } else if (tipo === 'status') {
            const classeStatus = valor.toLowerCase().replace(' ', '').replace('í', 'i');
            display.classList.add(`status-${classeStatus}`);
        }
    } else {
        display.classList.add(`${tipo}-indefinid${tipo === 'prioridade' ? 'a' : 'o'}`);
    }
}

// ======================================================
// FUNÇÕES DE ARQUIVAMENTO
// ======================================================

/**
 * Arquiva ou restaura um projeto
 * @param {HTMLElement} linha - Linha da tabela
 * @param {number} arquivado - 1 para arquivar, 0 para restaurar
 */
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
            // Atualiza dados originais (tabela oculta)
            const trOriginal = document.querySelector(`#dados-projetos tr[data-id="${id}"]`);
            if (trOriginal) {
                trOriginal.dataset.arquivado = arquivado;
            }
            
            // Atualiza array de projetos
            atualizarProjetosDoDOM();
            
            // Renderiza tabelas novamente
            renderizarTabelas();
            
            console.log(`Projeto ${arquivado ? 'arquivado' : 'restaurado'} com sucesso`);
        } else {
            alert("Erro ao atualizar arquivamento");
        }
    })
    .catch(() => {
        alert("Erro de conexão com o servidor");
    });
}

// ======================================================
// FUNÇÕES DE MODAL
// ======================================================

/**
 * Abre o modal com detalhes do projeto
 * @param {HTMLElement} linha - Linha da tabela
 */
function abrirModal(linha) {
    document.getElementById("detalhe-nome").textContent = linha.cells[0].innerText;
    document.getElementById("detalhe-categoria").textContent = linha.cells[1].innerText;
    document.getElementById("detalhe-prazo").textContent = linha.cells[4].innerText;
    document.getElementById("detalhe-prioridade").textContent = linha.dataset.prioridade || "Não definida";
    document.getElementById("detalhe-status").textContent = linha.dataset.status || "Não definido";
    document.getElementById("detalhe-descricao").textContent = linha.dataset.descricao || "Sem descrição";

    document.getElementById("modalDetalhes").style.display = "block";
}

// ======================================================
// FUNÇÕES DE FILTRO
// ======================================================

/**
 * Filtra a tabela de projetos ativos
 */
function filtrarTabela() {
    const linhas = document.querySelectorAll("#tabela-projetos tbody tr");
    let linhasVisiveis = 0;
    
    linhas.forEach(linha => {
        // Ignora linha de mensagem
        if (linha.classList.contains("mensagem-tr")) {
            return;
        }
        
        const status = linha.dataset.status;
        const prioridade = linha.dataset.prioridade;
        const filtroStatus = document.getElementById("filtro-status").value;
        const filtroPrioridade = document.getElementById("filtro-prioridade").value;

        const statusOK = !filtroStatus || filtroStatus === "" || filtroStatus === status;
        const prioridadeOK = !filtroPrioridade || filtroPrioridade === "" || filtroPrioridade === prioridade;

        if (statusOK && prioridadeOK) {
            linha.style.display = "";
            linhasVisiveis++;
        } else {
            linha.style.display = "none";
        }
    });
    
    atualizarMensagensFiltro(linhasVisiveis);
}

/**
 * Atualiza mensagens quando não há resultados no filtro
 * @param {number} linhasVisiveis - Quantidade de linhas visíveis
 */
function atualizarMensagensFiltro(linhasVisiveis) {
    const tbodyProjetos = document.querySelector("#tabela-projetos tbody");
    const filtroStatus = document.getElementById("filtro-status").value;
    const filtroPrioridade = document.getElementById("filtro-prioridade").value;
    
    // Se há filtro ativo e nenhuma linha visível, mostra mensagem
    if ((filtroStatus || filtroPrioridade) && linhasVisiveis === 0) {
        const mensagemTr = tbodyProjetos.querySelector(".mensagem-tr");
        if (!mensagemTr) {
            const tr = document.createElement("tr");
            tr.className = "mensagem-tr";
            tr.innerHTML = `
                <td colspan="6" class="mensagem-central">
                    Nenhum projeto encontrado com os filtros selecionados.
                </td>
            `;
            tbodyProjetos.appendChild(tr);
        }
    } else {
        // Remove mensagem de filtro se existir
        const mensagemFiltro = tbodyProjetos.querySelector(".mensagem-tr");
        if (mensagemFiltro && !mensagemFiltro.querySelector(".mensagem-central")?.textContent.includes("nenhum projeto cadastrado")) {
            mensagemFiltro.remove();
        }
    }
}

/**
 * Atualiza mensagens das tabelas
 */
function atualizarMensagens() {
    const ativos = document.querySelectorAll("#tabela-projetos tbody tr:not(.mensagem-tr)").length;
    const arquivados = document.querySelectorAll("#tabela-ocultos tbody tr:not(.mensagem-tr)").length;
    
    // Remove mensagens existentes se houver dados
    if (ativos > 0) {
        const msgAtivos = document.querySelector("#tabela-projetos .mensagem-tr");
        if (msgAtivos) msgAtivos.remove();
    }
    
    if (arquivados > 0) {
        const msgArquivados = document.querySelector("#tabela-ocultos .mensagem-tr");
        if (msgArquivados) msgArquivados.remove();
    }
}

// ======================================================
// EVENT LISTENERS E INICIALIZAÇÃO
// ======================================================

document.addEventListener("DOMContentLoaded", () => {
    console.log("DOM completamente carregado");
    
    // Inicializa projetos
    inicializarProjetos();
    
    // Renderiza tabelas
    renderizarTabelas();
    
    // Elementos DOM
    const filtroStatus = document.getElementById("filtro-status");
    const filtroPrioridade = document.getElementById("filtro-prioridade");
    const modal = document.getElementById("modalDetalhes");
    const fechar = document.querySelector(".fechar");
    const btnToggle = document.getElementById("btnToggleArquivar");
    const containerArquivar = document.getElementById("containerArquivar");
    
    // ================= CLIQUE GERAL =================
    document.addEventListener("click", e => {
        // 📋 EDITAR PRIORIDADE
        const btnEditarPrioridade = e.target.closest(".botao-editar-prioridade");
        if (btnEditarPrioridade) {
            e.preventDefault();
            e.stopPropagation();
            iniciarEdicao(btnEditarPrioridade.closest("tr"), 'prioridade');
            return;
        }
        
        // 📈 EDITAR STATUS
        const btnEditarStatus = e.target.closest(".botao-editar-status");
        if (btnEditarStatus) {
            e.preventDefault();
            e.stopPropagation();
            iniciarEdicao(btnEditarStatus.closest("tr"), 'status');
            return;
        }
        
        // 👁️ VISUALIZAR
        const btnVisualizar = e.target.closest(".botao-visualizar");
        if (btnVisualizar) {
            e.preventDefault();
            e.stopPropagation();
            abrirModal(btnVisualizar.closest("tr"));
            return;
        }
        
        // 📂 ARQUIVAR / ♻️ RESTAURAR
        const btnOcultar = e.target.closest(".botao-ocultar");
        if (btnOcultar) {
            e.preventDefault();
            e.stopPropagation();
            
            const linha = btnOcultar.closest("tr");
            const estaNaTabelaPrincipal = linha.closest("#tabela-projetos");
            
            atualizarArquivado(linha, estaNaTabelaPrincipal ? 1 : 0);
            return;
        }
    });
    
    // ================= CHANGE DOS SELECTS =================
    document.addEventListener("change", e => {
        const select = e.target.closest(".select-prioridade, .select-status");
        if (select) {
            e.preventDefault();
            e.stopPropagation();
            salvarCampo(select);
        }
    });
    
    // ================= FECHAR MODAL =================
    if (fechar) {
        fechar.addEventListener("click", () => {
            modal.style.display = "none";
        });
    }
    
    window.addEventListener("click", e => {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });
    
    // ================= TOGGLE ARQUIVADOS =================
    if (btnToggle && containerArquivar) {
        btnToggle.addEventListener("click", () => {
            const aberto = containerArquivar.style.display === "block";
            containerArquivar.style.display = aberto ? "none" : "block";
            btnToggle.textContent = aberto
                ? "📂 Ver Projetos Arquivados"
                : "🔙 Ocultar Arquivados";
            
            // Atualiza mensagens quando mostrar arquivados
            if (!aberto) {
                setTimeout(atualizarMensagens, 50);
            }
        });
    }
    
    // ================= FILTROS =================
    if (filtroStatus) {
        filtroStatus.addEventListener("change", filtrarTabela);
    }
    
    if (filtroPrioridade) {
        filtroPrioridade.addEventListener("change", filtrarTabela);
    }
    
    // ================= INICIALIZAÇÃO FINAL =================
    atualizarMensagens();
    
    // Log de diagnóstico
    setTimeout(() => {
        console.log("=== DIAGNÓSTICO INICIAL ===");
        console.log("Total de projetos:", projetos.length);
        console.log("Botões de edição prioridade:", document.querySelectorAll('.botao-editar-prioridade').length);
        console.log("Botões de edição status:", document.querySelectorAll('.botao-editar-status').length);
        console.log("Botões de visualizar:", document.querySelectorAll('.botao-visualizar').length);
        console.log("=== PRONTO PARA USO ===");
    }, 1000);
});

// ======================================================
// EVENT LISTENER PARA TECLA ESC (CANCELAR EDIÇÃO)
// ======================================================
document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
        cancelarEdicaoAnterior();
    }
});