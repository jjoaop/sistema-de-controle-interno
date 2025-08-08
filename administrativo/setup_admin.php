<?php
require '../config/conexao.php';
require '../config/seguranca.php';

$usuario = 'Sudo_TI';
$senha = 'teste'; 

$chave_secreta = 'seilaqualquercoisa';

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE CAST(AES_DECRYPT(usuario, ?) AS CHAR) = ?");
    $stmt->execute([$chave_secreta, $usuario]);
    $existe = $stmt->fetchColumn();

    if ($existe) {
        echo "O usuário 'Sudo_TI' já existe no sistema.";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO usuarios (usuario, senha, tipo)
            VALUES (AES_ENCRYPT(?, ?), AES_ENCRYPT(?, ?), 'administrador')
        ");
        $stmt->execute([$usuario, $chave_secreta, $senha, $chave_secreta]);
        echo "Administrador 'Sudo_TI' cadastrado com sucesso!";
    }
} catch (PDOException $e) {
    echo "Erro ao cadastrar administrador: " . htmlspecialchars($e->getMessage());
}
?>
