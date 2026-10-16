<?php
$pageTitle = 'Consoles para Colecionadores';
$bodyClass = 'page-inner';
require_once __DIR__ . '/php/config.php';

$pdo = get_db();
$stmtConsoles = $pdo->query("SELECT id, titulo, plataforma, genero as era, preco, imagem, descricao, condicao FROM produtos WHERE tipo = 'console' AND status = 'ativo' ORDER BY id ASC");
$consoles = $stmtConsoles->fetchAll();

require_once __DIR__ . '/php/header.php';
?>

    <main>
        <section class="page-hero page-hero--compact">
            <div class="page-hero__inner">
                <p class="eyebrow">🖥️ Para colecionadores</p>
                <h1>Consoles históricos, preservados com cuidado.</h1>
                <p class="page-hero__lead">Cada console é inspecionado, testado e preparado pela nossa equipe técnica. Autenticidade garantida.</p>
            </div>
        </section>

        <section class="consoles-section" style="background:var(--black);border-top:none;">
            <div class="consoles-section__inner">
                <div class="consoles-grid">

                    <?php foreach ($consoles as $c): ?>
                    <article class="console-card">
                        <div class="console-card__img-wrap">
                            <img src="assets/<?= !empty($c['imagem']) ? $c['imagem'] : 'console-ps1.jpg' ?>" alt="<?= htmlspecialchars($c['titulo']) ?>" loading="lazy">
                            <span class="console-card__condition"><?= htmlspecialchars($c['condicao'] ?: 'Estado: Ótimo') ?></span>
                        </div>
                        <div class="console-card__body">
                            <p class="console-card__era"><?= htmlspecialchars($c['era']) ?></p>
                            <h3 class="console-card__title"><?= htmlspecialchars($c['titulo']) ?></h3>
                            <p class="console-card__desc"><?= htmlspecialchars($c['descricao']) ?></p>
                            <div class="console-card__footer">
                                <span class="console-card__price">R$ <?= number_format($c['preco'], 2, ',', '.') ?></span>
                                <div style="display:flex;gap:8px;align-items:center;">
                                    <a href="mensagens.php?produto=<?= urlencode($c['titulo']) ?>" class="btn btn--outline" style="padding:8px 14px;font-size:0.78rem;" title="Conversar com o vendedor sobre este console">
                                        💬 Negociar
                                    </a>
                                    <a href="contato.php" class="btn btn--ghost" style="padding:8px 14px;font-size:0.78rem;">Consultar</a>
                                </div>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>

                    <article class="console-card" style="background:rgba(59,130,246,0.04);border-color:rgba(59,130,246,0.2);">
                        <div class="console-card__img-wrap" style="display:flex;align-items:center;justify-content:center;background:rgba(59,130,246,0.06);">
                            <span style="font-size:5rem;">📞</span>
                        </div>
                        <div class="console-card__body">
                            <p class="console-card__era">Consulte nossa equipe</p>
                            <h3 class="console-card__title">Tem algo específico em mente?</h3>
                            <p class="console-card__desc">Trabalhamos com outros consoles sob encomenda (Neo Geo, Saturn, Dreamcast). Entre em contato e iremos verificar disponibilidade para você.</p>
                            <div class="console-card__footer">
                                <span class="console-card__price" style="font-size:1rem;">Sob consulta</span>
                                <a href="contato.php" class="btn btn--primary" style="padding:10px 18px;font-size:0.8rem;">Fale conosco</a>
                            </div>
                        </div>
                    </article>

                </div>
            </div>
        </section>

        <section class="cta-band">
            <div class="cta-band__inner">
                <h2>Coleção completa, entrega segura.</h2>
                <p>Cada console é cuidadosamente embalado para chegar perfeito até você.</p>
                <a href="criar-conta.php" class="btn btn--primary">Crie sua conta para comprar</a>
            </div>
        </section>
    </main>

<?php require_once __DIR__ . '/php/footer.php'; ?>
