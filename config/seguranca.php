<?php
function protegerEntrada($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

function csrfProtecao() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        session_start();
        if (empty($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Requisição inválida!");
        }
    }
}
?>
