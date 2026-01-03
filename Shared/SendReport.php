<?php
// Envia relatorio para os professores 
session_start();
include("../Config/db.php");


if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Public/Login.php");
    exit;
}

$aluno_id = $_SESSION['usuario_id'];


$sqlProjetos = "
 SELECT DISTINCT p.id, p.nome
FROM projetos p
LEFT JOIN projeto_aluno pa ON pa.projeto_id = p.id
LEFT JOIN projeto_orientador po ON po.projeto_id = p.id
WHERE
    pa.usuario_id = ?
    OR po.professor_id = ?
    OR p.criador_id = ?
ORDER BY p.nome;
";
$stmt = $conn->prepare($sqlProjetos);
$stmt->bind_param("iii", $aluno_id, $aluno_id, $aluno_id);

$stmt->execute();
$resultProjetos = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Enviar Relatório</title>

<link rel="stylesheet" href="../Assets/css/Header.css">
<link rel="stylesheet" href="../Assets/css/Footer.css">
<link rel="stylesheet" href="../Assets/css/Report.css">

<script>
async function carregarProfessores(projetoId) {
    const selectProfessor = document.getElementById("professor_id");
    selectProfessor.innerHTML = '<option value="">Carregando...</option>';

    if (!projetoId) {
        selectProfessor.innerHTML = '<option value="">Selecione um projeto primeiro</option>';
        return;
    }

    try {
        const response = await fetch(`../Config/GetTeachersByProjeto.php?projeto_id=${projetoId}`);
        const dados = await response.json();

        selectProfessor.innerHTML = '<option value="">Selecione um professor</option>';

        dados.forEach(prof => {
            const option = document.createElement("option");
            option.value = prof.id;
            option.textContent = prof.nome;
            selectProfessor.appendChild(option);
        });

    } catch (erro) {
        selectProfessor.innerHTML = '<option value="">Erro ao carregar</option>';
    }
}
</script>

</head>
<body>

<?php include("../Includes/Header.php"); ?>

<section class="form-container">
<h2>📄 Enviar Relatório ao Professor</h2>

<form method="POST" action="../Config/ProcessSendReport.php">

  <div class="form-group">
    <label>Projeto</label>
    <select name="projeto_id" required onchange="carregarProfessores(this.value)">
      <option value="">Selecione um projeto</option>
      <?php while ($p = $resultProjetos->fetch_assoc()): ?>
        <option value="<?= $p['id'] ?>">
          <?= htmlspecialchars($p['nome']) ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <div class="form-group">
    <label>Professor</label>
    <select name="professor_id" id="professor_id" required>
      <option value="">Selecione um projeto primeiro</option>
    </select>
  </div>

  <div class="form-group">
    <label>Título do Relatório</label>
    <input type="text" name="titulo" required>
  </div>

  <div class="form-group">
    <label>Descrição</label>
    <textarea name="descricao" rows="6" required></textarea>
  </div>

  <div class="form-actions">
    <button type="submit">📤 Enviar Relatório</button>
    <a href="Home.php" class="btn-voltar">⬅ Voltar</a>
  </div>

</form>
</section>

<?php include("../Includes/Footer.php"); ?>
</body>
</html>
