<?php

function registrarLog($pdo, $usuario_id, $acao, $categoria, $descricao, $referencia_id = null, $referencia_tipo = null)
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;

    $sql = "
        INSERT INTO logs
        (
            usuario_id,
            acao,
            categoria,
            descricao,
            referencia_id,
            referencia_tipo,
            ip_usuario
        )
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $usuario_id,
        $acao,
        $categoria,
        $descricao,
        $referencia_id,
        $referencia_tipo,
        $ip
    ]);
}
?>