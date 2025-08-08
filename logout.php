<?php
session_start();
require 'config/conexao.php';
require 'config/functions-log.php';

if (isset($_SESSION['usuario_id'], $_SESSION['tipo'])) {
    registrarLog($pdo, $_SESSION['usuario_id'], $_SESSION['tipo'], 'Logout realizado');
}

$_SESSION = [];
session_destroy();
session_regenerate_id(true);

header("Location: index.php");
exit;
?>
