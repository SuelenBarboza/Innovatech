<?php
include("../Config/db.php"); 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nomeProjeto = $_POST['nomeProjeto'];
    $nomeMembro  = $_POST['nomeMembro'];
    $emailMembro = $_POST['emailMembro'];
    $funcaoMembro = $_POST['funcaoMembro'];

    $sqlProjeto = "SELECT id FROM projetos WHERE nome = ?";
    $stmtProjeto = $conn->prepare($sqlProjeto);
    $stmtProjeto->bind_param("s", $nomeProjeto);
    $stmtProjeto->execute();
    $result = $stmtProjeto->get_result();

    if ($result->num_rows > 0) {
        $projeto = $result->fetch_assoc();
        $idProjeto = $projeto['id'];

        $sql = "INSERT INTO membros (nome, email, funcao, id_projeto) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nomeMembro, $emailMembro, $funcaoMembro, $idProjeto);

        if ($stmt->execute()) {
            header("Location: ../ViewListMembers.php?msg=sucesso");
            exit();
        } else {
            echo "Erro ao adicionar: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "Projeto não encontrado!";
    }

    $stmtProjeto->close();
    $conn->close();
}
?>
