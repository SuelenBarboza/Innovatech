<?php
include("../config/conexao.php");

$sql = "SELECT * FROM tarefas ORDER BY prazo ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="../Assets/css/Header.css">
  <link rel="stylesheet" href="../Assets/css/Footer.css">
  <link rel="stylesheet" href="../Assets/css/ViewListTasks.css">
  <title>Lista de Tarefas</title>
</head>
<body>

  <div id="header"></div>

  <h1>Lista de Tarefas</h1>

  <table id="tabela-tarefas">
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
      <tr data-descricao="Finalizar a documentação técnica do sistema.">
        <td><a href="ViewTasks.php?nome=Documentação%20Técnica" class="link-tarefas">Documentação Técnica</a></td>
        <td>Documentação</td>
        <td><span class="prioridade alta">Alta</span></td>
        <td><span class="status andamento">Em andamento</span></td>
        <td>15/04/2025</td>
        <td><progress value="50" max="100"></progress> 50%</td>
        <td>
          <button class="botao-visualizar">👁️</button>
          <button class="botao-editar">✏️</button>
          <button class="botao-ocultar">📂</button>
        </td>
      </tr>

      <tr data-descricao="Criar layout responsivo para a tela de login.">
        <td><a href="ViewTasks.php?nome=Layout%20Login" class="link-tarefas">Layout Login</a></td>
        <td>Front-end</td>
        <td><span class="prioridade media">Média</span></td>
        <td><span class="status concluido">Concluído</span></td>
        <td>05/04/2025</td>
        <td><progress value="100" max="100"></progress> 100%</td>
        <td>
          <button class="botao-visualizar">👁️</button>
          <button class="botao-editar">✏️</button>
          <button class="botao-ocultar">📂</button>
        </td>
      </tr>

      <tr data-descricao="Implementar funcionalidade de upload de arquivos.">
        <td><a href="ViewTasks.php?nome=Upload%20de%20Arquivos" class="link-tarefas">Upload de Arquivos</a></td>
        <td>Back-end</td>
        <td><span class="prioridade baixa">Baixa</span></td>
        <td><span class="status planejamento">Início</span></td>
        <td>20/04/2025</td>
        <td><progress value="10" max="100"></progress> 10%</td>
        <td>
          <button class="botao-visualizar">👁️</button>
          <button class="botao-editar">✏️</button>
          <button class="botao-ocultar">📂</button>
        </td>
      </tr>

      <tr id="linha-sem-tarefas" style="display: none;">
        <td colspan="7" class="mensagem-central">Você ainda não possui nenhuma tarefa cadastrada.</td>
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
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $progress = $row['progresso']; // percentual
            $prioridadeClass = strtolower($row['prioridade']); // alta, media, baixa
            $statusClass = strtolower($row['status']); // andamento, concluido, planejamento

            echo "<tr data-descricao='".$row['descricao']."'>";
            echo "<td><a href='ViewTasks.php?id=".$row['id']."' class='link-tarefas'>".$row['nome']."</a></td>";
            echo "<td>".$row['categoria']."</td>";
            echo "<td><span class='prioridade $prioridadeClass'>".$row['prioridade']."</span></td>";
            echo "<td><span class='status $statusClass'>".$row['status']."</span></td>";
            echo "<td>".$row['prazo']."</td>";
            echo "<td><progress value='$progress' max='100'></progress> $progress%</td>";
            echo "<td>
                    <button class='botao-visualizar'>👁️</button>
                    <button class='botao-editar'>✏️</button>
                    <button class='botao-ocultar'>📂</button>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr id='linha-sem-tarefas'>
                <td colspan='7' class='mensagem-central'>Você ainda não possui nenhuma tarefa cadastrada.</td>
              </tr>";
    }
    $conn->close();
    ?>
  </tbody>
      <tbody>
        <tr id="linha-arquivar-vazia" style="display: none;">
          <td colspan="7" class="mensagem-central">Seus arquivos ocultos está vazio.</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Modal de Detalhes -->
  <div id="modalDetalhes" class="modal">
    <div class="modal-conteudo">
      <span class="fechar">&times;</span>
      <h2>Detalhes da Tarefa</h2>
      <p><strong>Nome:</strong> <span id="detalhe-nome"></span></p>
      <p><strong>Categoria:</strong> <span id="detalhe-categoria"></span></p>
      <p><strong>Prioridade:</strong> <span id="detalhe-prioridade"></span></p>
      <p><strong>Status:</strong> <span id="detalhe-status"></span></p>
      <p><strong>Prazo:</strong> <span id="detalhe-prazo"></span></p>
      <p><strong>Progresso:</strong> <span id="detalhe-progresso"></span></p>
      <p><strong>Descrição:</strong> <span id="detalhe-descricao"></span></p>
    </div>
  </div>

  <div id="footer"></div>

  <script src="../Assets/js/ViewListTasks.js"></script>
  <script src="../Assets/js/Header.js"></script>
  <script src="../Assets/js/Footer.js"></script>

</body>
</html>
