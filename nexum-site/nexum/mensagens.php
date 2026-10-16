<?php
$pageTitle = 'Mensagens · Chat Vendedor & Comprador';
$bodyClass = 'page-inner page-chat';
require_once __DIR__ . '/php/config.php';

$user = get_logged_user();

// Troca rápida de papel via GET para testes (ex: mensagens.php?demo=vendedor ou demo=comprador)
if (isset($_GET['demo'])) {
    $targetEmail = ($_GET['demo'] === 'vendedor') ? 'vendedor@nexum.com' : 'comprador@nexum.com';
    $pdo = get_db();
    $stmt = $pdo->prepare('SELECT id, nome, email, tipo FROM usuarios WHERE email = ?');
    $stmt->execute([$targetEmail]);
    $u = $stmt->fetch();
    if ($u) {
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['user_nome'] = $u['nome'];
        $_SESSION['user_email'] = $u['email'];
        $_SESSION['user_tipo'] = $u['tipo'];
        header('Location: mensagens.php' . (isset($_GET['produto']) ? '?produto=' . urlencode($_GET['produto']) : ''));
        exit;
    }
}

// Se não está logado, exibir tela explicativa com botões de teste rápido
if (!$user) {
    require_once __DIR__ . '/php/header.php';
    ?>
    <main>
        <section class="page-hero page-hero--compact">
            <div class="page-hero__inner">
                <p class="eyebrow">💬 Chat Direto Nexum</p>
                <h1>Conversa entre Vendedor e Comprador.</h1>
                <p class="page-hero__lead">Tire dúvidas sobre conservação de mídias físicas, combine formas de envio e negocie diretamente com colecionadores e lojas.</p>
            </div>
        </section>

        <section class="content-section">
            <div class="content-section__inner content-section__inner--narrow">
                
                <div class="chat-welcome-card">
                    <div class="chat-welcome-badge">Módulo de Atendimento</div>
                    <h2>Conecte-se para iniciar ou ver suas conversas</h2>
                    <p style="color:var(--silver-dim);font-size:0.95rem;margin-bottom:28px;">
                        Para testar o chat em tempo real, escolha um dos perfis de demonstração abaixo ou faça login na sua conta existente:
                    </p>

                    <div class="chat-roles-grid">
                        <div class="chat-role-card chat-role-card--vendedor">
                            <div class="role-icon">🏷️</div>
                            <h3>Acessar como Vendedor</h3>
                            <p>Responda dúvidas de compradores, informe sobre o estado dos CDs e confirme o envio dos jogos.</p>
                            <span class="user-role-tag role-tag--vendedor">RetroGames Brasil (Carlos)</span>
                            <a href="mensagens.php?demo=vendedor" class="btn btn--primary" style="width:100%;text-align:center;">
                                Entrar como Vendedor →
                            </a>
                        </div>

                        <div class="chat-role-card chat-role-card--comprador">
                            <div class="role-icon">🛒</div>
                            <h3>Acessar como Comprador</h3>
                            <p>Pergunte sobre riscos na mídia, fotos adicionais, manuais originais e faça ofertas diretas.</p>
                            <span class="user-role-tag role-tag--comprador">Lucas Mendes (Colecionador)</span>
                            <a href="mensagens.php?demo=comprador" class="btn btn--ghost" style="width:100%;text-align:center;">
                                Entrar como Comprador →
                            </a>
                        </div>
                    </div>

                    <div style="text-align:center;margin-top:28px;padding-top:20px;border-top:1px solid var(--line);">
                        <a href="login.php" style="color:var(--accent);font-size:0.9rem;">Já possui credenciais personalizadas? Acesse aqui</a>
                    </div>
                </div>

            </div>
        </section>
    </main>
    <?php
    require_once __DIR__ . '/php/footer.php';
    exit;
}

$pdo = get_db();
$userId = $user['id'];
$userRole = $user['tipo'];

// Iniciar conversa rápida a partir do catálogo (ex: ?produto=Final+Fantasy+VII)
if (isset($_GET['produto']) && !empty($_GET['produto'])) {
    $prodParam = trim($_GET['produto']);
    $outroTipo = ($userRole === 'vendedor') ? 'comprador' : 'vendedor';
    $stmtPartner = $pdo->prepare("SELECT id FROM usuarios WHERE tipo = ? AND status = 'ativo' AND id != ? LIMIT 1");
    $stmtPartner->execute([$outroTipo, $userId]);
    $partnerId = (int)$stmtPartner->fetchColumn();

    if ($partnerId > 0) {
        $cId = ($userRole === 'comprador') ? $userId : $partnerId;
        $vId = ($userRole === 'vendedor') ? $userId : $partnerId;

        $stmtCheck = $pdo->prepare("SELECT id FROM conversas WHERE comprador_id = ? AND vendedor_id = ? AND produto_titulo = ?");
        $stmtCheck->execute([$cId, $vId, $prodParam]);
        $existing = $stmtCheck->fetch();

        if ($existing) {
            header('Location: mensagens.php?id=' . $existing['id']);
            exit;
        } else {
            $stmtNew = $pdo->prepare("INSERT INTO conversas (comprador_id, vendedor_id, produto_titulo, atualizado_em) VALUES (?, ?, ?, NOW())");
            $stmtNew->execute([$cId, $vId, $prodParam]);
            $newConvId = (int)$pdo->lastInsertId();

            // Mensagem introdutória automática
            if ($userRole === 'comprador') {
                $msgInit = "Olá! Tenho interesse no jogo \"$prodParam\". Ele ainda está disponível e qual o estado da mídia física?";
                $pdo->prepare("INSERT INTO mensagens (conversa_id, remetente_id, mensagem, lida, enviado_em) VALUES (?, ?, ?, 0, NOW())")
                    ->execute([$newConvId, $userId, $msgInit]);
                $pdo->prepare("UPDATE conversas SET ultimo_remetente_id = ?, ultima_mensagem = ?, atualizado_em = NOW() WHERE id = ?")
                    ->execute([$userId, $msgInit, $newConvId]);
            }

            header('Location: mensagens.php?id=' . $newConvId);
            exit;
        }
    }
}

// Buscar todas as conversas do usuário logado
$stmtList = $pdo->prepare("
    SELECT c.id, c.produto_titulo, c.ultima_mensagem, c.atualizado_em, c.ultimo_remetente_id,
           u_outro.id as outro_id, u_outro.nome as outro_nome, u_outro.tipo as outro_tipo,
           (SELECT COUNT(m.id) FROM mensagens m WHERE m.conversa_id = c.id AND m.remetente_id != ? AND m.lida = 0) as unread_count
    FROM conversas c
    INNER JOIN usuarios u_outro ON u_outro.id = (CASE WHEN c.comprador_id = ? THEN c.vendedor_id ELSE c.comprador_id END)
    WHERE c.comprador_id = ? OR c.vendedor_id = ?
    ORDER BY c.atualizado_em DESC
");
$stmtList->execute([$userId, $userId, $userId, $userId]);
$conversas = $stmtList->fetchAll();

// Determinar conversa ativa
$activeConvId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($activeConvId <= 0 && !empty($conversas)) {
    $activeConvId = (int)$conversas[0]['id'];
}

$activeConv = null;
$mensagensAtivas = [];

if ($activeConvId > 0) {
    // Buscar dados da conversa ativa
    $stmtAct = $pdo->prepare("
        SELECT c.id, c.comprador_id, c.vendedor_id, c.produto_titulo,
               u_outro.id as outro_id, u_outro.nome as outro_nome, u_outro.tipo as outro_tipo, u_outro.email as outro_email
        FROM conversas c
        INNER JOIN usuarios u_outro ON u_outro.id = (CASE WHEN c.comprador_id = ? THEN c.vendedor_id ELSE c.comprador_id END)
        WHERE c.id = ? AND (c.comprador_id = ? OR c.vendedor_id = ?)
    ");
    $stmtAct->execute([$userId, $activeConvId, $userId, $userId]);
    $activeConv = $stmtAct->fetch();

    if ($activeConv) {
        // Marcar mensagens recebidas como lidas
        $pdo->prepare("UPDATE mensagens SET lida = 1 WHERE conversa_id = ? AND remetente_id != ? AND lida = 0")
            ->execute([$activeConvId, $userId]);

        // Carregar mensagens
        $stmtMsgs = $pdo->prepare("
            SELECT m.id, m.remetente_id, m.mensagem, m.lida, m.enviado_em,
                   u.nome as remetente_nome, u.tipo as remetente_tipo
            FROM mensagens m
            INNER JOIN usuarios u ON m.remetente_id = u.id
            WHERE m.conversa_id = ?
            ORDER BY m.enviado_em ASC, m.id ASC
        ");
        $stmtMsgs->execute([$activeConvId]);
        $mensagensAtivas = $stmtMsgs->fetchAll();
    }
}

// Chips de sugestão rápida para envio ágil
$sugestoes = ($userRole === 'comprador') ? [
    '📦 O disco tem algum risco superficial ou profundo?',
    '📖 Acompanha o manual e o encarte original?',
    '🚚 Consegue postar nos Correios ainda hoje?',
    '💰 Aceita R$ 20,00 de desconto no pagamento à vista?'
] : [
    '✅ Os discos estão 100% impecáveis e testados no console!',
    '📦 Caixa original com encarte e manuais preservados.',
    '🚚 Envio hoje mesmo se o pagamento for confirmado até às 14h!',
    '🤝 Aceito a proposta! Pode finalizar o pedido.'
];

require_once __DIR__ . '/php/header.php';
?>

    <main class="chat-page-main">
        <!-- Barra de Status do Usuário Atual com Alternador Rápido -->
        <section class="chat-status-bar">
            <div class="chat-status-bar__inner">
                <div class="chat-status-bar__user">
                    <span class="chat-status-bar__avatar">
                        <?= strtoupper(substr($user['nome'], 0, 1)) ?>
                    </span>
                    <div>
                        <div class="chat-status-bar__title">
                            Conectado como <strong><?= htmlspecialchars($user['nome']) ?></strong>
                            <span class="user-role-badge <?= $userRole === 'vendedor' ? 'role-badge--vendedor' : 'role-badge--comprador' ?>">
                                <?= $userRole === 'vendedor' ? '🏷️ Vendedor' : '🛒 Comprador' ?>
                            </span>
                        </div>
                        <div class="chat-status-bar__hint">
                            <?= $userRole === 'vendedor' 
                                ? 'Você está na visão do vendedor respondendo aos compradores interessados.' 
                                : 'Você está na visão do comprador tirando dúvidas sobre os jogos.' ?>
                        </div>
                    </div>
                </div>

                <div class="chat-status-bar__actions">
                    <!-- Botão de alternar papel para testar os dois lados -->
                    <?php if ($userRole === 'vendedor'): ?>
                        <a href="mensagens.php?demo=comprador<?= $activeConvId ? '&id='.$activeConvId : '' ?>" class="btn-role-switch" title="Mudar para o usuário comprador para responder">
                            <span>🔄</span> Testar como <strong>Comprador</strong>
                        </a>
                    <?php else: ?>
                        <a href="mensagens.php?demo=vendedor<?= $activeConvId ? '&id='.$activeConvId : '' ?>" class="btn-role-switch btn-role-switch--vendedor" title="Mudar para o vendedor para responder">
                            <span>🔄</span> Testar como <strong>Vendedor</strong>
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn-role-logout" title="Sair da conta">Sair</a>
                </div>
            </div>
        </section>

        <!-- Container Principal do Chat -->
        <section class="chat-app-container">
            <div class="chat-app-layout">

                <!-- Coluna Esquerda: Lista de Conversas -->
                <aside class="chat-sidebar">
                    <div class="chat-sidebar__header">
                        <h2>Conversas</h2>
                        <button class="chat-btn-new" id="btnNovaConversa" title="Iniciar nova conversa">
                            + Nova
                        </button>
                    </div>

                    <!-- Busca rápida -->
                    <div class="chat-search-wrap">
                        <input type="text" id="chatSearchInput" placeholder="Filtrar por nome ou jogo..." class="chat-search-input">
                    </div>

                    <!-- Lista de Conversas -->
                    <div class="chat-conv-list" id="convListContainer">
                        <?php if (empty($conversas)): ?>
                            <div class="chat-empty-list">
                                <span style="font-size:1.8rem;">💬</span>
                                <p>Nenhuma conversa ativa no momento.<br>Inicie uma nova conversa pelo botão acima ou pelo catálogo da loja.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($conversas as $c): 
                                $isActive = ($c['id'] == $activeConvId);
                                $lastTime = strtotime($c['atualizado_em']);
                                $timeStr = (date('Y-m-d') === date('Y-m-d', $lastTime)) ? date('H:i', $lastTime) : date('d/m', $lastTime);
                            ?>
                                <a href="mensagens.php?id=<?= $c['id'] ?>" class="chat-conv-item <?= $isActive ? 'is-active' : '' ?>" data-name="<?= strtolower(htmlspecialchars($c['outro_nome'])) ?>" data-prod="<?= strtolower(htmlspecialchars($c['produto_titulo'])) ?>">
                                    <div class="chat-conv-item__avatar">
                                        <?= strtoupper(substr($c['outro_nome'], 0, 1)) ?>
                                        <span class="chat-online-indicator"></span>
                                    </div>
                                    <div class="chat-conv-item__info">
                                        <div class="chat-conv-item__top">
                                            <span class="chat-conv-item__name"><?= htmlspecialchars($c['outro_nome']) ?></span>
                                            <span class="chat-conv-item__time"><?= $timeStr ?></span>
                                        </div>
                                        <div class="chat-conv-item__prod">
                                            <span>🎮</span> <?= htmlspecialchars($c['produto_titulo']) ?>
                                        </div>
                                        <div class="chat-conv-item__bottom">
                                            <p class="chat-conv-item__snippet">
                                                <?= htmlspecialchars(mb_strimwidth($c['ultima_mensagem'] ?? 'Sem mensagens', 0, 42, '...')) ?>
                                            </p>
                                            <?php if ((int)$c['unread_count'] > 0): ?>
                                                <span class="chat-unread-badge"><?= $c['unread_count'] ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </aside>

                <!-- Coluna Direita: Janela de Conversa Ativa -->
                <section class="chat-main-window">
                    <?php if ($activeConv): ?>
                        
                        <!-- Topbar da Conversa -->
                        <div class="chat-main-topbar">
                            <div class="chat-interlocutor">
                                <div class="chat-interlocutor__avatar">
                                    <?= strtoupper(substr($activeConv['outro_nome'], 0, 1)) ?>
                                    <span class="chat-online-indicator"></span>
                                </div>
                                <div>
                                    <div class="chat-interlocutor__name-wrap">
                                        <span class="chat-interlocutor__name"><?= htmlspecialchars($activeConv['outro_nome']) ?></span>
                                        <span class="user-role-badge <?= $activeConv['outro_tipo'] === 'vendedor' ? 'role-badge--vendedor' : 'role-badge--comprador' ?>">
                                            <?= $activeConv['outro_tipo'] === 'vendedor' ? '🏷️ Vendedor Oficial' : '🛒 Comprador' ?>
                                        </span>
                                    </div>
                                    <div class="chat-interlocutor__topic">
                                        <span>🎮 Referência:</span> <strong><?= htmlspecialchars($activeConv['produto_titulo']) ?></strong>
                                    </div>
                                </div>
                            </div>

                            <div class="chat-topbar-actions">
                                <a href="loja.php" class="btn btn--outline btn-sm" title="Consultar catálogo de jogos">
                                    🔍 Ver Loja
                                </a>
                            </div>
                        </div>

                        <!-- Área de Mensagens (com rolagem) -->
                        <div class="chat-messages-area" id="chatMessagesArea" data-conv-id="<?= $activeConv['id'] ?>">
                            <div class="chat-date-divider">
                                <span>Hoje · Mensagens criptografadas e seguras</span>
                            </div>

                            <div class="chat-messages-stream" id="chatStream">
                                <?php if (empty($mensagensAtivas)): ?>
                                    <div class="chat-start-notice">
                                        <span style="font-size:2rem;">👋</span>
                                        <p>Esta é uma nova conversa sobre <strong><?= htmlspecialchars($activeConv['produto_titulo']) ?></strong>.<br>Envie uma mensagem abaixo para iniciar o diálogo.</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($mensagensAtivas as $msg): 
                                        $isMe = ((int)$msg['remetente_id'] === $userId);
                                        $timeMsg = date('H:i', strtotime($msg['enviado_em']));
                                    ?>
                                        <div class="chat-bubble-row <?= $isMe ? 'chat-bubble-row--me' : 'chat-bubble-row--other' ?>" data-id="<?= $msg['id'] ?>">
                                            <div class="chat-bubble">
                                                <?php if (!$isMe): ?>
                                                    <div class="chat-bubble__sender"><?= htmlspecialchars($msg['remetente_nome']) ?></div>
                                                <?php endif; ?>
                                                <div class="chat-bubble__text"><?= nl2br(htmlspecialchars($msg['mensagem'])) ?></div>
                                                <div class="chat-bubble__meta">
                                                    <span class="chat-bubble__time"><?= $timeMsg ?></span>
                                                    <?php if ($isMe): ?>
                                                        <span class="chat-bubble__status" title="<?= $msg['lida'] ? 'Lida' : 'Enviada' ?>">
                                                            <?= $msg['lida'] ? '✓✓' : '✓' ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Chips de Respostas/Perguntas Rápidas -->
                        <div class="chat-quick-chips">
                            <span class="quick-chips-label">Sugestões rápidas:</span>
                            <div class="quick-chips-list">
                                <?php foreach ($sugestoes as $sug): ?>
                                    <button type="button" class="quick-chip-btn" onclick="preencherMensagem(<?= htmlspecialchars(json_encode($sug)) ?>)">
                                        <?= htmlspecialchars($sug) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Barra de Envio de Mensagem -->
                        <div class="chat-input-bar">
                            <form id="chatForm" class="chat-form" onsubmit="enviarMensagem(event)">
                                <input type="hidden" id="activeConvId" value="<?= $activeConv['id'] ?>">
                                <textarea id="chatInput" rows="1" placeholder="Digite sua mensagem aqui... (Pressione Enter para enviar)" required></textarea>
                                <button type="submit" id="btnEnviar" class="chat-btn-send" title="Enviar mensagem">
                                    <span>Enviar</span>
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>

                    <?php else: ?>
                        <!-- Nenhuma conversa selecionada -->
                        <div class="chat-no-active">
                            <div class="chat-no-active__inner">
                                <span style="font-size:3.5rem;">🎮</span>
                                <h2>Selecione uma conversa ao lado</h2>
                                <p>Ou inicie um novo contato para falar sobre conservação de jogos, mídias físicas e prazos de envio.</p>
                                <button class="btn btn--primary" onclick="abrirModalNovaConversa()">+ Iniciar Nova Conversa</button>
                            </div>
                        </div>
                    <?php endif; ?>
                </section>

            </div>
        </section>
    </main>

    <!-- Modal Nova Conversa -->
    <div id="modalNovaConversa" class="chat-modal" style="display:none;">
        <div class="chat-modal__backdrop" onclick="fecharModalNovaConversa()"></div>
        <div class="chat-modal__box">
            <div class="chat-modal__header">
                <h3>Nova Conversa</h3>
                <button type="button" class="chat-modal__close" onclick="fecharModalNovaConversa()">✕</button>
            </div>
            <form id="novaConversaForm" onsubmit="criarNovaConversa(event)">
                <div class="form__row">
                    <label for="modalProduto">Jogo / Produto de interesse</label>
                    <select id="modalProduto" class="chat-select">
                        <option value="Metal Gear Solid (PS1)">Metal Gear Solid · PS1 (CD)</option>
                        <option value="Final Fantasy VII (PS1)">Final Fantasy VII · PS1 (CD)</option>
                        <option value="Crash Bandicoot (PS1)">Crash Bandicoot · PS1 (CD)</option>
                        <option value="God of War (PS2)">God of War · PS2 (DVD)</option>
                        <option value="Zelda: Ocarina of Time (N64)">Zelda: Ocarina of Time · N64 (Cartucho)</option>
                        <option value="Consoles e Acessórios">Consoles e Acessórios Clássicos</option>
                        <option value="Dúvidas Gerais">Outro / Dúvidas Gerais</option>
                    </select>
                </div>

                <div class="form__row">
                    <label for="modalMensagem">Sua primeira mensagem</label>
                    <textarea id="modalMensagem" rows="3" placeholder="Olá! Gostaria de saber mais informações sobre o estado da mídia..." required></textarea>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:20px;">
                    <button type="button" class="btn btn--ghost" onclick="fecharModalNovaConversa()">Cancelar</button>
                    <button type="submit" class="btn btn--primary">Iniciar Conversa</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script JavaScript para Chat em Tempo Real -->
    <script>
    const currentUserId = <?= (int)$userId ?>;
    const currentConvId = <?= (int)$activeConvId ?>;
    let pollingInterval = null;

    // Rolar conversa para o final
    function rolarParaOFim() {
        const area = document.getElementById('chatMessagesArea');
        if (area) {
            area.scrollTop = area.scrollHeight;
        }
    }

    // Preencher chip no campo de mensagem
    function preencherMensagem(texto) {
        const input = document.getElementById('chatInput');
        if (input) {
            input.value = texto;
            input.focus();
        }
    }

    // Modal Nova Conversa
    function abrirModalNovaConversa() {
        document.getElementById('modalNovaConversa').style.display = 'flex';
    }
    function fecharModalNovaConversa() {
        document.getElementById('modalNovaConversa').style.display = 'none';
    }

    document.getElementById('btnNovaConversa')?.addEventListener('click', abrirModalNovaConversa);

    // Criar nova conversa
    async function criarNovaConversa(e) {
        e.preventDefault();
        const produto = document.getElementById('modalProduto').value;
        const msg = document.getElementById('modalMensagem').value.trim();

        if (!msg) return;

        const formData = new FormData();
        formData.append('action', 'start_conversation');
        formData.append('produto_titulo', produto);
        formData.append('mensagem_inicial', msg);

        try {
            const resp = await fetch('api_mensagens.php', { method: 'POST', body: formData });
            const data = await resp.json();
            if (data.success && data.conversa_id) {
                window.location.href = 'mensagens.php?id=' + data.conversa_id;
            } else {
                alert(data.error || 'Erro ao iniciar conversa');
            }
        } catch (err) {
            alert('Erro de comunicação com o servidor');
        }
    }

    // Enviar mensagem via AJAX
    async function enviarMensagem(e) {
        e.preventDefault();
        const input = document.getElementById('chatInput');
        const convId = document.getElementById('activeConvId')?.value;
        const texto = input.value.trim();

        if (!texto || !convId) return;

        input.value = '';
        input.focus();

        const formData = new FormData();
        formData.append('action', 'send_message');
        formData.append('conversa_id', convId);
        formData.append('mensagem', texto);

        // Feedback otimista imediato
        const stream = document.getElementById('chatStream');
        const tempId = 'temp_' + Date.now();
        const tempDiv = document.createElement('div');
        tempDiv.className = 'chat-bubble-row chat-bubble-row--me';
        tempDiv.id = tempId;
        tempDiv.innerHTML = `
            <div class="chat-bubble">
                <div class="chat-bubble__text">${escapeHtml(texto)}</div>
                <div class="chat-bubble__meta">
                    <span class="chat-bubble__time">Agora</span>
                    <span class="chat-bubble__status">⏳</span>
                </div>
            </div>
        `;
        stream.appendChild(tempDiv);
        rolarParaOFim();

        try {
            const resp = await fetch('api_mensagens.php', { method: 'POST', body: formData });
            const data = await resp.json();

            if (data.success && data.message) {
                const row = document.getElementById(tempId);
                if (row) {
                    row.querySelector('.chat-bubble__status').textContent = '✓';
                    row.querySelector('.chat-bubble__time').textContent = data.message.horario;
                }
            }
        } catch (err) {
            console.error('Erro ao enviar mensagem:', err);
        }
    }

    // Polling a cada 3 segundos para buscar novas respostas
    async function checarNovasMensagens() {
        if (!currentConvId) return;

        try {
            const resp = await fetch('api_mensagens.php?action=get_messages&conversa_id=' + currentConvId);
            const data = await resp.json();

            if (data.success && Array.isArray(data.messages)) {
                const stream = document.getElementById('chatStream');
                const existingIds = new Set(
                    Array.from(stream.querySelectorAll('.chat-bubble-row[data-id]'))
                         .map(el => parseInt(el.getAttribute('data-id')))
                );

                let novasAdicionadas = false;

                data.messages.forEach(m => {
                    if (!existingIds.has(m.id)) {
                        novasAdicionadas = true;
                        const row = document.createElement('div');
                        row.className = 'chat-bubble-row ' + (m.is_me ? 'chat-bubble-row--me' : 'chat-bubble-row--other');
                        row.setAttribute('data-id', m.id);
                        
                        let senderHtml = !m.is_me ? `<div class="chat-bubble__sender">${escapeHtml(m.remetente_nome)}</div>` : '';
                        let checkmark = m.is_me ? `<span class="chat-bubble__status">${m.lida ? '✓✓' : '✓'}</span>` : '';

                        row.innerHTML = `
                            <div class="chat-bubble">
                                ${senderHtml}
                                <div class="chat-bubble__text">${escapeHtml(m.mensagem)}</div>
                                <div class="chat-bubble__meta">
                                    <span class="chat-bubble__time">${m.horario}</span>
                                    ${checkmark}
                                </div>
                            </div>
                        `;
                        stream.appendChild(row);
                    }
                });

                if (novasAdicionadas) {
                    rolarParaOFim();
                }
            }
        } catch (err) {
            // Polling silencioso
        }
    }

    // Filtragem rápida da lista de conversas
    document.getElementById('chatSearchInput')?.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const items = document.querySelectorAll('.chat-conv-item');
        items.forEach(item => {
            const name = item.getAttribute('data-name') || '';
            const prod = item.getAttribute('data-prod') || '';
            if (name.includes(query) || prod.includes(query)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Submissão com Enter no textarea
    document.getElementById('chatInput')?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            document.getElementById('chatForm')?.dispatchEvent(new Event('submit'));
        }
    });

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Iniciar na carga da página
    window.addEventListener('DOMContentLoaded', () => {
        rolarParaOFim();
        if (currentConvId > 0) {
            pollingInterval = setInterval(checarNovasMensagens, 3000);
        }
    });
    </script>

<?php require_once __DIR__ . '/php/footer.php'; ?>
