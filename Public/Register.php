<?php
session_start();
require_once '../Config/db.php';

$erro = '';
$success = '';
$username = '';
$email = '';
$tipoSelecionado = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['password'] ?? '';
    $confirmar = $_POST['confirmPassword'] ?? '';
    $tipoSelecionado = $_POST['tipo_solicitado'] ?? '';

    // Validações
    if (!$username || !$email || !$senha || !$confirmar || !$tipoSelecionado) {
        $erro = "Preencha todos os campos.";
    } elseif ($senha !== $confirmar) {
        $erro = "As senhas não coincidem.";
    } else {
        // Verifica email
        $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $erro = "Este e-mail já está cadastrado.";
        } else {
            // Insere no banco
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo_solicitado, aprovado, ativo) VALUES (?, ?, ?, ?, 0, 1)");
            $sql->bind_param("ssss", $username, $email, $senhaHash, $tipoSelecionado);
            if ($sql->execute()) {
                $success = "Cadastro realizado com sucesso! Faça login.";
                $username = '';
                $email = '';
                $tipoSelecionado = '';
            } else {
                $erro = "Erro ao cadastrar usuário. Tente novamente.";
            }
        }
    }
}
?>
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

        <!-- Caixa de alerta -->
        <?php if ($erro): ?>
            <div class="alert alert-error"><?= $erro ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?= $success ?>
                <script>
                    // Espera 3 segundos (3000 ms) e redireciona para a página de login
                    setTimeout(function() {
                        window.location.href = "Login.php";
                    }, 3000);
                </script>
            </div>
        <?php endif; ?>

        <form method="POST">

            <!-- Nome -->
            <label for="username">Nome de usuário</label>
            <div class="input-container">
                <i class="fa-regular fa-user"></i>
                <input type="text" id="username" name="username" required
                       value="<?= htmlspecialchars($username) ?>">
            </div>

            <!-- Email -->
            <label for="email">E-mail</label>
            <div class="input-container">
                <i class="fa-regular fa-envelope icon-modify"></i>
                <input type="email" id="email" name="email" required
                       value="<?= htmlspecialchars($email) ?>">
            </div>

            <!-- Tipo solicitado -->
            <label for="tipo">Tipo de cadastro</label>
            <div class="input-container">
                <i class="fa-solid fa-user-tag icon-modify"></i>
                <select name="tipo_solicitado" id="tipo" required>
                    <option value="">Selecione</option>
                    <option value="Admin" <?= $tipoSelecionado === 'Admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="Aluno" <?= $tipoSelecionado === 'Aluno' ? 'selected' : '' ?>>Aluno</option>
                    <option value="Professor" <?= $tipoSelecionado === 'Professor' ? 'selected' : '' ?>>Professor</option>
                    <option value="Coordenador" <?= $tipoSelecionado === 'Coordenador' ? 'selected' : '' ?>>Coordenador</option>
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
