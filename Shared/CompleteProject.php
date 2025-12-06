<?php
include("../Config/Conexao.php"); 


$idProjeto = $_GET['id'] ?? null;

$nomeProjeto = "";

if ($idProjeto) {
    $sql = "SELECT nome FROM projetos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idProjeto);
    $stmt->execute();
    $stmt->bind_result($nomeProjeto);
    $stmt->fetch();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conclusão do Projeto</title>
    <link rel="stylesheet" href="../Assets/css/Header.css">
    <link rel="stylesheet" href="../Assets/css/Footer.css">
    <link rel="stylesheet" href="../Assets/css/CompleteProject.css"> 
</head>
<body>
    <?php include("../Includes/Header.php"); ?>

    <div class="form-container">
        <h1>Concluir Projeto</h1>

        <form id="formConclusao" method="POST" action="SalvarConclusao.php">
            <div class="form-group">
                <label for="nomeProjeto">Nome do Projeto</label>
                <input type="text" id="nomeProjeto" name="nomeProjeto" value="<?php echo $nomeProjeto; ?>" readonly>
                <input type="hidden" name="idProjeto" value="<?php echo $idProjeto; ?>"> 
            </div>

            <div class="form-group">
                <label for="responsavel">Finalizado por</label>
                <input type="text" id="responsavel" name="responsavel" placeholder="Digite seu nome">
                <!-- Colocar preenchido automaticamente com login depois-->
            </div>

            <div class="form-group">
                <label for="observacoes">Observações finais</label>
                <textarea id="observacoes" name="observacoes" placeholder="Escreva algo sobre o encerramento do projeto..."></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" id="salvar">Salvar</button>
                <button type="button" id="cancelar" onclick="window.location.href='viewlista.php'">Cancelar</button>
            </div>
        </form>
    </div>

    <?php include("../Includes/Footer.php"); ?>
</body>
</html>
