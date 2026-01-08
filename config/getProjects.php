<?php
header('Content-Type: application/json');
include("db.php"); // seu arquivo de conexão

$sql = "SELECT id, nome, descricao, data_inicio, data_fim FROM projetos WHERE arquivado = 0";
$result = $conn->query($sql);

$projects = [];
while ($row = $result->fetch_assoc()) {
    // Pegar alunos do projeto
    $sqlAlunos = "SELECT nome FROM alunos_projeto WHERE projeto_id=".$row['id'];
    $resAlunos = $conn->query($sqlAlunos);
    $alunos = [];
    while($a = $resAlunos->fetch_assoc()) $alunos[] = $a['nome'];

    $projects[] = [
        'id' => $row['id'],
        'nome' => $row['nome'],
        'descricao' => $row['descricao'],
        'data_inicio' => $row['data_inicio'],
        'data_fim' => $row['data_fim'],
        'alunos' => $alunos
    ];
}

echo json_encode($projects);
?>
