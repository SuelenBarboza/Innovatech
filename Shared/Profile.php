<?php
// Página de perfil do usuário
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Public/Login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Perfil</title>

    <link rel="stylesheet" href="../Assets/css/Header.css">
    <link rel="stylesheet" href="../Assets/css/Footer.css">
    <link rel="stylesheet" href="../Assets/css/Profile.css">
    
</head>
<body>

<?php include("../Includes/Header.php"); ?>

<div class="profile-container">

    <!-- TOPO DO PERFIL -->
    <div class="profile-header">
        <img 
            src="<?= $_SESSION['usuario_foto'] ?? '../Assets/img/avatar.png' ?>" 
            alt="Foto do usuário"
        >

        <div>
            <h2><?= $_SESSION['usuario_nome']; ?></h2>
            <div class="email"><?= $_SESSION['usuario_email']; ?></div>
            <div class="tipo"><?= $_SESSION['usuario_tipo']; ?></div>
        </div>
    </div>

    <!-- CONTEÚDO -->
    <div class="profile-content">

        <!-- INFORMAÇÕES -->
        <div class="profile-box">
            <h3>Informações do Usuário</h3>

            <p>
                <strong>Nome completo:</strong><br>
                <?= $_SESSION['usuario_nome']; ?>
            </p>

            <p>
                <strong>Email:</strong><br>
                <?= $_SESSION['usuario_email']; ?>
            </p>

            <p>
                <strong>Tipo de usuário:</strong><br>
                <?= $_SESSION['usuario_tipo']; ?>
            </p>
        </div>

        <!-- FOTO -->
        <div class="profile-box">
            <h3>Foto de Perfil</h3>

            <!-- Mensagem de erro ou sucesso -->
            <?php
            if (isset($_GET['erro'])) {
                echo '<div class="mensagem erro">';
                switch ($_GET['erro']) {
                    case 'tipo_invalido':
                        echo 'Formato de imagem inválido! Apenas JPG, PNG ou WEBP são permitidos.';
                        break;
                    case 'upload_falhou':
                        echo 'Erro ao fazer upload da foto. Tente novamente.';
                        break;
                    case 'nenhum_arquivo':
                        echo 'Nenhum arquivo enviado.';
                        break;
                }
                echo '</div>';
            }

            if (isset($_GET['sucesso']) && $_GET['sucesso'] === 'foto_salva') {
                echo '<div class="mensagem sucesso">Foto atualizada com sucesso!</div>';
            }
            ?>

            <form 
                action="../Config/SavePhoto.php" 
                method="POST" 
                enctype="multipart/form-data"
            >
                <label>Escolha uma nova foto</label><br>
                <input type="file" name="foto" accept="image/*" required>

                <button type="submit">Salvar foto</button>
            </form>
        </div>

    </div>
</div>

<?php include("../Includes/Footer.php"); ?>

</body>
</html>
