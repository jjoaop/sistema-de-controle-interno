<?php
session_start();
require '../config/conexao.php';
require '../config/seguranca.php';
require '../libs/export_functions.php';
require '../config/functions-log.php';

if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit;
}

$chave_secreta = 'seilaqualquercoisa';

if (isset($_GET['limpar']) && $_GET['limpar'] === '1') {
    try {
        $stmt = $pdo->prepare("
            DELETE FROM logs
            WHERE usuario_id NOT IN (
                SELECT id FROM usuarios 
                WHERE CAST(AES_DECRYPT(usuario, ?) AS CHAR) = 'Sudo_TI'
            )
        ");
        $stmt->execute([$chave_secreta]);
        header('Location: logs_sistema.php?status=logs_limpados');
        exit;
    } catch (PDOException $e) {
        die("Erro ao limpar logs: " . htmlspecialchars($e->getMessage()));
    }
}

if (isset($_GET['exportar'])) {
    $tipo_exportacao = $_GET['exportar'];
    exportarLogs($pdo, $tipo_exportacao, $chave_secreta);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            logs.id,
            CAST(AES_DECRYPT(usuarios.usuario, ?) AS CHAR) AS usuario_dec,
            logs.tipo_usuario,
            logs.acao,
            logs.data
        FROM logs
        INNER JOIN usuarios ON logs.usuario_id = usuarios.id
        WHERE CAST(AES_DECRYPT(usuarios.usuario, ?) AS CHAR) != 'Sudo_TI'
        ORDER BY logs.data DESC
    ");
    $stmt->execute([$chave_secreta, $chave_secreta]);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao carregar logs: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Logs do Sistema</title>
    <style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background-color: black;
    color: #ff9613;
    line-height: 1.6;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    scroll-behavior: smooth;
    background-image: url('../adwsifgbwioq78657gjhkj/midia/cdn/imagem2.php');
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
}

* {
    box-sizing: border-box;
}

h1 {
    text-align: center;
    color: #ff9613;
    background-color: #333;
    padding: 20px 40px;
    border-radius: 5px;
    margin-bottom: 30px;
    font-size: 3rem;
}

.actions a {
    display: inline-block;
    margin: 10px;
    background-color: #ff9613;
    color: #fff;
    text-decoration: none;
    padding: 15px 30px;
    border-radius: 5px;
    font-size: 20px;
    transition: background-color 0.3s ease;
}

.actions a:hover {
    background-color: red;
}

table {
    width: 90%;
    max-width: 1200px;
    margin: 20px auto;
    border-collapse: collapse;
    background-color: #333;
    color: #ff9613;
}

th, td {
    padding: 15px;
    text-align: left;
    border: 1px solid #555;
    font-size: 18px;
}

th {
    background-color: #444;
    font-size: 20px;
}

.no-logs, p {
    font-size: 20px;
    text-align: center;
    margin: 15px 0;
}

a {
    display: inline-block;
    margin-top: 30px;
    background-color: #ff9613;
    color: #fff;
    text-decoration: none;
    padding: 15px 30px;
    border-radius: 5px;
    font-size: 20px;
    transition: background-color 0.3s ease;
}

a:hover {
    background-color: red;
}

@media (max-width: 768px) {
    h1 {
        font-size: 4rem;
    }

    .actions a, th, td, p {
        font-size: 24px;
        padding: 20px;
    }

    table {
        width: 100%;
    }
}

    </style>
</head>
<body>
    <h1>Logs do Sistema</h1>

    <div class="actions">
        <a href="?limpar=1" onclick="return confirm('Tem certeza de que deseja limpar os logs? Os registros de Sudo_TI permanecerão.');">Limpar Logs</a>
        <a href="?exportar=excel">Exportar Excel</a>
        <a href="?exportar=word">Exportar Word</a>
        <a href="?exportar=txt">Exportar TXT</a>
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] === 'logs_limpados'): ?>
        <p style="color: green;">Logs limpos com sucesso!</p>
    <?php endif; ?>

    <div class="results">
        <?php if (!empty($logs)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuário</th>
                        <th>Tipo</th>
                        <th>Ação</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($log['id']); ?></td>
                            <td><?php echo htmlspecialchars($log['usuario_dec']); ?></td>
                            <td><?php echo htmlspecialchars($log['tipo_usuario']); ?></td>
                            <td><?php echo htmlspecialchars($log['acao']); ?></td>
                            <td><?php echo date('d/m/Y H:i:s', strtotime($log['data'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-logs">Nenhum log encontrado no sistema.</p>
        <?php endif; ?>
    </div>

    <a href="dashboard.php">Voltar ao Painel</a>
</body>
</html>
