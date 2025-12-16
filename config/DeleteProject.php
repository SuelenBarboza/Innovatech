<?php
include("../Config/db.php");

$id = $_GET['id'];

$sql = "DELETE FROM projetos WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: ../ViewListProject.php?msg=deletado");
} else {
    echo "Erro: " . $conn->error;
}

$stmt->close();
$conn->close();
