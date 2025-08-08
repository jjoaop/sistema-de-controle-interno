<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema</title>
    <link rel="stylesheet" href="js-css/style.css">
    <script>
        function validarCPF(cpf) {
            cpf = cpf.replace(/[^\d]+/g, '');
            if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;

            let soma = 0, resto;
            for (let i = 1; i <= 9; i++) soma += parseInt(cpf.substring(i - 1, i)) * (11 - i);
            resto = (soma * 10) % 11;
            if ((resto === 10) || (resto === 11)) resto = 0;
            if (resto !== parseInt(cpf.substring(9, 10))) return false;

            soma = 0;
            for (let i = 1; i <= 10; i++) soma += parseInt(cpf.substring(i - 1, i)) * (12 - i);
            resto = (soma * 10) % 11;
            if ((resto === 10) || (resto === 11)) resto = 0;
            return resto === parseInt(cpf.substring(10, 11));
        }

        function validarFormulario(event) {
            const cpfInput = document.getElementById('cpf');
            const cpf = cpfInput.value;

            if (!validarCPF(cpf)) {
                alert('CPF inválido. Por favor, insira um CPF válido.');
                cpfInput.focus();
                event.preventDefault();
            }
        }

        function consultarUsuario(event) {
            event.preventDefault();

            const consulta = document.getElementById('consulta').value;
            const resultadoDiv = document.getElementById('resultado');

            if (!consulta) {
                resultadoDiv.innerHTML = '<p style="color:red;">Digite um nome, CPF ou RG para consultar.</p>';
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open('GET', `qwerty/consultar_usuario.php?consulta=${encodeURIComponent(consulta)}`, true);

            xhr.onload = function () {
                if (xhr.status === 200) {
                    resultadoDiv.innerHTML = xhr.responseText;
                } else {
                    resultadoDiv.innerHTML = '<p style="color:red;">Erro ao realizar a consulta.</p>';
                }
            };

            xhr.onerror = function () {
                resultadoDiv.innerHTML = '<p style="color:red;">Erro na conexão com o servidor.</p>';
            };

            xhr.send();
        }
    </script>

    <script>
        const mesas = {};

        function buscarMesa() {
            const filtro = document.getElementById('filtro').value;

            fetch(`qwerty/buscar_mesa.php?filtro=${encodeURIComponent(filtro)}`)
                .then(res => res.json())
                .then(data => {
                    const resultadoDiv = document.getElementById('resultado-busca');
                    resultadoDiv.innerHTML = data.html || `<p style="color:red;">${data.message}</p>`;
                });
        }

        function ativarMesa(id) {
            const nome = document.getElementById(`nome-${id}`).value;
            const valorPorHora = parseFloat(document.getElementById(`valor-${id}`).value);

            if (!nome || isNaN(valorPorHora) || valorPorHora <= 0) {
                alert('Preencha o nome e o valor por hora corretamente.');
                return;
            }

            fetch(`qwerty/controlar_mesa.php?action=ativar&id=${id}&nome=${encodeURIComponent(nome)}&valor=${valorPorHora}`)
                .then(res => res.json())
                .then(() => location.reload());
        }

        function desativarMesa(id) {
            fetch(`qwerty/controlar_mesa.php?action=desativar&id=${id}`)
                .then(res => res.json())
                .then(() => location.reload());
        }

        function salvarAnotacoes(id) {
            const anotacoes = document.getElementById(`anotacoes-${id}`).value;

            fetch(`qwerty/salvar_anotacoes.php?id=${id}&anotacoes=${encodeURIComponent(anotacoes)}`)
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        alert('Erro ao salvar as anotações.');
                    }
                });
        }
    </script>

</head>
<body>
    <main>
        <header>
            <nav class="menu" style="position: fixed; width: 100%;">
                <div class="logo">
                    <a href="#">Sistema - <?php echo htmlspecialchars($_SESSION['usuario']); ?></a>
                </div>
                <ul class="menu-links">
                    <li><a href="#">Início</a></li>
                    <li><a href="#f-cadastro">Cadastro</a></li>
                    <li><a href="#f-consulta">Consulta</a></li>
                    <li><a href="#v-mesas">Mesas</a></li>
                    <li><a href="../logout.php">Sair</a></li>
                </ul>
                <div class="menu-toggle" id="menuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </nav>
        </header>
        <br><br>
        <section id="f-cadastro">
            <h2 style="margin-top: 0px;">Cadastro de Usuários</h2>
            <form action="qwerty/salvar_usuario.php" method="post" onsubmit="validarFormulario(event)">
                <label for="nome">Nome:</label><br>
                <input type="text" id="nome" name="nome" required><br><br>

                <label for="cpf">CPF:</label><br>
                <input type="text" id="cpf" name="cpf" maxlength="14" placeholder="000.000.000-00" required><br><br>

                <label for="rg">RG (opcional):</label><br>
                <input type="text" id="rg" name="rg" maxlength="20"><br><br>

                <label for="data_nascimento">Data de Nascimento:</label><br>
                <input type="date" id="data_nascimento" name="data_nascimento" required><br><br>

                <button type="submit">Cadastrar</button>
            </form>
        </section>


        <section id="f-consulta">
            <h2>Consulta de Usuários</h2>
            <form onsubmit="consultarUsuario(event)">
                <label for="consulta">Nome, CPF ou RG:</label><br>
                <input type="text" id="consulta" name="consulta" placeholder="Digite nome, CPF ou RG" required><br><br>
                <button type="submit">Consultar</button><button style="margin-left: 90px;" onclick="limparResultados()">Limpar</button>
                <script>
                    function limparResultados() {
                        location.reload();
                    }
                </script>
            </form>
            <div id="resultado" style="margin-top: 20px; border: 1px solid #ccc; padding: 10px; background-color: black;">
                <p style="text-align: center;">O resultado da consulta será exibido aqui.</p>
            </div>
        </section>



        <section id="v-mesas">
            <h3>Verificar mesas</h3>
            <div class="controle-d-mesas">
                <label for="filtro">Buscar Mesas:</label>
                <input type="text" id="filtro" placeholder="Ativa, Inativa ou número da mesa">
                <button onclick="buscarMesa()">Buscar</button>
            </div>
            <div id="resultado-busca"></div>

            <h2 style="margin-bottom: 0px;">Cadastrar Mesas</h2>
            <div id="mesas-container">
                <?php
                require '../config/conexao.php';

                $stmt = $pdo->query("SELECT * FROM mesas");
                while ($mesa = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <div class="mesa <?php echo $mesa['estado'] === 'ativa' ? 'ativa' : ''; ?>" id="mesa-<?php echo $mesa['id']; ?>">
                        <h3>Mesa <?php echo $mesa['id']; ?></h3>
                        <p><strong>Estado:</strong> <?php echo ucfirst($mesa['estado']); ?></p>
                        <p><strong>Usando:</strong> <?php echo htmlspecialchars($mesa['nome_usuario'] ?? 'N/A'); ?></p>
                        <p><strong>Início:</strong> <?php echo $mesa['tempo_inicio'] ? date('H:i:s', strtotime($mesa['tempo_inicio'])) : 'Não ativada'; ?></p>
                        <p><strong>Valor da mesa:</strong> R$ <?php echo number_format($mesa['valor_acumulado'], 2, ',', '.'); ?></p>
                        
                        <?php if ($mesa['estado'] === 'ativa'): ?>
                            <p><strong>Hora atual:</strong> <?php echo date('H:i:s')?></p>
                            <textarea id="anotacoes-<?php echo $mesa['id']; ?>" placeholder="Anotações..." rows="4" cols="20"><?php echo htmlspecialchars($mesa['anotacoes']); ?></textarea>
                            <button onclick="salvarAnotacoes(<?php echo $mesa['id']; ?>)">Salvar Anotações</button>
                            <button onclick="desativarMesa(<?php echo $mesa['id']; ?>)">Desativar</button>
                        <?php else: ?>
                            <input type="text" id="nome-<?php echo $mesa['id']; ?>" placeholder="Nome do usuário">
                            <input type="number" id="valor-<?php echo $mesa['id']; ?>" placeholder="Valor por hora">
                            <button onclick="ativarMesa(<?php echo $mesa['id']; ?>)">Ativar</button>
                        <?php endif; ?>
                    </div>
                    <?php
                }
                ?>
            </div>

            <script>
                function toggleMesasContainer() {
                    const mesasContainer = document.getElementById('mesas-container');

                    if (mesasContainer.style.display === 'none' || mesasContainer.style.display === '') {
                        mesasContainer.style.display = 'block';
                    } else {
                        mesasContainer.style.display = 'none';
                    }
                }

                document.addEventListener('DOMContentLoaded', function () {
                    const toggleButton = document.createElement('button');
                    toggleButton.textContent = 'Controle de mesas';
                    toggleButton.style.position = 'fixed';
                    toggleButton.style.top = '50%';
                    toggleButton.style.right = '20px';
                    toggleButton.style.transform = 'translateY(-50%)';
                    toggleButton.style.padding = '10px 20px';
                    toggleButton.style.backgroundColor = '#ff9613';
                    toggleButton.style.color = '#fff';
                    toggleButton.style.border = 'none';
                    toggleButton.style.borderRadius = '50px';
                    toggleButton.style.cursor = 'pointer';
                    toggleButton.style.zIndex = '1000';
                    toggleButton.style.fontSize = '18px';
                    toggleButton.style.fontWeight = 'bold';
                    toggleButton.onclick = toggleMesasContainer;

                    document.body.appendChild(toggleButton);
                });

            </script>
        </section>
        
        <br><br><br>
        
        <footer style="position: fixed; width: 100%">
            <p>&copy; 2024 Canguru Depósito de Bebidas</p>
        </footer>
        <script src="js-css/menu.js"></script>
    <main>
</body>

