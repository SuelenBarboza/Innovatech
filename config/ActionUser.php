<?php
require_once 'Protect.php';
require_once '../../Config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: validate_user.php");
    exit;
}

$id = (int) $_POST['id'];
$action = $_POST['action'];

$db = (new Database())->getConnection();

switch ($action) {
    case 'approve':
        $sql = "UPDATE usuarios SET aprovado = 1, ativo = 1 WHERE id = ?";
        break;

    case 'reject':
        $sql = "DELETE FROM usuarios WHERE id = ?";
        break;

    case 'block':
        $sql = "UPDATE usuarios SET ativo = 0 WHERE id = ?";
        break;

    default:
        header("Location: validate_user.php");
        exit;
}

$stmt = $db->prepare($sql);
$stmt->execute([$id]);

header("Location: ValidateUser.php");
exit;
