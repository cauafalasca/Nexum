<?php
$pageTitle = 'Entrar na sua conta';
$bodyClass = 'page-inner';
require_once __DIR__ . '/php/config.php';

$error = '';
$redirect = clean_input($_GET['redirect'] ?? 'mensagens.php');

// Acesso rápido de teste via POST ou GET seguro
if (isset($_POST['quick_login'])) {
    $targetEmail = $_POST['quick_login'] === 'vendedor' ? 'vendedor@nexum.com' : 'comprador@nexum.com';
    try {
        $pdo = get_db();
        $stmt = $pdo->prepare('SELECT id, nome, email, tipo, status FROM usuarios WHERE email = ?');
        $stmt->execute([$targetEmail]);
        $user = $stmt->fetch();
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nome'] = $user['nome'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_tipo'] = $user['tipo'];
            header('Location: ' . ($redirect ?: 'mensagens.php'));
            exit;
        }
    } catch (Exception $e) {
        $error = 'Erro ao processar login rápido.';
    }
}

// Login tradicional
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $email = clean_input($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $error = 'Preencha o e-mail e a senha.';
    } else {
        try {
            $pdo = get_db();
            $stmt = $pdo->prepare('SELECT id, nome, email, senha, tipo, status FROM usuarios WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($senha, $user['senha'])) {
                if ($user['status'] === 'inativo') {
                    $error = 'Sua conta está inativa. Entre em contato com o suporte.';
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_nome'] = $user['nome'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_tipo'] = $user['tipo'];
                    header('Location: ' . ($redirect ?: 'mensagens.php'));
                    exit;
                }
            } else {
                $error = 'E-mail ou senha incorretos.';
            }
        } catch (Exception $e) {
            $error = 'Erro de conexão com o banco. Tente novamente.';
        }
    }
}

// Se já está logado, redirecionar
if (!empty($_SESSION['user_id'])) {
    header('Location: mensagens.php');
    exit;
}

require_once __DIR__ . '/php/header.php';
?>

    <main>
        <section class="page-hero page-hero--compact">
            <div class="page-hero__inner">
                <p class="eyebrow">Área do Usuário</p>
                <h1>Acesse sua conta Nexum.</h1>
                <p class="page-hero__lead">Conecte-se para conversar com vendedores, acompanhar propostas e gerenciar sua coleção.</p>
            </div>
        </section>

        <section class="content-section">
            <div class="content-section__inner content-section__inner--narrow">

                <!-- Caixa de Demonstração Rápida -->
                <div class="demo-box" style="background:rgba(59,130,246,0.08);border:1px solid rgba(59,130,246,0.25);border-radius:10px;padding:20px;margin-bottom:28px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                        <span style="font-size:1.4rem;">⚡</span>
                        <div>
                            <strong style="color:var(--silver);font-size:0.95rem;display:block;">Acesso Rápido para Testar o Chat</strong>
                            <span style="color:var(--silver-dim);font-size:0.82rem;">Alterne entre Vendedor e Comprador com um único clique para testar as conversas:</span>
                        </div>
                    </div>
                    <form method="post" action="login.php" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
                        <button type="submit" name="quick_login" value="vendedor" class="btn btn--outline" style="border-color:rgba(59,130,246,0.5);color:#93c5fd;font-size:0.85rem;padding:12px 14px;display:flex;align-items:center;justify-content:center;gap:8px;">
                            <span>🏷️</span> Entrar como <strong>Vendedor</strong>
                        </button>
                        <button type="submit" name="quick_login" value="comprador" class="btn btn--outline" style="border-color:rgba(34,197,94,0.5);color:#86efac;font-size:0.85rem;padding:12px 14px;display:flex;align-items:center;justify-content:center;gap:8px;">
                            <span>🛒</span> Entrar como <strong>Comprador</strong>
                        </button>
                    </form>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert--error" role="alert"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form class="form" method="post" action="login.php" novalidate>
                    <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">

                    <div class="form__row">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" placeholder="seu-email@exemplo.com" required autocomplete="email">
                    </div>

                    <div class="form__row">
                        <label for="senha">Senha</label>
                        <input type="password" id="senha" name="senha" placeholder="••••••••" required autocomplete="current-password">
                    </div>

                    <button type="submit" name="login_submit" class="btn btn--primary" style="width:100%;margin-top:10px;">
                        Entrar na conta
                    </button>

                    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;font-size:0.88rem;color:var(--silver-dimmer);">
                        <span>Ainda não tem conta? <a href="criar-conta.php" style="color:var(--accent);">Cadastre-se gratuitamente →</a></span>
                        <a href="admin/login.php" style="color:var(--silver-dim);">Acesso Admin</a>
                    </div>
                </form>

            </div>
        </section>
    </main>

<?php require_once __DIR__ . '/php/footer.php'; ?>
