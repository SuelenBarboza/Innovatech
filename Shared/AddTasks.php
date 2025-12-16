<?php
include("../Config/db.php");


$sqlProjetos = "SELECT id, nome FROM projetos";
$resultProjetos = $conn->query($sqlProjetos);


$sqlMembros = "SELECT id, nome FROM usuarios";
$resultMembros = $conn->query($sqlMembros);
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="../Assets/css/Header.css"> 
  <link rel="stylesheet" href="../Assets/css/Footer.css">
  <link rel="stylesheet" href="../Assets/css/AddTasks.css">
  <title>Adicionar Tarefa</title>

</head>
<body>
     <?php include("../Includes/Header.php"); ?>
    
    <div class="form-container">
      <h1>Adicionar Nova Tarefa</h1>
    <form action="../config/process_tasks.php" method="POST">

      <div class="form-group">
        <label for="nome_tarefa">Nome da Tarefa:</label>
        <input type="text" id="nome_tarefa" name="nome_tarefa" required>
      </div>

      <div class="form-group">
        <label for="descricao">Descrição:</label>
        <textarea id="descricao" name="descricao" required></textarea>
      </div>

      <div class="form-group input-group">
        <div class="date-field">
          <label for="data_inicio">Data de Início:</label>
          <input type="date" id="data_inicio" name="data_inicio" required>
        </div>

        <div class="date-field">
          <label for="data_fim">Data de Fim:</label>
          <input type="date" id="data_fim" name="data_fim" required>
        </div>
      </div>

      <div class="form-group">
        <label for="aluno">Aluno:</label>
         <select id="aluno" name="aluno" required>
          <option value="">Selecione</option>
             <?php while($m = $resultMembros->fetch_assoc()) { ?>
          <option value="<?= $m['id'] ?>"><?= $m['nome'] ?></option>
             <?php } ?>
         </select>
      </div>

      <div class="form-group">
        <label for="projeto">Nome do Projeto:</label>
         <select id="projeto" name="projeto" required>
          <option value="">Selecione</option>
             <?php while($p = $resultProjetos->fetch_assoc()) { ?>
          <option value="<?= $p['id'] ?>"><?= $p['nome'] ?></option>
             <?php } ?>
         </select>
      </div>

      <div class="form-actions">
        <button type="submit" id="salvar">Salvar</button>
        <button type="button" id="cancelar" onclick="window.location.href='../Public/Home.php'">Cancelar</button>
      </div>

    </form>
  </div>

  <?php include("../Includes/Footer.php"); ?>
</body>
</html>
