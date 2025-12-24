<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../Assets/css/Header.css">
    <link rel="stylesheet" href="../Assets/css/Footer.css">
    <link rel="stylesheet" href="../Assets/css/RegisterProject.css">

    <title>Cadastro de Projeto</title>
</head>
<body>

<?php include("../Includes/Header.php"); ?>

<section class="form-container">
    <h2>Cadastro de Projetos</h2>

    <form action="../Config/ProcessRegisterProject.php" method="POST">

        <!-- Nome -->
        <div class="form-group">
            <label>Nome do Projeto</label>
            <input type="text" name="nome" required>
        </div>

        <!-- Descrição -->
        <div class="form-group">
            <label>Descrição</label>
            <textarea name="descricao" required></textarea>
        </div>

        <!-- Categoria -->
        <div class="form-group">
            <label>Categoria do Projeto</label>
            <select name="categoria">
                <option value="">- Sem categoria -</option>
                <option value="TCC">TCC</option>
                <option value="Pesquisa">Pesquisa</option>
                <option value="Extensão">Extensão</option>
                <option value="Pessoal">Pessoal</option>
                <option value="Outro">Outro</option>
            </select>
        </div>

        <!-- Datas -->
        <div class="form-group input-group">
            <div class="date-field">
                <label>Data de Início</label>
                <input type="date" name="data_inicio" required>
            </div>

            <div class="date-field">
                <label>Data de Conclusão</label>
                <input type="date" name="data_fim" required>
            </div>
        </div>

        <!-- ALUNOS -->
        <div id="alunos-section">
            <label>Alunos</label>

            <div class="form-group autocomplete">
                <div class="autocomplete-wrapper">
                    <input type="text"
                           class="autocomplete-input aluno-input"
                           autocomplete="off"
                           required>

                    <input type="hidden" name="aluno[]" class="aluno-id">

                    <div class="suggestions"></div>
                </div>
            </div>

            <button type="button" id="addAluno">Adicionar Aluno</button>
        </div>

        <!-- PROFESSORES -->
        <div id="professores-section">
            <label>Professores</label>

            <div class="form-group autocomplete">
                <div class="autocomplete-wrapper">
                    <input type="text"
                           class="autocomplete-input professor-input"
                           autocomplete="off"
                           required>

                    <input type="hidden" name="professor[]" class="professor-id">

                    <div class="suggestions"></div>
                </div>
            </div>

            <button type="button" id="addProfessor">Adicionar Professor</button>
        </div>

        <!-- BOTÕES -->
        <div class="form-actions">
            <button type="submit">Salvar</button>
            <button type="button" onclick="history.back()">Cancelar</button>
        </div>

    </form>
</section>

<?php include("../Includes/Footer.php"); ?>

<script src="../Assets/js/AddAP.js"></script>
<script src="../Assets/js/AutoComplete.js"></script>

</body>
</html>
