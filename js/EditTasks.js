// Carregamento para editar as tarefas (MODAL)
let acaoConfirmada = '';
    const modal = document.getElementById("modalConfirmacao");
    const mensagem = document.getElementById("mensagemModal");
    const confirmarBtn = document.getElementById("confirmarAcao");

    function abrirModal(texto, acao) {
      mensagem.innerText = texto;
      acaoConfirmada = acao;
      modal.classList.add("ativo");
    }

    function fecharModal() {
      modal.classList.remove("ativo");
      acaoConfirmada = '';
    }

    document.getElementById("salvar").addEventListener("click", function (e) {
      e.preventDefault();
      abrirModal("Deseja realmente salvar as alterações da tarefa?", "salvar");
    });

    document.getElementById("cancelar").addEventListener("click", function () {
      abrirModal("Deseja cancelar a edição da tarefa?", "cancelar");
    });

    confirmarBtn.addEventListener("click", function () {
      if (acaoConfirmada === "salvar") {
        alert("Salvando tarefa...");
      } else if (acaoConfirmada === "cancelar") {
        alert("Cancelando edição...");
        window.location.href = ""; 
      }
      fecharModal();
    });

    window.addEventListener("click", function (event) {
      if (event.target === modal) {
        fecharModal();
      }
    });