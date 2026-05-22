<?php
// Usado para visualizar os detalhes de um chamado específico pelo usuário
session_start();
include("../Config/db.php");

// ========================
// VERIFICA LOGIN
// ========================
if (!isset($_SESSION['usuario_id'])) {
    die("Acesso negado.");
}

// ========================
// VERIFICA ID DO CHAMADO
// ========================
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Chamado inválido.");
}

$usuario_id = $_SESSION['usuario_id'];
$chamado_id = (int) $_GET['id'];

// ========================
// BUSCA O CHAMADO
// ========================
$sql = "
    SELECT assunto, mensagem, resposta, status, data_abertura
    FROM suporte_chamados
    WHERE id = ? AND usuario_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $chamado_id, $usuario_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Chamado não encontrado.");
}

$chamado = $result->fetch_assoc();


// ========================
// USUÁRIO RESPONDE CHAMADO
// ========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nova_mensagem'])) {

    $nova_mensagem = trim($_POST['nova_mensagem']);

    if (!empty($nova_mensagem)) {

        // Junta mensagem antiga + nova resposta
        $mensagem_atual = $chamado['mensagem'];

        $mensagem_atual .= "\n\n";
        $mensagem_atual .= "============================\n";
        $mensagem_atual .= "Resposta do usuário:\n";
        $mensagem_atual .= $nova_mensagem;

        // Atualiza chamado
        $sqlUpdate = "
            UPDATE suporte_chamados
            SET 
                mensagem = ?,
                status = 'aberto'
            WHERE id = ? AND usuario_id = ?
        ";

        $stmtUpdate = $conn->prepare($sqlUpdate);

        $stmtUpdate->bind_param(
            "sii",
            $mensagem_atual,
            $chamado_id,
            $usuario_id
        );

        $stmtUpdate->execute();
        $stmtUpdate->close();

        // Atualiza os dados do chamado sem redirecionar
        $sqlReload = "
            SELECT assunto, mensagem, resposta, status, data_abertura
            FROM suporte_chamados
            WHERE id = ? AND usuario_id = ?
        ";

        $stmtReload = $conn->prepare($sqlReload);
        $stmtReload->bind_param("ii", $chamado_id, $usuario_id);
        $stmtReload->execute();

        $resultReload = $stmtReload->get_result();

        $chamado = $resultReload->fetch_assoc();
            }
}

// Header depois da lógica
include("../Includes/Header.php");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Chamado</title>

    <link rel="stylesheet" href="/Innovatech/Assets/css/Header.css">
    <link rel="stylesheet" href="/Innovatech/Assets/css/Footer.css">
    <link rel="stylesheet" href="/Innovatech/Assets/css/SupportViewUser.css">
    

    
</head>

<body>

<div class="container">

    <div class="chamado-card">

        <h1>Detalhes do Chamado</h1>

        <!-- ASSUNTO -->
        <div class="info">
            <strong>Assunto:</strong>

            <div class="mensagem-box">
                <?= htmlspecialchars($chamado['assunto']) ?>
            </div>
        </div>

        <!-- MENSAGEM -->
        <div class="info">
            <strong>Mensagem enviada:</strong>

            <div class="mensagem-box">
                <?= nl2br(htmlspecialchars($chamado['mensagem'])) ?>
            </div>
        </div>

        <!-- STATUS -->
        <div class="info">

            <strong>Status:</strong>

            <span class="status <?= htmlspecialchars($chamado['status']) ?>">
                <?= ucfirst($chamado['status']) ?>
            </span>

        </div>

        <!-- RESPOSTA -->
        <div class="info">

            <strong>Resposta do suporte:</strong>

            <div class="mensagem-box">

                <?= $chamado['resposta']
                    ? nl2br(htmlspecialchars($chamado['resposta']))
                    : '<em>Aguardando resposta do suporte...</em>' ?>

            </div>

        </div>

        <!-- CHAMADO FINALIZADO -->
        <?php if ($chamado['status'] === 'concluido'): ?>

            <div class="finalizado">

                ✅ Este chamado foi <strong>finalizado</strong>.<br><br>

                Caso tenha outra dúvida, abra um novo chamado.

            </div>

        <?php endif; ?>


        <!-- RESPOSTA DO USUÁRIO -->
        <?php if ($chamado['status'] !== 'concluido'): ?>

            <hr>

            <h3>Responder ao suporte</h3>

            <form method="POST">

                <textarea
                    name="nova_mensagem"
                    placeholder="Digite sua resposta..."
                    required
                ></textarea>

                <button type="submit" class="btn btn-enviar">
                    Enviar resposta
                </button>

            </form>

        <?php endif; ?>

        <!-- VOLTAR -->
        <a class="voltar" href="MyCallings.php">
            ⬅ Voltar para meus chamados
        </a>

    </div>

</div>

<?php include("../Includes/Footer.php"); ?>

</body>
</html>