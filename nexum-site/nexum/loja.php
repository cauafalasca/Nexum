<?php
$pageTitle = 'Loja — Jogos Físicos';
$bodyClass = 'page-inner';
require_once __DIR__ . '/php/header.php';

$jogos = [
    ['img' => 'game-mgs.jpg',   'titulo' => 'Metal Gear Solid',        'plataforma' => 'PS1',  'midia' => 'CD',      'genero' => 'Ação / Stealth',   'preco' => 'R$ 180,00'],
    ['img' => 'game-ff7.jpg',   'titulo' => 'Final Fantasy VII',        'plataforma' => 'PS1',  'midia' => 'CD',      'genero' => 'RPG',               'preco' => 'R$ 320,00'],
    ['img' => 'game-crash.jpg', 'titulo' => 'Crash Bandicoot',          'plataforma' => 'PS1',  'midia' => 'CD',      'genero' => 'Plataforma',        'preco' => 'R$ 150,00'],
    ['img' => 'game-gow.jpg',   'titulo' => 'God of War',               'plataforma' => 'PS2',  'midia' => 'DVD',     'genero' => 'Ação / Mitologia',  'preco' => 'R$ 210,00'],
    ['img' => 'game-zelda.jpg', 'titulo' => 'Zelda: Ocarina of Time',   'plataforma' => 'N64',  'midia' => 'Cartucho','genero' => 'Aventura / RPG',    'preco' => 'R$ 480,00'],
];
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
                            <img src="assets/<?= $j['img'] ?>" alt="<?= htmlspecialchars($j['titulo']) ?>" loading="lazy">
                            <span class="game-card__platform"><?= $j['plataforma'] ?></span>
                            <span class="game-card__badge-midia"><?= $j['midia'] ?></span>
                        </div>
                        <div class="game-card__body">
                            <h3 class="game-card__title"><?= htmlspecialchars($j['titulo']) ?></h3>
                            <p class="game-card__genre"><?= $j['genero'] ?></p>
                            <div class="game-card__footer">
                                <div>
                                    <span class="game-card__price-label">por</span>
                                    <span class="game-card__price"><?= $j['preco'] ?></span>
                                </div>
                                <button class="game-card__btn" title="Adicionar ao carrinho">+</button>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

<?php require_once __DIR__ . '/php/footer.php'; ?>
