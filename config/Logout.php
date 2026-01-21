<?php
//Sai do sistema e destrói a sessão
session_start();

$_SESSION = [];
session_destroy();

header("Location: ../Public/Login.php");
exit;
