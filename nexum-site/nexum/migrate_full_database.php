<?php
/**
 * migrate_full_database.php — Criação e integração completa de todo o banco de dados
 */
require_once __DIR__ . '/php/config.php';

try {
    $pdo = get_db();

    // 1. Tabela de PRODUTOS (Jogos e Consoles)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `produtos` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `titulo` VARCHAR(160) NOT NULL,
            `plataforma` VARCHAR(60) NOT NULL,
            `midia` VARCHAR(60) NOT NULL,
            `genero` VARCHAR(80) NOT NULL,
            `tipo` ENUM('jogo', 'console') NOT NULL DEFAULT 'jogo',
            `preco` DECIMAL(10,2) NOT NULL,
            `estoque` INT NOT NULL DEFAULT 1,
            `imagem` VARCHAR(255) NOT NULL,
            `descricao` TEXT NULL,
            `condicao` VARCHAR(100) DEFAULT 'Excelente (Original)',
            `status` ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
            `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX (`tipo`),
            INDEX (`status`),
            INDEX (`plataforma`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 2. Tabela de PEDIDOS
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `pedidos` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `usuario_id` INT NOT NULL,
            `comprador_nome` VARCHAR(120) NOT NULL,
            `comprador_email` VARCHAR(160) NOT NULL,
            `total` DECIMAL(10,2) NOT NULL,
            `status` ENUM('pago', 'enviado', 'entregue', 'pendente', 'cancelado') NOT NULL DEFAULT 'pago',
            `codigo_rastreio` VARCHAR(60) DEFAULT NULL,
            `forma_pagamento` VARCHAR(60) DEFAULT 'PIX',
            `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX (`usuario_id`),
            INDEX (`status`),
            INDEX (`criado_em`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 3. Tabela de ITENS DO PEDIDO
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `itens_pedido` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `pedido_id` INT NOT NULL,
            `produto_id` INT NOT NULL,
            `produto_titulo` VARCHAR(160) NOT NULL,
            `preco_unitario` DECIMAL(10,2) NOT NULL,
            `quantidade` INT NOT NULL DEFAULT 1,
            INDEX (`pedido_id`),
            FOREIGN KEY (`pedido_id`) REFERENCES `pedidos`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 4. Inserir Produtos Iniciais se a tabela estiver vazia
    $countProd = (int)$pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
    if ($countProd === 0) {
        $produtosIniciais = [
            // Jogos
            ['Metal Gear Solid', 'PS1', 'CD', 'Ação / Stealth', 'jogo', 180.00, 3, 'game-mgs.jpg', 'Duplo CD original japonês/americano impecável com encarte completo.', 'Excelente (Original)'],
            ['Final Fantasy VII', 'PS1', 'CD', 'RPG', 'jogo', 320.00, 2, 'game-ff7.jpg', 'Edição com 3 CDs originais em estado de colecionador. Caixa acrílica dupla perfeita.', 'Excelente (Original)'],
            ['Crash Bandicoot', 'PS1', 'CD', 'Plataforma', 'jogo', 150.00, 5, 'game-crash.jpg', 'Clássico da Naughty Dog em CD-ROM original Sony PlayStation.', 'Ótimo'],
            ['God of War', 'PS2', 'DVD', 'Ação / Mitologia', 'jogo', 210.00, 4, 'game-gow.jpg', 'DVD original PlayStation 2 com manual colorido e mapa da Grécia.', 'Excelente (Original)'],
            ['Zelda: Ocarina of Time', 'N64', 'Cartucho', 'Aventura / RPG', 'jogo', 480.00, 2, 'game-zelda.jpg', 'Cartucho original Nintendo 64 com carcaça cinza original e bateria interna nova.', 'Raridade (Original)'],
            ['Resident Evil 2', 'PS1', 'CD', 'Survival Horror', 'jogo', 260.00, 2, 'game-mgs.jpg', 'Dois discos originais (Leon e Claire) com caixas acrílicas originais.', 'Excelente (Original)'],
            ['Castlevania: Symphony of the Night', 'PS1', 'CD', 'Metroidvania', 'jogo', 680.00, 1, 'game-ff7.jpg', 'Edição raríssima com CD original e manual original sem rasgos.', 'Impecável (Colecionador)'],
            // Consoles
            ['PlayStation 1 (PS1)', 'PS1', 'Console', 'Sony · 1994 · CD-ROM', 'console', 450.00, 2, 'console-ps1.jpg', 'O console que definiu uma geração inteira. Acompanha 1 controle original DualShock, cabo AV e fonte. Testado e funcionando perfeitamente em todos os jogos.', 'Estado: Ótimo'],
            ['PlayStation 2 — Fat', 'PS2', 'Console', 'Sony · 2000 · DVD-ROM', 'console', 380.00, 3, 'console-ps1.jpg', 'O console mais vendido da história da indústria. Versão Fat com entrada para modem/HD. Acompanha 2 controles, cabo AV e fonte original.', 'Estado: Excelente'],
            ['Super Nintendo (SNES)', 'SNES', 'Console', 'Nintendo · 1990 · Cartucho 16-bit', 'console', 520.00, 1, 'console-ps1.jpg', 'Uma relíquia dos anos 90 em perfeito estado de conservação. Acompanha 2 controles originais e fonte original.', 'Estado: Impecável'],
            ['Sega Mega Drive 16-bit', 'Mega Drive', 'Console', 'Sega · 1989 · Cartucho 16-bit', 'console', 490.00, 2, 'console-ps1.jpg', 'Edição clássica do Mega Drive japonês/nacional com som estéreo. Acompanha controle de 6 botões.', 'Estado: Ótimo']
        ];

        $stmtProd = $pdo->prepare("
            INSERT INTO produtos (titulo, plataforma, midia, genero, tipo, preco, estoque, imagem, descricao, condicao, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'ativo')
        ");
        foreach ($produtosIniciais as $p) {
            $stmtProd->execute($p);
        }
    }

    // 5. Inserir Pedidos Iniciais de demonstração se a tabela estiver vazia
    $countPed = (int)$pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();
    if ($countPed === 0) {
        // Buscar ID do comprador Lucas Mendes
        $userId = (int)$pdo->query("SELECT id FROM usuarios WHERE email = 'comprador@nexum.com'")->fetchColumn();
        if ($userId <= 0) {
            $userId = 2;
        }

        $pedidos = [
            [
                'usuario_id' => $userId,
                'nome' => 'Lucas Mendes',
                'email' => 'comprador@nexum.com',
                'total' => 180.00,
                'status' => 'enviado',
                'rastreio' => 'BR849302194SP',
                'pagamento' => 'PIX',
                'itens' => [
                    ['produto_id' => 1, 'titulo' => 'Metal Gear Solid (PS1)', 'preco' => 180.00, 'qtd' => 1]
                ]
            ],
            [
                'usuario_id' => $userId,
                'nome' => 'Eduardo Ferreira',
                'email' => 'eduardo.f@gmail.com',
                'total' => 320.00,
                'status' => 'entregue',
                'rastreio' => 'BR102938475SP',
                'pagamento' => 'Cartão de Crédito',
                'itens' => [
                    ['produto_id' => 2, 'titulo' => 'Final Fantasy VII (PS1)', 'preco' => 320.00, 'qtd' => 1]
                ]
            ],
            [
                'usuario_id' => $userId,
                'nome' => 'Mariana Silveira',
                'email' => 'mariana.silveira@outlook.com',
                'total' => 480.00,
                'status' => 'pago',
                'rastreio' => 'Aguardando Postagem',
                'pagamento' => 'PIX',
                'itens' => [
                    ['produto_id' => 5, 'titulo' => 'Zelda: Ocarina of Time (N64)', 'preco' => 480.00, 'qtd' => 1]
                ]
            ]
        ];

        $stmtPed = $pdo->prepare("
            INSERT INTO pedidos (usuario_id, comprador_nome, comprador_email, total, status, codigo_rastreio, forma_pagamento, criado_em)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmtItem = $pdo->prepare("
            INSERT INTO itens_pedido (pedido_id, produto_id, produto_titulo, preco_unitario, quantidade)
            VALUES (?, ?, ?, ?, ?)
        ");

        foreach ($pedidos as $ped) {
            $stmtPed->execute([
                $ped['usuario_id'],
                $ped['nome'],
                $ped['email'],
                $ped['total'],
                $ped['status'],
                $ped['rastreio'],
                $ped['pagamento']
            ]);
            $pedId = (int)$pdo->lastInsertId();

            foreach ($ped['itens'] as $it) {
                $stmtItem->execute([
                    $pedId,
                    $it['produto_id'],
                    $it['titulo'],
                    $it['preco'],
                    $it['qtd']
                ]);
            }
        }
    }

    echo "SUCESSO: Todas as tabelas do banco de dados (produtos, pedidos, itens_pedido) foram criadas e integradas com sucesso!\n";

} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}
