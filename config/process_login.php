<?php
session_start();
include_once 'config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Conectar ao banco de dados
    $database = new Database();
    $db = $database->getConnection();
    
    // Preparar e executar a consulta
    $query = "SELECT u.id, u.nome, u.email, u.senha, u.aprovado, u.ativo, 
                     GROUP_CONCAT(r.nome) as roles
              FROM usuarios u
              LEFT JOIN usuarios_roles ur ON u.id = ur.usuario_id
              LEFT JOIN roles r ON ur.role_id = r.id
              WHERE u.email = :email
              GROUP BY u.id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    
    if ($stmt->rowCount() == 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $id = $row['id'];
        $nome = $row['nome'];
        $hashed_password = $row['senha'];
        $aprovado = $row['aprovado'];
        $ativo = $row['ativo'];
        $roles = $row['roles'] ? explode(',', $row['roles']) : [];
        
        // Verificar se a conta está ativa
        if (!$ativo) {
            header("Location: Login.php?error=inactive&email=" . urlencode($email));
            exit();
        }
        
        // Verificar se a conta está aprovada
        if (!$aprovado) {
            header("Location: Login.php?error=not_approved&email=" . urlencode($email));
            exit();
        }
        
        // Verificar a senha
        if (password_verify($password, $hashed_password)) {
            // Senha correta, iniciar sessão
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $nome;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_roles'] = $roles;
            
            // Lembrar usuário se solicitado
            if (isset($_POST['remember_me'])) {
                $token = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));
                
                // Salvar token no banco de dados
                $query = "INSERT INTO user_tokens (user_id, token, expiry) VALUES (:user_id, :token, :expiry)
                          ON DUPLICATE KEY UPDATE token = :token, expiry = :expiry";
                $stmt = $db->prepare($query);
                $stmt->bindParam(":user_id", $id);
                $stmt->bindParam(":token", $token);
                $stmt->bindParam(":expiry", $expiry);
                $stmt->execute();
                
                // Definir cookie
                setcookie("remember_token", $token, time() + (30 * 24 * 60 * 60), "/");
            }
            
            // Redirecionar para a página inicial
            header("Location: Innovatechmain/Public/Home.php");
            exit();
        } else {
            // Senha incorreta
            header("Location: Login.php?error=invalid_credentials&email=" . urlencode($email));
            exit();
        }
    } else {
        // Usuário não encontrado
        header("Location: Login.php?error=invalid_credentials");
        exit();
    }
} else {
    // Método não permitido
    header("Location: Login.php");
    exit();
}
?>