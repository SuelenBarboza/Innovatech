<?php
// Visualiza a lista de projetos 
include("../Config/db.php");
session_start();

if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não logado");
}

$usuario_id = (int) $_SESSION['usuario_id'];

// ================= QUERY CORRIGIDA =================
if ($_SESSION['usuario_tipo'] === 'Admin') {

    $sql = "
        SELECT p.*
        FROM projetos p
        ORDER BY p.criado_em DESC
    ";

    $stmt = $conn->prepare($sql);
}
else {

    $sql = "
        SELECT DISTINCT p.*
        FROM projetos p
        INNER JOIN projeto_usuario pu ON pu.projeto_id = p.id
        WHERE pu.usuario_id = ?
          AND pu.arquivado = 0
        ORDER BY p.criado_em DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['usuario_id']);
}



$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
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

<!-- ================= PROJETOS ATIVOS ================= -->
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
    <!-- JS VAI RENDERIZAR -->
  </tbody>
</table>

<button id="btnToggleArquivar">📂 Ver Projetos Arquivados</button>

<!-- ================= PROJETOS ARQUIVADOS ================= -->
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
      <!-- JS VAI RENDERIZAR -->
    </tbody>
  </table>
</div>

<!-- ================= DADOS INVISÍVEIS PARA O JS ================= -->
<table style="display:none">
  <tbody id="dados-projetos">
    <?php
    // Array para controlar IDs já processados
    $ids_processados = [];
    $contador = 0;
    
    while ($row = $result->fetch_assoc()) {
        $id = (int) $row['id'];
        
        // Se já processou este ID, pula para o próximo
        if (in_array($id, $ids_processados)) {
            continue;
        }
        
        // Adiciona ID à lista de processados
        $ids_processados[] = $id;
        $contador++;
        
        $nome = htmlspecialchars($row['nome']);
        $categoria = htmlspecialchars($row['categoria'] ?? "Não definido");
        $prioridade = $row['prioridade_usuario'] ?? "Não definido";
        $status = $row['status'] ?? "Não definido";
        $prazo = $row['data_fim']
            ? date("d/m/Y", strtotime($row['data_fim']))
            : "Não definido";
        $descricao = htmlspecialchars($row['descricao'] ?? "");
        $dataObservacoes = ($status === "Concluído") ? htmlspecialchars($row['observacoes'] ?? "") : "";
        $arquivado = (int) ($row['arquivado_usuario'] ?? 0);


        echo "
        <tr
          data-id='$id'
          data-nome='$nome'
          data-categoria='$categoria'
          data-prioridade='$prioridade'
          data-status='$status'
          data-prazo='$prazo'
          data-descricao='$descricao'
          data-observacoes='$dataObservacoes'
          data-arquivado='$arquivado'
        ></tr>";
    }
    ?>
  </tbody>
</table>

<!-- ================= MODAL ================= -->
<div id="modalDetalhes" class="modal" style="display:none;">
  <div class="modal-conteudo">
    <span class="fechar">&times;</span>
    <h2>Detalhes do Projeto</h2>
    <div class="modal-detalhes">
      <p><strong>Nome:</strong> <span id="detalhe-nome"></span></p>
      <p><strong>Categoria:</strong> <span id="detalhe-categoria"></span></p>
      <p><strong>Prazo:</strong> <span id="detalhe-prazo"></span></p>
      <p><strong>Prioridade:</strong> <span id="detalhe-prioridade"></span></p>
      <p><strong>Status:</strong> <span id="detalhe-status"></span></p>
      <p><strong>Descrição:</strong></p>
      <div id="detalhe-descricao" class="descricao-texto"></div>
    </div>
  </div>
</div>

<?php include("../Includes/Footer.php"); ?>
<script src="../Assets/js/ViewListProject.js"></script>

</body>
</html>