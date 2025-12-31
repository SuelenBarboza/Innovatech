<?php
// Lista de tarefas do usuário logado
include("../Config/db.php");
session_start();

if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não logado.");
}

$usuario_id = (int) $_SESSION['usuario_id'];

// ================= QUERY =================
// Puxa tarefas dos projetos que o usuário participa
$sql = "
SELECT t.*,
       tu.prioridade AS prioridade_usuario,
       COALESCE(tu.arquivado,0) AS arquivado_usuario,
       p.nome AS projeto_nome
FROM tarefas t
INNER JOIN projetos p ON p.id = t.projeto_id
LEFT JOIN tarefa_usuario tu ON tu.tarefa_id = t.id AND tu.usuario_id = ?
WHERE p.criador_id = ?
   OR EXISTS (SELECT 1 FROM projeto_aluno pa WHERE pa.projeto_id = p.id AND pa.usuario_id = ?)
   OR EXISTS (SELECT 1 FROM projeto_orientador po WHERE po.projeto_id = p.id AND po.professor_id = ?)
ORDER BY t.id DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $usuario_id, $usuario_id, $usuario_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Tarefas</title>
    
    <!-- CSS Stylesheets -->
    <link rel="stylesheet" href="../Assets/css/Header.css">
    <link rel="stylesheet" href="../Assets/css/Footer.css">
    <!-- <link rel="stylesheet" href="../Assets/css/ViewListProject.css"> -->
    <link rel="stylesheet" href="../Assets/css/ViewListTasks.css">

    

</head>
<body>

<?php include("../Includes/Header.php"); ?>

<main>
    <h1>Lista de Tarefas</h1>
    
    <!-- ================= FILTROS ================= -->
    <div class="filtros">
        <select id="filtro-status">
            <option value="">Todos os Status</option>
            <option value="Planejamento">Planejamento</option>
            <option value="Em Andamento">Em Andamento</option>
            <option value="Concluído">Concluído</option>
        </select>
        
        <select id="filtro-prioridade">
            <option value="">Todas as Prioridades</option>
            <option value="Alta">Alta</option>
            <option value="Média">Média</option>
            <option value="Baixa">Baixa</option>
        </select>
    </div>
    
    <!-- ================= TAREFAS ================= -->
    <table id="tabela-tarefas">
        <thead>
            <tr>
                <th>Nome da Tarefa</th>
                <th>Projeto</th>
                <th>Prioridade</th>
                <th>Status</th>
                <th>Prazo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <!-- JS vai renderizar -->
        </tbody>
    </table>
    
    <button id="btnToggleArquivar">📂 Ver Tarefas Arquivadas</button>
    
    <div id="containerArquivar" style="display:none;">
        <table id="tabela-ocultos">
            <thead>
                <tr>
                    <th>Nome da Tarefa</th>
                    <th>Projeto</th>
                    <th>Prioridade</th>
                    <th>Status</th>
                    <th>Prazo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <!-- JS vai renderizar -->
            </tbody>
        </table>
    </div>
</main>

<!-- ================= DADOS INVISÍVEIS PARA O JS ================= -->
<table style="display:none">
    <tbody id="dados-tarefas">
        <?php
        $ids_processados = [];
        while ($row = $result->fetch_assoc()) {
            $id = (int) $row['id'];
            if (in_array($id, $ids_processados)) continue;
            $ids_processados[] = $id;
            
            $nome_tarefa = htmlspecialchars($row['nome']);
            $projeto = htmlspecialchars($row['projeto_nome']);
            
            // Usa "Não definido" se prioridade_usuario for nulo
            $prioridade = $row['prioridade_usuario'] ? htmlspecialchars($row['prioridade_usuario']) : "Não definido";
            
            // Usa "Não definido" se status for nulo ou vazio
            $status = ($row['status'] && $row['status'] !== "") ? htmlspecialchars($row['status']) : "Não definido";
            
            $prazo = $row['data_fim'] ? date("d/m/Y", strtotime($row['data_fim'])) : "Não definido";
            $descricao = htmlspecialchars($row['descricao'] ?? "");
            $arquivado = (int) ($row['arquivado_usuario'] ?? 0);
            
            echo "
            <tr
                data-id='$id'
                data-nome-tarefa='$nome_tarefa'
                data-projeto='$projeto'
                data-prioridade='$prioridade'
                data-status='$status'
                data-prazo='$prazo'
                data-descricao='$descricao'
                data-arquivado='$arquivado'
            ></tr>";
        }
        ?>
    </tbody>
</table>

<!-- ================= MODAL ================= -->
<div id="modalDetalhes" class="modal" style="display:none;">
    <div class="modal-conteudo">
        <span class="fechar">&times;</span>
        <h2>Detalhes da Tarefa</h2>
        <div class="modal-detalhes">
            <p><strong>Nome da Tarefa:</strong> <span id="detalhe-nome-tarefa"></span></p>
            <p><strong>Projeto:</strong> <span id="detalhe-projeto"></span></p>
            <p><strong>Prazo:</strong> <span id="detalhe-prazo"></span></p>
            <p><strong>Prioridade:</strong> <span id="detalhe-prioridade"></span></p>
            <p><strong>Status:</strong> <span id="detalhe-status"></span></p>
            <p><strong>Descrição:</strong></p>
            <div id="detalhe-descricao" class="descricao-texto"></div>
        </div>
    </div>
</div>

<?php include("../Includes/Footer.php"); ?>
<script src="../Assets/js/ViewListTasks.js"></script>

</body>
</html>