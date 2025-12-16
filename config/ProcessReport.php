<?php
include("../Config/db.php"); 

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nomeProjeto = $_POST['nomeProjeto'];
    $periodoInicio = $_POST['periodoInicio'];
    $periodoFim = $_POST['periodoFim'];
    $descricao = $_POST['descricaoDesempenho'];

    $sql = "INSERT INTO relatorios_desempenho (projeto, periodo_inicio, periodo_fim, descricao) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nomeProjeto, $periodoInicio, $periodoFim, $descricao);

    if ($stmt->execute()) {
        header("Location: ../ViewLista.html?msg=sucesso");
        exit();
    } else {
        echo "Erro ao salvar o relatório: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
