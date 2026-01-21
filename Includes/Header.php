<?php
// Inicia a sessão apenas se ainda não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redireciona caso não esteja logado
if(!isset($_SESSION['usuario_id'])){
    header("Location: ../Public/Login.php");
    exit;
}

// Dados do usuário
$usuario_nome = $_SESSION['usuario_nome'] ?? 'Usuário';
$usuario_tipo = $_SESSION['usuario_tipo'] ?? '';
$usuario_foto = $_SESSION['usuario_foto'] ?? '../Assets/img/avatar.png';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<header>
    <nav>
        <ul class="menu">
            <!-- Logo -->
            <li>
                <div class="logo-container">
                    <a href="../Public/Home.php">
                        <img src="../Assets/Img/logo/logo.png" alt="Logo">
                    </a>
                </div>
            </li>

            <!-- Início -->
            <li><a href="../Public/Home.php">Início</a></li>

            <!-- Projetos -->
            <li>
                <a href="#">Projetos</a>
                <ul class="submenu">
                    <li><a href="../Shared/RegisterProject.php">Cadastrar Projetos</a></li>
                    <li><a href="../Shared/ViewListProject.php">Visualizar Lista de Projetos</a></li>
                </ul>
            </li>

            <!-- Tarefas -->
            <li>
                <a href="#">Tarefas</a>
                <ul class="submenu">
                    <li><a href="../Shared/AddTasks.php">Adicionar Tarefas</a></li>
                    <li><a href="../Shared/ViewListTasks.php">Visualizar Lista de Tarefas</a></li>
                </ul>
            </li>

            <!-- Colaboração -->
            <li>
                <a href="#">Colaboração</a>
                <ul class="submenu">
                    <li><a href="../Shared/Comments.php">Comentários</a></li>
                    <li><a href="../Shared/ViewComments.php">Visualizar Comentários</a></li>

                    <?php if($usuario_tipo === 'Aluno'): ?>
                        <li><a href="../Shared/SendReport.php">Relatório de Progresso</a></li>
                        <li><a href="../Shared/MyReports.php">Meus Relatórios</a></li>
                    <?php endif; ?>

                    <?php if($usuario_tipo === 'Professor'): ?>
                        <li><a href="../Shared/ViewReportsTeacher.php">Relatórios Recebidos</a></li>
                    <?php endif; ?>
                </ul>
            </li>

            <!-- Calendário -->
            <li>
                <a href="#">Calendário</a>
                <ul class="submenu">
                    <li><a href="http://localhost:3000">Visualizar Calendário</a></li>
                </ul>
            </li>

            <!-- Gerenciamento -->
            <?php if(in_array($usuario_tipo, ['Admin','Coordenador'])): ?>
            <li>
                <a href="#">Gerenciamento</a>
                <ul class="submenu">
                    <?php if($usuario_tipo === 'Admin'): ?>
                        <li><a href="../Shared/UserManagerAdmin.php">Gerenciar Usuários</a></li>
                    <?php endif; ?>
                    <?php if($usuario_tipo === 'Coordenador'): ?>
                        <li><a href="../Shared/UserManagerCoord.php">Gerenciar Alunos e Professores</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>

            
            <!-- Suporte -->
            <li>
                <a href="#">Suporte</a>
                <ul class="submenu">
                    <?php if(in_array($usuario_tipo, ['Aluno','Professor','Coordenador'])): ?>
                        <li><a href="../Shared/Support.php">Solicitar Suporte</a></li>
                        <li><a href="../Shared/MyCallings.php">Meus Chamados</a></li>
                    <?php endif; ?>

                    <?php if($usuario_tipo === 'Admin'): ?>
                        <li><a href="../Shared/SuportAdmin.php">Painel de Solicitações</a></li>
                    <?php endif; ?>
                </ul>
            </li>


            <!-- Perfil do usuário -->
            <li class="user-profile">
                <a href="#">
                    <img src="<?= $usuario_foto ?>" class="user-avatar" alt="Foto do usuário">
                    <div class="user-info">
                        <span class="user-name"><?= $usuario_nome ?></span>
                        <span class="user-role"><?= $usuario_tipo ?></span>
                    </div>
                </a>
                <ul class="submenu">
                    <li><a href="../Shared/Profile.php">Meu perfil</a></li>
                    <li><a href="../Config/Logout.php">Sair</a></li>
                </ul>
            </li>

            <!-- Notificações -->
            <li class="bell-icon">
                <a href="javascript:void(0)">
                    <i class="fa-regular fa-bell"></i>
                    <span class="notification-bubble"></span>
                </a>
                <div class="notification-menu">
                    <ul>
                        <li><a href="link1.html">Você tem 3 novos recados!</a></li>
                        <li><a href="link2.html">Reunião agendada para amanhã.</a></li>
                        <li><a href="link3.html">Novos projetos adicionados.</a></li>
                    </ul>
                </div>
            </li>

        </ul>
    </nav>
</header>
</body>
</html>
