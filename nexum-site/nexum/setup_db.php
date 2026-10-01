<?php
/**
 * setup_db.php — Script de instalação do banco de dados.
 * Acesse http://localhost/nexum-site/nexum/setup_db.php UMA VEZ para configurar.
 * Delete este arquivo após a instalação!
 */

$host = 'localhost';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Criar banco
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `nexum_games` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `nexum_games`");

    // Tabela de usuários
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `usuarios` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `nome` VARCHAR(120) NOT NULL,
            `email` VARCHAR(160) NOT NULL UNIQUE,
            `cpf` VARCHAR(11) DEFAULT NULL,
            `senha` VARCHAR(255) NOT NULL,
            `tipo` ENUM('comprador','vendedor') NOT NULL DEFAULT 'comprador',
            `status` ENUM('ativo','inativo','pendente') NOT NULL DEFAULT 'pendente',
            `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Tabela de admins
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `admins` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `usuario` VARCHAR(60) NOT NULL UNIQUE,
            `senha` VARCHAR(255) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Admin padrão: usuario=admin, senha=nexum2026
    $adminHash = password_hash('nexum2026', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO `admins` (usuario, senha) VALUES (?, ?)");
    $stmt->execute(['admin', $adminHash]);

    echo "<pre style='font-family:monospace;background:#0a0a0c;color:#86efac;padding:32px;'>";
    echo "✅ Banco de dados criado com sucesso!\n\n";
    echo "📁 Banco: nexum_games\n";
    echo "📋 Tabelas criadas: usuarios, admins\n";
    echo "🔑 Admin criado: usuario=admin | senha=nexum2026\n\n";
    echo "⚠️  DELETE este arquivo agora: setup_db.php\n\n";
    echo "🌐 Acesse o painel admin em:\n";
    echo "   http://localhost/nexum-site/nexum/admin/login.php\n";
    echo "</pre>";

} catch (PDOException $e) {
    echo "<pre style='font-family:monospace;background:#0a0a0c;color:#f87171;padding:32px;'>";
    echo "❌ Erro: " . $e->getMessage() . "\n\n";
    echo "Verifique:\n";
    echo "• MySQL está ativo no XAMPP Control Panel?\n";
    echo "• Credenciais root corretas (sem senha no XAMPP padrão)?\n";
    echo "</pre>";
}
