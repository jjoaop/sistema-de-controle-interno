<?php
require '../config/conexao.php';

try {
    $stmt = $pdo->prepare("
        DELETE FROM mesas_historico
        WHERE data_registro < DATE_SUB(NOW(), INTERVAL 15 DAY)
    ");
    $stmt->execute();

    echo "Registros antigos excluídos com sucesso.";
} catch (PDOException $e) {
    die("Erro ao limpar histórico: " . htmlspecialchars($e->getMessage()));
}
?>
