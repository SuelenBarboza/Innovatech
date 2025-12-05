<?php
include("conexao.php"); 

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nome = $_POST['nomeUsuario'];
    $email = $_POST['emailUsuario'];
    $assunto = $_POST['assunto'];
    $mensagem = $_POST['mensagem'];

    $sql = "INSERT INTO suporte (nome, email, assunto, mensagem) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nome, $email, $assunto, $mensagem);

    if ($stmt->execute()) {
        header("Location: ../Public/Home.html?msg=sucesso");
        exit();
    } else {
        echo "Erro ao enviar sua mensagem: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
