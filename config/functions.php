<?php
session_start();
require 'conexao.php';
require 'seguranca.php';

if (!isset($_SESSION['admin'])) {
    echo "Acesso negado.";
    exit;
}

require 'conexao.php';

/**
 * Função para registrar logs no banco de dados
 *
 * @param PDO $pdo A conexão PDO com o banco de dados
 * @param int $usuario_id O ID do usuário que está realizando a ação
 * @param string $tipo_usuario O tipo de usuário (operador ou administrador)
 * @param string $acao A ação que está sendo registrada
 */

 function registrarLog($pdo, $usuario_id, $tipo_usuario, $acao) {
    if ($usuario_id === null) {
        echo "<script>alert('Usuário ou senha inválidos');</script>";
        return;
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO logs (usuario_id, tipo_usuario, acao) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$usuario_id, $tipo_usuario, $acao]);
    } catch (PDOException $e) {
        echo "<script>alert('Usuário ou senha inválidos');</script>";
    }
}

/**
 * Função para descriptografar o nome de um usuário
 *
 * @param PDO $pdo A conexão PDO com o banco de dados
 * @param int $usuario_id O ID do usuário a ser descriptografado
 * @param string $chave_secreta A chave secreta usada na criptografia
 * @return string O nome descriptografado do usuário
 */
function obterUsuarioDescriptografado($pdo, $usuario_id, $chave_secreta) {
    try {
        $stmt = $pdo->prepare("
            SELECT CAST(AES_DECRYPT(usuario, ?) AS CHAR) AS usuario_dec 
            FROM usuarios 
            WHERE id = ?
        ");
        $stmt->execute([$chave_secreta, $usuario_id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        return $usuario['usuario_dec'] ?? null;
    } catch (PDOException $e) {
        error_log("Erro ao descriptografar o usuário: " . $e->getMessage());
        return null;
    }
}

/**
 * Função para verificar se um usuário é administrador
 *
 * @param string $tipo_usuario O tipo de usuário
 * @return bool Retorna true se for administrador, false caso contrário
 */
function verificarAdministrador($tipo_usuario) {
    return $tipo_usuario === 'administrador';
}

/**
 * Função para redirecionar usuários não autenticados
 */
function redirecionarNaoAutenticado() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../login.php');
        exit;
    }
}

/**
 * Função para obter o nome do tipo de usuário em formato legível
 *
 * @param string $tipo_usuario O tipo do usuário
 * @return string O nome legível do tipo de usuário
 */
function obterNomeTipoUsuario($tipo_usuario) {
    return $tipo_usuario === 'administrador' ? 'Administrador' : 'Operador';
}
?>

