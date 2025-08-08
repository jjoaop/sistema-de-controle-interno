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
    $senha = htmlspecialchars($_POST['senha']);

    if (empty($usuario) || empty($senha)) {
        $mensagem = "Por favor, preencha todos os campos.";
    } else {
        $chave_secreta = 'seilaqualquercoisa';

        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE CAST(AES_DECRYPT(usuario, ?) AS CHAR) = ? AND tipo = 'administrador'");
            $stmt->execute([$chave_secreta, $usuario]);
            $existe = $stmt->fetchColumn();

            if ($existe > 0) {
                $mensagem = "O administrador '$usuario' já está cadastrado.";
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO usuarios (usuario, senha, tipo)
                    VALUES (AES_ENCRYPT(?, ?), AES_ENCRYPT(?, ?), 'administrador')
                ");
                $stmt->execute([$usuario, $chave_secreta, $senha, $chave_secreta]);
                $mensagem = "Administrador '$usuario' cadastrado com sucesso!";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao cadastrar administrador: " . htmlspecialchars($e->getMessage());
        }
    }
}

$chave_secreta = 'seilaqualquercoisa';
$stmt = $pdo->prepare("
    SELECT CAST(AES_DECRYPT(usuario, ?) AS CHAR) AS usuario_dec
    FROM usuarios
    WHERE tipo = 'administrador' AND CAST(AES_DECRYPT(usuario, ?) AS CHAR) != 'Sudo_TI'
");
$stmt->execute([$chave_secreta, $chave_secreta]);
$administradores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Administrador</title>
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
    max-width: 800px;
    width: 100%;
}

label {
    font-size: 24px;
    margin-bottom: 12px;
    display: block;
}

input[type="text"], input[type="password"] {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border: none;
    border-radius: 5px;
    font-size: 20px;
    background-color: #444;
    color: #ff9613;
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
    width: 100%;
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

ul {
    list-style-type: none;
    padding: 0;
    margin-top: 20px;
}

li {
    background-color: #444;
    color: #ff9613;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 5px;
}

@media (max-width: 768px) {
    form {
        padding: 60px;
        max-width: 100%;
    }

    h1 {
        font-size: 4rem;
    }

    label, input, button {
        font-size: 32px;
    }
}

    </style>
</head>
<body>
    <h1>Adicionar Administrador</h1>

    <?php if (!empty($mensagem)) echo "<p style='color: green;'>$mensagem</p>"; ?>

    <form method="POST">
        <label for="usuario">Usuário:</label>
        <input type="text" id="usuario" name="usuario" required>
        <br>
        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>
        <br>
        <button type="submit">Adicionar Administrador</button>
    </form>

    <h2>Administradores Existentes</h2>
    <ul>
        <?php if (!empty($administradores)): ?>
            <?php foreach ($administradores as $admin): ?>
                <li><?php echo htmlspecialchars($admin['usuario_dec']); ?></li>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: red;">Nenhum administrador disponível para exibição.</p>
        <?php endif; ?>
    </ul>

    <a href="dashboard.php">Voltar ao Painel</a>
</body>
</html>
