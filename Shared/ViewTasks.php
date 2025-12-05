<?php
include("../config/conexao.php");

$sql = "SELECT * FROM tarefas";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Detalhes da Tarefa</title>
  <link rel="stylesheet" href="../Assets/css/Header.css">
  <link rel="stylesheet" href="../Assets/css/Footer.css">
  <link rel="stylesheet" href="../Assets/css/ViewProject.css"> 
</head>
<body>

  <div id="header"></div>

  <section class="form-container">
    <h2>Detalhes da Tarefa</h2>
    <div id="dados-tarefa">
      <!-- Conteúdo inserido via JavaScript -->
    </div>
    <a href="ViewListTasks.html" class="btn-voltar">⬅️ Voltar para a Lista de Tarefas</a>
  </section>

  <div id="footer"></div>

  <script src="../Includes/js/Header.js"></script>
  <script src="../Includes/js/Footer.js"></script>
  <script>
    const tarefas = [
      {
        nome: "Documentação Técnica",
        categoria: "Documentação",
        prioridade: "Alta",
        status: "Em andamento",
        prazo: "15/04/2025",
        progresso: "50%",
        descricao: "Finalizar a documentação técnica do sistema."
      },
      {
        nome: "Layout Login",
        categoria: "Front-end",
        prioridade: "Média",
        status: "Concluído",
        prazo: "05/04/2025",
        progresso: "100%",
        descricao: "Criar layout responsivo para a tela de login."
      },
      {
        nome: "Upload de Arquivos",
        categoria: "Back-end",
        prioridade: "Baixa",
        status: "Início",
        prazo: "20/04/2025",
        progresso: "10%",
        descricao: "Implementar funcionalidade de upload de arquivos."
      }
    ];

    function obterNomeTarefa() {
      const urlParams = new URLSearchParams(window.location.search);
      return urlParams.get('nome');
    }

    function exibirDadosTarefa(tarefa) {
      const container = document.getElementById("dados-tarefa");

      if (!tarefa) {
        container.innerHTML = "<p>Tarefa não encontrada.</p>";
        return;
      }

      container.innerHTML = `
        <p><strong>Nome:</strong> ${tarefa.nome}</p>
        <p><strong>Categoria:</strong> ${tarefa.categoria}</p>
        <p><strong>Prioridade:</strong> ${tarefa.prioridade}</p>
        <p><strong>Status:</strong> ${tarefa.status}</p>
        <p><strong>Prazo:</strong> ${tarefa.prazo}</p>
        <p><strong>Progresso:</strong> ${tarefa.progresso}</p>
        <p><strong>Descrição:</strong> ${tarefa.descricao}</p>
      `;
    }

    document.addEventListener("DOMContentLoaded", () => {
      const nomeTarefa = decodeURIComponent(obterNomeTarefa());
      const tarefaEncontrada = tarefas.find(t => t.nome === nomeTarefa);
      exibirDadosTarefa(tarefaEncontrada);
    });
  </script>

</body>
</html>
