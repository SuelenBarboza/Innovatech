console.log("JS carregado!");

document.addEventListener("DOMContentLoaded", () => {
  const tabelaProjetos = document.querySelector("#tabela-projetos tbody");
  const tabelaArquivados = document.querySelector("#tabela-ocultos tbody");
  const toggleBtn = document.getElementById("btnToggleArquivar");
  const containerArquivados = document.getElementById("containerArquivar");
  const modal = document.getElementById("modalDetalhes");
  const spanFechar = document.querySelector(".fechar");
  const linhaSemProjetos = document.getElementById("linha-sem-projetos");
  const linhaArquivadosVazia = document.getElementById("linha-arquivar-vazia");

  // Mostrar/Ocultar projetos arquivados
  toggleBtn.addEventListener("click", () => {
    const estaOculta = containerArquivados.style.display === "none" || containerArquivados.style.display === "";
    containerArquivados.style.display = estaOculta ? "block" : "none";
    toggleBtn.textContent = estaOculta ? "🔙 Ocultar Arquivados" : "📂 Ver Projetos Arquivados";
  });

  // Editar prioridade
  tabelaProjetos.addEventListener("click", (e) => {
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

  // Arquivar projeto
  tabelaProjetos.addEventListener("click", (e) => {
    if (e.target.textContent === "📂") {
      const linha = e.target.closest("tr");
      const novaLinha = linha.cloneNode(true);

      novaLinha.querySelectorAll("button").forEach(btn => {
        if (btn.textContent === "📂") btn.textContent = "♻️";
      });

      tabelaArquivados.appendChild(novaLinha);
      linha.remove();
      atualizarMensagemSemProjetos();
    }
  });

  // Restaurar projeto
  tabelaArquivados.addEventListener("click", (e) => {
    if (e.target.textContent === "♻️") {
      const linha = e.target.closest("tr");
      const novaLinha = linha.cloneNode(true);

      novaLinha.querySelectorAll("button").forEach(btn => {
        if (btn.textContent === "♻️") btn.textContent = "📂";
      });

      tabelaProjetos.appendChild(novaLinha);
      linha.remove();
      atualizarMensagemSemProjetos();
    }
  });

  // Modal de detalhes do projeto
  tabelaProjetos.addEventListener("click", (e) => {
    if (e.target.textContent === "👁️") {
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

  // Fechar modal (por botão, clique fora ou ESC)
  spanFechar.onclick = () => {
    modal.style.display = "none";
  };

  window.onclick = (event) => {
    if (event.target === modal) {
      modal.style.display = "none";
    }
  };

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      modal.style.display = "none";
    }
  });

  // Mensagem: sem projetos
  function atualizarMensagemSemProjetos() {
    const linhas = tabelaProjetos.querySelectorAll("tr");
    const linhasValidas = Array.from(linhas).filter(l =>
      !l.querySelector("th") && l.id !== "linha-sem-projetos"
    );
    linhaSemProjetos.style.display = linhasValidas.length === 0 ? "" : "none";
    atualizarMensagemArquivadosVazia();
  }

  // Mensagem: lixeira vazia
  function atualizarMensagemArquivadosVazia() {
    const linhas = tabelaArquivados.querySelectorAll("tr");
    const linhasValidas = Array.from(linhas).filter(l =>
      !l.querySelector("th") && l.id !== "linha-arquivar-vazia"
    );
    linhaArquivadosVazia.style.display = linhasValidas.length === 0 ? "" : "none";
  }

  // Inicialização
  atualizarMensagemSemProjetos();
  atualizarMensagemArquivadosVazia();

  // Transformar nome do projeto em link para ViewProject.html
  const linhas = document.querySelectorAll("#tabela-projetos tbody tr");

  linhas.forEach((linha) => {
    const nomeTd = linha.querySelector("td:first-child");
    const nomeProjeto = nomeTd.textContent.trim();

    const link = document.createElement("a");
    link.href = `ViewProject.html?nome=${encodeURIComponent(nomeProjeto)}`;
    link.textContent = nomeProjeto;
    link.classList.add("link-projeto");

    nomeTd.innerHTML = "";
    nomeTd.appendChild(link);
  });
});
