<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php
// ============================================================
// CONFIGURAÇÃO DO BANCO DE DADOS
// ============================================================
$host     = 'localhost';
$dbname   = 'innovatech_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ==================== PROJETOS ====================
    $projetosLogs = $pdo->query("
        SELECT
            p.criado_em                                  AS data_hora,
            'projeto'                                    AS categoria,
            CONCAT('Projeto \"', p.nome, '\" foi criado no sistema') AS descricao,
            p.criador_id                                 AS usuario_id,
            'criado'                                     AS status,
            p.nome                                       AS projeto_nome,
            p.id                                         AS projeto_id,
            p.categoria                                  AS projeto_categoria,
            p.prioridade                                 AS projeto_prioridade,
            p.status                                     AS projeto_status,
            p.data_inicio                                AS projeto_inicio,
            p.data_fim                                   AS projeto_fim,
            p.descricao                                  AS detalhe_extra,
            NULL                                         AS item_nome
        FROM projetos p
        JOIN usuarios u ON p.criador_id = u.id
        WHERE p.arquivado = 0
    ")->fetchAll(PDO::FETCH_ASSOC);

    // ==================== TAREFAS ====================
    $tarefasLogs = $pdo->query("
        SELECT
            t.criado_em                                  AS data_hora,
            'tarefa'                                     AS categoria,
            CONCAT('Tarefa \"', t.nome, '\" criada no projeto \"', p.nome, '\"') AS descricao,
            COALESCE(t.responsavel_id, p.criador_id)     AS usuario_id,
            t.status                                     AS status,
            p.nome                                       AS projeto_nome,
            p.id                                         AS projeto_id,
            t.prioridade                                 AS projeto_prioridade,
            p.status                                     AS projeto_status,
            p.data_inicio                                AS projeto_inicio,
            p.data_fim                                   AS projeto_fim,
            t.descricao                                  AS detalhe_extra,
            t.nome                                       AS item_nome
        FROM tarefas t
        JOIN projetos p ON t.projeto_id = p.id
        LEFT JOIN usuarios u ON t.responsavel_id = u.id
        WHERE t.arquivado = 0
    ")->fetchAll(PDO::FETCH_ASSOC);

    // ==================== RELATÓRIOS ====================
    $relatoriosLogs = $pdo->query("
        SELECT
            r.criado_em                                  AS data_hora,
            'relatorio'                                  AS categoria,
            CONCAT('Relatório \"', r.titulo, '\" enviado no projeto \"', p.nome, '\"') AS descricao,
            r.aluno_id                                   AS usuario_id,
            r.status                                     AS status,
            p.nome                                       AS projeto_nome,
            p.id                                         AS projeto_id,
            p.prioridade                                 AS projeto_prioridade,
            p.status                                     AS projeto_status,
            p.data_inicio                                AS projeto_inicio,
            p.data_fim                                   AS projeto_fim,
            r.descricao                                  AS detalhe_extra,
            r.titulo                                     AS item_nome
        FROM relatorios r
        JOIN projetos p ON r.projeto_id = p.id
        JOIN usuarios u ON r.aluno_id = u.id
    ")->fetchAll(PDO::FETCH_ASSOC);

    // ==================== RESPOSTAS ====================
    $respostasLogs = $pdo->query("
        SELECT
            rr.respondido_em                             AS data_hora,
            'resposta'                                   AS categoria,
            CONCAT('Resposta ao relatório \"', r.titulo, '\" no projeto \"', p.nome, '\"') AS descricao,
            rr.respondente_id                            AS usuario_id,
            'respondido'                                 AS status,
            p.nome                                       AS projeto_nome,
            p.id                                         AS projeto_id,
            p.prioridade                                 AS projeto_prioridade,
            p.status                                     AS projeto_status,
            p.data_inicio                                AS projeto_inicio,
            p.data_fim                                   AS projeto_fim,
            rr.resposta                                  AS detalhe_extra,
            r.titulo                                     AS item_nome
        FROM resposta_relatorio rr
        JOIN relatorios r      ON rr.relatorio_id = r.id
        JOIN projetos p        ON r.projeto_id    = p.id
        JOIN usuarios u        ON rr.respondente_id = u.id
        WHERE rr.respondido_em IS NOT NULL
    ")->fetchAll(PDO::FETCH_ASSOC);

    // ==================== COMENTÁRIOS ====================
    $comentariosLogs = $pdo->query("
        SELECT
            c.criado_em                                  AS data_hora,
            'comentario'                                 AS categoria,
            CONCAT('Comentário adicionado no projeto \"', p.nome, '\"') AS descricao,
            c.usuario_id,
            'comentado'                                  AS status,
            p.nome                                       AS projeto_nome,
            p.id                                         AS projeto_id,
            p.prioridade                                 AS projeto_prioridade,
            p.status                                     AS projeto_status,
            p.data_inicio                                AS projeto_inicio,
            p.data_fim                                   AS projeto_fim,
            c.comentario                                 AS detalhe_extra,
            NULL                                         AS item_nome
        FROM comentarios c
        JOIN projetos p ON c.projeto_id = p.id
        JOIN usuarios u ON c.usuario_id = u.id
    ")->fetchAll(PDO::FETCH_ASSOC);

    // ==================== SUPORTE ====================
    $suporteLogs = $pdo->query("
        SELECT
            s.data_abertura                              AS data_hora,
            'suporte'                                    AS categoria,
            CONCAT('Chamado de suporte \"', s.assunto, '\" aberto') AS descricao,
            s.usuario_id,
            s.status                                     AS status,
            NULL                                         AS projeto_nome,
            NULL                                         AS projeto_id,
            NULL                                         AS projeto_prioridade,
            NULL                                         AS projeto_status,
            NULL                                         AS projeto_inicio,
            NULL                                         AS projeto_fim,
            s.mensagem                                   AS detalhe_extra,
            s.assunto                                    AS item_nome
        FROM suporte_chamados s
    ")->fetchAll(PDO::FETCH_ASSOC);

    // ==================== ALUNOS ADICIONADOS ====================
    $alunosAdicionados = $pdo->query("
        SELECT
            proj.criado_em                               AS data_hora,
            'aluno'                                      AS categoria,
            CONCAT('Aluno adicionado ao projeto \"', proj.nome, '\"') AS descricao,
            p.usuario_id,
            'adicionado'                                 AS status,
            proj.nome                                    AS projeto_nome,
            proj.id                                      AS projeto_id,
            proj.prioridade                              AS projeto_prioridade,
            proj.status                                  AS projeto_status,
            proj.data_inicio                             AS projeto_inicio,
            proj.data_fim                                AS projeto_fim,
            NULL                                         AS detalhe_extra,
            u.nome                                       AS item_nome
        FROM projeto_usuario p
        JOIN usuarios u ON p.usuario_id = u.id
        JOIN projetos proj ON p.projeto_id = proj.id
        WHERE p.papel = 'Aluno'
    ")->fetchAll(PDO::FETCH_ASSOC);

    // ==================== MESCLAR & ORDENAR ====================
    $todosLogs = array_merge(
        $projetosLogs,
        $tarefasLogs,
        $relatoriosLogs,
        $respostasLogs,
        $comentariosLogs,
        $suporteLogs,
        $alunosAdicionados
    );

    $todosLogs = array_filter($todosLogs, function ($log) {
        return !empty($log['data_hora']) && $log['data_hora'] !== '0000-00-00 00:00:00';
    });

    usort($todosLogs, function ($a, $b) {
        return strtotime($b['data_hora']) - strtotime($a['data_hora']);
    });

    // ==================== ESTATÍSTICAS ====================
    $stats = [
        'total'      => count($todosLogs),
        'projetos'   => count($projetosLogs),
        'tarefas'    => count($tarefasLogs),
        'relatorios' => count($relatoriosLogs),
        'suporte'    => count($suporteLogs),
    ];

} catch (PDOException $e) {
    die("Erro ao conectar ao banco: " . $e->getMessage());
}

// ============================================================
// HELPER: busca dados completos do usuário
// ============================================================
function getUsuario(PDO $pdo, $id): array {
    static $cache = [];
    $default = ['nome' => 'Sistema', 'papel' => 'Sistema', 'email' => '—'];
    if (!$id) return $default;
    if (!isset($cache[$id])) {
        $stmt = $pdo->prepare("SELECT nome, tipo_usuario AS papel, email FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $cache[$id] = $row ? [
            'nome'  => (string)($row['nome']  ?? 'Sistema'),
            'papel' => (string)($row['papel'] ?? 'Sistema'),
            'email' => (string)($row['email'] ?? '—'),
        ] : $default;
    }
    return $cache[$id];
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

<?php include("../Includes/Header.php"); ?>

<div class="logs-page">

    <!-- ===== CABEÇALHO DA PÁGINA ===== -->
    <div class="page-header">
        <div class="page-header-left">
            <h1><span class="dot"></span> Logs do Sistema</h1>
            <p>// Registro completo de todas as ações e eventos da plataforma InnovaTech</p>
        </div>
        <div class="header-stats">
            <div class="stat-pill">
                <div class="val"><?= $stats['total'] ?></div>
                <div class="lbl">Total</div>
            </div>
            <div class="stat-pill">
                <div class="val"><?= $stats['projetos'] ?></div>
                <div class="lbl">Projetos</div>
            </div>
            <div class="stat-pill">
                <div class="val"><?= $stats['relatorios'] ?></div>
                <div class="lbl">Relatórios</div>
            </div>
            <div class="stat-pill">
                <div class="val"><?= $stats['tarefas'] ?></div>
                <div class="lbl">Tarefas</div>
            </div>
            <div class="stat-pill">
                <div class="val"><?= $stats['suporte'] ?></div>
                <div class="lbl">Suporte</div>
            </div>
        </div>
    </div>

    <!-- ===== CONTROLES ===== -->
    <div class="controls-bar">
        <div class="search-wrap">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
            </svg>
            <input type="text" class="search-input" id="searchInput" placeholder="Buscar por ação, usuário, projeto ou e-mail...">
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

    <!-- ===== CHIPS DE CATEGORIA ===== -->
    <div class="category-chips" id="categoryChips">
        <div class="chip active" data-cat="all"><span class="chip-dot" style="background:#94a3b8"></span> Todos</div>
        <div class="chip" data-cat="projeto"><span class="chip-dot" style="background:#60a5fa"></span> Projetos</div>
        <div class="chip" data-cat="aluno"><span class="chip-dot" style="background:#22c55e"></span> Alunos</div>
        <div class="chip" data-cat="relatorio"><span class="chip-dot" style="background:#f59e0b"></span> Relatórios</div>
        <div class="chip" data-cat="resposta"><span class="chip-dot" style="background:#a855f7"></span> Respostas</div>
        <div class="chip" data-cat="comentario"><span class="chip-dot" style="background:#06b6d4"></span> Comentários</div>
        <div class="chip" data-cat="tarefa"><span class="chip-dot" style="background:#f97316"></span> Tarefas</div>
        <div class="chip" data-cat="suporte"><span class="chip-dot" style="background:#ef4444"></span> Suporte</div>
    </div>

    <!-- ===== TABELA DE LOGS ===== -->
    <div class="logs-container">
        <div class="logs-table-header">
            <span>Data / Hora</span>
            <span>Categoria</span>
            <span>Descrição</span>
            <span>Usuário</span>
            <span>Status</span>
            <span>Detalhes</span>
        </div>

        <div id="logsBody">
        <?php if (empty($todosLogs)): ?>
            <div class="empty-state" style="display:block;">Nenhum log encontrado.</div>
        <?php else: ?>
            <?php
            $categoriasLabel = [
                'projeto'   => 'Projeto',
                'aluno'     => 'Aluno',
                'relatorio' => 'Relatório',
                'resposta'  => 'Resposta',
                'comentario'=> 'Comentário',
                'tarefa'    => 'Tarefa',
                'suporte'   => 'Suporte',
            ];
            foreach ($todosLogs as $log):
                $usuario     = getUsuario($pdo, $log['usuario_id']);
                $data        = new DateTime($log['data_hora']);
                $nomePartes  = explode(' ', $usuario['nome']);
                $iniciais    = substr(implode('', array_map(fn($n) => $n[0], $nomePartes)), 0, 2);
                $statusClass = preg_replace('/[^a-z]/i', '', strtolower((string)($log['status'] ?? '')));
                $statusLabel = ucfirst(str_replace('_', ' ', strtolower((string)($log['status'] ?? ''))));

                // — Dados para o modal (escapados para JSON inline) —
                $modal = [
                    'categoria'        => $categoriasLabel[$log['categoria']] ?? ucfirst($log['categoria']),
                    'data_hora'        => $data->format('d/m/Y H:i:s'),
                    'dia_semana'       => ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'][(int)$data->format('w')],
                    'status'           => $statusLabel,
                    'usuario_nome'     => $usuario['nome'],
                    'usuario_papel'    => $usuario['papel'],
                    'usuario_email'    => $usuario['email'] ?? '—',
                    'projeto_nome'     => $log['projeto_nome']     ?? '—',
                    'projeto_id'       => $log['projeto_id']       ?? '—',
                    'projeto_cat'      => $log['projeto_categoria'] ?? ($log['projeto_status'] ?? '—'),
                    'projeto_status'   => $log['projeto_status']   ?? '—',
                    'projeto_prior'    => $log['projeto_prioridade'] ?? '—',
                    'projeto_inicio'   => !empty($log['projeto_inicio']) ? (new DateTime($log['projeto_inicio']))->format('d/m/Y') : '—',
                    'projeto_fim'      => !empty($log['projeto_fim'])    ? (new DateTime($log['projeto_fim']))->format('d/m/Y')    : '—',
                    'item_nome'        => $log['item_nome']        ?? '—',
                    'detalhe_extra'    => $log['detalhe_extra']    ?? '',
                    'descricao'        => strip_tags($log['descricao']),
                ];
                $modalJson = htmlspecialchars(json_encode($modal, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
            ?>
            <div class="log-row"
                 data-cat="<?= htmlspecialchars((string)($log['categoria'] ?? '')) ?>"
                 data-role="<?= htmlspecialchars((string)($usuario['papel'] ?? 'Sistema')) ?>"
                 data-ts="<?= htmlspecialchars((string)($log['data_hora'] ?? '')) ?>"
                 data-search="<?= strtolower(htmlspecialchars(strip_tags((string)($log['descricao'] ?? '')) . ' ' . ($usuario['nome'] ?? '') . ' ' . ($log['projeto_nome'] ?? '') . ' ' . ($usuario['email'] ?? ''))) ?>"
                 data-modal="<?= $modalJson ?>">

                <!-- Data / Hora -->
                <div class="log-ts">
                    <div class="date"><?= $data->format('d/m/Y') ?></div>
                    <div class="time"><?= $data->format('H:i:s') ?></div>
                </div>

                <!-- Categoria -->
                <div>
                    <span class="log-cat cat-<?= htmlspecialchars((string)($log['categoria'] ?? '')) ?>">
                        <?= $categoriasLabel[$log['categoria']] ?? ucfirst($log['categoria']) ?>
                    </span>
                </div>

                <!-- Descrição -->
                <div class="log-desc"><?= $log['descricao'] ?></div>

                <!-- Usuário -->
                <div class="log-actor">
                    <div class="actor-avatar role-<?= htmlspecialchars((string)($usuario['papel'] ?? 'Sistema')) ?>">
                        <?= strtoupper($iniciais) ?>
                    </div>
                    <div class="actor-info">
                        <div class="actor-name"><?= htmlspecialchars((string)($usuario['nome'] ?? 'Sistema')) ?></div>
                        <div class="actor-role"><?= htmlspecialchars((string)($usuario['papel'] ?? 'Sistema')) ?></div>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <span class="log-status status-<?= htmlspecialchars($statusClass) ?>">
                        <?= htmlspecialchars((string)($statusLabel ?? '')) ?>
                    </span>
                </div>

                <!-- Botão de Detalhes -->
                <div class="log-detail-btn-wrap">
                    <button class="btn-detail" onclick="openModal(this)" title="Ver detalhes completos">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                            <line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/>
                        </svg>
                        Detalhes
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
        </div>

        <!-- Paginação -->
        <div class="pagination" id="pagination">
            <span class="pagination-info" id="paginationInfo"></span>
            <div class="pagination-btns" id="paginationBtns"></div>
        </div>
    </div><!-- /.logs-container -->
</div><!-- /.logs-page -->

<!-- ===================================================================
     MODAL DE DETALHES DO LOG
     =================================================================== -->
<div class="modal-overlay" id="modalOverlay" onclick="closeModal(event)">
    <div class="modal-detail" id="modalDetail">

        <div class="modal-header" id="modalHeader">
            <div class="modal-header-left">
                <span class="modal-cat-badge" id="modalCatBadge"></span>
                <h2 id="modalTitle"></h2>
            </div>
            <button class="modal-close" onclick="closeModal(null)" title="Fechar">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <div class="modal-body">

            <!-- Bloco: Quando -->
            <div class="modal-section">
                <div class="modal-section-title">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Quando aconteceu
                </div>
                <div class="modal-grid-2">
                    <div class="modal-field">
                        <span class="mf-label">Data</span>
                        <span class="mf-value" id="mDataHora"></span>
                    </div>
                    <div class="modal-field">
                        <span class="mf-label">Dia da semana</span>
                        <span class="mf-value" id="mDiaSemana"></span>
                    </div>
                </div>
            </div>

            <!-- Bloco: Quem -->
            <div class="modal-section">
                <div class="modal-section-title">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Quem realizou
                </div>
                <div class="modal-grid-3">
                    <div class="modal-field">
                        <span class="mf-label">Nome</span>
                        <span class="mf-value" id="mUsuarioNome"></span>
                    </div>
                    <div class="modal-field">
                        <span class="mf-label">Papel</span>
                        <span class="mf-value" id="mUsuarioPapel"></span>
                    </div>
                    <div class="modal-field">
                        <span class="mf-label">E-mail</span>
                        <span class="mf-value" id="mUsuarioEmail"></span>
                    </div>
                </div>
            </div>

            <!-- Bloco: Onde / Projeto -->
            <div class="modal-section" id="mProjetoSection">
                <div class="modal-section-title">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    Onde ocorreu (Projeto)
                </div>
                <div class="modal-grid-3">
                    <div class="modal-field">
                        <span class="mf-label">Nome do projeto</span>
                        <span class="mf-value" id="mProjNome"></span>
                    </div>
                    <div class="modal-field">
                        <span class="mf-label">ID do projeto</span>
                        <span class="mf-value" id="mProjId"></span>
                    </div>
                    <div class="modal-field">
                        <span class="mf-label">Categoria</span>
                        <span class="mf-value" id="mProjCat"></span>
                    </div>
                    <div class="modal-field">
                        <span class="mf-label">Status do projeto</span>
                        <span class="mf-value" id="mProjStatus"></span>
                    </div>
                    <div class="modal-field">
                        <span class="mf-label">Prioridade</span>
                        <span class="mf-value" id="mProjPrior"></span>
                    </div>
                    <div class="modal-field">
                        <span class="mf-label">Prazo</span>
                        <span class="mf-value" id="mProjPrazo"></span>
                    </div>
                </div>
            </div>

            <!-- Bloco: O que -->
            <div class="modal-section">
                <div class="modal-section-title">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    O que foi feito
                </div>
                <div class="modal-field full-width">
                    <span class="mf-label">Ação registrada</span>
                    <span class="mf-value" id="mDescricao"></span>
                </div>
                <div class="modal-field full-width" id="mItemWrap" style="margin-top:.75rem">
                    <span class="mf-label" id="mItemLabel">Item relacionado</span>
                    <span class="mf-value" id="mItemNome"></span>
                </div>
                <div class="modal-field full-width" id="mDetalheWrap" style="margin-top:.75rem">
                    <span class="mf-label">Conteúdo / Detalhe</span>
                    <div class="mf-content-box" id="mDetalheExtra"></div>
                </div>
            </div>

            <!-- Bloco: Status -->
            <div class="modal-section">
                <div class="modal-section-title">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    Status do evento
                </div>
                <div class="modal-field">
                    <span class="mf-label">Status</span>
                    <span class="mf-value" id="mStatus"></span>
                </div>
            </div>

        </div><!-- /.modal-body -->
    </div><!-- /.modal-detail -->
</div><!-- /.modal-overlay -->

<script src="../Assets/js/Header.js"></script>
<?php include("../Includes/Footer.php"); ?>
<script src="../Assets/js/Logs.js"></script>
</body>
</html>