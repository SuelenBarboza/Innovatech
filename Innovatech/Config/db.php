<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "innovatech_db";

// Ativa relatórios de erro para desenvolvimento
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>
