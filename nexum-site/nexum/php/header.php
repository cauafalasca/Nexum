<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="<?= get_active_lang() === 'en' ? 'en' : (get_active_lang() === 'es' ? 'es' : 'pt-BR') ?>" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? clean_input($pageTitle) . ' · ' . SITE_NAME : SITE_NAME ?></title>
    <meta name="description" content="Nexum Games — a maior loja de jogos físicos em CD do Brasil. Jogos originais, consoles para colecionadores e entrega segura.">
    <script>
        (function() {
            var savedTheme = localStorage.getItem('nexum_theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="<?= $bodyClass ?? '' ?>">

    <div class="topline" aria-hidden="true"></div>

    <header class="site-header" id="siteHeader">
        <div class="site-header__inner">
            <a href="index.php" class="logo" aria-label="Nexum Games — página inicial">
                <?php include __DIR__ . '/logo.php'; ?>
            </a>

            <button class="nav-toggle" id="navToggle" aria-label="Abrir menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>

            <nav class="nav" id="siteNav">
                <ul class="nav__list">
                    <?php 
                    $loggedUser = get_logged_user();
                    $unreadCount = $loggedUser ? get_unread_messages_count($loggedUser['id']) : 0;
                    foreach ($NAV_ITEMS as $item): 
                        $isMsg = ($item['href'] === 'mensagens.php');
                    ?>
                    <li>
                        <a class="nav__link <?= is_active_page($item['href']) ?>" href="<?= clean_input($item['href']) ?>">
                            <?= clean_input($item['label']) ?>
                            <?php if ($isMsg && $unreadCount > 0): ?>
                                <span class="nav-badge-unread" title="<?= $unreadCount ?> novas mensagens"><?= $unreadCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="nav__actions">
                    <?php if ($loggedUser): ?>
                        <a href="mensagens.php" class="user-pill" title="Conectado como <?= htmlspecialchars($loggedUser['nome']) ?>">
                            <span class="user-pill__role"><?= $loggedUser['tipo'] === 'vendedor' ? '🏷️ Vendedor' : '🛒 Comprador' ?></span>
                            <span class="user-pill__name"><?= htmlspecialchars(explode(' ', $loggedUser['nome'])[0]) ?></span>
                            <?php if ($unreadCount > 0): ?>
                                <span class="user-pill__dot"></span>
                            <?php endif; ?>
                        </a>
                        <a href="logout.php" class="btn-nav-logout" title="Sair da conta"><?= __t('sair') ?></a>
                    <?php else: ?>
                        <a href="login.php" class="nav__link nav__link--login"><?= __t('entrar') ?></a>
                        <a href="criar-conta.php" class="btn btn--ghost nav__cta"><?= __t('crie_conta') ?></a>
                    <?php endif; ?>
                    <a href="configuracoes.php" class="nav__settings-link" title="Configurações (Tema, Idioma, Termos, Avaliações)">⚙️</a>
                </div>
            </nav>
        </div>
    </header>

