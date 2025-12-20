<?php
include("db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $status = $_POST['status'] ?? '';
    
    if (!empty($id) && !empty($status)) {
        $stmt = $conn->prepare("UPDATE projetos SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        
        if ($stmt->execute()) {
            echo "ok";
        } else {
            echo "erro";
        }
        
        $stmt->close();
    } else {
        echo "dados_invalidos";
    }
} else {
    echo "metodo_invalido";
}

$conn->close();
?>