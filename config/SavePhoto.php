<?php
// Salva a foto de perfil do usuário
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
        // Redireciona para perfil com mensagem de erro
        header("Location: ../Shared/Profile.php?erro=tipo_invalido");
        exit;
    }

    // Pasta de destino
    $pasta = "../Assets/Uploads/";

    if (!is_dir($pasta)) {
        mkdir($pasta, 0777, true);
    }

    // Nome do arquivo
    $nomeArquivo = "usuario_" . $idUsuario . "." . $ext;
    $destino = $pasta . $nomeArquivo;

    if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
        header("Location: ../Shared/Profile.php?erro=upload_falhou");
        exit;
    }

    // Salva no banco
    $sql = "UPDATE usuarios SET foto = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $destino, $idUsuario);
    $stmt->execute();

    // Atualiza session
    $_SESSION['usuario_foto'] = $destino;

    // Redireciona para perfil com sucesso
    header("Location: ../Shared/Profile.php?sucesso=foto_salva");
    exit;
} else {
    // Nenhum arquivo enviado
    header("Location: ../Shared/Profile.php?erro=nenhum_arquivo");
    exit;
}
