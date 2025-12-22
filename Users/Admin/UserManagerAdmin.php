[file name]: ProcessRegister.php
[file content begin]
<?php
session_start();
include("../Config/db.php");

// Verificar se é admin
if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'Admin') {
    header("Location: ../View/Login.php?msg=acesso_negado");
    exit;
}

// Processar requisições POST para aprovação/rejeição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        $acao = $_POST['acao'];
        $usuario_id = $_POST['usuario_id'] ?? 0;
        
        if ($acao === 'aprovar') {
            $tipo_aprovado = $_POST['tipo_aprovado'] ?? '';
            
            $sql = "UPDATE usuarios SET status = 'ativo', tipo = ? WHERE id = ? AND status = 'pendente'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $tipo_aprovado, $usuario_id);
            
            if ($stmt->execute()) {
                $_SESSION['msg'] = "Usuário aprovado com sucesso!";
            } else {
                $_SESSION['msg'] = "Erro ao aprovar usuário.";
            }
            $stmt->close();
            
        } elseif ($acao === 'rejeitar') {
            $sql = "DELETE FROM usuarios WHERE id = ? AND status = 'pendente'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $usuario_id);
            
            if ($stmt->execute()) {
                $_SESSION['msg'] = "Registro rejeitado e removido.";
            } else {
                $_SESSION['msg'] = "Erro ao rejeitar registro.";
            }
            $stmt->close();
        }
        
        header("Location: ProcessRegister.php");
        exit;
    }
}

// Buscar usuários pendentes para aprovação
$sql = "SELECT id, username, email, tipo_solicitado, data_criacao 
        FROM usuarios 
        WHERE status = 'pendente' 
        ORDER BY data_criacao ASC";
$result = $conn->query($sql);

$usuarios_pendentes = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $usuarios_pendentes[] = $row;
    }
}

// Buscar estatísticas
$sql_stats = "SELECT 
    tipo_solicitado,
    COUNT(*) as total
    FROM usuarios 
    WHERE status = 'pendente'
    GROUP BY tipo_solicitado";
$result_stats = $conn->query($sql_stats);

$estatisticas = [];
if ($result_stats->num_rows > 0) {
    while($row = $result_stats->fetch_assoc()) {
        $estatisticas[$row['tipo_solicitado']] = $row['total'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprovação de Cadastros - Admin</title>
    <link rel="stylesheet" href="../Assets/css/Admin.css">
    <script src="https://kit.fontawesome.com/d7734ef980.js" crossorigin="anonymous"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #4a6ee0 0%, #6a3093 100%);
            color: white;
            padding: 25px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .btn-voltar {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .btn-voltar:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 25px;
            background: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            text-align: center;
            border-top: 4px solid;
        }
        
        .stat-card.admin { border-color: #dc3545; }
        .stat-card.coordinator { border-color: #28a745; }
        .stat-card.teacher { border-color: #007bff; }
        .stat-card.student { border-color: #ffc107; }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .main-content {
            padding: 30px;
        }
        
        .section-title {
            font-size: 20px;
            margin-bottom: 20px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 18px;
        }
        
        .usuarios-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
        
        .usuario-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
        }
        
        .usuario-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .usuario-nome {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        .tipo-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-admin { background: #ffebee; color: #dc3545; }
        .badge-coordinator { background: #e8f5e9; color: #28a745; }
        .badge-teacher { background: #e3f2fd; color: #007bff; }
        .badge-student { background: #fff3e0; color: #ff9800; }
        
        .usuario-info {
            margin-bottom: 20px;
        }
        
        .info-item {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #555;
        }
        
        .form-aprovacao {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .select-tipo {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .btn-aprovar {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-aprovar:hover {
            background: #218838;
        }
        
        .btn-rejeitar {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-rejeitar:hover {
            background: #c82333;
        }
        
        .mensagem {
            padding: 15px 20px;
            margin: 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        .sucesso {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .erro {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .usuarios-grid {
                grid-template-columns: 1fr;
            }
            
            .form-aprovacao {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <i class="fas fa-user-shield"></i>
                Painel de Aprovação - Administrador
            </h1>
            <div class="header-actions">
                <a href="../Shared/Dashboard.php" class="btn-voltar">
                    <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
                </a>
            </div>
        </div>
        
        <?php if (isset($_SESSION['msg'])): ?>
            <div class="mensagem sucesso">
                <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
            </div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-card admin">
                <i class="fas fa-user-shield fa-2x"></i>
                <div class="stat-number"><?php echo $estatisticas['Admin'] ?? 0; ?></div>
                <div>Admins Pendentes</div>
            </div>
            <div class="stat-card coordinator">
                <i class="fas fa-user-tie fa-2x"></i>
                <div class="stat-number"><?php echo $estatisticas['coordinator'] ?? 0; ?></div>
                <div>Coordenadores Pendentes</div>
            </div>
            <div class="stat-card teacher">
                <i class="fas fa-chalkboard-teacher fa-2x"></i>
                <div class="stat-number"><?php echo $estatisticas['teacher'] ?? 0; ?></div>
                <div>Professores Pendentes</div>
            </div>
            <div class="stat-card student">
                <i class="fas fa-user-graduate fa-2x"></i>
                <div class="stat-number"><?php echo $estatisticas['student'] ?? 0; ?></div>
                <div>Alunos Pendentes</div>
            </div>
        </div>
        
        <div class="main-content">
            <h2 class="section-title">
                <i class="fas fa-users"></i>
                Solicitações de Cadastro Pendentes
                <span style="font-size: 14px; color: #666; margin-left: 10px;">
                    (Total: <?php echo count($usuarios_pendentes); ?>)
                </span>
            </h2>
            
            <?php if (empty($usuarios_pendentes)): ?>
                <div class="no-data">
                    <i class="fas fa-check-circle fa-3x" style="color: #28a745; margin-bottom: 20px;"></i>
                    <p>Não há solicitações de cadastro pendentes no momento.</p>
                </div>
            <?php else: ?>
                <div class="usuarios-grid">
                    <?php foreach ($usuarios_pendentes as $usuario): 
                        $badge_class = 'badge-' . strtolower($usuario['tipo_solicitado']);
                    ?>
                    <div class="usuario-card">
                        <div class="usuario-header">
                            <div class="usuario-nome"><?php echo htmlspecialchars($usuario['username']); ?></div>
                            <span class="tipo-badge <?php echo $badge_class; ?>">
                                <?php echo htmlspecialchars($usuario['tipo_solicitado']); ?>
                            </span>
                        </div>
                        
                        <div class="usuario-info">
                            <div class="info-item">
                                <i class="fas fa-envelope"></i>
                                <span><?php echo htmlspecialchars($usuario['email']); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Solicitado em: <?php echo date('d/m/Y H:i', strtotime($usuario['data_criacao'])); ?></span>
                            </div>
                        </div>
                        
                        <form method="POST" class="form-aprovacao">
                            <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">
                            <input type="hidden" name="tipo_solicitado" value="<?php echo $usuario['tipo_solicitado']; ?>">
                            
                            <?php if ($usuario['tipo_solicitado'] !== 'Admin'): ?>
                            <select name="tipo_aprovado" class="select-tipo" required>
                                <option value="">Aprovar como...</option>
                                <option value="<?php echo $usuario['tipo_solicitado']; ?>" selected>
                                    <?php echo $usuario['tipo_solicitado']; ?> (solicitado)
                                </option>
                                <?php 
                                $tipos_permitidos = ['Admin', 'coordinator', 'teacher', 'student'];
                                foreach ($tipos_permitidos as $tipo):
                                    if ($tipo !== $usuario['tipo_solicitado']):
                                ?>
                                <option value="<?php echo $tipo; ?>"><?php echo $tipo; ?></option>
                                <?php endif; endforeach; ?>
                            </select>
                            <?php else: ?>
                                <input type="hidden" name="tipo_aprovado" value="Admin">
                                <div style="flex: 1; padding: 10px; color: #666;">
                                    Admin - Aprovação direta
                                </div>
                            <?php endif; ?>
                            
                            <div style="display: flex; gap: 10px;">
                                <button type="submit" name="acao" value="aprovar" class="btn-aprovar">
                                    <i class="fas fa-check"></i> Aprovar
                                </button>
                                <button type="submit" name="acao" value="rejeitar" class="btn-rejeitar" 
                                        onclick="return confirm('Tem certeza que deseja rejeitar este registro?')">
                                    <i class="fas fa-times"></i> Rejeitar
                                </button>
                            </div>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>
[file content end]