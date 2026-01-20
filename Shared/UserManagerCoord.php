<?php
// Página de gerenciamento de aprovações de cadastros para coordenadores
session_start();
include("../Config/db.php");

// ==========================
// VERIFICAÇÃO DE PERMISSÃO
// ==========================
if (!isset($_SESSION['usuario_tipo']) || !in_array($_SESSION['usuario_tipo'], ['Admin', 'Coordenador'])) {
    header("Location: ../View/Login.php?msg=acesso_negado");
    exit;
}

// ==========================
// PROCESSAR APROVAÇÃO / REJEIÇÃO
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $usuario_id = (int)($_POST['usuario_id'] ?? 0);
    $acao = $_POST['acao'];

    if ($acao === 'aprovar') {
        $tipo_aprovado = $_POST['tipo_aprovado'] ?? '';
        if (!in_array($tipo_aprovado, ['Professor', 'Aluno'])) {
            $_SESSION['msg'] = "Tipo de usuário não permitido!";
            header("Location: UserManagerCoordenador.php");
            exit;
        }

        $stmt = $conn->prepare("
            UPDATE usuarios 
            SET aprovado = 1, tipo_usuario = ?
            WHERE id = ? AND aprovado = 0 AND ativo = 1
              AND tipo_solicitado IN ('Aluno','Professor')
        ");
        $stmt->bind_param("si", $tipo_aprovado, $usuario_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['msg'] = "Usuário aprovado com sucesso!";
    }

    if ($acao === 'rejeitar') {
        $stmt = $conn->prepare("
            DELETE FROM usuarios 
            WHERE id = ? AND aprovado = 0 AND ativo = 1
              AND tipo_solicitado IN ('Aluno','Professor')
        ");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['msg'] = "Solicitação rejeitada.";
    }

    header("Location: UserManagerCoordenador.php");
    exit;
}

// ==========================
// BUSCAR TODOS OS USUÁRIOS PENDENTES (SEM LIMITE)
// ==========================
$result = $conn->query("
    SELECT id, nome, email, tipo_solicitado, criado_em 
    FROM usuarios 
    WHERE ativo = 1 AND aprovado = 0
      AND tipo_solicitado IN ('Aluno','Professor')
    ORDER BY criado_em ASC
");
$usuarios = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// ==========================
// ESTATÍSTICAS
// ==========================
$estatisticas = ['Professor' => 0, 'Aluno' => 0];
foreach ($usuarios as $u) {
    $estatisticas[$u['tipo_solicitado']]++;
}

// Tipos permitidos para aprovação
$tipos_permitidos = ['Professor', 'Aluno'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Painel do Coordenador - Aprovar Cadastros</title>

<link rel="stylesheet" href="../Assets/css/Header.css">
<link rel="stylesheet" href="../Assets/css/Footer.css">
<link rel="stylesheet" href="../Assets/css/UserManagerCoord.css">

<script src="https://kit.fontawesome.com/d7734ef980.js" crossorigin="anonymous"></script>
</head>

<body>

<?php include("../Includes/Header.php"); ?>

<div class="container">

<?php if(isset($_SESSION['msg'])): ?>
    <div class="mensagem sucesso"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
<?php endif; ?>

<div class="stats-grid">
    <div class="stat-card teacher">
        <i class="fas fa-chalkboard-teacher fa-2x"></i>
        <div class="stat-number"><?= $estatisticas['Professor'] ?></div>
        <div>Professores Pendentes</div>
    </div>

    <div class="stat-card student">
        <i class="fas fa-user-graduate fa-2x"></i>
        <div class="stat-number"><?= $estatisticas['Aluno'] ?></div>
        <div>Alunos Pendentes</div>
    </div>
</div>

<div class="main-content">
    <h2 class="section-title">
        <i class="fas fa-user-check"></i> Aprovação de Cadastros
        <span class="total-counter">(Total: <?= count($usuarios) ?>)</span>
    </h2>

    <?php if(empty($usuarios)): ?>
        <div class="no-data">
            <i class="fas fa-check-circle fa-3x"></i>
            <p>Não há solicitações de cadastro pendentes.</p>
        </div>
    <?php else: ?>

    <!-- Barra de busca e filtros (100% JavaScript) -->
    <div class="barra-busca-container">
        <div class="barra-busca">
            <div class="grupo-busca">
                <i class="fas fa-search"></i>
                <input type="text" id="barra-busca" class="barra-busca-input" placeholder="Buscar por nome ou e-mail...">
            </div>
            <select id="filtro-tipo" class="barra-busca-input" style="min-width: 200px;">
                <option value="">Todos os tipos</option>
                <option value="Professor">Professor</option>
                <option value="Aluno">Aluno</option>
            </select>
            <div class="filtros-rapidos">
                <button class="filtro-btn" data-tipo="Professor"><i class="fas fa-chalkboard-teacher"></i> Professor</button>
                <button class="filtro-btn" data-tipo="Aluno"><i class="fas fa-user-graduate"></i> Aluno</button>
                <button class="limpar-busca" id="limpar-busca"><i class="fas fa-times"></i> Limpar</button>
            </div>
        </div>
        
        <!-- Controles de paginação (TOP) -->
        <div class="controles-tabela" style="margin-top: 10px; justify-content: flex-end;">
            <div class="itens-por-pagina">
                <span>Itens por página:</span>
                <select id="itens-por-pagina-select">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="9999">Todos</option>
                </select>
            </div>
        </div>
    </div>

    <div class="contador-resultados">
        <i class="fas fa-filter"></i>
        Mostrando <span id="contador-resultados"><?= count($usuarios) ?></span> de 
        <span id="total-resultados"><?= count($usuarios) ?></span> solicitações
        <span style="margin-left: 15px; color: #6c63ff;">
            Página <span id="pagina-atual">1</span> de <span id="total-paginas">1</span>
        </span>
    </div>

    <!-- Tabela de usuários pendentes -->
    <div class="tabela-container">
        <table class="tabela-usuarios">
            <thead>
                <tr>
                    <th data-ordenar="nome"><i class="fas fa-user"></i> Nome <span class="ordenacao"></span></th>
                    <th data-ordenar="email"><i class="fas fa-envelope"></i> E-mail <span class="ordenacao"></span></th>
                    <th data-ordenar="data"><i class="fas fa-calendar-alt"></i> Data <span class="ordenacao"></span></th>
                    <th data-ordenar="tipo"><i class="fas fa-tag"></i> Tipo <span class="ordenacao"></span></th>
                    <th><i class="fas fa-cogs"></i> Ações</th>
                </tr>
            </thead>
            <tbody id="tabela-corpo">
                <?php foreach($usuarios as $usuario): ?>
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
                                    <option value="<?= $usuario['tipo_solicitado']; ?>" selected>
                                        <?= $usuario['tipo_solicitado']; ?> (solicitado)
                                    </option>
                                    <?php 
                                    foreach($tipos_permitidos as $tipo):
                                        if($tipo !== $usuario['tipo_solicitado']): ?>
                                            <option value="<?= $tipo ?>"><?= $tipo ?></option>
                                        <?php endif; 
                                    endforeach; ?>
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

    <!-- Controles de paginação (BOTTOM) - Renderizado pelo JavaScript -->
    <div id="paginacao-container" class="controles-tabela" style="margin-top: 20px; display: none;">
        <div class="itens-por-pagina">
            <span>Itens por página:</span>
            <select id="itens-por-pagina-select-bottom">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="9999">Todos</option>
            </select>
        </div>
        <div class="paginacao" id="paginacao-botoes"></div>
        <div class="paginacao-info">
            Página <span id="pagina-info">1</span> de <span id="total-paginas-info">1</span>
        </div>
    </div>

    <?php endif; ?>
</div>

</div>

<script src="../Assets/Js/UserManagerCoord.js"></script>

<?php include("../Includes/Footer.php"); ?>
<?php $conn->close(); ?>