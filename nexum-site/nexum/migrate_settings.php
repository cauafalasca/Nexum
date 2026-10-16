<?php
/**
 * migrate_settings.php — Criação da tabela de avaliações do site e semente de avaliações
 */
require_once __DIR__ . '/php/config.php';

try {
    $pdo = get_db();

    // 1. Tabela de avaliações
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `avaliacoes` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `usuario_id` INT NULL,
            `nome` VARCHAR(120) NOT NULL,
            `email` VARCHAR(160) NOT NULL,
            `nota` TINYINT NOT NULL DEFAULT 5,
            `categoria` ENUM('geral', 'atendimento', 'midia_fisica', 'entrega') NOT NULL DEFAULT 'geral',
            `titulo` VARCHAR(160) NULL,
            `comentario` TEXT NOT NULL,
            `tipo_usuario` ENUM('comprador', 'vendedor', 'colecionador') NOT NULL DEFAULT 'comprador',
            `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX (`nota`),
            INDEX (`categoria`),
            INDEX (`criado_em`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 2. Inserir avaliações iniciais se estiver vazia
    $count = (int)$pdo->query("SELECT COUNT(*) FROM avaliacoes")->fetchColumn();
    if ($count === 0) {
        $reviews = [
            [
                'nome' => 'Lucas Mendes',
                'email' => 'comprador@nexum.com',
                'nota' => 5,
                'categoria' => 'midia_fisica',
                'titulo' => 'CD original impecável e sem nenhum risco!',
                'comentario' => 'Comprei o Metal Gear Solid de PS1 e a mídia veio perfeita, testada no console original e rodando lisinho. O cuidado na embalagem com plástico bolha e caixa reforçada me surpreendeu. Melhor lugar para colecionador de verdade.',
                'tipo_usuario' => 'colecionador',
                'criado_em' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ],
            [
                'nome' => 'RetroGames Brasil (Carlos)',
                'email' => 'vendedor@nexum.com',
                'nota' => 5,
                'categoria' => 'atendimento',
                'titulo' => 'O chat direto com os compradores facilitou muito as vendas',
                'comentario' => 'Como vendedor de jogos antigos, poder conversar em tempo real com os compradores para tirar dúvidas sobre o estado do encarte e mandar confirmação de envio aumentou muito a confiança de quem compra. Plataforma excelente!',
                'tipo_usuario' => 'vendedor',
                'criado_em' => date('Y-m-d H:i:s', strtotime('-4 days'))
            ],
            [
                'nome' => 'Eduardo Ferreira',
                'email' => 'eduardo.f@gmail.com',
                'nota' => 5,
                'categoria' => 'entrega',
                'titulo' => 'Chegou muito rápido e muito bem protegido',
                'comentario' => 'Final Fantasy VII em mídia física de PS1 chegou aqui em SP em apenas 2 dias úteis. Veio com caixa acrílica dupla original sem nenhum trincado. O atendimento no site e o suporte são nota 10.',
                'tipo_usuario' => 'comprador',
                'criado_em' => date('Y-m-d H:i:s', strtotime('-1 week'))
            ],
            [
                'nome' => 'Mariana Silveira',
                'email' => 'mariana.silveira@outlook.com',
                'nota' => 4,
                'categoria' => 'geral',
                'titulo' => 'Excelente catálogo de mídias físicas raras',
                'comentario' => 'A experiência de encontrar jogos originais em CD e cartuchos sem emulação é única. A navegação do site é rápida, limpa e o modo escuro é lindo. Parabéns a toda a equipe da Nexum Games.',
                'tipo_usuario' => 'colecionador',
                'criado_em' => date('Y-m-d H:i:s', strtotime('-10 days'))
            ]
        ];

        $stmt = $pdo->prepare("
            INSERT INTO avaliacoes (nome, email, nota, categoria, titulo, comentario, tipo_usuario, criado_em)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        foreach ($reviews as $r) {
            $stmt->execute([
                $r['nome'],
                $r['email'],
                $r['nota'],
                $r['categoria'],
                $r['titulo'],
                $r['comentario'],
                $r['tipo_usuario'],
                $r['criado_em']
            ]);
        }
    }

    echo "SUCESSO: Tabela 'avaliacoes' configurada com sucesso!\n";

} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}
