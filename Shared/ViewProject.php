<?php
include("../config/conexao.php");

$sql = "SELECT * FROM projetos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Detalhes do Projeto</title>
  <link rel="stylesheet" href="../Assets/css/Header.css">
  <link rel="stylesheet" href="../Assets/css/Footer.css">
  <link rel="stylesheet" href="../Assets/css/ViewProject.css"> 
</head>
<body>

  <?php include("../Includes/Header.php"); ?>

  <section class="form-container">
    <h2>Detalhes do Projeto</h2>
    <div id="dados-projeto">
    </div>
    <a href="ViewListProject.php" class="btn-voltar">⬅️ Voltar para a Lista</a>


</section>

   <?php include("../Includes/Footer.php"); ?>

  <script>
    // Lista simulada de projetos
    const projetos = [
      {
        nome: "Portal Acadêmico",
        data_inicio: "2024-01-10",
        data_fim: "2025-06-30",
        alunos: ["João Silva", "Maria Souza"],
        professores: ["Prof. Ana Lima", "Prof. Carlos Mendes"],
        descricao: "Sistema acadêmico completo com controle de disciplinas, notas e calendário."
      },
      {
        nome: "Loja Online",
        data_inicio: "2024-03-01",
        data_fim: "2025-04-10",
        alunos: ["Lucas Rocha"],
        professores: ["Prof. Fernanda Tavares"],
        descricao: "Loja virtual com carrinho de compras, login e painel administrativo."
      },
      {
        nome: "App de Tarefas",
        data_inicio: "2025-01-01",
        data_fim: "2025-08-15",
        alunos: ["Carla Dias", "Igor Santos"],
        professores: ["Prof. Roberto Maia"],
        descricao: "Aplicativo para gerenciamento de tarefas diárias com notificações."
      }
    ];

    // Função para pegar o nome da URL
    function obterNomeProjeto() {
      const urlParams = new URLSearchParams(window.location.search);
      return urlParams.get('nome');
    }

    function exibirDadosProjeto(projeto) {
      const container = document.getElementById("dados-projeto");

      if (!projeto) {
        container.innerHTML = "<p>Projeto não encontrado.</p>";
        return;
      }

      container.innerHTML = `
        <p><strong>Nome:</strong> ${projeto.nome}</p>
        <p><strong>Data de Início:</strong> ${projeto.data_inicio}</p>
        <p><strong>Data de Conclusão:</strong> ${projeto.data_fim}</p>
        <p><strong>Alunos:</strong> ${projeto.alunos.join(", ")}</p>
        <p><strong>Professores:</strong> ${projeto.professores.join(", ")}</p>
        <p><strong>Descrição:</strong> ${projeto.descricao}</p>
      `;
    }

    document.addEventListener("DOMContentLoaded", () => {
      const nomeProjeto = decodeURIComponent(obterNomeProjeto());
      const projetoEncontrado = projetos.find(p => p.nome === nomeProjeto);
      exibirDadosProjeto(projetoEncontrado);
    });
  </script>

</body>
</html>
