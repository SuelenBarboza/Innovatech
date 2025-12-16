<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Assets/Css/Login.css">
    <script src="https://kit.fontawesome.com/d7734ef980.js" crossorigin="anonymous"></script>
    <title>Login</title>
</head>

<body>
    <div class="container">
        
        <img src="../Assets/Img/Fundo-login.png" alt="Logo" class="img-menor">

        <aside class="image-section"></aside>

        <main class="form-section">
            <h2>Entrar</h2>
            <p>Se você não tem uma conta <a class="logcad" href="Register.php">Cadastre-se aqui</a></p>

             <!-- Exibir mensagens de erro/sucesso -->
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php 
                    $error = $_GET['error'];
                    if ($error == 'invalid_credentials') {
                        echo "E-mail ou senha incorretos.";
                    } elseif ($error == 'not_approved') {
                        echo "Sua conta ainda não foi aprovada por um administrador.";
                    } elseif ($error == 'inactive') {
                        echo "Sua conta está desativada.";
                    } else {
                        echo "Erro ao fazer login. Tente novamente.";
                    }
                    ?>
                </div>
            <?php endif; ?>

            <form action="Home.php" method="POST">
                <div class="dadoslogin">
                    <label for="email">E-mail</label>
                    <div>
                        <!-- ícone de email -->
                        <i class="fa-regular fa-envelope icon-modify"></i>
                        <input type="email" id="email" name="email" placeholder="Insira seu email" required>
                    </div>

                    <label for="senha">Senha</label>
                    <div>
                        <i class="fa-solid fa-lock icon-modify"></i>
                        <input type="password" id="passwordL" name="password" placeholder="Insira sua senha" required>
                        <i class="fa-solid fa-eye" id="mostrarSenha"></i>
                    </div>
                </div>

                <div class="lembrar-esqueci">
                    <label for="ck">
                        <input type="checkbox" id="ck" name="ck">
                        Lembrar senha
                    </label>
                    <a href="esqueci.html">Esqueci minha senha</a>
                </div>

                <button type="submit">Login</button>
            </form>

            <section class="social-media">
                <p>Ou conecte-se com:</p>
                <ul class="list-social-media">
                    <a class="link-social-media" href="https://pt-br.facebook.com/">
                        <li class="item-social-media facebook">
                            <i class="fa-brands fa-facebook"></i>
                        </li>
                    </a>
                    <a class="link-social-media" href="https://www.google.com/">
                        <li class="item-social-media google">
                            <i class="fa-brands fa-google-plus-g"></i>
                        </li>
                    </a>
                    <a class="link-social-media" href="https://www.linkedin.com/home">
                        <li class="item-social-media linkedin">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </li>
                    </a>
                </ul>
            </section>
        </main>
    </div>

    <script src="../Assets/js/PasswordLog.js"></script>    
</body>

</html>
