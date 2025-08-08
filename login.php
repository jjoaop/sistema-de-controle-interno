<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <style>
body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    background-image: url('public-img/img.jpeg');
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
    color: #eaeaea;
    scroll-behavior: smooth;
    overflow-x: hidden;
    background-color: black;
}

* {
    box-sizing: border-box;
}

h2 {
    text-align: center;
    color: #ff9613;
    background-color: #333;
    padding: 10px 0;
    border-radius: 5px;
}

form {
    background-color: #1e1e1ead;
    padding: 20px;
    margin: 50px auto;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.6);
    max-width: 400px;
    width: 90%;
}

label {
    font-weight: bold;
    color: #ff9613;
    display: block;
    margin-top: 10px;
}

input[type="text"], input[type="password"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #555;
    border-radius: 5px;
    background-color: #333;
    color: #fff;
    font-size: 16px;
}

button {
    width: 100%;
    background-color: #1e90ff;
    color: #fff;
    border: none;
    padding: 10px 20px;
    margin: 10px 0;
    cursor: pointer;
    border-radius: 5px;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #007acc;
}

.error-message p {
    color: #ff4d4d;
    text-align: center;
    font-weight: bold;
}

.form-footer {
    text-align: center;
    margin-top: 10px;
}

.form-footer p {
    margin: 5px 0;
    color: #eaeaea;
}

.form-footer a {
    color: #1e90ff;
    text-decoration: none;
    font-weight: bold;
}

.form-footer a:hover {
    text-decoration: underline;
}

@media (max-width: 600px) {
    form {
        margin: 20px;
        padding: 15px;
    }

    h2 {
        font-size: 18px;
    }

    input[type="text"], input[type="password"], button {
        font-size: 14px;
    }

    .form-footer p {
        font-size: 14px;
    }
}


    </style>
</head>
<body>

    <br><br><br><br><br><br><br><br>
    <form action="autenticar.php" method="post">
        <h2>Login</h2>
        <div class="error-message">
            <?php if (isset($_GET['erro'])): ?>
                <p><?= htmlspecialchars($_GET['erro']); ?></p>
            <?php endif; ?>
        </div>
        <form action="autenticar.php" method="post">
            <label for="usuario">Usuário:</label>
            <input type="text" name="usuario" id="usuario" required>
            <label for="senha">Senha:</label>
            <input type="password" name="senha" id="senha" required>
            <button type="submit">Entrar</button>
        </form>


        <div class="form-footer">
            <br><br><br><br>
            <p><a href="#" style="color: yellow; font-size: 20px;" onclick="esquecisenha()">Esqueci minha senha</a></p>

            <script>
                function esquecisenha() {
                    alert("Entre em contato com o Administrador ou suporte de T.I.");
                }
                </script>
        </div>
    </form>
</body>
</html>


