<?php
include("../Config/db.php");

// ==========================
// VALIDAÇÃO DO ID
// ==========================
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ViewListProject.php");
    exit;
}

$id = (int) $_GET['id'];

// ==========================
// BUSCA DO PROJETO
// ==========================
$sqlProjeto = "SELECT * FROM projetos WHERE id = ?";
$stmtProjeto = $conn->prepare($sqlProjeto);
$stmtProjeto->bind_param("i", $id);
$stmtProjeto->execute();
$resultProjeto = $stmtProjeto->get_result();

if ($resultProjeto->num_rows === 0) {
    echo "Projeto não encontrado.";
    exit;
}

$projeto = $resultProjeto->fetch_assoc();

// ==========================
// BUSCA DOS ALUNOS
// ==========================
$sqlAlunos = "
    SELECT u.nome
    FROM projeto_aluno pa
    INNER JOIN usuarios u ON pa.usuario_id = u.id
    WHERE pa.projeto_id = ?
";
$stmtAlunos = $conn->prepare($sqlAlunos);
$stmtAlunos->bind_param("i", $id);
$stmtAlunos->execute();
$resultAlunos = $stmtAlunos->get_result();

$alunos = [];
while ($row = $resultAlunos->fetch_assoc()) {
    $alunos[] = $row['nome'];
}

// ==========================
// BUSCA DOS ORIENTADORES
// ==========================
$sqlProfessores = "
    SELECT u.nome
    FROM projeto_orientador po
    INNER JOIN usuarios u ON po.professor_id = u.id
    WHERE po.projeto_id = ?
";
$stmtProfessores = $conn->prepare($sqlProfessores);
$stmtProfessores->bind_param("i", $id);
$stmtProfessores->execute();
$resultProfessores = $stmtProfessores->get_result();

$professores = [];
while ($row = $resultProfessores->fetch_assoc()) {
    $professores[] = $row['nome'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Detalhes do Projeto</title>

  <link rel="stylesheet" href="../Assets/css/Header.css">
  <link rel="stylesheet" href="../Assets/css/Footer.css">
  <link rel="stylesheet" href="../Assets/css/ViewProject.css">
</head>
<body>

<?php include("../Includes/Header.php"); ?>

<section class="form-container">
  <h2>Detalhes do Projeto</h2>

  <div id="dados-projeto">

    <p><strong>Nome:</strong> <?= htmlspecialchars($projeto['nome']) ?></p>

    <p><strong>Categoria:</strong>
      <?= !empty($projeto['categoria']) ? htmlspecialchars($projeto['categoria']) : 'Não definida' ?>
    </p>

    <p><strong>Prioridade:</strong>
      <?= !empty($projeto['prioridade']) ? htmlspecialchars($projeto['prioridade']) : 'Não definida' ?>
    </p>

    <p><strong>Status:</strong>
      <?= !empty($projeto['status']) ? htmlspecialchars($projeto['status']) : 'Não definido' ?>
    </p>

    <p><strong>Data de Início:</strong>
      <?= date("d/m/Y", strtotime($projeto['data_inicio'])) ?>
    </p>

    <p><strong>Data de Conclusão:</strong>
      <?= !empty($projeto['data_fim'])
          ? date("d/m/Y", strtotime($projeto['data_fim']))
          : 'Não definida' ?>
    </p>

    <p><strong>Alunos:</strong><br>
      <?= !empty($alunos) ? implode(", ", array_map("htmlspecialchars", $alunos)) : 'Não definido' ?>
    </p>

    <p><strong>Orientador(es):</strong><br>
      <?= !empty($professores) ? implode(", ", array_map("htmlspecialchars", $professores)) : 'Não definido' ?>
    </p>

    <p><strong>Descrição:</strong><br>
      <?= nl2br(htmlspecialchars($projeto['descricao'])) ?>
    </p>

  </div>

  <!-- AÇÕES -->
  <div class="acoes-projeto">
    <a href="EditProject.php?id=<?= $projeto['id'] ?>" class="btn-editar">
      ✏️ Editar Projeto
    </a>

    <a href="ViewListProject.php" class="btn-voltar">
      ⬅️ Voltar para a Lista
    </a>
  </div>
</section>

<?php include("../Includes/Footer.php"); ?>

</body>
</html>
