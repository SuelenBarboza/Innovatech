<?php
include("../Config/db.php");

$sql = "SELECT * FROM projetos ORDER BY data_fim IS NULL, data_fim ASC";
$result = $conn->query($sql);

if (!$result) {
    die("Erro na consulta: " . $conn->error);
}
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
      <th>Ações</th>
    </tr>
  </thead>
  <tbody>
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $prazo = $row['data_fim'] ? date("d/m/Y", strtotime($row['data_fim'])) : "-";
            $categoria_nome = !empty($row['categoria']) ? htmlspecialchars($row['categoria']) : "-";
          
            echo "<tr 
              data-id='" . $row['id'] . "'
              data-descricao='" . htmlspecialchars($row['descricao']) . "'
              data-prioridade='" . htmlspecialchars($row['prioridade']) . "'
              data-status='" . htmlspecialchars($row['status']) . "'
            >
              <td>
                <a href='ViewProject.html?nome=" . urlencode($row['nome']) . "' class='link-projeto'>
                  " . htmlspecialchars($row['nome']) . "
                </a>
              </td>

              <td class='categoria-cell'>" . $categoria_nome . "</td>

              <td class='prioridade-cell'>
                <span class='prioridade-display prioridade-" . (!empty($row['prioridade']) ? strtolower($row['prioridade']) : 'indefinida') . "'>
                    " . ($row['prioridade'] ? htmlspecialchars($row['prioridade']) : "Não definida") . "
                </span>
                <select class='select-prioridade hidden' data-id='" . $row['id'] . "' data-field='prioridade'>
                    <option value='' " . (empty($row['prioridade']) ? 'selected' : '') . ">Selecionar</option>
                    <option value='Baixa' " . ($row['prioridade'] == 'Baixa' ? 'selected' : '') . ">Baixa</option>
                    <option value='Média' " . ($row['prioridade'] == 'Média' ? 'selected' : '') . ">Média</option>
                    <option value='Alta' " . ($row['prioridade'] == 'Alta' ? 'selected' : '') . ">Alta</option>
                </select>
            </td>

            <td class='status-cell'>
                <span class='status-display status-" . (!empty($row['status']) ? strtolower(str_replace(' ', '', $row['status'])) : 'indefinido') . "'>
                    " . htmlspecialchars($row['status']) . "
                </span>
                <select class='select-status hidden' data-id='" . $row['id'] . "' data-field='status'>
                    <option value='' " . (empty($row['status']) ? 'selected' : '') . ">Selecionar</option>
                    <option value='Planejamento' " . ($row['status'] == 'Planejamento' ? 'selected' : '') . ">Planejamento</option>
                    <option value='Andamento' " . ($row['status'] == 'Andamento' ? 'selected' : '') . ">Andamento</option>
                    <option value='Concluído' " . ($row['status'] == 'Concluído' ? 'selected' : '') . ">Concluído</option>
                    <option value='Pendente' " . ($row['status'] == 'Pendente' ? 'selected' : '') . ">Pendente</option>
                </select>
            </td>

              <td>$prazo</td>

              <td>
                <button class='botao-visualizar'>👁️</button>
                <button class='botao-editar'>✏️</button>
                <button class='botao-ocultar'>📂</button>
              </td>
            </tr>";
        }
    } else {
        echo "<tr id='linha-sem-projetos'>
                <td colspan='6' class='mensagem-central'>Você ainda não possui nenhum projeto cadastrado.</td>
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
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <tr id="linha-arquivar-vazia" style="display: none;">
        <td colspan="6" class="mensagem-central">Seus arquivos ocultos estão vazios.</td>
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
    <p><strong>Descrição:</strong> <span id="detalhe-descricao"></span></p>
  </div>
</div>

<?php include("../Includes/Footer.php"); ?>

<script src="../Assets/js/ViewListProject.js"></script>

</body>
</html>