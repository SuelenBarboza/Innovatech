<?php
include("../config/conexao.php");

$sql = "SELECT * FROM projetos ORDER BY prazo ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="../Assets/css/Header.css">
  <link rel="stylesheet" href="../Assets/css/Footer.css">
  <link rel="stylesheet" href="../Assets/css/ViewListProject.css">
  <title>Lista de Projetos</title>
  
</head>
<body>

   <?php include("../Includes/Header.php"); ?>

  <h1>Lista de Projetos</h1>

  <table id="tabela-projetos">
    <thead>
      <tr>
        <th>Nome</th>
        <th>Categoria</th>
        <th>Prioridade</th>
        <th>Status</th>
        <th>Prazo</th>
        <th>Progresso</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <tr data-descricao="Sistema acadêmico completo com controle de disciplinas, notas e calendário.">
        <td><a href="ViewListProject.html?nome=Portal%20Acadêmico" class="link-projeto">Portal Acadêmico</a></td>
        <td>Sistema</td>
        <td><span class="prioridade alta">Alta</span></td>
        <td><span class="status andamento">Em andamento</span></td>
        <td>30/06/2025</td>
        <td><progress value="60" max="100"></progress> 60%</td>
        <td>
          <button class="botao-visualizar">👁️</button>
          <button class="botao-editar">✏️</button>
          <button class="botao-ocultar">📂</button>

        </td>
      </tr>
      
      <tr data-descricao="Loja virtual com carrinho de compras, login e painel administrativo.">
        <td><a href="ViewListProject.html?nome=Loja%20Online" class="link-projeto">Loja Online</a></td>
        <td>Site</td>
        <td><span class="prioridade media">Média</span></td>
        <td><span class="status concluido">Concluído</span></td>
        <td>10/04/2025</td>
        <td><progress value="100" max="100"></progress> 100%</td>
        <td>
          <button class="botao-visualizar">👁️</button>
          <button class="botao-editar">✏️</button>
          <button class="botao-ocultar">📂</button>
        </td>
      </tr>
      <tr data-descricao="Aplicativo para gerenciamento de tarefas diárias com notificações.">
        <td><a href="ViewListProject.html?nome=App%20de%20Tarefas" class="link-projeto">App de Tarefas</a></td>
        <td>Aplicativo</td>
        <td><span class="prioridade baixa">Baixa</span></td>
        <td><span class="status planejamento">Início</span></td>
        <td>15/08/2025</td>
        <td><progress value="10" max="100"></progress> 10%</td>
        <td>
          <button class="botao-visualizar">👁️</button>
          <button class="botao-editar">✏️</button>
          <button class="botao-ocultar">📂</button>
        </td>
      </tr>
      <tr id="linha-sem-projetos" style="display: none;">
        <td colspan="7" class="mensagem-central">Você ainda não possui nenhum projeto cadastrado.</td>
      </tr>
    </tbody>
  </table>

  <button id="btnToggleArquivar">📂 Ver Projetos Arquivados</button>



  <div id="containerArquivar" style="display: none;">
    <table id="tabela-ocultos">
      <thead>
        <tr>
          <th>Nome</th>
          <th>Categoria</th>
          <th>Prioridade</th>
          <th>Status</th>
          <th>Prazo</th>
          <th>Progresso</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <tr id="linha-arquivar-vazia" style="display: none;">
          <td colspan="7" class="mensagem-central">Seus arquivos ocultos está vazio.</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div id="modalDetalhes" class="modal">
    <div class="modal-conteudo">
      <span class="fechar">&times;</span>
      <h2>Detalhes do Projeto</h2>
      <p><strong>Nome:</strong> <span id="detalhe-nome"></span></p>
      <p><strong>Categoria:</strong> <span id="detalhe-categoria"></span></p>
      <p><strong>Prioridade:</strong> <span id="detalhe-prioridade"></span></p>
      <p><strong>Status:</strong> <span id="detalhe-status"></span></p>
      <p><strong>Prazo:</strong> <span id="detalhe-prazo"></span></p>
      <p><strong>Progresso:</strong> <span id="detalhe-progresso"></span></p>
      <p><strong>Descrição:</strong> <span id="detalhe-descricao"></span></p>
    </div>
  </div>

  <?php include("../Includes/Footer.php"); ?>

  <script src="../Assets/js/ViewListProject.js"></script>
  

</body>
</html>
