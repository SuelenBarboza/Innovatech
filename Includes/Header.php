<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    
</head>
<body>
    <header>

        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
     <nav>

         <ul class="menu">
            <li>
                <div class="logo-container">
                    <a href="../Public/Home.php">
                        <img src="../Assets/Img/logo/logo.png" alt="Logo">
                    </a>
                </div>
              </li>
              <li>
                 <a href="#">Inicio</a>
                    <ul class="submenu">
                        <li><a href="../Public/Home.php">Inicio</a></li>
                    </ul>
             </li>
             <li>
                 <a href="#">Projetos</a>
                    <ul class="submenu">
                        <li><a href="../Shared/RegisterProject.php">Cadastrar Projetos</a></li>
                        <li><a href="../Shared/EditProject.php">Editar Projetos</a></li>
                        <li><a href="../Shared/ViewListProject.php">Visualizar Lista de Projetos</a></li>
                        <li><a href="../Shared/CompleteProject.php">Concluir Projeto</a></li>
                    </ul>
             </li>
 
             <li>
                 <a href="#">Tarefas</a>
                 <ul class="submenu">
                     <li><a href="../Shared/AddTasks.php">Adicionar Tarefas</a></li>
                     <li><a href="../Shared/EditTasks.php">Editar Tarefas</a></li>
                     <li><a href="../Shared/ViewListTasks.php">Visualizar Lista de Tarefas</a></li>
                     <li><a href="../Shared/CompleteTasks.php">Concluir Tarefas</a></li>

                 </ul>
             </li>
 
             <li>
                 <a href="#">Colaboração</a>
                 <ul class="submenu">
                     <li><a href="../Shared/AddMembers.php">Adicionar Membros</a></li>
                     <li><a href="../Shared/DeleteMember.php">Excluir Membro</a></li>
                     <li><a href="../Shared/AssignTasks.php">Atribuir Tarefas</a></li>
                     <li><a href="../Shared/DeleteTasks.php">Excluir Tarefa</a></li>
                     <li><a href="../Shared/Comments.php">Comentários</a></li>
                 </ul>
             </li>
 
             <li>
                 <a href="#">Calendário</a>
                 <ul class="submenu">
                     <li><a href="http://localhost:3000">Visualizar Calendário</a></li>
                 </ul>
             </li>
 
             <li>
                 <a href="#">Relatórios</a>
                 <ul class="submenu">
                     <li><a href="../Shared/ProgressReport.php">Relatório de Progresso</a></li>
                     <li><a href="../Shared/PerformanceReport.php">Relatório de Desempenho</a></li>
                 </ul>
             </li>
 
             <li>
                 <a href="#">Configurações</a>
                 <ul class="submenu">
                     <li><a href="../Shared/SystemSettings.php">Configurações do Sistema</a></li>
                     <li><a href="../Users/Admin/UserManagerAdmin.php">Gerenciar Usuários</a></li>
                     
                 </ul>
             </li>
 
             <li><a href="../Shared/Support.php">Suporte</a></li>
             <li><a href="#">Sair</a></li>
 
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
 
 
 

    <script src="/Assets/Js/Notification.js"></script>
 
    
</body>
</html>