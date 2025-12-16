<?php
include("../Config/db.php"); 

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
    $alunos = $_POST['aluno']; 
    $professores = $_POST['professor']; 


    $sql = "INSERT INTO projetos (nome, descricao, data_inicio, data_fim) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nome, $descricao, $data_inicio, $data_fim);

    if ($stmt->execute()) {
        $projeto_id = $conn->insert_id; 

        
        foreach($alunos as $aluno) {
            $sqlAluno = "INSERT INTO projeto_aluno (projeto_id, nome_aluno) VALUES (?, ?)";
            $stmtAluno = $conn->prepare($sqlAluno);
            $stmtAluno->bind_param("is", $projeto_id, $aluno);
            $stmtAluno->execute();
            $stmtAluno->close();
        }

    
        foreach($professores as $prof) {
            $sqlProf = "INSERT INTO projeto_professor (projeto_id, nome_professor) VALUES (?, ?)";
            $stmtProf = $conn->prepare($sqlProf);
            $stmtProf->bind_param("is", $projeto_id, $prof);
            $stmtProf->execute();
            $stmtProf->close();
        }

        header("Location: ../ViewListProject.php?msg=sucesso");
        exit();
    } else {
        echo "Erro ao salvar o projeto: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
