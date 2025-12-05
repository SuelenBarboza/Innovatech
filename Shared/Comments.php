<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Comentários</title>
    <link rel="stylesheet" href="../Assets/css/Header.css" />
    <link rel="stylesheet" href="../Assets/css/Footer.css" />
    <link rel="stylesheet" href="../Assets/css/Comments.css" />
</head>
<body>
    <div id="header"></div>

    <div class="form-container">
        <h1>Deixe seu Comentário</h1>

        <form id="formComentarios">
            <div class="form-group">
                <label for="nomeUsuario">Seu Nome:</label>
                <input type="text" id="nomeUsuario" name="nomeUsuario" placeholder="Digite seu nome" required />
            </div>

            <div class="form-group">
                <label for="comentario">Comentário:</label>
                <textarea id="comentario" name="comentario" placeholder="Escreva seu comentário aqui" required></textarea>
            </div>

            <div class="form-group">
                 <label for="assunto">Projeto/Tarefa:</label> <!--Sera só para o projeto ou para tarefa tmb? -->
                 <select id="projeto_id" name="projeto_id" required>
                   <option value="">Selecione um projeto</option>
                     <?php while($p = $resultProjetos->fetch_assoc()) { ?>
                   <option value="<?= $p['id'] ?>"><?= $p['nome'] ?></option>
                     <?php } ?>
                 </select>
            </div>

            <div class="form-actions">
                <button type="submit" id="enviarComentario">Enviar</button>
                <button type="reset" id="limparComentario">Limpar</button>
            </div>
        </form>
    </div>

    <div id="footer"></div>

    <script src="../Assets/js/Header.js"></script>
    <script src="../Assets/js/Footer.js"></script>
</body>
</html>
