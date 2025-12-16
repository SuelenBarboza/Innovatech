<?php
include("../Config/db.php"); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idProjeto   = $_POST['idProjeto'];
    $responsavel = $_POST['responsavel'];
    $observacoes = $_POST['observacoes'];


    $sql = "UPDATE projetos 
            SET status = 'Concluído', 
                responsavel_finalizacao = ?, 
                observacoes_finais = ?, 
                data_conclusao = NOW() 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $responsavel, $observacoes, $idProjeto);

    if ($stmt->execute()) {
        echo "<script>alert('Projeto concluído com sucesso!'); window.location.href='viewlista.php';</script>";
    } else {
        echo "Erro ao concluir projeto: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
