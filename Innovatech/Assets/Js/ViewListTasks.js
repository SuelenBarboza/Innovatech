console.log("JS de tarefas carregado!");

document.addEventListener("DOMContentLoaded", () => {
  const tabelaTarefas = document.querySelector("#tabela-tarefas tbody");
  const tabelaArquivados = document.querySelector("#tabela-ocultos tbody");
  const toggleBtn = document.getElementById("btnToggleArquivar");
  const containerArquivar = document.getElementById("containerArquivar");
  const modal = document.getElementById("modalDetalhes");
  const spanFechar = document.querySelector(".fechar");
  const linhaSemTarefas = document.getElementById("linha-sem-tarefas");
  const linhaArquivarVazia = document.getElementById("linha-arquivar-vazia");

  // Alternar exibição da lixeira
  toggleBtn.addEventListener("click", () => {
    const estaOculta = containerArquivar.style.display === "none" || containerArquivar.style.display === "";
    containerArquivar.style.display = estaOculta ? "block" : "none";
    toggleBtn.textContent = estaOculta ? "🔙 Ocultar Arquivados" : "📂 Ver Projetos Arquivados";
  });

  // Edição de prioridade
  tabelaTarefas.addEventListener("click", (e) => {
    if (e.target.classList.contains("botao-editar")) {
      const linha = e.target.closest("tr");
      const celulaPrioridade = linha.querySelector(".prioridade");

      const select = document.createElement("select");
      select.innerHTML = `
        <option value="alta">Alta</option>
        <option value="media">Média</option>
        <option value="baixa">Baixa</option>
      `;

      const valorAtual = celulaPrioridade.textContent.trim().toLowerCase();
      select.value = valorAtual;

      celulaPrioridade.innerHTML = "";
      celulaPrioridade.appendChild(select);

      const btnSalvar = document.createElement("button");
      btnSalvar.textContent = "Salvar";
      btnSalvar.classList.add("salvar");
      celulaPrioridade.appendChild(btnSalvar);

      btnSalvar.addEventListener("click", () => {
        const novaPrioridade = select.value;
        celulaPrioridade.className = "prioridade";
        celulaPrioridade.textContent = novaPrioridade.charAt(0).toUpperCase() + novaPrioridade.slice(1);
        celulaPrioridade.classList.add(novaPrioridade);
      });
    }
  });

  // Arquivar tarefas (📂)
  tabelaTarefas.addEventListener("click", (e) => {
    if (e.target.classList.contains("botao-ocultar")) {
      const linha = e.target.closest("tr");
      const novaLinha = linha.cloneNode(true);

      novaLinha.querySelectorAll("button").forEach(btn => {
        if (btn.classList.contains("botao-ocultar")) {
          btn.textContent = "♻️";
          btn.classList.remove("botao-ocultar");
          btn.classList.add("botao-restaurar");
        }
      });

      tabelaArquivados.appendChild(novaLinha);
      linha.remove();
      atualizarMensagens();
    }
  });

  // Restaurar tarefas (♻️)
  tabelaArquivados.addEventListener("click", (e) => {
    if (e.target.classList.contains("botao-restaurar")) {
      const linha = e.target.closest("tr");
      const novaLinha = linha.cloneNode(true);

      novaLinha.querySelectorAll("button").forEach(btn => {
        if (btn.classList.contains("botao-restaurar")) {
          btn.textContent = "📂";
          btn.classList.remove("botao-restaurar");
          btn.classList.add("botao-ocultar");
        }
      });

      tabelaTarefas.appendChild(novaLinha);
      linha.remove();
      atualizarMensagens();
    }
  });

  // Modal de detalhes
  tabelaTarefas.addEventListener("click", (e) => {
    if (e.target.classList.contains("botao-visualizar")) {
      const linha = e.target.closest("tr");
      document.getElementById("detalhe-nome").textContent = linha.cells[0].textContent;
      document.getElementById("detalhe-categoria").textContent = linha.cells[1].textContent;
      document.getElementById("detalhe-prioridade").textContent = linha.cells[2].textContent;
      document.getElementById("detalhe-status").textContent = linha.cells[3].textContent;
      document.getElementById("detalhe-prazo").textContent = linha.cells[4].textContent;
      document.getElementById("detalhe-progresso").textContent = linha.cells[5].textContent;
      document.getElementById("detalhe-descricao").textContent = linha.getAttribute("data-descricao") || "Sem descrição";
      modal.style.display = "block";
    }
  });

  // Fechar modal
  spanFechar.onclick = () => modal.style.display = "none";
  window.onclick = (event) => {
    if (event.target === modal) modal.style.display = "none";
  };
  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") modal.style.display = "none";
  });

  function atualizarMensagens() {
    const tarefas = Array.from(tabelaTarefas.querySelectorAll("tr")).filter(l => !l.querySelector("th") && l.id !== "linha-sem-tarefas");
    linhaSemTarefas.style.display = tarefas.length === 0 ? "" : "none";

    const arquivados = Array.from(tabelaArquivados.querySelectorAll("tr")).filter(l => !l.querySelector("th") && l.id !== "linha-arquivar-vazia");
    linhaArquivarVazia.style.display = arquivados.length === 0 ? "" : "none";
  }

  atualizarMensagens();
});
