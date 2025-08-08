<?php
require '../../config/conexao.php';
require '../../config/seguranca.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../login.php");
    exit;
}

$id = intval($_GET['id']);
$anotacoes = htmlspecialchars($_GET['anotacoes']);

try {
    $stmt = $pdo->prepare("UPDATE mesas SET anotacoes = ? WHERE id = ?");
    $success = $stmt->execute([$anotacoes, $id]);

    if ($success) {
        $stmt = $pdo->prepare("SELECT * FROM mesas WHERE id = ?");
        $stmt->execute([$id]);
        $mesa = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($mesa) {
            $stmt = $pdo->prepare("
                INSERT INTO mesas_historico (mesa_id, nome_usuario, estado, tempo_inicio, tempo_fim, valor_por_hora, valor_acumulado, anotacoes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $mesa['id'],
                $mesa['nome_usuario'],
                $mesa['estado'],
                $mesa['tempo_inicio'],
                $mesa['tempo_fim'],
                $mesa['valor_por_hora'],
                $mesa['valor_acumulado'],
                $anotacoes
            ]);
        }

        echo json_encode(['success' => true, 'message' => 'Anotações salvas e registradas no histórico.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao salvar as anotações.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
?>
