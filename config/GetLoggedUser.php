<?php
header("Access-Control-Allow-Origin: http://localhost:3000"); // React
header("Access-Control-Allow-Credentials: true");             // permite cookies
header("Content-Type: application/json");

session_start();
include("../Config/db.php");

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(null);
    exit;
}

$usuario = [
    "nome" => $_SESSION['usuario_nome'] ?? "Usuário",
    "tipo" => $_SESSION['usuario_tipo'] ?? "",
    "foto" => $_SESSION['usuario_foto'] ?? "../Assets/img/avatar.png"
];

echo json_encode($usuario);
