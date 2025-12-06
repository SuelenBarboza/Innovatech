<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Suporte</title>
    <link rel="stylesheet" href="../Assets/css/Header.css" />
    <link rel="stylesheet" href="../Assets/css/Footer.css" />
    <link rel="stylesheet" href="../Assets/css/Support.css" />
</head>
<body>
    <?php include("../Includes/Header.php"); ?>

    <div class="form-container">
        <h1>Contato de Suporte</h1>

        <form id="formSupport" method="POST" action="../config/process_support.php">
            <div class="form-group">
                <label for="nomeUsuario">Nome:</label>
                <input type="text" id="nomeUsuario" name="nomeUsuario" placeholder="Digite seu nome" required />
            </div>

            <div class="form-group">
                <label for="emailUsuario">Email:</label>
                <input type="email" id="emailUsuario" name="emailUsuario" placeholder="Digite seu email" required />
            </div>

            <div class="form-group">
                <label for="assunto">Assunto:</label>
                <input type="text" id="assunto" name="assunto" placeholder="Digite o assunto" required />
            </div>

            <div class="form-group">
                <label for="mensagem">Mensagem:</label>
                <textarea id="mensagem" name="mensagem" placeholder="Descreva sua dúvida ou problema" required></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" id="enviarSupport">Enviar</button>
                <button type="reset" id="cancelarSupport">Cancelar</button>
            </div>
        </form>
    </div>

    <?php include("../Includes/Footer.php"); ?>
</body>
</html>
