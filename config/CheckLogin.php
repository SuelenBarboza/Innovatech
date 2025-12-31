<?php
// Para impedir que um aluno entre em páginas/pastas que não são dele, como admin.
session_start();

function isLoggedIn() {
    return isset($_SESSION['usuario_id']);
}


function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../Login.php");
        exit();
    }
}

function getUserRole() {
    return $_SESSION['user_roles'] ?? [];
}

function hasRole($role) {
    return in_array($role, $_SESSION['user_roles'] ?? []);
}
?>