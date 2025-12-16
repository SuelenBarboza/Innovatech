<?php
include("../Config/db.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nomeProjeto = $_POST['nomeProjeto'];
    $nomeMembro = $_POST['nomeMembro'];

    // Primeiro, pegar o id do membro
    $sql_select = "SELECT id FROM membros WHERE nome=? AND projeto=?";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param("ss", $nomeMembro, $nomeProjeto);
    $stmt_select->execute();
    $result = $stmt_select->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id = $row['id'];

        // Deletar o membro
        $sql_delete = "DELETE FROM membros WHERE id=?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $id);

        if ($stmt_delete->execute()) {
            header("Location: ../ViewListMembers.php?msg=deletado");
            exit();
        } else {
            echo "Erro ao deletar: " . $conn->error;
        }

        $stmt_delete->close();
    } else {
        echo "Membro não encontrado.";
    }

    $stmt_select->close();
    $conn->close();
}
?>