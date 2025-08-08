<?php
session_start();
require '../config/conexao.php';
require '../config/seguranca.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../login.php');
    exit;
}

?>