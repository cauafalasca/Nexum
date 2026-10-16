<?php
$pageTitle = 'Configurações do Site';
$bodyClass = 'page-inner page-settings';
require_once __DIR__ . '/php/config.php';

$activeTab = clean_input($_GET['tab'] ?? 'tema');
$validTabs = ['tema', 'idioma', 'termos', 'avaliacoes'];
if (!in_array($activeTab, $validTabs)) {
    $activeTab = 'tema';
}

$user = get_logged_user();
$pdo = get_db();

// Estatísticas de avaliações
$stmtStats = $pdo->query("SELECT AVG(nota) as media, COUNT(*) as total FROM avaliacoes");
$stats = $stmtStats->fetch();
$mediaNota = $stats['media'] ? number_format((float)$stats['media'], 1) : '5.0';
$totalAvaliacoes = (int)($stats['total'] ?? 0);

// Buscar avaliações recentes
$stmtAvaliacoes = $pdo->query("
    SELECT id, nome, nota, categoria, titulo, comentario, tipo_usuario, criado_em 
    FROM avaliacoes 
    ORDER BY criado_em DESC 
    LIMIT 20
");
$avaliacoesList = $stmtAvaliacoes->fetchAll();

require_once __DIR__ . '/php/header.php';
?>

    <main class="settings-page">
        <!-- Hero das Configurações -->
        <section class="page-hero page-hero--compact">
            <div class="page-hero__inner">
                <p class="eyebrow">⚙️ Personalização & Termos</p>
                <h1>Configurações do Site</h1>
                <p class="page-hero__lead">Ajuste o tema visual (modo escuro/claro), escolha seu idioma preferido, consulte nossos termos de autenticidade e veja as avaliações da comunidade.</p>
            </div>
        </section>

        <!-- Navegação por Abas das Configurações -->
        <div class="settings-nav-bar">
            <div class="settings-nav-bar__inner">
                <a href="?tab=tema" class="settings-nav-tab <?= $activeTab === 'tema' ? 'is-active' : '' ?>">
                    <span class="tab-icon">🎨</span>
                    <span>Modo Escuro / Claro</span>
                </a>
                <a href="?tab=idioma" class="settings-nav-tab <?= $activeTab === 'idioma' ? 'is-active' : '' ?>">
                    <span class="tab-icon">🌐</span>
                    <span>Idioma</span>
                </a>
                <a href="?tab=termos" class="settings-nav-tab <?= $activeTab === 'termos' ? 'is-active' : '' ?>">
                    <span class="tab-icon">📜</span>
                    <span>Termos de Uso</span>
                </a>
                <a href="?tab=avaliacoes" class="settings-nav-tab <?= $activeTab === 'avaliacoes' ? 'is-active' : '' ?>">
                    <span class="tab-icon">⭐</span>
                    <span>Avaliações do Site</span>
                    <span class="tab-badge"><?= $totalAvaliacoes ?></span>
                </a>
            </div>
        </div>

        <section class="content-section">
            <div class="content-section__inner content-section__inner--wide">

                <!-- ============================================================
                     ABA 1: MODO ESCURO / MODO CLARO
                     ============================================================ -->
                <?php if ($activeTab === 'tema'): ?>
                <div class="settings-panel">
                    <div class="settings-panel__header">
                        <h2>Aparência & Tema do Sistema</h2>
                        <p>Escolha a paleta de cores que melhor se adapta ao seu ambiente. A alteração é instantânea e fica salva nas suas preferências.</p>
                    </div>

                    <div class="theme-cards-grid">
                        <!-- Card Modo Escuro -->
                        <div class="theme-card" data-theme-choice="dark" onclick="setTheme('dark')">
                            <div class="theme-card__preview theme-preview--dark">
                                <div class="theme-preview__header"></div>
                                <div class="theme-preview__content">
                                    <div class="preview-bubble preview-bubble--left"></div>
                                    <div class="preview-bubble preview-bubble--right"></div>
                                </div>
                            </div>
                            <div class="theme-card__body">
                                <div class="theme-card__title-row">
                                    <h3>🌙 Modo Escuro (Padrão)</h3>
                                    <span class="theme-indicator theme-indicator--active">Ativo</span>
                                </div>
                                <p>Fundo preto profundo e grafite com acentos em azul vibrante. Projetado para alto contraste, conforto visual em ambientes noturnos e visual gamer premium.</p>
                                <button type="button" class="btn btn--primary btn-sm" onclick="setTheme('dark'); event.stopPropagation();">
                                    Ativar Modo Escuro
                                </button>
                            </div>
                        </div>

                        <!-- Card Modo Claro -->
                        <div class="theme-card" data-theme-choice="light" onclick="setTheme('light')">
                            <div class="theme-card__preview theme-preview--light">
                                <div class="theme-preview__header"></div>
                                <div class="theme-preview__content">
                                    <div class="preview-bubble preview-bubble--left"></div>
                                    <div class="preview-bubble preview-bubble--right"></div>
                                </div>
                            </div>
                            <div class="theme-card__body">
                                <div class="theme-card__title-row">
                                    <h3>☀️ Modo Claro</h3>
                                    <span class="theme-indicator">Alternativo</span>
                                </div>
                                <p>Fundo claro suave com tipografia nítida em ardósia escuro. Excelente para leitura sob luz do dia ou para quem prefere interfaces claras e tradicionais.</p>
                                <button type="button" class="btn btn--outline btn-sm" onclick="setTheme('light'); event.stopPropagation();">
                                    Ativar Modo Claro
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- ============================================================
                     ABA 2: IDIOMA
                     ============================================================ -->
                <?php if ($activeTab === 'idioma'): 
                    $currentLang = get_active_lang();
                ?>
                <div class="settings-panel">
                    <div class="settings-panel__header">
                        <h2>Selecione o Idioma do Site</h2>
                        <p>Escolha o idioma de exibição para navegação, menus e botões da plataforma.</p>
                    </div>

                    <div class="lang-cards-grid">
                        <!-- Português -->
                        <a href="?tab=idioma&lang=pt" class="lang-card <?= $currentLang === 'pt' ? 'is-selected' : '' ?>">
                            <span class="lang-card__flag">🇧🇷</span>
                            <div class="lang-card__info">
                                <h3>Português (Brasil)</h3>
                                <p>Idioma principal e nativo da Nexum Games.</p>
                            </div>
                            <span class="lang-card__status"><?= $currentLang === 'pt' ? '✓ Selecionado' : 'Selecionar' ?></span>
                        </a>

                        <!-- English -->
                        <a href="?tab=idioma&lang=en" class="lang-card <?= $currentLang === 'en' ? 'is-selected' : '' ?>">
                            <span class="lang-card__flag">🇺🇸</span>
                            <div class="lang-card__info">
                                <h3>English (United States)</h3>
                                <p>Global language for international collectors.</p>
                            </div>
                            <span class="lang-card__status"><?= $currentLang === 'en' ? '✓ Selected' : 'Select' ?></span>
                        </a>

                        <!-- Español -->
                        <a href="?tab=idioma&lang=es" class="lang-card <?= $currentLang === 'es' ? 'is-selected' : '' ?>">
                            <span class="lang-card__flag">🇪🇸</span>
                            <div class="lang-card__info">
                                <h3>Español</h3>
                                <p>Para coleccionistas y compradores de habla hispana.</p>
                            </div>
                            <span class="lang-card__status"><?= $currentLang === 'es' ? '✓ Seleccionado' : 'Seleccionar' ?></span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- ============================================================
                     ABA 3: TERMOS DE USO & POLÍTICAS
                     ============================================================ -->
                <?php if ($activeTab === 'termos'): ?>
                <div class="settings-panel">
                    <div class="settings-panel__header" style="display:flex;justify-content:space-between;align-items:flex-start;gap:20px;flex-wrap:wrap;">
                        <div>
                            <h2>Termos de Uso, Autenticidade & Garantia</h2>
                            <p>Condições gerais aplicáveis à compra, venda e negociação de mídias físicas originais na Nexum Games.</p>
                        </div>
                        <button type="button" class="btn btn--outline btn-sm" onclick="window.print()">
                            🖨️ Imprimir Termos
                        </button>
                    </div>

                    <div class="terms-accordion">
                        <!-- Seção 1 -->
                        <article class="terms-card">
                            <div class="terms-card__header">
                                <span class="terms-badge">1. Autenticidade</span>
                                <h3>Proibição Total de Cópias Repro e Pirataria</h3>
                            </div>
                            <div class="terms-card__body">
                                <p>A Nexum Games é um marketplace e loja exclusivamente focado em <strong>mídias físicas originais de época</strong> (CDs, DVDs e Cartuchos). É terminantemente proibido o anúncio, envio ou comercialização de reproduções não autorizadas, repros chinesas, CDs regraváveis (CD-R) ou caixas contrafeitas.</p>
                                <ul>
                                    <li>Todos os jogos listados devem possuir numeração serial correspondente na matriz do disco.</li>
                                    <li>Discos duplos (como Final Fantasy VII ou Metal Gear Solid) devem conter todos os volumes originais.</li>
                                </ul>
                            </div>
                        </article>

                        <!-- Seção 2 -->
                        <article class="terms-card">
                            <div class="terms-card__header">
                                <span class="terms-badge">2. Transparência</span>
                                <h3>Obrigações e Responsabilidade do Vendedor</h3>
                            </div>
                            <div class="terms-card__body">
                                <p>O vendedor assume o compromisso legal de prestar informações verídicas e detalhadas no chat com o comprador:</p>
                                <ul>
                                    <li>Descrever com precisão a existência de riscos superficiais ou profundos na superfície de leitura da mídia.</li>
                                    <li>Informar se o encarte, manual, panfletos e estojo original (acrílico ou snapcase) acompanham o jogo.</li>
                                    <li>Realizar teste de boot e leitura em console original antes de efetuar o envio.</li>
                                </ul>
                            </div>
                        </article>

                        <!-- Seção 3 -->
                        <article class="terms-card">
                            <div class="terms-card__header">
                                <span class="terms-badge">3. Garantia</span>
                                <h3>Garantia de 7 Dias e Proteção ao Comprador</h3>
                            </div>
                            <div class="terms-card__body">
                                <p>Todo comprador possui <strong>7 (sete) dias corridos</strong> a partir do recebimento para testar a mídia física em seu console. Caso o jogo apresente travamentos de leitura (DED), congelamentos em CGs ou falha de reconhecimento, a devolução é integral com reembolso garantido.</p>
                            </div>
                        </article>

                        <!-- Seção 4 -->
                        <article class="terms-card">
                            <div class="terms-card__header">
                                <span class="terms-badge">4. Logística</span>
                                <h3>Embalagem Reforçada e Prazos de Envio</h3>
                            </div>
                            <div class="terms-card__body">
                                <p>Jogos antigos em CD e acrílico são itens frágeis de colecionador. Por isso, exigimos padrão rigoroso de embalagem:</p>
                                <ul>
                                    <li>Uso mandatório de <strong>mínimo de 3 camadas de plástico bolha</strong> ao redor de caixas acrílicas.</li>
                                    <li>Postagem com código de rastreamento oficial dos Correios e seguro contra extravio.</li>
                                    <li>Prazos acordados no chat entre vendedor e comprador devem ser respeitados rigorosamente.</li>
                                </ul>
                            </div>
                        </article>

                        <!-- Seção 5 -->
                        <article class="terms-card">
                            <div class="terms-card__header">
                                <span class="terms-badge">5. Privacidade</span>
                                <h3>Tratamento de Dados Pessoais (LGPD)</h3>
                            </div>
                            <div class="terms-card__body">
                                <p>A Nexum Games cumpre a Lei Geral de Proteção de Dados (Lei nº 13.709/2018). Seus dados cadastrais, e-mails e histórico de conversas são mantidos sob criptografia e utilizados unicamente para viabilizar as compras e entregas na plataforma.</p>
                            </div>
                        </article>
                    </div>
                </div>
                <?php endif; ?>

                <!-- ============================================================
                     ABA 4: AVALIAÇÕES DO SITE
                     ============================================================ -->
                <?php if ($activeTab === 'avaliacoes'): ?>
                <div class="settings-panel">
                    <div class="settings-panel__header">
                        <h2>Avaliações da Comunidade Nexum Games</h2>
                        <p>Opiniões reais de compradores e vendedores sobre o estado das mídias, atendimento pelo chat e rapidez na entrega.</p>
                    </div>

                    <!-- Resumo das Notas -->
                    <div class="reviews-summary-card">
                        <div class="reviews-summary__score">
                            <span class="score-number"><?= $mediaNota ?></span>
                            <div class="score-stars">★★★★★</div>
                            <span class="score-total"><?= $totalAvaliacoes ?> avaliações verificadas</span>
                        </div>
                        <div class="reviews-summary__highlights">
                            <div class="summary-pill">
                                <span class="pill-icon">💿</span>
                                <div>
                                    <strong>100% Mídia Original</strong>
                                    <span>Garantia de inspecção técnica</span>
                                </div>
                            </div>
                            <div class="summary-pill">
                                <span class="pill-icon">💬</span>
                                <div>
                                    <strong>Chat Direto Ativo</strong>
                                    <span>Vendedor e comprador conectados</span>
                                </div>
                            </div>
                            <div class="summary-pill">
                                <span class="pill-icon">🚚</span>
                                <div>
                                    <strong>Embalagem Blindada</strong>
                                    <span>Plástico bolha reforçado</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="reviews-layout-grid">
                        <!-- Formulário de Envio de Nova Avaliação -->
                        <div class="review-form-card">
                            <h3>Deixe sua avaliação sobre o site</h3>
                            <p style="font-size:0.85rem;color:var(--silver-dim);margin-bottom:20px;">
                                Conte como foi sua experiência com a compra de jogos físicos ou com o atendimento.
                            </p>

                            <div id="reviewSuccessAlert" class="alert alert--success" style="display:none;margin-bottom:16px;"></div>
                            <div id="reviewErrorAlert" class="alert alert--error" style="display:none;margin-bottom:16px;"></div>

                            <form id="newReviewForm" onsubmit="enviarAvaliacao(event)">
                                <!-- Seletor de Estrelas -->
                                <div class="form__row">
                                    <label>Sua nota para o site:</label>
                                    <div class="star-rating-selector" id="starRating">
                                        <button type="button" class="star-btn" data-value="1" onclick="setRating(1)">★</button>
                                        <button type="button" class="star-btn" data-value="2" onclick="setRating(2)">★</button>
                                        <button type="button" class="star-btn" data-value="3" onclick="setRating(3)">★</button>
                                        <button type="button" class="star-btn" data-value="4" onclick="setRating(4)">★</button>
                                        <button type="button" class="star-btn is-active" data-value="5" onclick="setRating(5)">★</button>
                                        <span class="rating-label" id="ratingLabel">5 de 5 estrelas (Excelente)</span>
                                    </div>
                                    <input type="hidden" id="formNota" value="5">
                                </div>

                                <div class="form__row">
                                    <label for="formCategoria">Principal aspecto avaliado</label>
                                    <select id="formCategoria" class="chat-select">
                                        <option value="midia_fisica">💿 Mídia Física Original & Conservação</option>
                                        <option value="atendimento">💬 Atendimento & Chat com Vendedor</option>
                                        <option value="entrega">🚚 Rapidez na Entrega & Embalagem</option>
                                        <option value="geral" selected>🎮 Experiência Geral no Site</option>
                                    </select>
                                </div>

                                <div class="form__row">
                                    <label for="formNome">Seu Nome Completo</label>
                                    <input type="text" id="formNome" value="<?= $user ? htmlspecialchars($user['nome']) : '' ?>" placeholder="Ex: Roberto Silveira" required>
                                </div>

                                <div class="form__row">
                                    <label for="formEmail">Seu E-mail</label>
                                    <input type="email" id="formEmail" value="<?= $user ? htmlspecialchars($user['email']) : '' ?>" placeholder="seu@email.com" required>
                                </div>

                                <div class="form__row">
                                    <label for="formTitulo">Título do Comentário</label>
                                    <input type="text" id="formTitulo" placeholder="Ex: Ótima experiência e CD impecável" required>
                                </div>

                                <div class="form__row">
                                    <label for="formComentario">Sua Mensagem Detalhada</label>
                                    <textarea id="formComentario" rows="3" placeholder="Escreva aqui sua opinião sincera sobre a loja, jogos ou atendimento..." required></textarea>
                                </div>

                                <button type="submit" id="btnSubmitReview" class="btn btn--primary" style="width:100%;margin-top:10px;">
                                    Publicar Avaliação
                                </button>
                            </form>
                        </div>

                        <!-- Lista de Avaliações Recentes -->
                        <div class="reviews-feed">
                            <div class="reviews-feed__header">
                                <h3>Todas as Avaliações</h3>
                                <div class="feed-filter-tags">
                                    <button type="button" class="filter-tag is-active" onclick="filtrarAvaliacoes('todas', this)">Todas</button>
                                    <button type="button" class="filter-tag" onclick="filtrarAvaliacoes('midia_fisica', this)">Mídia Física</button>
                                    <button type="button" class="filter-tag" onclick="filtrarAvaliacoes('atendimento', this)">Atendimento</button>
                                    <button type="button" class="filter-tag" onclick="filtrarAvaliacoes('entrega', this)">Entrega</button>
                                </div>
                            </div>

                            <div class="reviews-stream" id="reviewsStream">
                                <?php foreach ($avaliacoesList as $av): 
                                    $starsStr = str_repeat('★', (int)$av['nota']) . str_repeat('☆', 5 - (int)$av['nota']);
                                    $catMap = [
                                        'midia_fisica' => '💿 Mídia Física',
                                        'atendimento'  => '💬 Atendimento',
                                        'entrega'      => '🚚 Entrega Segura',
                                        'geral'        => '🎮 Experiência Geral'
                                    ];
                                    $catLabel = $catMap[$av['categoria']] ?? '🎮 Avaliação';
                                    $dateStr = date('d/m/Y', strtotime($av['criado_em']));
                                ?>
                                <article class="review-item" data-cat="<?= htmlspecialchars($av['categoria']) ?>">
                                    <div class="review-item__top">
                                        <div class="review-author">
                                            <div class="review-avatar">
                                                <?= strtoupper(substr($av['nome'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="review-author__name">
                                                    <?= htmlspecialchars($av['nome']) ?>
                                                    <span class="user-role-badge role-badge--comprador">Verificado</span>
                                                </div>
                                                <span class="review-date"><?= $dateStr ?></span>
                                            </div>
                                        </div>
                                        <div class="review-stars-badge">
                                            <span class="review-stars"><?= $starsStr ?></span>
                                            <span class="review-cat-pill"><?= $catLabel ?></span>
                                        </div>
                                    </div>
                                    <?php if (!empty($av['titulo'])): ?>
                                        <h4 class="review-item__title"><?= htmlspecialchars($av['titulo']) ?></h4>
                                    <?php endif; ?>
                                    <p class="review-item__text"><?= nl2br(htmlspecialchars($av['comentario'])) ?></p>
                                </article>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </section>
    </main>

    <!-- Script de Avaliações e Rating -->
    <script>
    let currentNota = 5;

    function setRating(val) {
        currentNota = val;
        document.getElementById('formNota').value = val;
        const labels = {
            1: '1 de 5 estrelas (Muito Fraco)',
            2: '2 de 5 estrelas (Regular)',
            3: '3 de 5 estrelas (Bom)',
            4: '4 de 5 estrelas (Muito Bom)',
            5: '5 de 5 estrelas (Excelente)'
        };
        document.getElementById('ratingLabel').textContent = labels[val] || (val + ' de 5 estrelas');

        document.querySelectorAll('.star-btn').forEach(btn => {
            const v = parseInt(btn.getAttribute('data-value'));
            btn.classList.toggle('is-active', v <= val);
        });
    }

    async function enviarAvaliacao(e) {
        e.preventDefault();
        const nome = document.getElementById('formNome').value.trim();
        const email = document.getElementById('formEmail').value.trim();
        const titulo = document.getElementById('formTitulo').value.trim();
        const comentario = document.getElementById('formComentario').value.trim();
        const categoria = document.getElementById('formCategoria').value;
        const nota = currentNota;

        const btn = document.getElementById('btnSubmitReview');
        const alertSuccess = document.getElementById('reviewSuccessAlert');
        const alertError = document.getElementById('reviewErrorAlert');

        alertSuccess.style.display = 'none';
        alertError.style.display = 'none';
        btn.disabled = true;
        btn.textContent = 'Enviando avaliação...';

        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('nome', nome);
        formData.append('email', email);
        formData.append('titulo', titulo);
        formData.append('comentario', comentario);
        formData.append('categoria', categoria);
        formData.append('nota', nota);

        try {
            const resp = await fetch('api_avaliacoes.php', { method: 'POST', body: formData });
            const data = await resp.json();

            if (data.success && data.review) {
                alertSuccess.textContent = '✓ ' + data.message;
                alertSuccess.style.display = 'block';

                // Adicionar nova avaliação no topo da lista
                const stream = document.getElementById('reviewsStream');
                const newRev = data.review;
                const stars = '★'.repeat(newRev.nota) + '☆'.repeat(5 - newRev.nota);

                const item = document.createElement('article');
                item.className = 'review-item review-item--new';
                item.setAttribute('data-cat', newRev.categoria);
                item.innerHTML = `
                    <div class="review-item__top">
                        <div class="review-author">
                            <div class="review-avatar">${newRev.nome.charAt(0).toUpperCase()}</div>
                            <div>
                                <div class="review-author__name">
                                    ${escapeHtml(newRev.nome)}
                                    <span class="user-role-badge role-badge--comprador">Verificado</span>
                                </div>
                                <span class="review-date">${newRev.data}</span>
                            </div>
                        </div>
                        <div class="review-stars-badge">
                            <span class="review-stars">${stars}</span>
                            <span class="review-cat-pill">🎮 Nova Avaliação</span>
                        </div>
                    </div>
                    ${newRev.titulo ? `<h4 class="review-item__title">${escapeHtml(newRev.titulo)}</h4>` : ''}
                    <p class="review-item__text">${escapeHtml(newRev.comentario)}</p>
                `;
                stream.insertBefore(item, stream.firstChild);

                // Limpar campos
                document.getElementById('formTitulo').value = '';
                document.getElementById('formComentario').value = '';
            } else {
                alertError.textContent = '⚠ ' + (data.error || 'Erro ao publicar avaliação.');
                alertError.style.display = 'block';
            }
        } catch (err) {
            alertError.textContent = '⚠ Erro de conexão com o servidor.';
            alertError.style.display = 'block';
        } finally {
            btn.disabled = false;
            btn.textContent = 'Publicar Avaliação';
        }
    }

    function filtrarAvaliacoes(cat, btn) {
        document.querySelectorAll('.filter-tag').forEach(b => b.classList.remove('is-active'));
        btn.classList.add('is-active');

        const items = document.querySelectorAll('.review-item');
        items.forEach(el => {
            if (cat === 'todas' || el.getAttribute('data-cat') === cat) {
                el.style.display = 'block';
            } else {
                el.style.display = 'none';
            }
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    </script>

<?php require_once __DIR__ . '/php/footer.php'; ?>
