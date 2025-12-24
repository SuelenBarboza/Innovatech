<?php
require_once __DIR__ . "/db.php";

header('Content-Type: application/json; charset=utf-8');

$termo = $_POST['termo'] ?? '';
$tipo  = $_POST['tipo']  ?? ''; // student OU teacher

$retorno = [];

// Debug: verificar o que está chegando
error_log("Termo recebido: " . $termo);
error_log("Tipo recebido: " . $tipo);

if (empty($termo) || empty($tipo)) {
    echo json_encode([]);
    exit;
}

// Mapear os tipos do frontend para os tipos do banco
$tipoBanco = '';
if ($tipo === 'student') {
    $tipoBanco = 'aluno'; // ou o valor que você tem no campo tipo_solicitado
} elseif ($tipo === 'teacher') {
    $tipoBanco = 'professor'; // ou o valor que você tem no campo tipo_solicitado
}

$sql = "SELECT id, nome
        FROM usuarios
        WHERE nome LIKE ?
          AND tipo_solicitado = ?
          AND aprovado = 1
          AND ativo = 1
        LIMIT 10";

$stmt = $conn->prepare($sql);
$like = "%$termo%";
$stmt->bind_param("ss", $like, $tipoBanco);
$stmt->execute();

$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $retorno[] = [
        "id" => $row['id'],
        "nome" => $row['nome']
    ];
}

// Debug: verificar resultados
error_log("Resultados encontrados: " . count($retorno));

echo json_encode($retorno);
exit;