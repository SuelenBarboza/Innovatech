<?php
// Mostra os chamados do usuário logado
session_start();
include("../Config/db.php");

// Verifica login
if (!isset($_SESSION['usuario_id'])) {
    die("Acesso negado.");
}

$usuario_id = $_SESSION['usuario_id'];

// Busca chamados do usuário
$sql = "
    SELECT id, assunto, status, data_abertura
    FROM suporte_chamados
    WHERE usuario_id = ?
    ORDER BY 
        CASE 
            WHEN status = 'concluido' THEN 1 
            ELSE 0 
        END,
        data_abertura DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

include("../Includes/Header.php");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meus Chamados</title>

    <link rel="stylesheet" href="/Innovatech/Assets/css/Header.css">
    <link rel="stylesheet" href="/Innovatech/Assets/css/Footer.css">

    <style>
        .container {
            max-width: 900px;
            margin: 40px auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #f5f5f5;
        }

        .status {
            font-weight: bold;
        }

        .status.aberto { color: #ff9800; }
        .status.respondido { color: #2196F3; }
        .status.concluido { color: #4CAF50; }

        a.btn {
            padding: 6px 12px;
            background: #2196F3;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
        }

        a.btn:hover {
            background: #1976D2;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Meus Chamados</h1>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Assunto</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['assunto']) ?></td>
                        <td class="status <?= $row['status'] ?>">
                            <?= ucfirst($row['status']) ?>
                        </td>
                        <td>
                            <?= date('d/m/Y H:i', strtotime($row['data_abertura'])) ?>
                        </td>
                        <td>
                           <a class="btn" href="SupportViewUser.php?id=<?= $row['id'] ?>">
                                Ver chamado
                            </a>


                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Você ainda não abriu nenhum chamado.</p>
    <?php endif; ?>
</div>

<?php include("../Includes/Footer.php"); ?>
</body>
</html>
