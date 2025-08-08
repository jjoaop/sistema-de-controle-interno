<?php
session_start();
require '../config/conexao.php';
require '../config/seguranca.php';

if (!isset($_SESSION['admin'])) {
    echo "Acesso negado.";
    exit;
}

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = htmlspecialchars($_POST['usuario']);

    if ($usuario === 'Sudo_TI') {
        $mensagem = "O administrador 'Sudo_TI' não pode ser removido.";
    } else {
        $chave_secreta = 'seilaqualquercoisa';

        try {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE CAST(AES_DECRYPT(usuario, ?) AS CHAR) = ? AND tipo = 'administrador'");
            $stmt->execute([$chave_secreta, $usuario]);

            $mensagem = "Administrador '$usuario' removido com sucesso!";
        } catch (PDOException $e) {
            $mensagem = "Erro ao remover administrador: " . htmlspecialchars($e->getMessage());
        }
    }
}

$chave_secreta = 'seilaqualquercoisa';

try {
    $stmt = $pdo->prepare("
        SELECT CAST(AES_DECRYPT(usuario, ?) AS CHAR) AS usuario_dec
        FROM usuarios
        WHERE tipo = 'administrador'
        AND CAST(AES_DECRYPT(usuario, ?) AS CHAR) != 'Sudo_TI'
    ");
    $stmt->execute([$chave_secreta, $chave_secreta]);
    $administradores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao carregar administradores: " . htmlspecialchars($e->getMessage());
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Remover Administrador</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: black;
            color: #ff9613;
            line-height: 1.6;
            scroll-behavior: smooth;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
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

        h2, li {
            text-align: center;
            color: #ff9613;
            background-color: #333;
            padding: 20px 40px;
            border-radius: 5px;
            margin-bottom: 30px;
            font-size: 2.2rem;
        }

        form {
            background-color: #333;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            max-width: 700px;
            width: 100%;
        }

        label {
            font-size: 24px;
            margin-bottom: 12px;
            display: block;
        }

        select {
            width: 90%;
            padding: 12px;
            margin-bottom: 20px;
            border: none;
            border-radius: 5px;
            font-size: 20px;
            background-color: #444;
            color: #ff9613;
        }

        select option {
            background-color: #222;
        }

        button {
            background-color: #ff9613;
            color: #fff;
            border: none;
            padding: 15px 30px;
            border-radius: 5px;
            font-size: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            width: 90%;
        }

        button:hover {
            background-color: red;
            transform: scale(1.05);
        }

        p {
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
            form {
                padding: 60px;
                max-width: 100%;
            }

            h1 {
                font-size: 4rem;
            }

            label, select {
                font-size: 32px;
            }

            a {
                font-size: 42px;
            }
        }

    </style>
</head>
<body>
    <h1>Remover Administrador</h1>

    <?php if (!empty($mensagem)) echo "<p style='color: green;'>$mensagem</p>"; ?>

    <?php if (!empty($administradores)): ?>
        <form method="POST">
            <label for="usuario">Selecione o Administrador:</label>
            <select id="usuario" name="usuario" required>
                <option value="" disabled selected>Escolha um administrador</option>
                <?php foreach ($administradores as $admin): ?>
                    <option value="<?php echo htmlspecialchars($admin['usuario_dec']); ?>">
                        <?php echo htmlspecialchars($admin['usuario_dec']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Remover</button>
        </form>
    <?php else: ?>
        <p style="color: red;">Nenhum administrador disponível para remover.</p>
    <?php endif; ?>

    <a href="dashboard.php">Voltar ao Painel</a>
</body>
</html>
