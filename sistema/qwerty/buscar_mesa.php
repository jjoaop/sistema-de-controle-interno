<?php
require '../../config/conexao.php';
require '../../config/seguranca.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../login.php");
    exit;
}

$filtro = htmlspecialchars($_GET['filtro']);

if (is_numeric($filtro)) {
    $stmt = $pdo->prepare("SELECT * FROM mesas WHERE id = ?");
    $stmt->execute([intval($filtro)]);
} elseif ($filtro === 'ativa') {
    $stmt = $pdo->query("SELECT * FROM mesas WHERE estado = 'ativa'");
} elseif ($filtro === 'inativa') {
    $stmt = $pdo->query("SELECT * FROM mesas WHERE estado = 'inativa'");
} else {
    echo json_encode(['success' => false, 'message' => 'Filtro inválido.']);
    exit;
}

$mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($mesas) === 0) {
    echo json_encode(['success' => false, 'message' => 'Nenhuma mesa encontrada.']);
    exit;
}

$html = '';
foreach ($mesas as $mesa) {
    $html .= '<div class="mesa">';
    $html .= '<h3>Mesa ' . $mesa['id'] . '</h3>';
    $html .= '<p><strong>Estado:</strong> ' . $mesa['estado'] . '</p>';
    $html .= '<p><strong>Usuário:</strong> ' . ($mesa['nome_usuario'] ?? 'N/A') . '</p>';
    $html .= '</div>';
}

echo json_encode(['success' => true, 'html' => $html]);
?>
