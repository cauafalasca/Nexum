<?php
require_once __DIR__ . '/php/auth.php';
admin_check_auth();

$pageActive = 'pedidos';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos · Admin Nexum Games</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<?php include __DIR__ . '/php/sidebar.php'; ?>

<main class="admin-main">
    <div class="admin-topbar">
        <span class="admin-topbar__title">Pedidos</span>
        <div class="admin-topbar__actions">
            <a href="logout.php" class="btn-admin btn-sm btn-danger">Sair</a>
        </div>
    </div>

    <div class="admin-content">
        <div class="admin-table-wrap">
            <div class="admin-table-header">
                <h3>Pedidos</h3>
            </div>
            <div class="empty-state">
                <div class="empty-state__icon">📦</div>
                <p class="empty-state__text">
                    Nenhum pedido registrado ainda.<br>
                    O módulo de pedidos será ativado quando o carrinho de compras for implementado.
                </p>
            </div>
        </div>
    </div>
</main>

</body>
</html>
