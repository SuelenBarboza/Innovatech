<?php
include("../Config/db.php");
session_start();

if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não logado.");
}

$usuario_id = (int) $_SESSION['usuario_id'];

// ==========================
// VALIDAÇÃO DO ID DA TAREFA
// ==========================
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ViewListTask.php");
    exit;
}

$tarefa_id = (int) $_GET['id'];

// ==========================
// BUSCA DA TAREFA
// ==========================
$sqlTarefa = "
    SELECT *
    FROM tarefas
    WHERE id = ?
";
$stmtTarefa = $conn->prepare($sqlTarefa);
$stmtTarefa->bind_param("i", $tarefa_id);
$stmtTarefa->execute();
$resultTarefa = $stmtTarefa->get_result();

if ($resultTarefa->num_rows === 0) {
    die("Tarefa não encontrada.");
}

$tarefa = $resultTarefa->fetch_assoc();

// ==========================
// BUSCA PROJETOS DO USUÁRIO
// ==========================
$sqlProjetos = "
    SELECT DISTINCT p.id, p.nome
    FROM projetos p
    LEFT JOIN projeto_aluno pa ON pa.projeto_id = p.id
    LEFT JOIN projeto_orientador po ON po.projeto_id = p.id
    WHERE p.criador_id = ? OR pa.usuario_id = ? OR po.professor_id = ?
    ORDER BY p.nome ASC
";

$stmt = $conn->prepare($sqlProjetos);
$stmt->bind_param("iii", $usuario_id, $usuario_id, $usuario_id);
$stmt->execute();
$resultProjetos = $stmt->get_result();

// ==========================
// BUSCA ALUNOS DO PROJETO ATUAL
// ==========================
$alunos = [];
if (!empty($tarefa['projeto_id'])) {
    $sqlAlunos = "
        SELECT u.id, u.nome
        FROM projeto_aluno pa
        INNER JOIN usuarios u ON pa.usuario_id = u.id
        WHERE pa.projeto_id = ?
    ";
    $stmtAlunos = $conn->prepare($sqlAlunos);
    $stmtAlunos->bind_param("i", $tarefa['projeto_id']);
    $stmtAlunos->execute();
    $resultAlunos = $stmtAlunos->get_result();

    while ($a = $resultAlunos->fetch_assoc()) {
        $alunos[] = $a;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

<link rel="stylesheet" href="../Assets/css/Header.css"> 
<link rel="stylesheet" href="../Assets/css/Footer.css">
<link rel="stylesheet" href="../Assets/css/AddTasks.css">

<title>Editar Tarefa</title>
</head>
<body>

<?php include("../Includes/Header.php"); ?>

<div class="form-container">
  <h1>Editar Tarefa</h1>

  <form action="../Config/ProcessEditTasks.php" method="POST">

    <input type="hidden" name="tarefa_id" value="<?= $tarefa['id'] ?>">

    <!-- PROJETO -->
    <div class="form-group">
      <label for="projeto">Projeto:</label>
      <select id="projeto" name="projeto" required>
        <option value="">Selecione um projeto</option>
        <?php while($p = $resultProjetos->fetch_assoc()) { ?>
          <option value="<?= $p['id'] ?>"
            <?= $p['id'] == $tarefa['projeto_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($p['nome']) ?>
          </option>
        <?php } ?>
      </select>
    </div>

    <!-- ALUNO -->
    <div class="form-group">
      <label for="aluno">Aluno Responsável:</label>
      <select id="aluno" name="aluno" required>
        <option value="">Selecione um aluno</option>
        <?php foreach ($alunos as $a) { ?>
          <option value="<?= $a['id'] ?>"
            <?= $a['id'] == $tarefa['responsavel_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($a['nome']) ?>
          </option>
        <?php } ?>
      </select>
    </div>

    <!-- NOME -->
    <div class="form-group">
      <label for="nome_tarefa">Nome da Tarefa:</label>
      <input type="text" id="nome_tarefa" name="nome_tarefa"
             value="<?= htmlspecialchars($tarefa['nome']) ?>" required>
    </div>

    <!-- DESCRIÇÃO -->
    <div class="form-group">
      <label for="descricao">Descrição:</label>
      <textarea id="descricao" name="descricao" required><?= htmlspecialchars($tarefa['descricao']) ?></textarea>
    </div>

    <!-- DATAS -->
    <div class="form-group input-group">
      <div class="date-field">
        <label for="data_inicio">Data de Início:</label>
        <input type="date" id="data_inicio" name="data_inicio"
               value="<?= $tarefa['data_inicio'] ?>">
      </div>

      <div class="date-field">
        <label for="data_fim">Data de Fim:</label>
        <input type="date" id="data_fim" name="data_fim"
               value="<?= $tarefa['data_fim'] ?>">
      </div>
    </div>

    <!-- AÇÕES -->
    <div class="form-actions">
      <button type="submit">Salvar Alterações</button>
      <button type="button" onclick="window.location.href='ViewListTask.php'">
        Cancelar
      </button>
    </div>

  </form>
</div>

<?php include("../Includes/Footer.php"); ?>

