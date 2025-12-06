<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Relatório de Desempenho</title>
    <link rel="stylesheet" href="../Assets/css/Header.css" />
    <link rel="stylesheet" href="../Assets/css/Footer.css" />
    <link rel="stylesheet" href="../Assets/css/PerformanceReport.css" />
</head>
<body>
    <?php include("../Includes/Header.php"); ?>

    <div class="form-container">
        <h1>Relatório de Desempenho</h1>

        <form id="formPerformanceReport" method="POST" action="../config/process_report.php">
            <div class="form-group">
                <label for="nomeProjeto">Nome do Projeto:</label>
                <input type="text" id="nomeProjeto" name="nomeProjeto" placeholder="Digite o nome do projeto" required />
            </div>

            <div class="form-group">
                <label for="periodoInicio">Período Início:</label>
                <input type="date" id="periodoInicio" name="periodoInicio" required />
            </div>

            <div class="form-group">
                <label for="periodoFim">Período Fim:</label>
                <input type="date" id="periodoFim" name="periodoFim" required />
            </div>

            <div class="form-group">
                <label for="descricaoDesempenho">Descrição do Desempenho:</label>
                <textarea id="descricaoDesempenho" name="descricaoDesempenho" placeholder="Descreva o desempenho observado" required></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" id="enviarRelatorioDesempenho">Enviar</button>
                <button type="button" id="cancelarRelatorioDesempenho" onclick="window.location.href='ViewLista.php'">Cancelar</button>
            </div>
        </form>
    </div>

   <?php include("../Includes/Footer.php"); ?>
</body>
</html>
