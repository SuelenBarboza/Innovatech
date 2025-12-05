<?php
include("../Config/Conexao.php"); 

$idTarefa = $_GET['id'] ?? null;
$nomeTarefa = "";

if ($idTarefa) {
    $sql = "SELECT nome FROM tarefas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idTarefa);
    $stmt->execute();
    $stmt->bind_result($nomeTarefa);
    $stmt->fetch();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concluir Tarefa</title>
    <link rel="stylesheet" href="../Assets/css/Header.css"> 
    <link rel="stylesheet" href="../Assets/css/Footer.css">
    <link rel="stylesheet" href="../Assets/css/CompleteTasks.css">
</head>
<body>
    <div id="header"></div>

    <div class="form-container">
        <h1>Concluir Tarefa</h1>

        <form id="formConclusaoTarefa" method="POST" action="conclusao_tasks.php">
            <div class="form-group">
                <label for="nomeTarefa">Nome da Tarefa:</label>
                <input type="text" id="nomeTarefa" name="nomeTarefa" value="<?php echo $nomeTarefa; ?>" readonly>
                <input type="hidden" name="idTarefa" value="<?php echo $idTarefa; ?>">
            </div>

            <div class="form-group">
                <label for="concluidoPor">Concluído por:</label>
                <input type="text" id="concluidoPor" name="concluidoPor" placeholder="Digite seu nome ou deixe que o sistema preencha">
            </div>

            <div class="form-group">
                <label for="comentarioTarefa">Observações ou Comentário Final:</label>
                <textarea id="comentarioTarefa" name="comentarioTarefa" placeholder="Digite uma observação, se desejar..."></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" id="salvarTarefa">Salvar</button>
                <button type="button" id="cancelarTarefa" onclick="window.location.href='ViewTasks.html'">Cancelar</button>
            </div>
        </form>
    </div>

    <div id="footer"></div>

    <script src="../Assets/js/Header.js"></script>
    <script src="../Assets/js/Footer.js"></script> 
</body>
</html>
