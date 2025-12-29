<?php
// Conexão com banco de dados 
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "innovatech_db";

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Charset obrigatório (acentos, emojis, português)
$conn->set_charset("utf8mb4");
