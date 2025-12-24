<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Assets/css/Register.css">
    <script src="https://kit.fontawesome.com/d7734ef980.js" crossorigin="anonymous"></script>
    <title>Cadastro</title>
</head>
<body>

<div class="container">

    <img src="../Assets/Img/Fundo-login.png" alt="Logo" class="img-menor">
    <aside class="image-section"></aside>

    <main class="form-section cadastro">
        <h2>Cadastro</h2>
        <p>Já tem uma conta? <a href="Login.php">Faça login</a></p>

        <form action="../Config/ProcessRegister.php" method="POST">

            <!-- Nome -->
            <label for="username">Nome de usuário</label>
            <div class="input-container">
                <i class="fa-regular fa-user"></i>
                <input type="text" id="username" name="username" required>
            </div>

            <!-- Email -->
            <label for="email">E-mail</label>
            <div class="input-container">
                <i class="fa-regular fa-envelope icon-modify"></i>
                <input type="email" id="email" name="email" required>
            </div>

            <!-- Tipo solicitado -->
            <label for="tipo">Tipo de cadastro</label>
            <div class="input-container">
                <i class="fa-solid fa-user-tag icon-modify"></i>
                <select name="tipo_solicitado" id="tipo" required>
                    <option value="">Selecione</option>
                    <option value="Admin">Admin</option>
                    <option value="Aluno">Aluno</option>
                    <option value="Professor">Professor</option>
                    <option value="Coordenador">Coordenador</option>
                </select>
            </div>

            <!-- Senha -->
            <label for="password">Senha</label>
            <div class="input-container">
                <i class="fa-solid fa-lock icon-modify"></i>
                <input type="password" id="password" name="password" required>
                <i class="fa-solid fa-eye" id="mostrarSenha" style="cursor: pointer;"></i>
            </div>

            <!-- Confirmar senha -->
            <label for="confirmPassword">Confirmar Senha</label>
            <div class="input-container">
                <i class="fa-solid fa-lock icon-modify"></i>
                <input type="password" id="confirmPassword" name="confirmPassword" required>
                <i class="fa-solid fa-eye" id="mostrarConfirmarSenha" style="cursor: pointer;"></i>
            </div>

            <button type="submit">Registrar-se</button>

        </form>
    </main>
</div>

<script src="../Assets/js/PasswordReg.js"></script>
</body>
</html>
