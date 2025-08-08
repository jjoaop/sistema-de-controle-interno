<?php
function registrarLog($pdo, $usuarioId, $tipoUsuario, $acao) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO logs (usuario_id, tipo_usuario, acao) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$usuarioId, $tipoUsuario, $acao]);
    } catch (PDOException $e) {
        die("Erro ao registrar log: " . htmlspecialchars($e->getMessage()));
    }
}
?>
