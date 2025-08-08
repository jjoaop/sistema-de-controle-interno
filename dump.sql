-- Tabela de usuários
CREATE TABLE `usuarios` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `usuario` VARBINARY(255) NOT NULL,
  `senha` VARBINARY(255) NOT NULL,
  `tipo` ENUM('administrador', 'operador') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de logs
CREATE TABLE `logs` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT NOT NULL,
  `tipo_usuario` VARCHAR(50) NOT NULL,
  `acao` TEXT NOT NULL,
  `data` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de mesas
CREATE TABLE `mesas` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `estado` ENUM('ativa', 'inativa') NOT NULL DEFAULT 'inativa',
  `nome_usuario` VARCHAR(100),
  `tempo_inicio` DATETIME DEFAULT NULL,
  `valor_acumulado` DECIMAL(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuário administrador inicial
INSERT INTO `usuarios` (`usuario`, `senha`, `tipo`)
VALUES (
  AES_ENCRYPT('Sudo_TI', 'seilaqualquercoisa'),
  AES_ENCRYPT('teste', 'seilaqualquercoisa'),
  'administrador'
);
