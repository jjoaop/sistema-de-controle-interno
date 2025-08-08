<?php
session_start();
require 'config/conexao.php';
require 'config/functions-log.php';
require 'idafgsbsjdhk654wsd4/defesa_bruteforce.php';

$ip = $_SERVER['REMOTE_ADDR'];

verificarTentativasArquivo($ip);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim(filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING));
    $senha = trim(filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING));

    if (empty($usuario) || empty($senha)) {
        registrarTentativaArquivo($ip);
        header("Location: login.php?erro=preenchimento");
        exit;
    }

    $chave_secreta = 'seilaqualquercoisa';

    try {
        $stmt = $pdo->prepare("
            SELECT 
                id,
                CAST(AES_DECRYPT(usuario, ?) AS CHAR) AS usuario_dec,
                CAST(AES_DECRYPT(senha, ?) AS CHAR) AS senha_dec,
                tipo
            FROM usuarios
            WHERE CAST(AES_DECRYPT(usuario, ?) AS CHAR) = ?
        ");
        $stmt->execute([$chave_secreta, $chave_secreta, $chave_secreta, $usuario]);
        $usuarioDB = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuarioDB && hash_equals($usuarioDB['senha_dec'], $senha)) {
            session_regenerate_id(true);

            $_SESSION['usuario_id'] = $usuarioDB['id'];
            $_SESSION['usuario'] = $usuarioDB['usuario_dec'];
            $_SESSION['tipo'] = $usuarioDB['tipo'];

            registrarLog($pdo, $usuarioDB['id'], $usuarioDB['tipo'], 'Login realizado');

            header("Location: sistema/");
            exit;
        } else {
            registrarTentativaArquivo($ip);
            echo "<script>alert('Usuário ou senha inválidos'); history.back();</script>";
            exit;
        }
    } catch (PDOException $e) {
        echo "<script>alert('Usuário ou senha inválidos'); history.back();</script>";
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
?>
