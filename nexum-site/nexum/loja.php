<?php
$pageTitle = 'Loja — Jogos Físicos';
$bodyClass = 'page-inner';
require_once __DIR__ . '/php/config.php';

$pdo = get_db();
$stmtJogos = $pdo->query("SELECT id, titulo, plataforma, midia, genero, preco, imagem, estoque FROM produtos WHERE tipo = 'jogo' AND status = 'ativo' ORDER BY id ASC");
$jogos = $stmtJogos->fetchAll();

require_once __DIR__ . '/php/header.php';
?>

    <main>
        <section class="page-hero page-hero--compact">
            <div class="page-hero__inner">
                <p class="eyebrow">💿 Catálogo</p>
                <h1>Jogos físicos originais.</h1>
                <p class="page-hero__lead">CD, DVD e cartuchos — 100% originais, inspecionados pela nossa equipe. Sem digital, sem cópia.</p>
            </div>
        </section>

        <section class="catalog-section">
            <div class="catalog-section__inner">
                <div class="catalog-grid">
                    <?php foreach ($jogos as $j): ?>
                    <article class="game-card">
                        <div class="game-card__img-wrap">
                            <img src="assets/<?= !empty($j['imagem']) ? $j['imagem'] : ($j['img'] ?? 'game-mgs.jpg') ?>" alt="<?= htmlspecialchars($j['titulo']) ?>" loading="lazy">
                            <span class="game-card__platform"><?= htmlspecialchars($j['plataforma']) ?></span>
                            <span class="game-card__badge-midia"><?= htmlspecialchars($j['midia']) ?></span>
                        </div>
                        <div class="game-card__body">
                            <h3 class="game-card__title"><?= htmlspecialchars($j['titulo']) ?></h3>
                            <p class="game-card__genre"><?= htmlspecialchars($j['genero']) ?></p>
                            <div class="game-card__footer">
                                <div>
                                    <span class="game-card__price-label">por</span>
                                    <span class="game-card__price">R$ <?= number_format($j['preco'], 2, ',', '.') ?></span>
                                </div>
                                <button class="game-card__btn" title="Adicionar ao carrinho">+</button>
                            </div>
                            <div class="game-card__chat-cta">
                                <a href="mensagens.php?produto=<?= urlencode($j['titulo'] . ' (' . $j['plataforma'] . ')') ?>" class="btn-card-chat" title="Tirar dúvidas com o vendedor deste jogo">
                                    <span>💬</span> Falar com vendedor
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

<?php require_once __DIR__ . '/php/footer.php'; ?>
