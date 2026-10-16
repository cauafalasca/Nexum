<?php
$pageTitle = 'Início';
$bodyClass = 'page-home';
require_once __DIR__ . '/php/config.php';

$pdo = get_db();
$stmtDestaques = $pdo->query("SELECT id, titulo, plataforma, midia, genero, preco, imagem FROM produtos WHERE tipo = 'jogo' AND status = 'ativo' ORDER BY id ASC LIMIT 3");
$destaques = $stmtDestaques->fetchAll();

require_once __DIR__ . '/php/header.php';
?>

    <main>
        <section class="hero" id="hero">
            <div class="hero__bg" role="img" aria-label="Fundo de jogos clássicos em CD"></div>
            <div class="hero__scrim"></div>

            <div class="hero__content">
                <p class="eyebrow">🎮 Só física. Só original.</p>
                <h1 class="hero__title">
                    Os melhores jogos<br>
                    em <span class="hero__title-accent">mídia física</span>,<br>
                    na sua coleção.
                </h1>
                <p class="hero__subtitle">
                    Na Nexum Games você encontra jogos originais em CD, cartuchos e DVD
                    para todas as plataformas clássicas. Sem digital. Sem emulação.
                    A experiência real de ter o game nas mãos.
                </p>
                <div class="hero__actions">
                    <a href="loja.php" class="btn btn--primary">Ver catálogo</a>
                    <a href="sobre.php" class="btn btn--text">Conheça a Nexum →</a>
                </div>

                <div class="hero__badges">
                    <span class="hero__badge"><span class="hero__badge-icon">💿</span> Jogos em CD/DVD</span>
                    <span class="hero__badge"><span class="hero__badge-icon">🎮</span> Cartuchos originais</span>
                    <span class="hero__badge"><span class="hero__badge-icon">🖥️</span> Consoles p/ colecionadores</span>
                    <span class="hero__badge"><span class="hero__badge-icon">🚚</span> Entrega segura</span>
                </div>
            </div>

            <a href="#destaques" class="scroll-cue" aria-label="Rolar para o conteúdo">
                <span></span>
            </a>
        </section>

        <!-- Por que a Nexum Games -->
        <section class="highlights" id="destaques">
            <div class="highlights__inner">
                <p class="section-eyebrow">Por que a Nexum Games</p>
                <h2 class="section-title">Três pilares da nossa loja.</h2>

                <div class="highlights__grid">
                    <article class="highlight-card">
                        <span class="highlight-card__icon">💿</span>
                        <span class="highlight-card__mark">I</span>
                        <h3>Jogos Originais</h3>
                        <p>Todos os nossos jogos são originais, físicos e em bom estado de conservação. Nada de cópia, nada de digital. CD, DVD e cartucho do jeito que deve ser.</p>
                    </article>
                    <article class="highlight-card">
                        <span class="highlight-card__icon">🖥️</span>
                        <span class="highlight-card__mark">II</span>
                        <h3>Consoles Raros</h3>
                        <p>Para o colecionador de verdade: consoles clássicos em estado impecável. PS1, PS2, Super Nintendo, Mega Drive e mais — curados com carinho.</p>
                    </article>
                    <article class="highlight-card">
                        <span class="highlight-card__icon">🚚</span>
                        <span class="highlight-card__mark">III</span>
                        <h3>Entrega Segura</h3>
                        <p>Cada item é embalado com cuidado especial para chegar na sua mão exatamente como saiu daqui. Sua coleção merece esse cuidado.</p>
                    </article>
                </div>
            </div>
        </section>

        <!-- Catálogo em destaque -->
        <section class="catalog-section" id="catalogo">
            <div class="catalog-section__inner">
                <div class="catalog-section__header">
                    <div class="catalog-section__header-text">
                        <p class="section-eyebrow">Catálogo</p>
                        <h2 class="section-title">Destaques da semana</h2>
                    </div>
                    <a href="loja.php" class="btn btn--ghost">Ver todos →</a>
                </div>

                <div class="catalog-grid">
                    <?php foreach ($destaques as $d): ?>
                    <article class="game-card">
                        <div class="game-card__img-wrap">
                            <img src="assets/<?= !empty($d['imagem']) ? $d['imagem'] : 'game-mgs.jpg' ?>" alt="<?= htmlspecialchars($d['titulo']) ?>" loading="lazy">
                            <span class="game-card__platform"><?= htmlspecialchars($d['plataforma']) ?></span>
                            <span class="game-card__badge-midia"><?= htmlspecialchars($d['midia']) ?></span>
                        </div>
                        <div class="game-card__body">
                            <h3 class="game-card__title"><?= htmlspecialchars($d['titulo']) ?></h3>
                            <p class="game-card__genre"><?= htmlspecialchars($d['genero']) ?></p>
                            <div class="game-card__footer">
                                <div>
                                    <span class="game-card__price-label">por</span>
                                    <span class="game-card__price">R$ <?= number_format($d['preco'], 2, ',', '.') ?></span>
                                </div>
                                <button class="game-card__btn" title="Adicionar ao carrinho">+</button>
                            </div>
                            <div class="game-card__chat-cta">
                                <a href="mensagens.php?produto=<?= urlencode($d['titulo'] . ' (' . $d['plataforma'] . ')') ?>" class="btn-card-chat" title="Tirar dúvidas com o vendedor deste jogo">
                                    <span>💬</span> Falar com vendedor
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>

                    <article class="game-card">
                        <div class="game-card__img-wrap">
                            <img src="assets/game-gow.jpg" alt="God of War - PlayStation 2" loading="lazy">
                            <span class="game-card__platform">PS2</span>
                            <span class="game-card__badge-midia">DVD</span>
                        </div>
                        <div class="game-card__body">
                            <h3 class="game-card__title">God of War</h3>
                            <p class="game-card__genre">Ação / Mitologia</p>
                            <div class="game-card__footer">
                                <div>
                                    <span class="game-card__price-label">por</span>
                                    <span class="game-card__price">R$ 210,00</span>
                                </div>
                                <button class="game-card__btn" title="Adicionar ao carrinho">+</button>
                            </div>
                        </div>
                    </article>

                    <article class="game-card">
                        <div class="game-card__img-wrap">
                            <img src="assets/game-zelda.jpg" alt="The Legend of Zelda: Ocarina of Time - N64" loading="lazy">
                            <span class="game-card__platform">N64</span>
                            <span class="game-card__badge-midia">Cartucho</span>
                        </div>
                        <div class="game-card__body">
                            <h3 class="game-card__title">Zelda: Ocarina of Time</h3>
                            <p class="game-card__genre">Aventura / RPG</p>
                            <div class="game-card__footer">
                                <div>
                                    <span class="game-card__price-label">por</span>
                                    <span class="game-card__price">R$ 480,00</span>
                                </div>
                                <button class="game-card__btn" title="Adicionar ao carrinho">+</button>
                            </div>
                        </div>
                    </article>

                    <!-- Card CTA -->
                    <article class="game-card" style="display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;min-height:340px;background:rgba(59,130,246,0.05);border-color:rgba(59,130,246,0.3);">
                        <span style="font-size:2.5rem;margin-bottom:16px;">🎮</span>
                        <h3 style="font-size:1rem;margin-bottom:8px;color:var(--silver);">+500 títulos</h3>
                        <p style="font-size:0.85rem;margin-bottom:20px;">no nosso catálogo completo</p>
                        <a href="loja.php" class="btn btn--primary" style="font-size:0.8rem;padding:10px 20px;">Ver todos</a>
                    </article>
                </div>
            </div>
        </section>

        <!-- Consoles para colecionadores -->
        <section class="consoles-section" id="consoles">
            <div class="consoles-section__inner">
                <p class="section-eyebrow">Para colecionadores</p>
                <h2 class="section-title">Consoles históricos, preservados.</h2>

                <div class="consoles-grid">
                    <article class="console-card">
                        <div class="console-card__img-wrap">
                            <img src="assets/console-ps1.jpg" alt="PlayStation 1 para colecionadores" loading="lazy">
                            <span class="console-card__condition">Estado: Ótimo</span>
                        </div>
                        <div class="console-card__body">
                            <p class="console-card__era">Sony · 1994</p>
                            <h3 class="console-card__title">PlayStation 1 (PS1)</h3>
                            <p class="console-card__desc">O console que definiu uma geração. Acompanha 1 controle original e cabo AV. Testado e funcionando perfeitamente.</p>
                            <div class="console-card__footer">
                                <span class="console-card__price">R$ 450,00</span>
                                <a href="contato.php" class="btn btn--ghost" style="padding:10px 18px;font-size:0.8rem;">Consultar</a>
                            </div>
                        </div>
                    </article>

                    <article class="console-card" style="background:rgba(59,130,246,0.04);">
                        <div class="console-card__img-wrap" style="display:flex;align-items:center;justify-content:center;font-size:4rem;">
                            🖥️
                        </div>
                        <div class="console-card__body">
                            <p class="console-card__era">Sony · 2000</p>
                            <h3 class="console-card__title">PlayStation 2 (PS2)</h3>
                            <p class="console-card__desc">O console mais vendido da história. Diversas unidades disponíveis — Fat e Slim. Todos testados e verificados.</p>
                            <div class="console-card__footer">
                                <span class="console-card__price">A partir de R$ 380,00</span>
                                <a href="contato.php" class="btn btn--ghost" style="padding:10px 18px;font-size:0.8rem;">Consultar</a>
                            </div>
                        </div>
                    </article>

                    <article class="console-card" style="background:rgba(59,130,246,0.04);">
                        <div class="console-card__img-wrap" style="display:flex;align-items:center;justify-content:center;font-size:4rem;">
                            🕹️
                        </div>
                        <div class="console-card__body">
                            <p class="console-card__era">Nintendo · 1990</p>
                            <h3 class="console-card__title">Super Nintendo (SNES)</h3>
                            <p class="console-card__desc">Uma relíquia dos anos 90. Acompanha 2 controles e fonte original. Perfeito para quem quer reviver os clássicos 16-bit.</p>
                            <div class="console-card__footer">
                                <span class="console-card__price">R$ 520,00</span>
                                <a href="contato.php" class="btn btn--ghost" style="padding:10px 18px;font-size:0.8rem;">Consultar</a>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="cta-band">
            <div class="cta-band__inner">
                <h2>Pronto para montar sua coleção?</h2>
                <p>Crie sua conta, explore o catálogo e receba novidades diretamente no seu e-mail.</p>
                <a href="criar-conta.php" class="btn btn--primary">Crie sua conta grátis</a>
            </div>
        </section>
    </main>

<?php require_once __DIR__ . '/php/footer.php'; ?>
