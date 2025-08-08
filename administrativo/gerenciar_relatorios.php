<?php 
session_start();
require '../config/conexao.php';
require '../config/seguranca.php';

if (!isset($_SESSION['admin'])) {
    echo "Acesso negado.";
    exit;
}
?>
<?php if (isset($_GET['status'])): ?>
    <?php if ($_GET['status'] === 'historico_limpo'): ?>
        <script>alert('Histórico de mesas limpo com sucesso!'); window.history.back();</script>
    <?php elseif ($_GET['status'] === 'erro_limpeza'): ?>
        <script>alert('Erro ao limpar o histórico de mesas.'); window.history.back();</script>
    <?php endif; ?>
<?php endif; ?>
