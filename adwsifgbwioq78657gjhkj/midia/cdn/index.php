<?php
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Content-Security-Policy: default-src \'self\'');


session_start();
require '../../../config/conexao.php';
require '../../../config/seguranca.php';

//verifica se o usuário está logado e se tem permissões
if (!isset($_SESSION['admin'])) {
    header('Location: ../../../login.php');
    exit;
}

?>