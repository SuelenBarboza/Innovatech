<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = htmlspecialchars($_POST['nomeUsuario']);
    $email = filter_var($_POST['emailUsuario'], FILTER_VALIDATE_EMAIL);
    $assunto = htmlspecialchars($_POST['assunto']);
    $mensagem = htmlspecialchars($_POST['mensagem']);

    if ($email) {
        $to = "suporte@seudominio.com";
        $headers = "From: $email\r\nReply-To: $email";
        if (mail($to, $assunto, $mensagem, $headers)) {
            echo "Mensagem enviada com sucesso!";
        } else {
            echo "Erro ao enviar a mensagem.";
        }
    } else {
        echo "Email inválido.";
    }
}
?>
