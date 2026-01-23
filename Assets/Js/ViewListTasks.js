// ViewListTask.js
// ======================================================
// CONFIGURAÇÕES E VARIÁVEIS GLOBAIS
// ======================================================
console.log("ViewListTask.js carregado!");

let tarefas = [];
let editandoId = null;

// CORREÇÃO DEFINITIVA: Caminhos corretos baseados na estrutura real
// Assets/Js/ → ../../Config/ (sobe dois níveis: Js → Assets → raiz → Config)
const CONFIG = {
    endpoints: {
        prioridade: "/Innovatech/Config/UpdatePriorityTasks.php",
        status: "/Innovatech/Config/UpdateStatusTasks.php",
        arquivar: "/Innovatech/Config/UpdateArchivedTasks.php"
    }
};



// ======================================================
// FUNÇÕES DE DIAGNÓSTICO
// ======================================================



// ======================================================
// FUNÇÕES DE INICIALIZAÇÃO E DADOS
// ======================================================

function inicializarTarefas() {
    tarefas = [];
    
    document.querySelectorAll("#dados-tarefas tr").forEach(tr => {
        tarefas.push({
            id: tr.dataset.id,
            nomeTarefa: tr.dataset.nomeTarefa,
            projeto: tr.dataset.projeto,
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
            nomeTarefa: tr.dataset.nomeTarefa,
            projeto: tr.dataset.projeto,
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

function renderizarTabelas() {
    const tbodyTarefas = document.querySelector("#tabela-tarefas tbody");
    const tbodyArquivados = document.querySelector("#tabela-ocultos tbody");

    tbodyTarefas.innerHTML = "";
    tbodyArquivados.innerHTML = "";

    const ativas = tarefas.filter(t => t.arquivado === 0);
    const arquivadas = tarefas.filter(t => t.arquivado === 1);

    if (ativas.length === 0) {
        tbodyTarefas.innerHTML = `
            <tr class="mensagem-tr">
                <td colspan="6" class="mensagem-central">
                    Você ainda não possui nenhuma tarefa.
                </td>
            </tr>`;
    }

    if (arquivadas.length === 0) {
        tbodyArquivados.innerHTML = `
            <tr class="mensagem-tr">
                <td colspan="6" class="mensagem-central">
                    Suas tarefas arquivadas estão vazias.
                </td>
            </tr>`;
    }

    ativas.forEach(t => tbodyTarefas.appendChild(criarLinha(t)));
    arquivadas.forEach(t => tbodyArquivados.appendChild(criarLinha(t)));
    
    setTimeout(filtrarTabela, 50);
}

function criarLinha(t) {
    const tr = document.createElement("tr");

    tr.dataset.id = t.id;
    tr.dataset.prioridade = t.prioridade;
    tr.dataset.status = t.status;
    tr.dataset.descricao = t.descricao;
    tr.dataset.arquivado = t.arquivado;

    tr.innerHTML = `
        <td>
            <a href="ViewTasks.php?id=${t.id}" class="link-tarefa" title="Ver detalhes completos da tarefa">
                ${t.nomeTarefa}
            </a>
        </td>
        <td>${t.projeto}</td>

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
            <button class="botao-visualizar" title="Ver detalhes rápidos">👁️</button>
            <button class="botao-editar-prioridade" title="Editar Prioridade">📋</button>
            <button class="botao-editar-status" title="Editar Status">📈</button>
            <button class="botao-ocultar">
                ${t.arquivado ? "♻️ " : "📂 "}
            </button>
        </td>
    `;

    aplicarVisual(tr);

    const selectPrioridade = tr.querySelector(".select-prioridade");
    if (selectPrioridade && t.prioridade && t.prioridade !== "Não definido") {
        selectPrioridade.value = t.prioridade;
    }

    const selectStatus = tr.querySelector(".select-status");
    if (selectStatus && t.status && t.status !== "Não definido") {
        selectStatus.value = t.status;
    }

    return tr;
}

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

function iniciarEdicao(linha, campo) {
    if (editandoId && editandoId !== linha.dataset.id) {
        cancelarEdicaoAnterior();
    }
    
    editandoId = linha.dataset.id;
    
    if (campo === 'prioridade') {
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
    
    if (campo === 'status') {
        const statusDisplay = linha.querySelector('.status-display');
        const statusSelect = linha.querySelector('.select-status');
        
        if (statusDisplay && statusSelect) {
            statusDisplay.classList.add('hidden');
            statusSelect.classList.remove('hidden');
            
            const valorAtual = linha.dataset.status;
            if (valorAtual && valorAtual !== "Não definido") {
                statusSelect.value = valorAtual;
            }
            
            statusSelect.focus();
        }
    }
}

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
    
    const endpoint = CONFIG.endpoints[campo];
    
    // Usar URL com cache-busting
    const timestamp = new Date().getTime();
    const url = `${endpoint}?t=${timestamp}`;
    
    fetch(url, {
        method: "POST",
        headers: { 
            "Content-Type": "application/x-www-form-urlencoded",
            "Cache-Control": "no-cache, no-store, must-revalidate",
            "Pragma": "no-cache",
            "Expires": "0"
        },
        body: `id=${id}&${campo}=${encodeURIComponent(valor)}`
    })
    .then(res => {
        console.log(`Resposta ${campo}:`, res.status, res.statusText);
        return res.text();
    })
    .then(res => {
        console.log(`Conteúdo resposta ${campo}:`, res);
        
        if (res.trim() === "ok") {
            atualizarDisplay(display, valor, campo);
            linha.dataset[campo] = valor;
            
            const trOriginal = document.querySelector(`#dados-tarefas tr[data-id="${id}"]`);
            if (trOriginal) {
                trOriginal.dataset[campo] = valor;
            }
            
            atualizarTarefasDoDOM();
            select.classList.add('hidden');
            display.classList.remove('hidden');
            editandoId = null;
            
            console.log(`✓ ${campo} salvo com sucesso:`, valor);
            
            // Atualizar interface
            renderizarTabelas();
        } else {
            throw new Error(`Resposta do servidor: ${res.substring(0, 100)}`);
        }
    })
    .catch((error) => {
        console.error(`✗ Erro ao salvar ${campo}:`, error);
        
        // Mostrar erro específico
        let mensagem = `Erro ao salvar ${campo}`;
        if (error.message.includes("404")) {
            mensagem = `Arquivo não encontrado. Verifique se ${endpoint} existe.`;
        } else if (error.message.includes("Failed to fetch")) {
            mensagem = "Erro de conexão. Verifique a URL.";
        }
        
        alert(`${mensagem}\n\nURL tentada: ${endpoint}`);
        
        select.classList.add('hidden');
        display.classList.remove('hidden');
        editandoId = null;
    });
}

function atualizarDisplay(display, valor, tipo) {
    if (!display) return;
    
    const valorExibido = (!valor || valor === "" || valor === "Não definido") ? "Não definido" : valor;
    display.textContent = valorExibido;
    display.className = `${tipo}-display`;
    
    const classesParaRemover = [];
    for (const className of display.classList) {
        if (className.startsWith(`${tipo}-`) && className !== `${tipo}-display`) {
            classesParaRemover.push(className);
        }
    }
    classesParaRemover.forEach(className => display.classList.remove(className));
    
    if (valorExibido !== "Não definido") {
        if (tipo === 'prioridade') {
            const classePrioridade = valorExibido.toLowerCase();
            display.classList.add(`prioridade-${classePrioridade}`);
        } else if (tipo === 'status') {
            const classeStatus = valorExibido.toLowerCase().replace(' ', '').replace('í', 'i').replace('ç', 'c');
            display.classList.add(`status-${classeStatus}`);
        }
    } else {
        display.classList.add(`${tipo}-indefinido`);
    }
}

// ======================================================
// FUNÇÕES DE ARQUIVAMENTO
// ======================================================

function atualizarArquivado(linha, arquivado) {
    const id = linha.dataset.id;
    const botao = linha.querySelector('.botao-ocultar');
    
    botao.disabled = true;
    botao.textContent = arquivado ? "⏳ Arquiviando..." : "⏳ Restaurando...";
    
    const endpoint = CONFIG.endpoints.arquivar;
    const timestamp = new Date().getTime();
    const url = `${endpoint}?t=${timestamp}`;
    
    fetch(url, {
        method: "POST",
        headers: { 
            "Content-Type": "application/x-www-form-urlencoded",
            "Cache-Control": "no-cache, no-store, must-revalidate"
        },
        body: `id=${id}&arquivado=${arquivado}`
    })
    .then(res => res.text())
    .then(res => {
        console.log(`Resposta arquivamento:`, res);
        
        if (res.trim() === "ok") {
            console.log(`✓ Tarefa ${id} ${arquivado ? 'arquivada' : 'restaurada'} com sucesso`);
            
            const trOriginal = document.querySelector(`#dados-tarefas tr[data-id="${id}"]`);
            if (trOriginal) {
                trOriginal.dataset.arquivado = arquivado;
            }
            
            linha.dataset.arquivado = arquivado;
            
            setTimeout(() => {
                inicializarTarefas();
                renderizarTabelas();
                
                botao.disabled = false;
                botao.textContent = arquivado ? "♻️ Restaurar" : "📂 Arquivar";
            }, 300);
            
        } else {
            throw new Error(`Resposta do servidor: ${res}`);
        }
    })
    .catch((error) => {
        console.error("✗ Erro ao atualizar arquivamento:", error);
        
        alert(`Erro ao arquivar/restaurar tarefa.\nURL: ${endpoint}\nErro: ${error.message}`);
        
        botao.disabled = false;
        botao.textContent = arquivado ? "📂 Arquivar" : "♻️ Restaurar";
    });
}

// ======================================================
// FUNÇÕES DE MODAL
// ======================================================

function abrirModal(linha) {
    document.getElementById("detalhe-nome-tarefa").textContent = linha.cells[0].innerText;
    document.getElementById("detalhe-projeto").textContent = linha.cells[1].innerText;
    document.getElementById("detalhe-prazo").textContent = linha.cells[4].innerText;
    
    const prioridadeDisplay = linha.querySelector('.prioridade-display');
    document.getElementById("detalhe-prioridade").textContent = prioridadeDisplay ? prioridadeDisplay.textContent : "Não definido";
    
    const statusDisplay = linha.querySelector('.status-display');
    document.getElementById("detalhe-status").textContent = statusDisplay ? statusDisplay.textContent : "Não definido";
    
    const descricaoElem = document.getElementById("detalhe-descricao");
    descricaoElem.textContent = linha.dataset.descricao || "Sem descrição";

    document.getElementById("modalDetalhes").style.display = "block";
}

// ======================================================
// FUNÇÕES DE FILTRO
// ======================================================

function filtrarTabela() {
    const linhas = document.querySelectorAll("#tabela-tarefas tbody tr");
    let linhasVisiveis = 0;
    
    linhas.forEach(linha => {
        if (linha.classList.contains("mensagem-tr")) {
            return;
        }
        
        const statusDisplay = linha.querySelector('.status-display');
        const prioridadeDisplay = linha.querySelector('.prioridade-display');
        
        const status = statusDisplay ? statusDisplay.textContent : "Não definido";
        const prioridade = prioridadeDisplay ? prioridadeDisplay.textContent : "Não definido";
        
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

function atualizarMensagensFiltro(linhasVisiveis) {
    const tbodyTarefas = document.querySelector("#tabela-tarefas tbody");
    const filtroStatus = document.getElementById("filtro-status").value;
    const filtroPrioridade = document.getElementById("filtro-prioridade").value;
    
    if ((filtroStatus || filtroPrioridade) && linhasVisiveis === 0) {
        const mensagemTr = tbodyTarefas.querySelector(".mensagem-tr");
        if (!mensagemTr) {
            const tr = document.createElement("tr");
            tr.className = "mensagem-tr";
            tr.innerHTML = `
                <td colspan="6" class="mensagem-central">
                    Nenhuma tarefa encontrada com os filtros selecionados.
                </td>
            `;
            tbodyTarefas.appendChild(tr);
        }
    } else {
        const mensagemFiltro = tbodyTarefas.querySelector(".mensagem-tr");
        if (mensagemFiltro && !mensagemFiltro.querySelector(".mensagem-central")?.textContent.includes("nenhuma tarefa")) {
            mensagemFiltro.remove();
        }
    }
}

function atualizarMensagens() {
    const ativas = document.querySelectorAll("#tabela-tarefas tbody tr:not(.mensagem-tr)").length;
    const arquivadas = document.querySelectorAll("#tabela-ocultos tbody tr:not(.mensagem-tr)").length;
    
    if (ativas > 0) {
        const msgAtivas = document.querySelector("#tabela-tarefas .mensagem-tr");
        if (msgAtivas) msgAtivas.remove();
    }
    
    if (arquivadas > 0) {
        const msgArquivadas = document.querySelector("#tabela-ocultos .mensagem-tr");
        if (msgArquivadas) msgArquivadas.remove();
    }
}

// ======================================================
// EVENT LISTENERS E INICIALIZAÇÃO
// ======================================================

document.addEventListener("DOMContentLoaded", () => {
    console.log("DOM completamente carregado");
    
    
    
    // Inicializar após 1 segundo (para dar tempo de diagnosticar)
    setTimeout(() => {
        inicializarTarefas();
        renderizarTabelas();
        
        console.log("Endpoints configurados:", CONFIG.endpoints);
    }, 1000);
    
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
            const estaNaTabelaPrincipal = linha.closest("#tabela-tarefas");
            
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
                ? "📂 Ver Tarefas Arquivadas"
                : "🔙 Ocultar Arquivadas";
            
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
    
    // Log final
    setTimeout(() => {
        console.log("=== CONFIGURAÇÃO FINAL ===");
        console.log("Endpoints:", CONFIG.endpoints);
        console.log("Total tarefas:", tarefas.length);
        console.log("=== SISTEMA PRONTO ===");
    }, 2000);
});

// ======================================================
// EVENT LISTENER PARA TECLA ESC
// ======================================================
document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
        cancelarEdicaoAnterior();
    }
});