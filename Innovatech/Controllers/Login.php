<?php
session_start();
require_once "../Config/db.php";

$email = $_POST['email'];
$senha = $_POST['password'];

// BUSCAR USUÁRIO
$sql = "SELECT * FROM registers WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$status = "falha"; // status padrão
$usuario_id = null;

if ($result->num_rows === 0) {
    // USUÁRIO NÃO EXISTE
    $status = "falha";
} else {
    $user = $result->fetch_assoc();
    $usuario_id = $user['id'];

    if (password_verify($senha, $user['senha'])) {
        // LOGIN OK
        $status = "sucesso";

        // ATUALIZAR ULTIMO LOGIN
        $update = $conn->prepare("UPDATE registers SET ultimo_login = NOW() WHERE id = ?");
        $update->bind_param("i", $usuario_id);
        $update->execute();

        // CRIAR SESSÃO
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nome'] = $user['nome'];
        $_SESSION['usuario_tipo'] = $user['tipo'];
    }
}

// REGISTRAR NO LOGINS
$ip = $_SERVER['REMOTE_ADDR'];
$sqlLog = "INSERT INTO logins (usuario_id, email, status, ip) VALUES (?, ?, ?, ?)";
$stmtLog = $conn->prepare($sqlLog);
$stmtLog->bind_param("isss", $usuario_id, $email, $status, $ip);
$stmtLog->execute();

// REDIRECIONAR
if ($status === "sucesso") {
    header("Location: ../Public/Home.html");
    exit;
} else {
    echo "Login falhou. E-mail ou senha incorretos.";
}

$stmt->close();
$stmtLog->close();
$conn->close();
