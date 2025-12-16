<?php
include("db.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nome = $_POST['nomeUsuario'];
    $email = $_POST['emailUsuario'];
    $senha = password_hash($_POST['senhaUsuario'], PASSWORD_DEFAULT); 
    $perfil = $_POST['perfilUsuario'];


    $sqlCheck = "SELECT id FROM usuarios WHERE email=?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("s", $email);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows > 0) {
        echo "Esse email já está cadastrado!";
        exit;
    }
    $stmtCheck->close();

    $sql = "INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nome, $email, $senha, $perfil);

    if ($stmt->execute()) {
        header("Location: ../ViewList.html?msg=sucesso");
    } else {
        echo "Erro: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
