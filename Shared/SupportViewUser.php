<?php
// Usado para visualizar os detalhes de um chamado específico pelo usuário
session_start();
include("../Config/db.php");

// Verifica login
if (!isset($_SESSION['usuario_id'])) {
    die("Acesso negado.");
}

// Verifica ID do chamado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Chamado inválido.");
}

$usuario_id = $_SESSION['usuario_id'];
$chamado_id = (int) $_GET['id'];

// Busca o chamado
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

    <style>
        .container {
            max-width: 900px;
            margin: 40px auto;
        }

        .chamado-card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }

        .status {
            font-weight: bold;
        }

        .status.respondido { color: #2196F3; }
        .status.concluido { color: #4CAF50; }

        .finalizado {
            background: #f0f8f0;
            border-left: 4px solid #4CAF50;
            padding: 12px;
            margin-top: 15px;
        }

        a.voltar {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #2196F3;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Detalhes do Chamado</h1>

    <div class="chamado-card">

        <p><strong>Assunto:</strong>
            <?= htmlspecialchars($chamado['assunto']) ?>
        </p>

        <p><strong>Mensagem enviada:</strong><br>
            <?= nl2br(htmlspecialchars($chamado['mensagem'])) ?>
        </p>

        <p>
            <strong>Status:</strong>
            <span class="status <?= htmlspecialchars($chamado['status']) ?>">
                <?= ucfirst($chamado['status']) ?>
            </span>
        </p>

        <p><strong>Resposta do suporte:</strong><br>
            <?= $chamado['resposta']
                ? nl2br(htmlspecialchars($chamado['resposta']))
                : '<em>Aguardando resposta do suporte...</em>' ?>
        </p>

        <?php if ($chamado['status'] === 'concluido'): ?>
            <div class="finalizado">
                ✅ Este chamado foi <strong>finalizado</strong>.<br>
                Caso tenha outra dúvida, abra um novo chamado.
            </div>
        <?php endif; ?>

        <a class="voltar" href="MyCallings.php">⬅ Voltar para meus chamados</a>
    </div>
</div>

<?php include("../Includes/Footer.php"); ?>
</body>
</html>
