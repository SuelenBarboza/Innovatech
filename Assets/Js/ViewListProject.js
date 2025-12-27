console.log("JS carregado!");

// ======================================================
// CONFIG
// ======================================================
const CONFIG = {
    endpoints: {
        prioridade: "../Config/UpdatePrioridade.php",
        status: "../Config/UpdateStatus.php"
    }
};

// ======================================================
// EDIÇÃO (PRIORIDADE E STATUS)
// ======================================================
let editandoId = null; // Guarda qual linha está editando

function iniciarEdicao(linha) {
    // Se já está editando, cancela a edição anterior
    if (editandoId && editandoId !== linha.dataset.id) {
        cancelarEdicaoAnterior();
    }
    
    editandoId = linha.dataset.id;
    
    // Esconde todos os displays e mostra todos os selects desta linha
    const displays = linha.querySelectorAll('.prioridade-display, .status-display');
    const selects = linha.querySelectorAll('.select-prioridade, .select-status');
    
    displays.forEach(display => display.classList.add('hidden'));
    selects.forEach(select => {
        select.classList.remove('hidden');
        select.focus(); // Foca no último select
    });
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
    const campo = select.dataset.field; // 'prioridade' ou 'status'
    const valor = select.value;
    const id = select.dataset.id;
    const linha = select.closest('tr');
    const display = linha.querySelector(`.${campo}-display`);
    
    // Se não selecionou nada, volta para o display
    if (valor === "") {
        select.classList.add('hidden');
        display.classList.remove('hidden');
        editandoId = null;
        return;
    }
    
    // Determina endpoint correto
    const endpoint = campo === 'prioridade' ? CONFIG.endpoints.prioridade : CONFIG.endpoints.status;
    const paramName = campo; // 'prioridade' ou 'status'
    
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
            
            // Atualiza dataset
            linha.dataset[campo] = valor;
            
            // Esconde select e mostra display atualizado
            select.classList.add('hidden');
            display.classList.remove('hidden');
            
            editandoId = null;
            console.log(`${campo} salvo com sucesso:`, valor);
        } else {
            alert(`Erro ao salvar ${campo}`);
            // Reverte para o display original
            select.classList.add('hidden');
            display.classList.remove('hidden');
            editandoId = null;
        }
    })
    .catch(() => {
        alert("Erro de conexão com o servidor");
        // Reverte para o display original
        select.classList.add('hidden');
        display.classList.remove('hidden');
        editandoId = null;
    });
}

function atualizarDisplay(display, valor, tipo) {
    if (!display) return;
    
    display.textContent = valor || (tipo === 'prioridade' ? "Não definida" : "Indefinido");
    display.className = `${tipo}-display`;
    
    if (valor) {
        if (tipo === 'prioridade') {
            display.classList.add(`prioridade-${valor.toLowerCase()}`);
        } else if (tipo === 'status') {
            const classeStatus = valor.toLowerCase().replace(' ', '').replace('í', 'i');
            display.classList.add(`status-${classeStatus}`);
        }
    } else {
        // Se não tem valor, aplica classe indefinida
        display.classList.add(`${tipo}-indefinid${tipo === 'prioridade' ? 'a' : 'o'}`);
    }
}

// ======================================================
// MODAL
// ======================================================
function abrirModal(linha) {
    document.getElementById("detalhe-nome").textContent = linha.cells[0].innerText;
    document.getElementById("detalhe-categoria").textContent = linha.cells[1].innerText;
    document.getElementById("detalhe-prazo").textContent = linha.cells[4].innerText;
    
    // Usa dataset atualizado
    document.getElementById("detalhe-prioridade").textContent = 
        linha.dataset.prioridade || "Não definida";
    
    document.getElementById("detalhe-status").textContent = 
        linha.dataset.status || "Não definido";
    
    document.getElementById("detalhe-descricao").textContent = 
        linha.dataset.descricao || "Sem descrição";

    document.getElementById("modalDetalhes").style.display = "block";
}

// ======================================================
// ARQUIVAR / RESTAURAR
// ======================================================
function atualizarMensagens() {
    const tabelaProjetos = document.querySelector("#tabela-projetos tbody");
    const tabelaArquivados = document.querySelector("#tabela-ocultos tbody");

    const linhaSemProjetos = document.getElementById("linha-sem-projetos");
    const linhaArquivadosVazia = document.getElementById("linha-arquivar-vazia");

    if (linhaSemProjetos) {
        linhaSemProjetos.style.display = tabelaProjetos.children.length ? "none" : "";
    }

    if (linhaArquivadosVazia) {
        linhaArquivadosVazia.style.display = tabelaArquivados.children.length > 1 ? "none" : "";
    }
}

// ======================================================
// EVENTOS
// ======================================================
document.addEventListener("DOMContentLoaded", () => {
    const tabelaProjetos = document.querySelector("#tabela-projetos tbody");
    const tabelaArquivados = document.querySelector("#tabela-ocultos tbody");

    // Clique geral
    document.addEventListener("click", e => {
        // ✏️ INICIAR EDIÇÃO (PRIORIDADE E STATUS)
        const btnEditar = e.target.closest(".botao-editar");
        if (btnEditar) {
            e.preventDefault();
            e.stopPropagation();
            iniciarEdicao(btnEditar.closest("tr"));
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

            if (linha.closest("#tabela-projetos")) {
                const clone = linha.cloneNode(true);
                clone.querySelector(".botao-ocultar").textContent = "♻️";
                tabelaArquivados.appendChild(clone);
                linha.remove();
            } else {
                const clone = linha.cloneNode(true);
                clone.querySelector(".botao-ocultar").textContent = "📂";
                tabelaProjetos.appendChild(clone);
                linha.remove();
            }

            atualizarMensagens();
            return;
        }
    });

    // CHANGE DO SELECT (SALVAR AUTOMATICAMENTE)
    document.addEventListener("change", e => {
        const select = e.target.closest(".select-prioridade, .select-status");
        if (select) {
            e.preventDefault();
            e.stopPropagation();
            salvarCampo(select);
        }
    });

    // FECHAR MODAL
    const modal = document.getElementById("modalDetalhes");
    const fechar = document.querySelector(".fechar");

    fechar?.addEventListener("click", () => modal.style.display = "none");
    window.addEventListener("click", e => {
        if (e.target === modal) modal.style.display = "none";
    });

    // TOGGLE ARQUIVADOS
    const btnToggle = document.getElementById("btnToggleArquivar");
    const containerArquivar = document.getElementById("containerArquivar");

    btnToggle?.addEventListener("click", () => {
        const aberto = containerArquivar.style.display === "block";
        containerArquivar.style.display = aberto ? "none" : "block";
        btnToggle.textContent = aberto
            ? "📂 Ver Projetos Arquivados"
            : "🔙 Ocultar Arquivados";
    });

    atualizarMensagens();


    const filtroStatus = document.getElementById("filtro-status");
    const filtroPrioridade = document.getElementById("filtro-prioridade");

    function filtrarTabela() {
    const linhas = document.querySelectorAll("#tabela-projetos tbody tr");

    linhas.forEach(linha => {
        const status = linha.dataset.status;
        const prioridade = linha.dataset.prioridade;

        const statusOK = !filtroStatus.value || filtroStatus.value === status;
        const prioridadeOK = !filtroPrioridade.value || filtroPrioridade.value === prioridade;

        linha.style.display = (statusOK && prioridadeOK) ? "" : "none";
    });
    }

    filtroStatus.addEventListener("change", filtrarTabela);
    filtroPrioridade.addEventListener("change", filtrarTabela);

});