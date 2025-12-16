<?php
session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_tipo'], ['Admin','Coordenador'])) {
    header("Location: ../../Public/Login.php");
    exit;
}
