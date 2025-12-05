<?php
include("../Config/Conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idTarefa     = $_POST['idTarefa'];
    $concluidoPor = $_POST['concluidoPor'];
    $comentario   = $_POST['comentarioTarefa'];

    $sql = "UPDATE tarefas 
            SET status = 'Concluída',
                concluido_por = ?,
                comentario_final = ?,
                data_conclusao = NOW()
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $concluidoPor, $comentario, $idTarefa);

    if ($stmt->execute()) {
        echo "<script>alert('Tarefa concluída com sucesso!'); window.location.href='ViewTasks.php';</script>";
    } else {
        echo "Erro: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
