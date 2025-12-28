<?php
include("../Config/db.php");

// ORDEM: projeto mais novo em cima
$sql = "SELECT * FROM projetos ORDER BY id DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Erro na consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Lista de Projetos</title>

  <link rel="stylesheet" href="../Assets/css/Header.css">
  <link rel="stylesheet" href="../Assets/css/Footer.css">
  <link rel="stylesheet" href="../Assets/css/ViewListProject.css">
</head>
<body>

<?php include("../Includes/Header.php"); ?>

<h1>Lista de Projetos</h1>

<!-- ================= FILTROS ================= -->
<div class="filtros">
  <select id="filtro-status">
    <option value="">Status</option>
    <option value="Planejamento">Planejamento</option>
    <option value="Andamento">Andamento</option>
    <option value="Concluído">Concluído</option>
    <option value="Pendente">Pendente</option>
    <option value="Não definido">Não definido</option>
  </select>

  <select id="filtro-prioridade">
    <option value="">Prioridade</option>
    <option value="Alta">Alta</option>
    <option value="Média">Média</option>
    <option value="Baixa">Baixa</option>
    <option value="Não definido">Não definido</option>
  </select>
</div>

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
    while ($row = $result->fetch_assoc()) {

        $id = (int) $row['id'];
        $nome = htmlspecialchars($row['nome']);

        $categoria = !empty($row['categoria'])
            ? htmlspecialchars($row['categoria'])
            : "Não definido";

        $prioridadeTexto = !empty($row['prioridade'])
            ? htmlspecialchars($row['prioridade'])
            : "Não definido";

        $prioridadeClasse = !empty($row['prioridade'])
            ? strtolower($row['prioridade'])
            : "indefinida";

        $statusTexto = !empty($row['status'])
            ? htmlspecialchars($row['status'])
            : "Não definido";

        $statusClasse = !empty($row['status'])
            ? strtolower(str_replace(' ', '', $row['status']))
            : "indefinido";

        $prazo = !empty($row['data_fim'])
            ? date("d/m/Y", strtotime($row['data_fim']))
            : "Não definido";

        $descricao = htmlspecialchars($row['descricao']);

        echo "
        <tr
          data-id=\"$id\"
          data-status=\"$statusTexto\"
          data-prioridade=\"$prioridadeTexto\"
          data-descricao=\"$descricao\"
        >
          <td>
            <a href=\"ViewProject.php?id=$id\" class=\"link-projeto\">
              $nome
            </a>
          </td>

          <td class=\"categoria-cell\">$categoria</td>

          <td class=\"prioridade-cell\">
            <span class=\"prioridade-display prioridade-$prioridadeClasse\">
              $prioridadeTexto
            </span>

            <select class=\"select-prioridade hidden\" data-id=\"$id\" data-field=\"prioridade\">
              <option value=\"\">Selecionar</option>
              <option value=\"Baixa\">Baixa</option>
              <option value=\"Média\">Média</option>
              <option value=\"Alta\">Alta</option>
            </select>
          </td>

          <td class=\"status-cell\">
            <span class=\"status-display status-$statusClasse\">
              $statusTexto
            </span>

            <select class=\"select-status hidden\" data-id=\"$id\" data-field=\"status\">
              <option value=\"\">Selecionar</option>
              <option value=\"Planejamento\">Planejamento</option>
              <option value=\"Andamento\">Andamento</option>
              <option value=\"Pendente\">Pendente</option>
            </select>
          </td>

          <td>$prazo</td>

          <td>
            <button class=\"botao-visualizar\">👁️</button>
            <button class=\"botao-editar\">✏️</button>
            <button class=\"botao-ocultar\">📂</button>
          </td>
        </tr>";
    }
} else {
    echo "
    <tr>
      <td colspan=\"6\" class=\"mensagem-central\">
        Você ainda não possui nenhum projeto cadastrado.
      </td>
    </tr>";
}
?>
  </tbody>
</table>

<button id="btnToggleArquivar">📂 Ver Projetos Arquivados</button>

<div id="containerArquivar" style="display:none;">
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
      <tr>
        <td colspan="6" class="mensagem-central">
          Seus arquivos ocultos estão vazios.
        </td>
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
