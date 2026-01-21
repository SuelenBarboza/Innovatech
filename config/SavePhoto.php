<?php
//Salva a foto de perfil do usuário
session_start();
include("../Config/db.php");

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Public/Login.php");
    exit;
}

$idUsuario = $_SESSION['usuario_id'];

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {

    $arquivo = $_FILES['foto'];

    // Tipos permitidos
    $tiposPermitidos = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $tiposPermitidos)) {
        die("Formato de imagem inválido.");
    }

    // Pasta de destino
    $pasta = "../Assets/Uploads/";

    if (!is_dir($pasta)) {
        mkdir($pasta, 0777, true);
    }

    // Nome do arquivo
    $nomeArquivo = "usuario_" . $idUsuario . "." . $ext;
    $destino = $pasta . $nomeArquivo;

    move_uploaded_file($arquivo['tmp_name'], $destino);

    // Salva no banco
    $sql = "UPDATE usuarios SET foto = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $destino, $idUsuario);
    $stmt->execute();

    // Atualiza session
    $_SESSION['usuario_foto'] = $destino;

    // Volta para o perfil
    header("Location: ../Shared/Profile.php");
    exit;
}
