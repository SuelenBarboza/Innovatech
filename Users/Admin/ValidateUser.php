<?php
require_once 'Protect.php';
require_once '../../Config/db.php';

$db = (new Database())->getConnection();

$sql = "SELECT id, nome, email, tipo_solicitado, criado_em
        FROM usuarios
        WHERE aprovado = 0
        ORDER BY criado_em ASC";

$stmt = $db->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Assets/css/Header.css">
    <link rel="stylesheet" href="../../Assets/css/Footer.css">
    <link rel="stylesheet" href="../../Assets/css/ValidateUser.css">
    <title>Validação de Usuários</title>
</head>
<body>

<div id="header"></div>

<main>
    <h1 class="page-title">Validação de Usuários (Admin)</h1>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Perfil</th>
                    <th>Cadastro</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) === 0): ?>
                    <tr>
                        <td colspan="5" class="empty">Nenhum cadastro pendente</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['nome']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= $u['tipo_solicitado'] ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($u['criado_em'])) ?></td>
                    <td class="actions">
                        <form action="action_user.php" method="POST">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <button class="btn-approve" name="action" value="approve">Aprovar</button>
                        </form>

                        <form action="action_user.php" method="POST">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <button class="btn-reject" name="action" value="reject">Rejeitar</button>
                        </form>

                        <form action="action_user.php" method="POST">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <button class="btn-block" name="action" value="block">Bloquear</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<div id="footer"></div>

<script src="../../Assets/js/Header.js"></script>
<script src="../../Assets/js/Footer.js"></script>

</body>
</html>
