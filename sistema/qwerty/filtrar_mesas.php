<?php
require '../../config/conexao.php';
require '../../config/seguranca.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../login.php");
    exit;
}

$filtro = htmlspecialchars($_GET['filtro']);

try {
    if (strtolower($filtro) === 'ativas') {
        $stmt = $pdo->query("SELECT * FROM mesas WHERE estado = 'ativa'");
    } else {
        echo json_encode(['success' => false, 'message' => 'Filtro inválido.']);
        exit;
    }

    $mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($mesas) === 0) {
        echo json_encode(['success' => false, 'message' => 'Nenhuma mesa ativa encontrada.']);
        exit;
    }

    $html = '';
    foreach ($mesas as $mesa) {
        $html .= '<div class="mesa ativa" id="mesa-' . $mesa['id'] . '">';
        $html .= '<h3>Mesa ' . $mesa['id'] . '</h3>';
        $html .= '<p><strong>Tempo:</strong> <span id="tempo-' . $mesa['id'] . '">0h 0m 0s</span></p>';
        $html .= '<p><strong>Valor Acumulado:</strong> <span id="acumulado-' . $mesa['id'] . '">R$ ' . number_format($mesa['valor_acumulado'], 2, ',', '.') . '</span></p>';
        $html .= '<button onclick="desativarMesa(' . $mesa['id'] . ')">Desativar</button>';
        $html .= '</div>';
    }

    echo json_encode(['success' => true, 'html' => $html, 'mesasAtivas' => $mesas]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao filtrar mesas: ' . $e->getMessage()]);
}
?>
