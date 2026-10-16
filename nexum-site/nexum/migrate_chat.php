<?php
/**
 * migrate_chat.php — Criação das tabelas de chat/conversas e usuários de demonstração
 */
require_once __DIR__ . '/php/config.php';

try {
    $pdo = get_db();

    // 1. Tabela de conversas
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `conversas` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `comprador_id` INT NOT NULL,
            `vendedor_id` INT NOT NULL,
            `produto_titulo` VARCHAR(255) DEFAULT 'Dúvidas sobre produtos',
            `ultimo_remetente_id` INT DEFAULT NULL,
            `ultima_mensagem` TEXT DEFAULT NULL,
            `atualizado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX (`comprador_id`),
            INDEX (`vendedor_id`),
            INDEX (`atualizado_em`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 2. Tabela de mensagens
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `mensagens` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `conversa_id` INT NOT NULL,
            `remetente_id` INT NOT NULL,
            `mensagem` TEXT NOT NULL,
            `lida` TINYINT(1) NOT NULL DEFAULT 0,
            `enviado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX (`conversa_id`),
            INDEX (`remetente_id`),
            INDEX (`enviado_em`),
            FOREIGN KEY (`conversa_id`) REFERENCES `conversas`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 3. Usuários demo (Vendedor e Comprador)
    $hash = password_hash('nexum2026', PASSWORD_DEFAULT);

    // Vendedor Demo
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute(['vendedor@nexum.com']);
    $vendedor = $stmt->fetch();
    if (!$vendedor) {
        $stmtInsert = $pdo->prepare("
            INSERT INTO usuarios (nome, email, cpf, senha, tipo, status)
            VALUES (?, ?, ?, ?, 'vendedor', 'ativo')
        ");
        $stmtInsert->execute(['RetroGames Brasil (Carlos)', 'vendedor@nexum.com', '12345678901', $hash]);
        $vendedorId = (int)$pdo->lastInsertId();
    } else {
        $vendedorId = (int)$vendedor['id'];
    }

    // Comprador Demo
    $stmt->execute(['comprador@nexum.com']);
    $comprador = $stmt->fetch();
    if (!$comprador) {
        $stmtInsert = $pdo->prepare("
            INSERT INTO usuarios (nome, email, cpf, senha, tipo, status)
            VALUES (?, ?, ?, ?, 'comprador', 'ativo')
        ");
        $stmtInsert->execute(['Lucas Mendes (Colecionador)', 'comprador@nexum.com', null, $hash]);
        $compradorId = (int)$pdo->lastInsertId();
    } else {
        $compradorId = (int)$comprador['id'];
    }

    // 4. Conversa inicial de demonstração
    $stmt = $pdo->prepare("SELECT id FROM conversas WHERE comprador_id = ? AND vendedor_id = ?");
    $stmt->execute([$compradorId, $vendedorId]);
    $conversa = $stmt->fetch();

    if (!$conversa) {
        $stmtConv = $pdo->prepare("
            INSERT INTO conversas (comprador_id, vendedor_id, produto_titulo, ultimo_remetente_id, ultima_mensagem, atualizado_em)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $ultimoMsg = 'Consigo sim! Fechando até as 14h, posto nos Correios hoje mesmo com rastreio.';
        $stmtConv->execute([$compradorId, $vendedorId, 'Metal Gear Solid (PS1)', $vendedorId, $ultimoMsg]);
        $conversaId = (int)$pdo->lastInsertId();

        // Inserir histórico de mensagens
        $mensagensDemo = [
            [$compradorId, 'Olá Carlos! Vi seu anúncio do Metal Gear Solid para PS1. O CD tem algum risco no disco 1 ou 2? Vem com o encarte e a caixa acrílica original?', '2026-10-16 09:15:00'],
            [$vendedorId, 'Olá Lucas! Tudo bem? Os dois discos estão impecáveis, sem riscos e testados no meu PS1 original. O encarte e a caixa acrílica dupla original estão 100% perfeitos, sem trincados!', '2026-10-16 09:22:00'],
            [$compradorId, 'Excelente! É exatamente o que eu estava procurando para minha coleção. Você consegue postar ainda hoje se eu fechar o pagamento agora?', '2026-10-16 09:30:00'],
            [$vendedorId, 'Consigo sim! Fechando até as 14h, posto nos Correios hoje mesmo com rastreio.', '2026-10-16 09:35:00']
        ];

        $stmtMsg = $pdo->prepare("INSERT INTO mensagens (conversa_id, remetente_id, mensagem, lida, enviado_em) VALUES (?, ?, ?, 1, ?)");
        foreach ($mensagensDemo as $m) {
            $stmtMsg->execute([$conversaId, $m[0], $m[1], $m[2]]);
        }
    }

    echo "SUCESSO: Tabelas de conversas criadas e dados de teste inicializados!\n";
    echo "Vendedor ID: $vendedorId (vendedor@nexum.com)\n";
    echo "Comprador ID: $compradorId (comprador@nexum.com)\n";

} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}
