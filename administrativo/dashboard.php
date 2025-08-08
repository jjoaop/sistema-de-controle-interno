<?php
session_start();
require '../config/conexao.php';
require '../config/seguranca.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$adminAtual = $_SESSION['admin'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrativo</title>
    <style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #1e1e1ead;
    color: #ff9613;
    line-height: 1.6;
    scroll-behavior: smooth;
    background-image: url('../adwsifgbwioq78657gjhkj/midia/cdn/imagem1.php');
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
}

h1, h2 {
    text-align: center;
    color: #ff9613;
    margin: 20px 0;
}

h1 {
    font-size: 2rem;
    background-color: #111;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h2 {
    font-size: 1.5rem;
    color: #ff9613;
}

a {
    color: #3498db;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
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

button:hover {
    background-color: red;
    transform: scale(1.05);
}

button a {
    color: #fff;
    text-decoration: none;
}

.dashboard-section {
    margin: 30px auto;
    padding: 20px;
    max-width: 1200px;
    background-color: #1e1e1ead;
    border-radius: 10px;
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
}

.dashboard-section h2 {
    margin-bottom: 20px;
    font-size: 1.2rem;
    color: #ff9613;
    text-transform: uppercase;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.dashboard-item {
    background-color: #111;
    color: #ff9613;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.dashboard-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.dashboard-item a {
    font-size: 1.1rem;
    color: #fff;
}

.dashboard-item a:hover {
    text-decoration: underline;
}

ul {
    list-style-type: none;
    padding: 0;
}

ul li {
    background-color: #000000ad;
    margin: 10px 0;
    padding: 15px;
    border-radius: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

ul li a {
    color: #fff;
    text-decoration: none;
    font-size: 1rem;
}

ul li a:hover {
    text-decoration: underline;
}

form {
    margin-top: 30px;
    text-align: center;
}

form button {
    width: 100%;
    max-width: 300px;
}

footer {
    text-align: center;
    padding: 15px;
    background-color: #111;
    color: #fff;
    font-size: 0.9rem;
    position: fixed;
    width: 100%;
    bottom: 0;
}

@media (max-width: 768px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}


    </style>
</head>
<body>
    <h1>Bem-vindo ao Painel Administrativo, <?php echo htmlspecialchars($adminAtual); ?>! <br> <button><a href="logout.php">Sair</a></button></h1>
    

    <div class="dashboard-section">
        <h2>Gerenciamento de Mesas</h2>
        <div class="dashboard-grid">
            <div class="dashboard-item">
                <a href="ver_mesas.php">Ver Mesas Ativas</a>
            </div>
            <div class="dashboard-item">
                <a href="exportar_faturamento.php">Exportar Faturamento</a>
            </div>
        </div>
    </div>

    <div class="dashboard-section">
        <h2>Gerenciamento de Usuários</h2>
        <div class="dashboard-grid">
            <div class="dashboard-item">
                <a href="remover_operador.php">Remover Operador</a>
            </div>
            <div class="dashboard-item">
                <a href="adicionar_operador.php">Adicionar Operador</a>
            </div>
            <div class="dashboard-item">
                <a href="adicionar_administrador.php">Adicionar Administrador</a>
            </div>
            <div class="dashboard-item">
                <a href="remover_administrador.php">Remover Administrador</a>
            </div>
        </div>
    </div>

    <div class="dashboard-section">
        <h2>Configurações</h2>
        <div class="dashboard-grid">
            <div class="dashboard-item">
                <a href="game/index.php">Canguru</a>
            </div>
            <div class="dashboard-item">
                <a href="logs_sistema.php">Logs do Sistema</a>
            </div>
        </div>
    </div>

    <div>
        <?php
        $dir = 'relatorios/';
        $arquivos = array_diff(scandir($dir), ['.', '..', 'index.php']);
        ?>

        <h1>Gerenciar Relatórios</h1>

        <?php if (!empty($arquivos)): ?>
            <ul>
                <?php foreach ($arquivos as $arquivo): ?>
                    <li>
                        <?php echo $arquivo; ?>
                        <a href="<?php echo $dir . $arquivo; ?>" download>Download</a>
                        <a href="remover_relatorio.php?arquivo=<?php echo urlencode($arquivo); ?>">Excluir</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p style="text-align: center; color: white;">Nenhum relatório disponível.</p>
        <?php endif; ?>

        <!-- Botão para limpar histórico -->
        <form action="limpar_historico.php" method="post" onsubmit="return confirm('Tem certeza que deseja limpar todos os registros do histórico?');">
            <button type="submit">Limpar Histórico de Mesas</button>
        </form>
    </div>
    
    <br><br><br><br><br>

    <footer style="position: fixed; width: 100%">
        <p>&copy; <?php echo date('d-m-Y'); ?> / Canguru Depósito de Bebidas</p>
    </footer>

</body>
</html>
