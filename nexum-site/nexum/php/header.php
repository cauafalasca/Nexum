<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? clean_input($pageTitle) . ' · ' . SITE_NAME : SITE_NAME ?></title>
    <meta name="description" content="Nexum Games — a maior loja de jogos físicos em CD do Brasil. Jogos originais, consoles para colecionadores e entrega segura.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="<?= $bodyClass ?? '' ?>">

    <div class="topline" aria-hidden="true"></div>

    <header class="site-header" id="siteHeader">
        <div class="site-header__inner">
            <a href="index.php" class="logo" aria-label="Nexum Games — página inicial">
                <span class="logo__n">N</span>ex<span class="logo__u">u</span>m <span class="logo__games">Games</span>
            </a>

            <button class="nav-toggle" id="navToggle" aria-label="Abrir menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>

            <nav class="nav" id="siteNav">
                <ul class="nav__list">
                    <?php foreach ($NAV_ITEMS as $item): ?>
                    <li>
                        <a class="nav__link <?= is_active_page($item['href']) ?>" href="<?= clean_input($item['href']) ?>">
                            <?= clean_input($item['label']) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="nav__actions">
                    <a href="criar-conta.php" class="btn btn--ghost nav__cta">Crie sua conta</a>
                    <a href="admin/login.php" class="nav__admin-link" title="Painel Admin">⚙</a>
                </div>
            </nav>
        </div>
    </header>

