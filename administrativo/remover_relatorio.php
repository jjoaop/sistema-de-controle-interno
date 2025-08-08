<?php
session_start();
require '../config/conexao.php';
require '../config/seguranca.php';

if (!isset($_SESSION['admin'])) {
    echo "Acesso negado.";
    exit;
}

$arquivo = $_GET['arquivo'] ?? null;

if ($arquivo === 'index.php') {
    echo "O arquivo 'index.php' não pode ser removido.";
    exit;
}

if ($arquivo) {
    $caminho = 'relatorios/' . $arquivo;
    if (file_exists($caminho)) {
        unlink($caminho);
        echo "<script>alert('Relatório " . $arquivo . " removido com sucesso.'); window.history.back();</script>";
        
    } else {
        echo "Arquivo não encontrado.";
    }
} else {
    echo "Nenhum arquivo especificado.";
}
?>
