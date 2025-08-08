<?php
require '../config/conexao.php';

/**
 * Exporta os logs do sistema no formato solicitado.
 *
 * @param PDO $pdo Conexão com o banco de dados.
 * @param string $tipo Tipo de exportação: 'excel', 'word', 'txt'.
 * @param string $chave_secreta Chave usada na descriptografia.
 */
function exportarLogs($pdo, $tipo, $chave_secreta) {
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

        if (empty($logs)) {
            echo "<script>
                    alert('Nenhum registro encontrado para exportação.');
                    window.history.back();
                </script>";
            die();
        }

        $filename = "logs_sistema_" . date('Y-m-d_H-i-s');

        switch ($tipo) {
            case 'excel':
                exportarExcel($logs, $filename);
                break;
            case 'word':
                exportarWord($logs, $filename);
                break;
            case 'txt':
                exportarTXT($logs, $filename);
                break;
            default:
                die("Formato de exportação inválido.");
        }
    } catch (PDOException $e) {
        die("Erro ao exportar logs: " . htmlspecialchars($e->getMessage()));
    }
}

/**
 * Exporta os logs para um arquivo TXT.
 */
function exportarTXT($logs, $filename) {
    header('Content-Type: text/plain');
    header("Content-Disposition: attachment; filename={$filename}.txt");

    echo "Logs do Sistema\n\n";

    foreach ($logs as $log) {
        echo "ID: {$log['id']}\n";
        echo "Usuário: {$log['usuario_dec']}\n";
        echo "Tipo: {$log['tipo_usuario']}\n";
        echo "Ação: {$log['acao']}\n";
        echo "Data: " . date('d/m/Y H:i:s', strtotime($log['data'])) . "\n";
        echo "---------------------------\n";
    }

    exit;
}

/**
 * Exporta os logs para um arquivo Excel.
 */
function exportarExcel($logs, $filename) {
    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename={$filename}.xls");

    echo "ID\tUsuário\tTipo\tAção\tData\n";

    foreach ($logs as $log) {
        echo "{$log['id']}\t";
        echo "{$log['usuario_dec']}\t";
        echo "{$log['tipo_usuario']}\t";
        echo "{$log['acao']}\t";
        echo date('d/m/Y H:i:s', strtotime($log['data'])) . "\n";
    }

    exit;
}

/**
 * Exporta os logs para um arquivo Word de formato simples e limpo.
 */
function exportarWord($logs, $filename) {
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header("Content-Disposition: attachment; filename={$filename}.doc");

    echo "Logs do Sistema\n\n";

    foreach ($logs as $log) {
        echo "ID: {$log['id']}\n";
        echo "Usuário: {$log['usuario_dec']}\n";
        echo "Tipo: {$log['tipo_usuario']}\n";
        echo "Ação: {$log['acao']}\n";
        echo "Data: " . date('d/m/Y H:i:s', strtotime($log['data'])) . "\n";
        echo "---------------------------\n";
    }

    exit;
}
?>
