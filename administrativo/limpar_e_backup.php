<?php
session_start();
require '../config/conexao.php';
require '../config/seguranca.php';

if (!isset($_SESSION['admin'])) {
    echo "Acesso negado.";
    exit;
}

try {
    $hoje = date('Y-m-d');
    $stmt = $pdo->prepare("
        SELECT 
            mesa_id, nome_usuario, estado, tempo_inicio, tempo_fim, valor_por_hora, 
            valor_acumulado, anotacoes, data_registro
        FROM mesas_historico
        WHERE DATE(data_registro) = ?
    ");
    $stmt->execute([$hoje]);
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($registros)) {
        echo "Nenhum registro encontrado para o dia $hoje.";
        exit;
    }

    $filename = "relatorios/faturamento_$hoje.csv";

    if (!file_exists('relatorios/')) {
        mkdir('relatorios/', 0777, true);
    }

    $file = fopen($filename, 'w');

    fputcsv($file, [
        'ID Mesa', 'Usuário', 'Estado', 'Início', 
        'Término', 'Valor/Hora (R$)', 'Valor Total (R$)', 'Anotações', 'Data do Registro'
    ]);

    foreach ($registros as $registro) {
        $inicio = $registro['tempo_inicio'] ? date('d/m/Y H:i:s', strtotime($registro['tempo_inicio'])) : 'N/A';
        $fim = $registro['tempo_fim'] ? date('d/m/Y H:i:s', strtotime($registro['tempo_fim'])) : 'N/A';

        fputcsv($file, [
            $registro['mesa_id'],
            $registro['nome_usuario'] ?? 'N/A',
            ucfirst($registro['estado']),
            $inicio,
            $fim,
            number_format($registro['valor_por_hora'], 2, ',', '.'),
            number_format($registro['valor_acumulado'], 2, ',', '.'),
            $registro['anotacoes'] ?? 'Sem anotações',
            date('d/m/Y H:i:s', strtotime($registro['data_registro']))
        ]);
    }

    fclose($file);

    $stmt = $pdo->prepare("DELETE FROM mesas_historico WHERE DATE(data_registro) = ?");
    $stmt->execute([$hoje]);

    echo "Relatório do dia $hoje salvo em $filename e registros limpos.";
} catch (PDOException $e) {
    die("Erro ao processar o backup: " . $e->getMessage());
}
?>
