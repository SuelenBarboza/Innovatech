<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'Admin') {
    header("Location: ../../Public/Login.php");
    exit;
}
