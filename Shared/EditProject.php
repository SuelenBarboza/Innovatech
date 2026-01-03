<?php
// Edita o projeto existente
include("../Config/db.php");

if (!isset($_GET['id'])) {
    header("Location: ViewProjects.php");
    exit;
}

$id = intval($_GET['id']);

/* ================= PROJETO ================= */
$sql = "SELECT * FROM projetos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Projeto não encontrado.";
    exit;
}

$projeto = $result->fetch_assoc();

/* ================= ALUNOS ================= */
$sqlAlunos = "
    SELECT u.id, u.nome
    FROM projeto_aluno pa
    INNER JOIN usuarios u ON u.id = pa.usuario_id
    WHERE pa.projeto_id = ?
";
$stmt = $conn->prepare($sqlAlunos);
$stmt->bind_param("i", $id);
$stmt->execute();
$alunos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

/* ================= PROFESSORES ================= */
$sqlProf = "
    SELECT u.id, u.nome
    FROM projeto_orientador po
    INNER JOIN usuarios u ON u.id = po.professor_id
    WHERE po.projeto_id = ?
";
$stmt = $conn->prepare($sqlProf);
$stmt->bind_param("i", $id);
$stmt->execute();
$professores = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>


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

    <title>Editar Projeto</title>
</head>
<body>

<?php include("../Includes/Header.php"); ?>

<section class="form-container">
    <h2>Editar Projeto</h2>

    <form action="../Config/ProcessEditProject.php" method="POST">

        <input type="hidden" name="id_projeto" value="<?= $projeto['id'] ?>">

        <!-- Nome -->
        <div class="form-group">
            <label>Nome do Projeto</label>
            <input type="text" name="nome" value="<?= $projeto['nome'] ?>" required>
        </div>

        <!-- Descrição -->
        <div class="form-group">
            <label>Descrição</label>
            <textarea name="descricao" required><?= $projeto['descricao'] ?></textarea>
        </div>

        <!-- Categoria -->
        <div class="form-group">
            <label>Categoria</label>
            <select name="categoria">
                <option value="">- Sem categoria -</option>
                <?php
                $cats = ["TCC","Pesquisa","Extensão","Pessoal","Outro"];
                foreach ($cats as $cat):
                ?>
                <option value="<?= $cat ?>" <?= $projeto['categoria']==$cat ? "selected" : "" ?>>
                    <?= $cat ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Datas -->
        <div class="form-group input-group">
            <div class="date-field">
                <label>Data de Início</label>
                <input type="date" name="data_inicio" value="<?= $projeto['data_inicio'] ?>" required>
            </div>
            <div class="date-field">
                <label>Data de Conclusão</label>
                <input type="date" name="data_fim" value="<?= $projeto['data_fim'] ?>" required>
            </div>
        </div>

        <!-- ALUNOS -->
        <div id="alunos-section">
            <label>Alunos</label>

            <?php foreach ($alunos as $aluno): ?>
            <div class="form-group autocomplete">
                <div class="autocomplete-wrapper">
                    <input type="text"
                           class="autocomplete-input aluno-input"
                           value="<?= $aluno['nome'] ?>"
                           autocomplete="off"
                           required>

                    <input type="hidden"
                           name="aluno[]"
                           class="aluno-id"
                           value="<?= $aluno['id'] ?>">

                    <div class="suggestions"></div>
                </div>
            </div>
            <?php endforeach; ?>

            <button type="button" id="addAluno">Adicionar Aluno</button>
        </div>

        <!-- PROFESSORES -->
        <div id="professores-section">
            <label>Professores</label>

            <?php foreach ($professores as $prof): ?>
            <div class="form-group autocomplete">
                <div class="autocomplete-wrapper">
                    <input type="text"
                           class="autocomplete-input professor-input"
                           value="<?= $prof['nome'] ?>"
                           autocomplete="off"
                           required>

                    <input type="hidden"
                           name="professor[]"
                           class="professor-id"
                           value="<?= $prof['id'] ?>">

                    <div class="suggestions"></div>
                </div>
            </div>
            <?php endforeach; ?>

            <button type="button" id="addProfessor">Adicionar Professor</button>
        </div>

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
