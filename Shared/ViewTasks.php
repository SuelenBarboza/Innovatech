<?php
// Visualizar as tarefas detalhadas
include("../Config/db.php");
session_start();

// ==========================
// VALIDAÇÃO DO ID
// ==========================
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ViewListTasks.php");
    exit;
}

$id = (int) $_GET['id'];

// ==========================
// BUSCA DA TAREFA + PROJETO
// ==========================
$sql = "
    SELECT 
        t.id,
        t.nome,
        t.descricao,
        t.prioridade,
        t.status,
        t.data_inicio,
        t.data_fim,
        t.projeto_id,
        p.nome AS nome_projeto
    FROM tarefas t
    LEFT JOIN projetos p ON t.projeto_id = p.id
    WHERE t.id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Tarefa não encontrada.";
    exit;
}

$tarefa = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Detalhes da Tarefa</title>

  <link rel="stylesheet" href="../Assets/css/Header.css">
  <link rel="stylesheet" href="../Assets/css/Footer.css">
  <link rel="stylesheet" href="../Assets/css/ViewTasks.css">
</head>
<body>

<?php include("../Includes/Header.php"); ?>

<section class="form-container">
  <h2>Detalhes da Tarefa</h2>

  <div id="dados-tarefa">

    <!-- 1 -->
    <p>
      <strong>Tarefa</strong>
      <?= htmlspecialchars($tarefa['nome']) ?>
    </p>

    <!-- 2 -->
    <p>
      <strong>Projeto</strong>
      <?= !empty($tarefa['nome_projeto'])
          ? htmlspecialchars($tarefa['nome_projeto'])
          : 'Não vinculado' ?>
    </p>

    <!-- 3 -->
    <p>
      <strong>Prioridade</strong>
      <?= !empty($tarefa['prioridade'])
          ? htmlspecialchars($tarefa['prioridade'])
          : 'Não definida' ?>
    </p>

    <!-- 4 -->
    <p>
      <strong>Status</strong>
      <?= !empty($tarefa['status'])
          ? htmlspecialchars($tarefa['status'])
          : 'Não definido' ?>
    </p>

    <!-- 5 -->
    <p>
      <strong>Data de Início</strong>
      <?= !empty($tarefa['data_inicio'])
          ? date("d/m/Y", strtotime($tarefa['data_inicio']))
          : 'Não definida' ?>
    </p>

    <!-- 6 -->
    <p>
      <strong>Data de Conclusão</strong>
      <?= !empty($tarefa['data_fim'])
          ? date("d/m/Y", strtotime($tarefa['data_fim']))
          : 'Não definida' ?>
    </p>

    <!-- 7 -->
    <p>
      <strong>Descrição</strong>
      <?= !empty($tarefa['descricao'])
          ? nl2br(htmlspecialchars($tarefa['descricao']))
          : 'Sem descrição' ?>
    </p>

  </div>

  <!-- AÇÕES -->
  <div class="acoes-projeto">

    <a href="EditTasks.php?id=<?= $tarefa['id'] ?>" class="btn-editar">
      ✏️ Editar Tarefa
    </a>

    <?php if (!empty($tarefa['projeto_id'])): ?>
      <a href="ViewProject.php?id=<?= $tarefa['projeto_id'] ?>" class="btn-editar">
        📁 Ver Projeto
      </a>
    <?php endif; ?>

    <a href="ViewListTask.php" class="btn-voltar">
      ⬅️ Voltar para a Lista
    </a>

  </div>
</section>

<?php include("../Includes/Footer.php"); ?>

</body>
</html>
