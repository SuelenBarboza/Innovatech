<?php
include("../Config/db.php");


$sql = "SELECT * FROM projetos ORDER BY data_fim IS NULL, data_fim ASC";
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
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Formatar data_fim para dd/mm/yyyy, se existir
            $prazo = $row['data_fim'] ? date("d/m/Y", strtotime($row['data_fim'])) : "-";
            echo "<tr data-descricao='" . htmlspecialchars($row['descricao']) . "'>
                    <td><a href='ViewListProject.html?nome=" . urlencode($row['nome']) . "' class='link-projeto'>" . htmlspecialchars($row['nome']) . "</a></td>
                    <td>-</td>
                    <td><span class='prioridade " . strtolower($row['prioridade']) . "'>" . $row['prioridade'] . "</span></td>
                    <td><span class='status " . strtolower(str_replace(' ', '', $row['status'])) . "'>" . $row['status'] . "</span></td>
                    <td>$prazo</td>
                    <td><progress value='0' max='100'></progress> 0%</td>
                    <td>
                      <button class='botao-visualizar'>👁️</button>
                      <button class='botao-editar'>✏️</button>
                      <button class='botao-ocultar'>📂</button>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr id='linha-sem-projetos'>
                <td colspan='7' class='mensagem-central'>Você ainda não possui nenhum projeto cadastrado.</td>
              </tr>";
    }
    ?>
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
        <td colspan="7" class="mensagem-central">Seus arquivos ocultos estão vazios.</td>
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
