<?php
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Content-Security-Policy: default-src \'self\'');

$file = '../bg/imgs/img2.jpeg'; // Caminho da imagem no servidor

if (file_exists($file)) {
    header('Content-Type: image/jpeg');
    readfile($file);
    exit;
} else {
    http_response_code(404);
    echo "Imagem não encontrada.";
    exit;
}
?>
