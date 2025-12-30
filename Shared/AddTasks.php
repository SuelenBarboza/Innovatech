<?php 
include("../Config/db.php");
session_start();

if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não logado.");
}

$usuario_id = (int) $_SESSION['usuario_id'];

// Busca projetos que o usuário participa (Criador, Aluno ou Orientador)
$sqlProjetos = "
SELECT DISTINCT p.id, p.nome
FROM projetos p
LEFT JOIN projeto_aluno pa ON pa.projeto_id = p.id
LEFT JOIN projeto_orientador po ON po.projeto_id = p.id
WHERE p.criador_id = ? OR pa.usuario_id = ? OR po.professor_id = ?
ORDER BY p.nome ASC
";

$stmt = $conn->prepare($sqlProjetos);
$stmt->bind_param("iii", $usuario_id, $usuario_id, $usuario_id);
$stmt->execute();
$resultProjetos = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<link rel="stylesheet" href="../Assets/css/Header.css"> 
<link rel="stylesheet" href="../Assets/css/Footer.css">
<link rel="stylesheet" href="../Assets/css/AddTasks.css">
<title>Adicionar Tarefa</title>
</head>
<body>
<?php include("../Includes/Header.php"); ?>

<div class="form-container">
  <h1>Adicionar Nova Tarefa</h1>
  <form action="../Config/ProcessRegisterTasks.php" method="POST">

    <div class="form-group">
      <label for="projeto">Projeto:</label>
      <select id="projeto" name="projeto" required>
        <option value="">Selecione um projeto</option>
        <?php while($p = $resultProjetos->fetch_assoc()) { ?>
          <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nome']) ?></option>
        <?php } ?>
      </select>
    </div>

    <div class="form-group">
      <label for="aluno">Aluno Responsável:</label>
      <select id="aluno" name="aluno" required>
        <option value="">Selecione o projeto primeiro</option>
      </select>
    </div>

    <div class="form-group">
      <label for="nome_tarefa">Nome da Tarefa:</label>
      <input type="text" id="nome_tarefa" name="nome_tarefa" required>
    </div>

    <div class="form-group">
      <label for="descricao">Descrição:</label>
      <textarea id="descricao" name="descricao" required></textarea>
    </div>

    <div class="form-group input-group">
      <div class="date-field">
        <label for="data_inicio">Data de Início:</label>
        <input type="date" id="data_inicio" name="data_inicio">
      </div>

      <div class="date-field">
        <label for="data_fim">Data de Fim:</label>
        <input type="date" id="data_fim" name="data_fim">
      </div>
    </div>

    <div class="form-actions">
      <button type="submit">Salvar</button>
      <button type="button" onclick="window.location.href='../Public/Home.php'">Cancelar</button>
    </div>

  </form>
</div>

<?php include("../Includes/Footer.php"); ?>

<script>
// Carrega alunos ao selecionar o projeto
document.getElementById('projeto').addEventListener('change', function() {
    const projetoId = this.value;
    const alunoSelect = document.getElementById('aluno');
    alunoSelect.innerHTML = '<option>Carregando...</option>';

    if(!projetoId) {
        alunoSelect.innerHTML = '<option>Selecione o projeto primeiro</option>';
        return;
    }

    fetch('../Config/GetAlunosProjeto.php?projeto_id=' + projetoId)
        .then(res => res.json())
        .then(data => {
            if(data.length === 0) {
                alunoSelect.innerHTML = '<option>Nenhum aluno encontrado</option>';
                return;
            }
            alunoSelect.innerHTML = '<option value="">Selecione um aluno</option>';
            data.forEach(aluno => {
                const option = document.createElement('option');
                option.value = aluno.id;
                option.textContent = aluno.nome;
                alunoSelect.appendChild(option);
            });
        })
        .catch(err => {
            console.error(err);
            alunoSelect.innerHTML = '<option>Erro ao carregar alunos</option>';
        });
});
</script>

</body>
</html>
