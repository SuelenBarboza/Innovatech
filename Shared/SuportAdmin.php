<?php
// Admin  responde e conclui chamados de suporte
session_start();
include("../Config/db.php"); // Conexão

// Verifica admin
if (!isset($_SESSION['usuario_tipo']) || strtolower(trim($_SESSION['usuario_tipo'])) !== 'admin') {
    die("Acesso negado.");
}
// ========================
// CONCLUIR CHAMADO
// ========================
if (isset($_GET['concluir'])) {

    $chamado_id = (int) $_GET['concluir'];

    $sql = "
        UPDATE suporte_chamados
        SET status = 'concluido'
        WHERE id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $chamado_id);
    $stmt->execute();
    $stmt->close();

    header("Location: SupportAdmin.php");
    exit;
}

// ========================
// RESPONDER CHAMADO
// ========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['chamado_id'])) {

    $chamado_id = (int) $_POST['chamado_id'];
    $resposta = trim($_POST['resposta']);

    if (!empty($resposta)) {
        $sql = "
            UPDATE suporte_chamados
            SET 
                resposta = ?,
                status = 'respondido',
                data_resposta = NOW()
            WHERE id = ?
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $resposta, $chamado_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: SupportAdmin.php");
    exit;
}

// ========================
// Pega todos os chamados
// ========================
$result = $conn->query("SELECT * FROM suporte_chamados ORDER BY data_abertura DESC");

// Checa se a consulta funcionou
if(!$result) {
    die("Erro ao buscar chamados: " . $conn->error);
}

// Inclui o header depois
include("../Includes/Header.php");
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Chamados de Suporte</title>
    <!-- CSS com caminhos absolutos -->
    <link rel="stylesheet" href="/Innovatech/Assets/css/Header.css">
    <link rel="stylesheet" href="/Innovatech/Assets/css/Footer.css">
    <link rel="stylesheet" href="/Innovatech/Assets/css/SupportAdmin.css">
    <style>
        /* Estilo básico se SupportAdmin.css não existir */
        .support-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .support-table th, .support-table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .btn { padding: 4px 8px; margin-right: 4px; text-decoration: none; background: #4CAF50; color: #fff; border-radius: 4px; }
        .btn-delete { background: #f44336; }
        .btn-concluir { background: #2196F3; }
        .modal { display: none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; }
        .modal-content { background:#fff; padding:20px; border-radius:8px; width: 400px; max-width: 90%; }
    </style>
</head>
<body>
<div class="container">
    <h1>Chamados de Suporte</h1>

    <table class="support-table">
        <thead>
            <tr>
                <th>Usuário</th>
                <th>Email</th>
                <th>Assunto</th>
                <th>Mensagem</th>
                <th>Resposta</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['usuario_nome']) ?></td>
                        <td><?= htmlspecialchars($row['usuario_email']) ?></td>
                        <td><?= htmlspecialchars($row['assunto']) ?></td>
                        <td><?= nl2br(htmlspecialchars($row['mensagem'])) ?></td>
                        <td><?= $row['resposta'] ? nl2br(htmlspecialchars($row['resposta'])) : '-' ?></td>
                        <td><?= ucfirst($row['status']) ?></td>
                        <td>
                            <?php if($row['status'] !== 'concluido'): ?>
                                <a href="#" class="btn" onclick="openModal(<?= $row['id'] ?>)">Responder</a>
                                <a href="?concluir=<?= $row['id'] ?>" class="btn btn-concluir">Concluir</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7">Nenhum chamado encontrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal de Resposta -->
<div id="modal" class="modal">
    <div class="modal-content">
        <h3>Responder Chamado</h3>
        <form method="POST" action="">
            <input type="hidden" name="chamado_id" id="chamado_id">
            <textarea name="resposta" placeholder="Digite sua resposta" required style="width:100%;height:120px;"></textarea>
            <div style="margin-top:10px;">
                <button type="submit" class="btn">Enviar Resposta</button>
                <button type="button" class="btn btn-delete" onclick="closeModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById('chamado_id').value = id;
    document.getElementById('modal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('modal').style.display = 'none';
}
</script>

<?php include("../Includes/Footer.php"); ?>
</body>
</html>
