<?php
session_start();
require '../config/conexao.php';
require '../config/seguranca.php';

if (!isset($_SESSION['admin'])) {
    echo "Acesso negado.";
    exit;
}

try {
    $dataInicio = $_GET['data_inicio'] ?? null;
    $dataFim = $_GET['data_fim'] ?? null;

    $sql = "
        SELECT 
            h.mesa_id,
            h.nome_usuario,
            h.estado,
            h.tempo_inicio,
            h.tempo_fim,
            h.valor_por_hora,
            h.valor_acumulado,
            h.anotacoes,
            h.data_registro
        FROM mesas_historico h
        INNER JOIN (
            SELECT mesa_id, nome_usuario, MAX(data_registro) AS ultima_atualizacao
            FROM mesas_historico
            GROUP BY mesa_id, nome_usuario
        ) agrupados ON h.mesa_id = agrupados.mesa_id 
                    AND h.nome_usuario = agrupados.nome_usuario 
                    AND h.data_registro = agrupados.ultima_atualizacao
    ";

    $params = [];
    if ($dataInicio && $dataFim) {
        $sql .= " WHERE h.data_registro BETWEEN ? AND ?";
        $params = [$dataInicio . " 00:00:00", $dataFim . " 23:59:59"];
    }
    $sql .= " ORDER BY h.data_registro DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $historico = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($historico)) {
        echo "<script>alert('Nenhum registro encontrado para exportação.'); window.history.back();</script>";
        die();
    }

    $dataHoje = date('Y-m-d');
    $filename = "faturamento_$dataHoje.csv";
    $path = "relatorios/$filename";

    if (!file_exists('relatorios')) {
        mkdir('relatorios', 0777, true);
    }

    $file = fopen($path, 'w');

    fputcsv($file, ["Relatório de Faturamento - Gerado em " . date('d/m/Y H:i:s')]);
    fputcsv($file, ["Período: " . ($dataInicio ? $dataInicio : "Sem início") . " a " . ($dataFim ? $dataFim : "Sem fim")]);
    fputcsv($file, []);

    fputcsv($file, [
        'ID Mesa', 'Usuário', 'Estado', 'Início', 
        'Término', 'Duração (Horas:Minutos)', 'Valor/Hora (R$)', 'Valor Total Calculado (R$)', 'Anotações', 'Data do Registro'
    ]);

    $faturamentoTotal = 0;

    foreach ($historico as $registro) {
        $inicio = $registro['tempo_inicio'] ? new DateTime($registro['tempo_inicio']) : null;
        $fim = $registro['tempo_fim'] ? new DateTime($registro['tempo_fim']) : null;
        $valorPorHora = floatval($registro['valor_por_hora']);
        $valorTotalCalculado = 0.00;
        $duracao = "N/A";

        if ($inicio && $fim) {
            $intervalo = $inicio->diff($fim);
            $totalMinutos = ($intervalo->days * 24 * 60) + ($intervalo->h * 60) + $intervalo->i;
            $intervalosDe61Minutos = ceil($totalMinutos / 61);
            $valorTotalCalculado = $intervalosDe61Minutos * $valorPorHora;

            $duracao = sprintf("%02d:%02d", floor($totalMinutos / 60), $totalMinutos % 60);
        }

        $faturamentoTotal += $valorTotalCalculado;

        fputcsv($file, [
            $registro['mesa_id'],
            $registro['nome_usuario'] ?? 'N/A',
            ucfirst($registro['estado']),
            $inicio ? $inicio->format('d/m/Y H:i:s') : 'N/A',
            $fim ? $fim->format('d/m/Y H:i:s') : 'N/A',
            $duracao,
            number_format($valorPorHora, 2, ',', '.'),
            number_format($valorTotalCalculado, 2, ',', '.'),
            $registro['anotacoes'] ?? 'Sem anotações',
            date('d/m/Y H:i:s', strtotime($registro['data_registro']))
        ]);
    }

    fputcsv($file, []);
    fputcsv($file, ['Faturamento Total:', '', '', '', '', '', '', number_format($faturamentoTotal, 2, ',', '.'), '', '']);

    fclose($file);

    header('Content-Type: application/csv');
    header("Content-Disposition: attachment; filename=$filename");
    readfile($path);
    exit;
} catch (PDOException $e) {
    die("Erro ao buscar registros: " . htmlspecialchars($e->getMessage()));
}
?>
