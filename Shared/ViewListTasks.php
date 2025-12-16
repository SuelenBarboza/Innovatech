<?php
include("../Config/db.php");

// Ordena pelo término da tarefa (data_fim), tarefas sem data vão para o final
$sql = "SELECT * FROM tarefas ORDER BY data_fim IS NULL, data_fim ASC";
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

<?php include("../Includes/Header.php"); ?>

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
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $progress = $row['progresso'] ?? 0; // valor padrão 0 se não tiver
            $prioridadeClass = strtolower($row['prioridade'] ?? 'baixa'); // alta, media, baixa
            $statusClass = strtolower(str_replace(' ', '', $row['status'] ?? 'planejamento')); // planejamento, andamento, concluido

            // Formata data_fim para dd/mm/yyyy ou "-" se null
            $prazo = $row['data_fim'] ? date("d/m/Y", strtotime($row['data_fim'])) : "-";

            echo "<tr data-descricao='".htmlspecialchars($row['descricao'])."'>";
            echo "<td><a href='ViewTasks.php?id=".$row['id']."' class='link-tarefas'>".htmlspecialchars($row['nome'])."</a></td>";
            echo "<td>".($row['categoria'] ?? "-")."</td>";
            echo "<td><span class='prioridade $prioridadeClass'>".$row['prioridade']."</span></td>";
            echo "<td><span class='status $statusClass'>".$row['status']."</span></td>";
            echo "<td>$prazo</td>";
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
</table>

<?php include("../Includes/Footer.php"); ?>
<script src="../Assets/js/ViewListTasks.js"></script>
</body>
</html>
