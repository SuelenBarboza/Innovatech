<?php
session_start();
include("../Config/db.php");

// Puxa todos os projetos
$sqlProjetos = "SELECT id, nome FROM projetos";
$resultProjetos = $conn->query($sqlProjetos);

// Puxa todos os usuários (membros)
$sqlMembros = "SELECT id, nome FROM usuarios";
$resultMembros = $conn->query($sqlMembros);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Atribuir Tarefas</title>
    <link rel="stylesheet" href="../Assets/css/Header.css" />
    <link rel="stylesheet" href="../Assets/css/Footer.css" />
    <link rel="stylesheet" href="../Assets/css/AssignTasks.css" />
</head>
<body>
    <?php include("../Includes/Header.php"); ?>

    <div class="form-container">
        <h1>Atribuir Tarefa ao Membro</h1>

        <form action="../Config/process_assign_task.php" method="POST">
            <!-- Projeto -->
            <div class="form-group">
               <label for="projeto_id">Nome do Projeto:</label>
                 <select id="projeto_id" name="projeto_id" required>
                   <option value="">Selecione</option>
                   <?php while($p = $resultProjetos->fetch_assoc()) { ?>
                       <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nome']) ?></option>
                   <?php } ?>
                 </select>
            </div>

            <!-- Membro -->
            <div class="form-group">
               <label for="membro_id">Nome do Membro:</label>
                 <select id="membro_id" name="membro_id" required>
                   <option value="">Selecione</option>
                   <?php while($m = $resultMembros->fetch_assoc()) { ?>
                       <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nome']) ?></option>
                   <?php } ?>
                 </select>
            </div>

            <!-- Descrição da Tarefa -->
            <div class="form-group">
                <label for="descricaoTarefa">Descrição da Tarefa:</label>
                <textarea id="descricaoTarefa" name="descricaoTarefa" placeholder="Descreva a tarefa" required></textarea>
            </div>

            <!-- Ações -->
            <div class="form-actions">
                <button type="submit" id="atribuirTarefa">Atribuir</button>
                <button type="button" id="cancelarTarefa" onclick="window.location.href='ViewLista.php'">Cancelar</button>
            </div>
        </form>
    </div>

    <?php include("../Includes/Footer.php"); ?>
</body>
</html>
