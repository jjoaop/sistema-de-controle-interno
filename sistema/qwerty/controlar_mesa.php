<?php
require '../../config/conexao.php';
require '../../config/seguranca.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../login.php");
    exit;
}

/**
 * Salva o estado atual da mesa no histórico.
 *
 * @param PDO $pdo Conexão com o banco de dados.
 * @param int $mesa_id ID da mesa.
 */
function salvarNoHistorico($pdo, $mesa_id, $estado) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM mesas WHERE id = ?");
        $stmt->execute([$mesa_id]);
        $mesa = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($mesa) {
            $stmt = $pdo->prepare("
                INSERT INTO mesas_historico (mesa_id, nome_usuario, estado, tempo_inicio, tempo_fim, valor_por_hora, valor_acumulado, anotacoes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $mesa['id'],
                $mesa['nome_usuario'],
                $estado,
                $mesa['tempo_inicio'],
                $estado === 'inativa' ? date('Y-m-d H:i:s') : null,
                $mesa['valor_por_hora'],
                $mesa['valor_acumulado'],
                $mesa['anotacoes']
            ]);
        }
    } catch (PDOException $e) {
        error_log("Erro ao salvar no histórico: " . $e->getMessage());
    }
}

$action = $_GET['action'];
$id = intval($_GET['id']);

if ($action === 'ativar') {
    $nome = htmlspecialchars($_GET['nome']);
    $valor = floatval($_GET['valor']);
    $inicio = date('Y-m-d H:i:s');

    try {
        $stmt = $pdo->prepare("UPDATE mesas SET nome_usuario = ?, estado = 'ativa', tempo_inicio = ?, valor_por_hora = ?, valor_acumulado = valor_acumulado + ? WHERE id = ?");
        $stmt->execute([$nome, $inicio, $valor, $valor, $id]);

        salvarNoHistorico($pdo, $id, 'ativa');

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao ativar a mesa.']);
    }
} elseif ($action === 'desativar') {
    try {
        salvarNoHistorico($pdo, $id, 'inativa');

        $stmt = $pdo->prepare("UPDATE mesas SET nome_usuario = NULL, estado = 'inativa', tempo_inicio = NULL, valor_por_hora = 0, valor_acumulado = 0, anotacoes = NULL WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao desativar a mesa.']);
    }
}
?>
