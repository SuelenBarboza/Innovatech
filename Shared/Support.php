<?php
session_start();
include("../Includes/Header.php");
include("../Config/db.php"); // Conexão com o banco

// Só usuários logados podem acessar
if (!isset($_SESSION['usuario_id'])) {
    die("Acesso negado. Faça login para abrir um chamado.");
}

// Pega dados do usuário da sessão
$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = $_SESSION['usuario_nome'];
$usuario_email = $_SESSION['usuario_email'];

// Função para mascarar email
function maskEmail($email) {
    $parts = explode("@", $email);
    $name = $parts[0];
    $domain = $parts[1];

    $nameMasked = substr($name, 0, 3) . str_repeat("*", max(0, strlen($name)-3));
    return $nameMasked . "@" . $domain;
}

$emailMasked = maskEmail($usuario_email);

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assunto = htmlspecialchars($_POST['assunto']);
    $mensagem = htmlspecialchars($_POST['mensagem']);

    $sql = "INSERT INTO suporte_chamados (usuario_id, usuario_nome, usuario_email, assunto, mensagem) 
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $usuario_id, $usuario_nome, $usuario_email, $assunto, $mensagem);

    if ($stmt->execute()) {
        $success = "Chamado enviado com sucesso!";
    } else {
        $error = "Erro ao enviar o chamado. Tente novamente.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Abrir Chamado - Suporte</title>
    <link rel="stylesheet" href="/Innovatech/Assets/css/Header.css" />
    <link rel="stylesheet" href="/Innovatech/Assets/css/Footer.css" />
    <link rel="stylesheet" href="/Innovatech/Assets/css/Support.css" />
</head>
<body>
<div class="form-container">
    <h1>Abrir Chamado de Suporte</h1>

    <?php if(isset($success)): ?>
        <div class="success"><?= $success ?></div>
    <?php elseif(isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="nomeUsuario">Nome:</label>
            <input type="text" id="nomeUsuario" value="<?= htmlspecialchars($usuario_nome) ?>" readonly />
        </div>

        <div class="form-group">
            <label for="emailUsuario">Email:</label>
            <!-- email mascarado visível -->
            <input type="text" id="emailMasked" value="<?= htmlspecialchars($emailMasked) ?>" readonly />
            <!-- email real enviado oculto -->
            <input type="hidden" name="emailUsuario" value="<?= htmlspecialchars($usuario_email) ?>" />
        </div>

        <div class="form-group">
            <label for="assunto">Assunto:</label>
            <input type="text" name="assunto" id="assunto" placeholder="Digite o assunto" required />
        </div>

        <div class="form-group">
            <label for="mensagem">Mensagem:</label>
            <textarea name="mensagem" id="mensagem" placeholder="Descreva seu problema ou dúvida" required></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" id="enviarSupport">Enviar Chamado</button>
            <button type="reset" id="cancelarSupport">Cancelar</button>
        </div>
    </form>
</div>

<?php include("../Includes/Footer.php"); ?>
</body>
</html>
