<?php
session_start();
require '../config/conexao.php';
require '../config/seguranca.php'; 

if (!isset($_SESSION['admin'])) {
    echo "Acesso negado.";
    exit;
}

try {
    $stmt = $pdo->prepare("TRUNCATE TABLE mesas_historico");
    $stmt->execute();

    header("Location: gerenciar_relatorios.php?status=historico_limpo");
    exit;
} catch (PDOException $e) {
    header("Location: gerenciar_relatorios.php?status=erro_limpeza");
    exit;
}
?>
