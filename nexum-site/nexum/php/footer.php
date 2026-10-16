    <footer class="site-footer">
        <div class="site-footer__inner">
            <div class="site-footer__brand">
                <a href="index.php" class="logo logo--small" aria-label="Nexum Games — página inicial">
                    <?php $logoSmall = true; include __DIR__ . '/logo.php'; ?>
                </a>
                <p class="site-footer__tagline">Jogos físicos. Memórias reais.</p>
            </div>

            <div class="site-footer__cols">
                <div>
                    <p class="site-footer__col-title">Loja</p>
                    <ul class="site-footer__links">
                        <li><a href="loja.php">Todos os jogos</a></li>
                        <li><a href="consoles.php">Consoles</a></li>
                        <li><a href="mensagens.php">Mensagens (Chat)</a></li>
                        <li><a href="criar-conta.php">Crie sua conta</a></li>
                    </ul>
                </div>
                <div>
                    <p class="site-footer__col-title"><?= __t('configuracoes') ?></p>
                    <ul class="site-footer__links">
                        <li><a href="configuracoes.php?tab=tema"><?= __t('modo_escuro') ?> / Claro</a></li>
                        <li><a href="configuracoes.php?tab=idioma"><?= __t('idioma') ?></a></li>
                        <li><a href="configuracoes.php?tab=termos"><?= __t('termos') ?></a></li>
                        <li><a href="configuracoes.php?tab=avaliacoes"><?= __t('avaliacoes') ?></a></li>
                    </ul>
                </div>
                <div>
                    <p class="site-footer__col-title">Institucional</p>
                    <ul class="site-footer__links">
                        <li><a href="sobre.php"><?= __t('sobre') ?></a></li>
                        <li><a href="contato.php"><?= __t('contato') ?></a></li>
                        <li><a href="admin/login.php">Painel Admin</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="site-footer__bottom">
            <p class="site-footer__copy">&copy; <?= date('Y') ?> Nexum Games. Todos os direitos reservados. · Vendemos apenas mídia física original.</p>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
