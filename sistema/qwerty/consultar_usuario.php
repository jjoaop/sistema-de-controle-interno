<?php
require '../../config/conexao.php';
require '../../config/seguranca.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $entrada = htmlspecialchars($_GET['consulta']);

    $chave_secreta = 'seilaqualquercoisa';

    try {
        $stmt = $pdo->prepare("
            SELECT 
                nome, 
                CAST(AES_DECRYPT(cpf, ?) AS CHAR) AS cpf, 
                rg, 
                data_nascimento, 
                criado_em 
            FROM usuarios_cadastrados 
            WHERE nome LIKE ? OR CAST(AES_DECRYPT(cpf, ?) AS CHAR) = ? OR rg = ?
        ");
        $stmt->execute([$chave_secreta, "%$entrada%", $chave_secreta, $entrada, $entrada]);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($usuarios) {
            echo "<h2>Usuários cadastrados</h2>";

            foreach ($usuarios as $usuario) {
                echo "<div style='margin-bottom: 20px; border: 1px solid #ccc; padding: 10px;'>";
                echo "<p><strong>Nome:</strong> " . htmlspecialchars($usuario['nome']) . "</p>";
                echo "<p><strong>CPF:</strong> " . htmlspecialchars($usuario['cpf']) . "</p>";
                echo "<p><strong>RG:</strong> " . htmlspecialchars($usuario['rg'] ?: 'Não informado') . "</p>";
                echo "<p><strong>Data de Nascimento:</strong> " . htmlspecialchars($usuario['data_nascimento']) . "</p>";
                echo "<p><strong>Data de Cadastro:</strong> " . htmlspecialchars($usuario['criado_em']) . "</p>";
                echo "</div>";
            }
        } else {
            echo '<h2 style="color: red; background-color: #ffffff00;">Nenhum usuário encontrado!</h2>';
        }
    } catch (PDOException $e) {
        echo "<script>
            alert('Erro ao consultar usuário: " . htmlspecialchars($e->getMessage()) . "');
            window.history.back();
        </script>";
    }
} else {
    echo "<script>
        alert('Método de requisição inválido.');
        window.history.back();
    </script>";
}
?>
