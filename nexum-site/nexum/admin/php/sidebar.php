<?php
$pageActive = $pageActive ?? 'dashboard';
$adminUser = $_SESSION['admin_user'] ?? 'admin';
?>
<aside class="admin-sidebar">
    <div class="sidebar__brand">
        <div class="sidebar__logo">Nexum <span>Games</span></div>
        <span class="sidebar__badge">Admin Panel</span>
    </div>

    <nav class="sidebar__nav">
        <div class="sidebar__section-label">Menu</div>
        <a href="index.php" class="<?= $pageActive === 'dashboard' ? 'active' : '' ?>">
            <span class="nav-icon">📊</span> Dashboard
        </a>
        <a href="usuarios.php" class="<?= $pageActive === 'usuarios' ? 'active' : '' ?>">
            <span class="nav-icon">👥</span> Usuários
        </a>
        <a href="produtos.php" class="<?= $pageActive === 'produtos' ? 'active' : '' ?>">
            <span class="nav-icon">🎮</span> Produtos
        </a>
        <a href="pedidos.php" class="<?= $pageActive === 'pedidos' ? 'active' : '' ?>">
            <span class="nav-icon">📦</span> Pedidos
        </a>

        <div class="sidebar__section-label">Site</div>
        <a href="../index.php" target="_blank">
            <span class="nav-icon">🌐</span> Ver site
        </a>
        <a href="../criar-conta.php" target="_blank">
            <span class="nav-icon">➕</span> Cadastro
        </a>
    </nav>

    <div class="sidebar__footer">
        <div class="sidebar__user">
            <div class="sidebar__avatar"><?= strtoupper(substr($adminUser, 0, 1)) ?></div>
            <div class="sidebar__user-info">
                <span class="sidebar__user-name"><?= htmlspecialchars($adminUser) ?></span>
                <span class="sidebar__user-role">Administrador</span>
            </div>
        </div>
        <a href="logout.php" class="btn-admin btn-sm btn-ghost" style="text-align:center;display:block;margin-top:4px;">
            Sair
        </a>
    </div>
</aside>
