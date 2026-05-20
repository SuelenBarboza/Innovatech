<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php
// Configuração do banco de dados
$host = 'localhost';
$dbname = 'innovatech_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // ==================== PROJETOS ====================
    $projetosLogs = $pdo->query("
        SELECT 
            p.criado_em as data_hora,
            'projeto' as categoria,
            CONCAT('Projeto <strong class=\"highlight\">\"', p.nome, '\"</strong> foi criado no sistema por ', u.nome) as descricao,
            p.criador_id as usuario_id,
            'criado' as status,
            p.nome as projeto_nome
        FROM projetos p
        JOIN usuarios u ON p.criador_id = u.id
        WHERE p.arquivado = 0
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // ==================== TAREFAS ====================
    $tarefasLogs = $pdo->query("
        SELECT 
            t.criado_em as data_hora,
            'tarefa' as categoria,
            CONCAT('Tarefa <strong class=\"highlight\">\"', t.nome, '\"</strong> foi criada no projeto <span class=\"project-name\">\"', p.nome, '\"</span> por ', COALESCE(u.nome, 'Sistema')) as descricao,
            COALESCE(t.responsavel_id, p.criador_id) as usuario_id,
            t.status as status,
            p.nome as projeto_nome
        FROM tarefas t
        JOIN projetos p ON t.projeto_id = p.id
        LEFT JOIN usuarios u ON t.responsavel_id = u.id
        WHERE t.arquivado = 0
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // ==================== RELATÓRIOS ====================
    $relatoriosLogs = $pdo->query("
        SELECT 
            r.criado_em as data_hora,
            'relatorio' as categoria,
            CONCAT('Relatório <strong class=\"highlight\">\"', r.titulo, '\"</strong> foi enviado no projeto <span class=\"project-name\">\"', p.nome, '\"</span> por ', u.nome) as descricao,
            r.aluno_id as usuario_id,
            r.status as status,
            p.nome as projeto_nome
        FROM relatorios r
        JOIN projetos p ON r.projeto_id = p.id
        JOIN usuarios u ON r.aluno_id = u.id
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // ==================== RESPOSTAS ====================
    $respostasLogs = $pdo->query("
        SELECT 
            rr.respondido_em as data_hora,
            'resposta' as categoria,
            CONCAT('Resposta ao relatório <strong class=\"highlight\">\"', r.titulo, '\"</strong> enviada por ', u.nome, ' no projeto <span class=\"project-name\">\"', p.nome, '\"</span>') as descricao,
            rr.respondente_id as usuario_id,
            'respondido' as status,
            p.nome as projeto_nome
        FROM resposta_relatorio rr
        JOIN relatorios r ON rr.relatorio_id = r.id
        JOIN projetos p ON r.projeto_id = p.id
        JOIN usuarios u ON rr.respondente_id = u.id
        WHERE rr.respondido_em IS NOT NULL
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // ==================== COMENTÁRIOS ====================
    $comentariosLogs = $pdo->query("
        SELECT 
            c.criado_em as data_hora,
            'comentario' as categoria,
            CONCAT('Comentário adicionado por ', u.nome, ' no projeto <span class=\"project-name\">\"', p.nome, '\"</span>: ', LEFT(c.comentario, 100), IF(LENGTH(c.comentario) > 100, '...', '')) as descricao,
            c.usuario_id,
            'comentado' as status,
            p.nome as projeto_nome
        FROM comentarios c
        JOIN projetos p ON c.projeto_id = p.id
        JOIN usuarios u ON c.usuario_id = u.id
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // ==================== SUPORTE ====================
    $suporteLogs = $pdo->query("
        SELECT 
            s.data_abertura as data_hora,
            'suporte' as categoria,
            CONCAT('Chamado de suporte <strong class=\"highlight\">\"', s.assunto, '\"</strong> aberto por ', s.usuario_nome) as descricao,
            s.usuario_id,
            s.status as status,
            NULL as projeto_nome
        FROM suporte_chamados s
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // ==================== ALUNOS ADICIONADOS ====================
    $alunosAdicionados = $pdo->query("
        SELECT 
            proj.criado_em as data_hora,
            'aluno' as categoria,
            CONCAT('<strong class=\"highlight\">', u.nome, '</strong> foi adicionado ao projeto <span class=\"project-name\">\"', proj.nome, '\"</span>') as descricao,
            p.usuario_id,
            'adicionado' as status,
            proj.nome as projeto_nome
        FROM projeto_usuario p
        JOIN usuarios u ON p.usuario_id = u.id
        JOIN projetos proj ON p.projeto_id = proj.id
        WHERE p.papel = 'Aluno'
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // ==================== MESCLAR TODOS OS LOGS ====================
    $todosLogs = array_merge(
        $projetosLogs, 
        $tarefasLogs, 
        $relatoriosLogs, 
        $respostasLogs, 
        $comentariosLogs, 
        $suporteLogs,
        $alunosAdicionados
    );
    
    // Filtrar logs com data válida
    $todosLogs = array_filter($todosLogs, function($log) {
        return !empty($log['data_hora']) && $log['data_hora'] !== '0000-00-00 00:00:00';
    });
    
    // Ordenar por data (mais recentes primeiro)
    usort($todosLogs, function($a, $b) {
        return strtotime($b['data_hora']) - strtotime($a['data_hora']);
    });
    
    // Estatísticas
    $stats = [
        'total' => count($todosLogs),
        'projetos' => count($projetosLogs),
        'tarefas' => count($tarefasLogs),
        'relatorios' => count($relatoriosLogs),
        'suporte' => count($suporteLogs)
    ];
    
} catch(PDOException $e) {
    die("Erro ao conectar ao banco: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Assets/css/Header.css">
    <link rel="stylesheet" href="../Assets/css/Footer.css">
    <link rel="stylesheet" href="../Assets/css/Logs.css">
    <title>Logs do Sistema — InnovaTech</title>
</head>
<body>
    <body>

    <?php include("../Includes/Header.php"); ?>

    <div class="logs-page">
        <div class="page-header">
            <div class="page-header-left">
                <h1><span class="dot"></span> Logs do Sistema</h1>
                <p>// Registro de todas as ações e eventos da plataforma InnovaTech</p>
            </div>
            <div class="header-stats">
                <div class="stat-pill">
                    <div class="val"><?php echo $stats['total']; ?></div>
                    <div class="lbl">Total</div>
                </div>
                <div class="stat-pill">
                    <div class="val"><?php echo $stats['projetos']; ?></div>
                    <div class="lbl">Projetos</div>
                </div>
                <div class="stat-pill">
                    <div class="val"><?php echo $stats['relatorios']; ?></div>
                    <div class="lbl">Relatórios</div>
                </div>
                <div class="stat-pill">
                    <div class="val"><?php echo $stats['tarefas']; ?></div>
                    <div class="lbl">Tarefas</div>
                </div>
                <div class="stat-pill">
                    <div class="val"><?php echo $stats['suporte']; ?></div>
                    <div class="lbl">Suporte</div>
                </div>
            </div>
        </div>

        <div class="controls-bar">
            <div class="search-wrap">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" class="search-input" id="searchInput" placeholder="Buscar por ação, usuário ou projeto...">
            </div>
            <select class="filter-select" id="roleFilter">
                <option value="">Todos os papéis</option>
                <option value="Admin">Admin</option>
                <option value="Professor">Professor</option>
                <option value="Aluno">Aluno</option>
                <option value="Coordenador">Coordenador</option>
            </select>
            <select class="filter-select" id="sortFilter">
                <option value="desc">Mais recentes</option>
                <option value="asc">Mais antigas</option>
            </select>
        </div>

        <div class="category-chips" id="categoryChips">
            <div class="chip active" data-cat="all">
                <span class="chip-dot" style="background:#94a3b8"></span> Todos
            </div>
            <div class="chip" data-cat="projeto">
                <span class="chip-dot" style="background:#60a5fa"></span> Projetos
            </div>
            <div class="chip" data-cat="aluno">
                <span class="chip-dot" style="background:#22c55e"></span> Alunos
            </div>
            <div class="chip" data-cat="relatorio">
                <span class="chip-dot" style="background:#f59e0b"></span> Relatórios
            </div>
            <div class="chip" data-cat="resposta">
                <span class="chip-dot" style="background:#a855f7"></span> Respostas
            </div>
            <div class="chip" data-cat="comentario">
                <span class="chip-dot" style="background:#06b6d4"></span> Comentários
            </div>
            <div class="chip" data-cat="tarefa">
                <span class="chip-dot" style="background:#f97316"></span> Tarefas
            </div>
            <div class="chip" data-cat="suporte">
                <span class="chip-dot" style="background:#ef4444"></span> Suporte
            </div>
        </div>

        <div class="logs-container">
            <div class="logs-table-header">
                <span>Data / Hora</span>
                <span>Categoria</span>
                <span>Descrição</span>
                <span>Usuário</span>
                <span>Status</span>
            </div>
            <div id="logsBody">
                <?php if (empty($todosLogs)): ?>
                    <div class="empty-state" style="display: block;">Nenhum log encontrado.</div>
                <?php else: ?>
                    <?php foreach ($todosLogs as $log): 
                        $stmt = $pdo->prepare("SELECT nome, tipo_usuario as papel FROM usuarios WHERE id = ?");
                        $stmt->execute([$log['usuario_id']]);
                        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if (!$usuario) {
                            $usuario = ['nome' => 'Sistema', 'papel' => 'Sistema'];
                        }
                        
                        $data = new DateTime($log['data_hora']);
                        $iniciais = implode('', array_map(function($n) { return $n[0]; }, explode(' ', $usuario['nome'])));
                        $iniciais = substr($iniciais, 0, 2);
                        
                        $categorias = [
                            'projeto' => 'Projeto',
                            'aluno' => 'Aluno',
                            'relatorio' => 'Relatório',
                            'resposta' => 'Resposta',
                            'comentario' => 'Comentário',
                            'tarefa' => 'Tarefa',
                            'suporte' => 'Suporte'
                        ];
                        
                        $statusDisplay = ucfirst(str_replace('_', ' ', strtolower($log['status'])));
                        $statusClass = preg_replace('/[^a-z]/i', '', strtolower($log['status']));
                    ?>
                        <div class="log-row" 
                             data-cat="<?php echo htmlspecialchars($log['categoria']); ?>"
                             data-role="<?php echo htmlspecialchars($usuario['papel'] ?? 'Sistema'); ?>"
                             data-ts="<?php echo htmlspecialchars($log['data_hora']); ?>"
                             data-search="<?php echo strtolower(htmlspecialchars(strip_tags($log['descricao']) . ' ' . ($usuario['nome'] ?? '') . ' ' . ($log['projeto_nome'] ?? ''))); ?>">
                            
                            <div class="log-ts">
                                <div class="date"><?php echo $data->format('d/m/Y'); ?></div>
                                <div class="time"><?php echo $data->format('H:i:s'); ?></div>
                            </div>
                            
                            <div>
                                <span class="log-cat cat-<?php echo htmlspecialchars($log['categoria']); ?>">
                                    <?php echo $categorias[$log['categoria']] ?? ucfirst($log['categoria']); ?>
                                </span>
                            </div>
                            
                            <div class="log-desc"><?php echo $log['descricao']; ?></div>
                            
                            <div class="log-actor">
                                <div class="actor-avatar role-<?php echo htmlspecialchars($usuario['papel'] ?? 'Sistema'); ?>">
                                    <?php echo strtoupper($iniciais); ?>
                                </div>
                                <div class="actor-info">
                                    <div class="actor-name"><?php echo htmlspecialchars($usuario['nome'] ?? 'Sistema'); ?></div>
                                    <div class="actor-role"><?php echo htmlspecialchars($usuario['papel'] ?? 'Sistema'); ?></div>
                                </div>
                            </div>
                            
                            <div>
                                <span class="log-status status-<?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($statusDisplay); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="pagination" id="pagination">
                <span class="pagination-info" id="paginationInfo"></span>
                <div class="pagination-btns" id="paginationBtns"></div>
            </div>
        </div>
    </div>

    <div id="footer"></div>

    <script src="../Assets/js/Header.js"></script>
    <?php include("../Includes/Footer.php"); ?>
    <script src="../Assets/js/Logs.js"></script>    
</body>
</html>