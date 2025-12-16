<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
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