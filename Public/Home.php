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
            <!-- Ícone -->
            <div class="icon-container">
                <i class="fas fa-graduation-cap"></i>
            </div>
            
            <!-- Mensagem -->
            <div class="welcome-message">
                <h1>Bem-vindo ao Academic Hub</h1>
                <h2>Gerenciador de projetos acadêmicos</h2>
            </div>
            
            <!-- Descrição -->
            <div class="description">
                <p>Gerencie seus projetos, acompanhe prazos e colabore com sua equipe de forma simples e eficiente.</p>
            </div>
            
            <!-- Destaques -->
            <div class="highlights">
                <div class="highlight-item">
                    <i class="fas fa-folder-open"></i>
                    <h3>Organize Projetos</h3>
                    <p>Mantenha todos os seus projetos organizados em um só lugar.</p>
                </div>
                
                <div class="highlight-item">
                    <i class="fas fa-calendar-alt"></i>
                    <h3>Acompanhe Prazos</h3>
                    <p>Nunca perca um prazo importante com nosso sistema de alertas.</p>
                </div>
                
                <div class="highlight-item">
                    <i class="fas fa-comments"></i>
                    <h3>Colabore</h3>
                    <p>Trabalhe em equipe com professores e colegas.</p>
                </div>
            </div>
            
        </div>
    </section>    
    
    <?php include("../Includes/Footer.php"); ?>
</body>
</html>