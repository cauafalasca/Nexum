<?php
require_once __DIR__ . '/php/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $user = trim($_POST['usuario'] ?? '');
    $pass = $_POST['senha'] ?? '';

    if (admin_try_login($user, $pass)) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'Usuário ou senha incorretos.';
    }
}

// Se já está logado, redirecionar
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login · Nexum Games</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="admin-login">

    <div class="login-card">
        <div class="login-card__logo">
            Nexum <span>Games</span>
        </div>
        <div class="login-card__sub">Painel Administrativo</div>

        <?php if ($error): ?>
            <div class="login-error">⚠ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="login.php" novalidate>
            <div class="form-group">
                <label for="usuario">Usuário</label>
                <input type="text" id="usuario" name="usuario" placeholder="admin" autocomplete="username" required>
            </div>

            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="••••••••" autocomplete="current-password" required>
            </div>

            <button type="submit" name="login_submit" class="btn-admin">
                Entrar no painel
            </button>
        </form>

        <p style="text-align:center;margin-top:24px;font-size:0.78rem;color:var(--silver-dimmer);">
            <a href="../index.php" style="color:var(--silver-dim);">← Voltar ao site</a>
        </p>
    </div>

</body>
</html>
