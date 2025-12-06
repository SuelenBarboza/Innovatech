<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Membro</title>
    <link rel="stylesheet" href="../Assets/css/Header.css">
    <link rel="stylesheet" href="../Assets/css/Footer.css">
    <link rel="stylesheet" href="../Assets/css/DeleteMember.css">
</head>
<body>
    <?php include("../Includes/Header.php"); ?>

    <div class="form-container">
        <h1>Excluir Membro do Projeto</h1>

        <form id="formExcluirMembro" method="POST" action="../config/delete_members.php">
            <div class="form-group">
                <label for="nomeProjeto">Nome do Projeto:</label>
                <input type="text" id="nomeProjeto" name="nomeProjeto" placeholder="Digite o nome do projeto" required>
            </div>

            <div class="form-group">
                <label for="nomeMembro">Nome do Membro:</label>
                <input type="text" id="nomeMembro" name="nomeMembro" placeholder="Digite o nome do membro" required>
            </div>

            <div class="form-actions">
                <button type="submit" id="excluirMembro">Excluir</button>
                <button type="button" id="cancelarMembro" onclick="window.location.href='../Public/Home.php'">Cancelar</button>
            </div>
        </form>
    </div>

    <?php include("../Includes/Footer.php"); ?>
</body>
</html>
