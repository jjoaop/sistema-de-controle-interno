<?php
session_start();
require '../config/conexao.php';
require '../config/seguranca.php';

if (!isset($_SESSION['admin'])) {
    echo "Acesso negado.";
    exit;
}


$stmt = $pdo->query("SELECT * FROM mesas WHERE estado = 'ativa'");
$mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Mesas Ativas</title>
    <style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background-color: black;
    color: #ff9613;
    line-height: 1.6;
    scroll-behavior: smooth;
    overflow-x: hidden;
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
    padding: 10px 0;
    border-radius: 5px;
}

.table-container {
    max-width: 1200px;
    margin: 50px auto;
    padding: 20px;
    background-color: #1e1e1ead;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    background-color: #333;
    color: #ff9613;
    text-align: left;
}

thead {
    background-color: #444;
}

thead th {
    padding: 10px;
    text-transform: uppercase;
}

tbody tr:nth-child(even) {
    background-color: #222;
}

tbody tr:hover {
    background-color: #555;
}

tbody td {
    padding: 10px;
    border-bottom: 1px solid #555;
}

.link-button {
    display: inline-block;
    margin-top: 20px;
    background-color: #ff9613;
    color: #fff;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

.link-button:hover {
    background-color: red;
}

@media (max-width: 768px) {
    .table-container {
        margin: 20px;
        padding: 15px;
    }

    h1 {
        font-size: 18px;
    }

    table, th, td {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .table-container {
        margin: 10px;
        padding: 10px;
    }

    h1 {
        font-size: 26px;
    }

    table, th, td {
        font-size: 18px;
        padding: 8px;
    }

    .link-button {
        font-size: 24px;
        padding: 8px 15px;
    }

    body {
        background-color: black;
    }

}

button {
    background-color: #ff9613;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
    transition: background-color 0.3s ease, transform 0.2s ease;
    display: inline-block;
}
    </style>
</head>
<body>
    <h1>Mesas Ativas</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuário</th>
                <th>Data de Ativação</th>
                <th>Valor Acumulado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mesas as $mesa): ?>
            <tr>
                <td><?php echo $mesa['id']; ?></td>
                <td><?php echo htmlspecialchars($mesa['nome_usuario']); ?></td>
                <td><?php echo $mesa['tempo_inicio'] ? date('d/m/Y H:i:s', strtotime($mesa['tempo_inicio'])) : 'N/A'; ?></td>
                <td>R$ <?php echo number_format($mesa['valor_acumulado'], 2, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>

    <div style="text-align: center;">
        <br><br><br><br><br>
        <button><a href="dashboard.php">Voltar ao Painel</a></button>
    </div>
    

</body>
</html>
