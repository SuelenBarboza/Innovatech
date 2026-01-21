<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Public/Login.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../Assets/Css/Header.css">
    <link rel="stylesheet" href="../Assets/Css/Footer.css">
    <link rel="stylesheet" href="../Assets/Css/Home.css"> 
    <title>Innovatech</title>
    
</head>

<body>
     <?php include("../Includes/Header.php") ?>
    
    <section class="sobre">
        <div class="container">
             <!-- <img src="../Assets/Img/FundoInnova.png" alt="Innovatech" class="logo">  -->
           
    </section>    
    
    <?php include("../Includes/Footer.php"); ?>

</body>
</html>
