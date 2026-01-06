<?php
//Página de gerenciamento de usuários pendentes - Admin
session_start();
include("../Config/db.php");

// ==========================
// VERIFICAÇÃO DE ADMIN
// ==========================
if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'Admin') {
    header("Location: ../View/Login.php?msg=acesso_negado");
    exit;
}

// ==========================
// PROCESSAR APROVAÇÃO/REJEIÇÃO
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $usuario_id = (int)($_POST['usuario_id'] ?? 0);
    $acao = $_POST['acao'];

    if ($acao === 'aprovar') {
        $tipo_aprovado = $_POST['tipo_aprovado'] ?? '';
        $stmt = $conn->prepare("UPDATE usuarios SET aprovado=1, tipo_solicitado=? WHERE id=? AND aprovado=0 AND ativo=1");
        $stmt->bind_param("si", $tipo_aprovado, $usuario_id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['msg'] = "Usuário aprovado com sucesso!";
    } elseif ($acao === 'rejeitar') {
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id=? AND aprovado=0 AND ativo=1");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['msg'] = "Registro rejeitado e removido.";
    }

    header("Location: UserManagerAdmin.php?pagina=" . ($_GET['pagina'] ?? 1));
    exit;
}

// ==========================
// PAGINAÇÃO (ORDEM CORRETA)
// ==========================
$limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 10;

// segurança
$limitesPermitidos = [5, 10, 25, 50];
if (!in_array($limite, $limitesPermitidos)) {
    $limite = 10;
}

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;

$offset = ($pagina - 1) * $limite;



// total de registros
$result_total = $conn->query("
    SELECT COUNT(*) AS total 
    FROM usuarios 
    WHERE ativo = 1 AND aprovado = 0
");
$total_registros = $result_total->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $limite);

// Ajustar página se for maior que o total
if ($pagina > $total_paginas && $total_paginas > 0) {
    $pagina = $total_paginas;
    $offset = ($pagina - 1) * $limite;
}

// ==========================
// BUSCAR USUÁRIOS PENDENTES
// ==========================
$result = $conn->query("
    SELECT id, nome, email, tipo_solicitado, criado_em 
    FROM usuarios 
    WHERE ativo = 1 AND aprovado = 0
    ORDER BY criado_em ASC
    LIMIT $limite OFFSET $offset
");

$usuarios_pendentes = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// ==========================
// ESTATÍSTICAS
// ==========================
$result_stats = $conn->query("SELECT tipo_solicitado, COUNT(*) AS total FROM usuarios WHERE ativo=1 AND aprovado=0 GROUP BY tipo_solicitado");
$estatisticas = [];
if ($result_stats) {
    while ($row = $result_stats->fetch_assoc()) {
        $estatisticas[$row['tipo_solicitado']] = $row['total'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Painel de Aprovação - Admin</title>

<link rel="stylesheet" href="../Assets/css/Header.css">
<link rel="stylesheet" href="../Assets/css/Footer.css">
<link rel="stylesheet" href="../Assets/css/UserManagerAdmin.css">

<script src="https://kit.fontawesome.com/d7734ef980.js" crossorigin="anonymous"></script>
</head>
<body>

<?php include("../Includes/Header.php"); ?>

<div class="container">

    <?php if(isset($_SESSION['msg'])): ?>
        <div class="mensagem sucesso"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
    <?php endif; ?>

    <div class="stats-grid">
        <div class="stat-card admin">
            <i class="fas fa-user-shield fa-2x"></i>
            <div class="stat-number"><?= $estatisticas['Admin'] ?? 0; ?></div>
            <div>Admins Pendentes</div>
        </div>
        <div class="stat-card coordinator">
            <i class="fas fa-user-tie fa-2x"></i>
            <div class="stat-number"><?= $estatisticas['Coordenador'] ?? 0; ?></div>
            <div>Coordenadores Pendentes</div>
        </div>
        <div class="stat-card teacher">
            <i class="fas fa-chalkboard-teacher fa-2x"></i>
            <div class="stat-number"><?= $estatisticas['Professor'] ?? 0; ?></div>
            <div>Professores Pendentes</div>
        </div>
        <div class="stat-card student">
            <i class="fas fa-user-graduate fa-2x"></i>
            <div class="stat-number"><?= $estatisticas['Aluno'] ?? 0; ?></div>
            <div>Alunos Pendentes</div>
        </div>
    </div>

    <div class="main-content">
        <h2 class="section-title">
            <i class="fas fa-users"></i> Solicitações de Cadastro Pendentes
            <span class="total-counter">(Total: <?= $total_registros ?>)</span>
        </h2>

        <?php if(empty($usuarios_pendentes)): ?>
            <div class="no-data">
                <i class="fas fa-check-circle fa-3x" style="color:#28a745;"></i>
                <p>Não há solicitações de cadastro pendentes.</p>
            </div>
        <?php else: ?>

        <!-- Barra de busca e filtros -->
        <div class="barra-busca-container">
            <div class="barra-busca">
                <div class="grupo-busca">
                    <i class="fas fa-search"></i>
                    <input type="text" id="barra-busca" class="barra-busca-input" placeholder="Buscar por nome ou e-mail...">
                </div>
                <select id="filtro-tipo" class="barra-busca-input" style="min-width: 200px;">
                    <option value="">Todos os tipos</option>
                    <option value="Admin">Admin</option>
                    <option value="Coordenador">Coordenador</option>
                    <option value="Professor">Professor</option>
                    <option value="Aluno">Aluno</option>
                </select>
                <div class="filtros-rapidos">
                    <button class="filtro-btn" data-tipo="Admin"><i class="fas fa-user-shield"></i> Admin</button>
                    <button class="filtro-btn" data-tipo="Coordenador"><i class="fas fa-user-tie"></i> Coordenador</button>
                    <button class="filtro-btn" data-tipo="Professor"><i class="fas fa-chalkboard-teacher"></i> Professor</button>
                    <button class="filtro-btn" data-tipo="Aluno"><i class="fas fa-user-graduate"></i> Aluno</button>
                    <button class="limpar-busca" id="limpar-busca"><i class="fas fa-times"></i> Limpar</button>
                </div>
            </div>
        </div>

        <div class="contador-resultados">
            <i class="fas fa-filter"></i>
            Mostrando <span id="contador-resultados"><?= count($usuarios_pendentes) ?></span> de 
            <span id="total-resultados"><?= $total_registros ?></span> solicitações
            <span style="margin-left: 15px; color: #6c63ff;">
                Página <?= $pagina ?> de <?= $total_paginas ?>
            </span>
        </div>

        <!-- Tabela de usuários pendentes -->
        <div class="tabela-container">
            <table class="tabela-usuarios">
                <thead>
                    <tr>
                        <th data-ordenar="nome"><i class="fas fa-user"></i> Nome <span class="ordenacao"></span></th>
                        <th data-ordenar="email"><i class="fas fa-envelope"></i> E-mail <span class="ordenacao"></span></th>
                        <th data-ordenar="data"><i class="fas fa-calendar-alt"></i> Data de Solicitação <span class="ordenacao"></span></th>
                        <th data-ordenar="tipo"><i class="fas fa-tag"></i> Tipo Solicitado <span class="ordenacao"></span></th>
                        <th><i class="fas fa-cogs"></i> Ações</th>
                    </tr>
                </thead>
                <tbody id="tabela-corpo">
                    <?php foreach($usuarios_pendentes as $usuario): ?>
                        <tr>
                            <td class="col-nome"><?= htmlspecialchars($usuario['nome']); ?></td>
                            <td class="col-email"><?= htmlspecialchars($usuario['email']); ?></td>
                            <td class="col-data" data-data="<?= $usuario['criado_em']; ?>">
                                <?= date('d/m/Y H:i', strtotime($usuario['criado_em'])); ?>
                            </td>
                            <td>
                                <span class="tipo-badge badge-<?= strtolower($usuario['tipo_solicitado']); ?>">
                                    <?= htmlspecialchars($usuario['tipo_solicitado']); ?>
                                </span>
                            </td>
                            <td class="col-acao">
                                <form method="POST" class="form-aprovacao">
                                    <input type="hidden" name="usuario_id" value="<?= $usuario['id']; ?>">
                                    <input type="hidden" name="tipo_solicitado" value="<?= $usuario['tipo_solicitado']; ?>">
                                    <select name="tipo_aprovado" class="select-tipo" required>
                                        <option value="">Aprovar como...</option>
                                        <option value="<?= $usuario['tipo_solicitado']; ?>" selected><?= $usuario['tipo_solicitado']; ?> (solicitado)</option>
                                        <?php foreach(['Admin','Coordenador','Professor','Aluno'] as $tipo):
                                            if($tipo !== $usuario['tipo_solicitado']): ?>
                                            <option value="<?= $tipo ?>"><?= $tipo ?></option>
                                        <?php endif; endforeach; ?>
                                    </select>
                                    <div class="acoes-botoes">
                                        <button type="submit" name="acao" value="aprovar" class="btn-aprovar">
                                            <i class="fas fa-check"></i> Aprovar
                                        </button>
                                        <button type="submit" name="acao" value="rejeitar" class="btn-rejeitar" 
                                                onclick="return confirm('Tem certeza que deseja rejeitar este registro?')">
                                            <i class="fas fa-times"></i> Rejeitar
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Controles de paginação -->
        <?php if($total_paginas > 1): ?>
        <div class="controles-tabela">
            <div class="itens-por-pagina">
                <span>Itens por página:</span>
                <select id="itens-por-pagina-select">
                    <option value="5" <?= $limite == 5 ? 'selected' : '' ?>>5</option>
                    <option value="10" <?= $limite == 10 ? 'selected' : '' ?>>10</option>
                    <option value="25" <?= $limite == 25 ? 'selected' : '' ?>>25</option>
                    <option value="50" <?= $limite == 50 ? 'selected' : '' ?>>50</option>
                </select>
            </div>
            <div class="paginacao">
                <?php if($pagina > 1): ?>
                    <a href="?pagina=<?= $i ?>&limite=<?= $limite ?>" class="pagina-btn">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                    <a href="?pagina=<?= $pagina - 1 ?>" class="pagina-btn">
                        <i class="fas fa-angle-left"></i>
                    </a>
                <?php else: ?>
                    <span class="pagina-btn disabled">
                        <i class="fas fa-angle-double-left"></i>
                    </span>
                    <span class="pagina-btn disabled">
                        <i class="fas fa-angle-left"></i>
                    </span>
                <?php endif; ?>

                <?php
                // Calcular intervalo de páginas para mostrar
                $inicio = max(1, $pagina - 2);
                $fim = min($total_paginas, $pagina + 2);
                
                // Ajustar para sempre mostrar 5 páginas se possível
                if ($fim - $inicio < 4 && $total_paginas > 5) {
                    if ($pagina <= 3) {
                        $fim = min(5, $total_paginas);
                    } elseif ($pagina >= $total_paginas - 2) {
                        $inicio = max(1, $total_paginas - 4);
                    }
                }
                
                // Mostrar "..." no início se necessário
                if ($inicio > 1): ?>
                    <span class="pagina-btn">...</span>
                <?php endif;
                
                // Mostrar páginas
                for ($i = $inicio; $i <= $fim; $i++): ?>
                    <a href="?pagina=<?= $i ?>" class="pagina-btn <?= $i == $pagina ? 'ativa' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor;
                
                // Mostrar "..." no final se necessário
                if ($fim < $total_paginas): ?>
                    <span class="pagina-btn">...</span>
                <?php endif; ?>

                <?php if($pagina < $total_paginas): ?>
                    <a href="?pagina=<?= $pagina + 1 ?>" class="pagina-btn">
                        <i class="fas fa-angle-right"></i>
                    </a>
                    <a href="?pagina=<?= $total_paginas ?>" class="pagina-btn">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                <?php else: ?>
                    <span class="pagina-btn disabled">
                        <i class="fas fa-angle-right"></i>
                    </span>
                    <span class="pagina-btn disabled">
                        <i class="fas fa-angle-double-right"></i>
                    </span>
                <?php endif; ?>
            </div>
            
            <div class="paginacao-info">
                <span>Página <?= $pagina ?> de <?= $total_paginas ?></span>
            </div>
        </div>
        <?php endif; ?>
        <!-- Fim dos controles de paginação -->

        <?php endif; ?>
    </div>

</div>
<script src="../Assets/Js/UserManagerAdmin.js"></script>

<?php include("../Includes/Footer.php"); ?>
<?php $conn->close(); ?>
