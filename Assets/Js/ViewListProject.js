console.log("JS carregado!");

// =====================
// CONFIGURAÇÕES
// =====================
const CONFIG = {
    endpoints: {
        prioridade: "../Config/UpdatePrioridade.php"
    },
    selectors: {
        tabelaProjetos: "#tabela-projetos tbody",
        tabelaArquivados: "#tabela-ocultos tbody",
        toggleBtn: "#btnToggleArquivar",
        containerArquivados: "#containerArquivar",
        modal: "#modalDetalhes",
        fecharModal: ".fechar",
        linhaSemProjetos: "#linha-sem-projetos",
        linhaArquivadosVazia: "#linha-arquivar-vazia"
    }
};

// =====================
// UTILITÁRIOS
// =====================
const Utils = {
    mostrarAlerta(msg) {
        alert(msg);
    }
};

// =====================
// PRIORIDADE (SALVA SÓ COM ✏️ + SALVAR)
// =====================
const PrioridadeManager = {

    iniciarEdicao(linha) {
        if (linha.classList.contains("editando")) return;

        linha.classList.add("editando");

        const select = linha.querySelector(".select-prioridade");

        const btnSalvar = document.createElement("button");
        btnSalvar.textContent = "💾 Salvar";
        btnSalvar.classList.add("btn-salvar");

        select.after(btnSalvar);

        btnSalvar.addEventListener("click", () => {
            this.salvarPrioridade(linha, select.value, btnSalvar);
        });
    },

    salvarPrioridade(linha, prioridade, btnSalvar) {
        const select = linha.querySelector(".select-prioridade");
        const id = select.dataset.id;

        fetch(CONFIG.endpoints.prioridade, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `id=${id}&prioridade=${encodeURIComponent(prioridade)}`
        })
        .then(res => res.text())
        .then(res => {
            if (res === "ok") {
                // Atualiza dataset para o modal
                linha.dataset.prioridade = prioridade;

                linha.classList.remove("editando");
                btnSalvar.remove();
            } else {
                Utils.mostrarAlerta("Erro ao salvar prioridade");
            }
        })
        .catch(() => Utils.mostrarAlerta("Erro de conexão com o servidor"));
    }
};

// =====================
// PROJETOS
// =====================
const ProjetoManager = {
    tabelaProjetos: null,
    tabelaArquivados: null,

    inicializar(tabelaProjetos, tabelaArquivados) {
        this.tabelaProjetos = tabelaProjetos;
        this.tabelaArquivados = tabelaArquivados;
        this.configurarEventos();
    },

    configurarEventos() {

        // ✏️ Editar prioridade
        this.tabelaProjetos.addEventListener("click", e => {
            if (e.target.classList.contains("botao-editar")) {
                const linha = e.target.closest("tr");
                PrioridadeManager.iniciarEdicao(linha);
            }
        });

        // 👁️ Modal detalhes
        this.tabelaProjetos.addEventListener("click", e => {
            if (e.target.classList.contains("botao-visualizar")) {
                this.abrirModal(e.target.closest("tr"));
            }
        });

        // 📂 Arquivar
        this.tabelaProjetos.addEventListener("click", e => {
            if (e.target.classList.contains("botao-ocultar")) {
                this.arquivarProjeto(e.target.closest("tr"));
            }
        });

        // ♻️ Restaurar
        this.tabelaArquivados.addEventListener("click", e => {
            if (e.target.textContent === "♻️") {
                this.restaurarProjeto(e.target.closest("tr"));
            }
        });
    },

    arquivarProjeto(linha) {
        const clone = linha.cloneNode(true);
        clone.querySelector(".botao-ocultar").textContent = "♻️";
        this.tabelaArquivados.appendChild(clone);
        linha.remove();
        this.atualizarMensagens();
    },

    restaurarProjeto(linha) {
        const clone = linha.cloneNode(true);
        clone.querySelector(".botao-ocultar").textContent = "📂";
        this.tabelaProjetos.appendChild(clone);
        linha.remove();
        this.atualizarMensagens();
    },

    abrirModal(linha) {
        document.getElementById("detalhe-nome").textContent = linha.cells[0].innerText;
        document.getElementById("detalhe-categoria").textContent = linha.cells[1].innerText;
        document.getElementById("detalhe-status").textContent = linha.cells[3].innerText;
        document.getElementById("detalhe-prazo").textContent = linha.cells[4].innerText;
        document.getElementById("detalhe-progresso").textContent = linha.cells[5].innerText;

        document.getElementById("detalhe-prioridade").textContent =
            linha.dataset.prioridade || "Sem prioridade definida";

        document.getElementById("detalhe-descricao").textContent =
            linha.dataset.descricao || "Sem descrição";

        document.querySelector(CONFIG.selectors.modal).style.display = "block";
    },

    atualizarMensagens() {
        const semProjetos = document.querySelector(CONFIG.selectors.linhaSemProjetos);
        if (semProjetos) {
            semProjetos.style.display =
                this.tabelaProjetos.children.length === 0 ? "" : "none";
        }

        const vazia = document.querySelector(CONFIG.selectors.linhaArquivadosVazia);
        if (vazia) {
            vazia.style.display =
                this.tabelaArquivados.children.length === 0 ? "" : "none";
        }
    }
};

// =====================
// MODAL
// =====================
const ModalManager = {
    inicializar() {
        const modal = document.querySelector(CONFIG.selectors.modal);
        const fechar = document.querySelector(CONFIG.selectors.fecharModal);

        fechar.onclick = () => modal.style.display = "none";

        window.onclick = e => {
            if (e.target === modal) modal.style.display = "none";
        };
    }
};

// =====================
// ARQUIVADOS
// =====================
const ArquivadosManager = {
    inicializar() {
        const btn = document.querySelector(CONFIG.selectors.toggleBtn);
        const container = document.querySelector(CONFIG.selectors.containerArquivados);

        btn.addEventListener("click", () => {
            const aberto = container.style.display === "block";
            container.style.display = aberto ? "none" : "block";
            btn.textContent = aberto
                ? "📂 Ver Projetos Arquivados"
                : "🔙 Ocultar Arquivados";
        });
    }
};

// =====================
// INIT
// =====================
document.addEventListener("DOMContentLoaded", () => {
    ModalManager.inicializar();
    ArquivadosManager.inicializar();

    const tabelaProjetos = document.querySelector(CONFIG.selectors.tabelaProjetos);
    const tabelaArquivados = document.querySelector(CONFIG.selectors.tabelaArquivados);

    if (tabelaProjetos && tabelaArquivados) {
        ProjetoManager.inicializar(tabelaProjetos, tabelaArquivados);
        ProjetoManager.atualizarMensagens();
    }
});
