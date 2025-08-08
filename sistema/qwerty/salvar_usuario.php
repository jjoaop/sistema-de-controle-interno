<?php
require '../../config/conexao.php';
require '../../config/seguranca.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = htmlspecialchars($_POST['nome']);
    $cpf = htmlspecialchars($_POST['cpf']);
    $rg = htmlspecialchars($_POST['rg']) ?: null; // RG é opcional
    $data_nascimento = $_POST['data_nascimento'];

    $chave_secreta = 'minha_chave_super_segura';

    try {
        $stmt = $pdo->prepare("
            INSERT INTO usuarios_cadastrados (nome, cpf, rg, data_nascimento) 
            VALUES (?, AES_ENCRYPT(?, ?), ?, ?)
        ");
        $stmt->execute([$nome, $cpf, $chave_secreta, $rg, $data_nascimento]);

        echo "<script>
            alert('Usuário cadastrado com sucesso!');
            window.history.back();
        </script>";
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $mensagemErro = "Erro: O CPF informado já está cadastrado.";
        } else {
            $mensagemErro = "Erro ao cadastrar usuário: " . $e->getMessage();
        }

        echo "<script>
            alert('$mensagemErro');
            window.history.back();
        </script>";
        exit;
    }
} else {
    echo "<script>
        alert('Método de requisição inválido.');
        window.history.back();
    </script>";
    exit;
}
?>
