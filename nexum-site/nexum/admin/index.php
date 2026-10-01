<?php
require_once __DIR__ . '/php/auth.php';
admin_check_auth();

// Buscar dados do banco
$pdo = get_db();

$totalUsuarios = (int)$pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
$totalCompradores = (int)$pdo->query("SELECT COUNT(*) FROM usuarios WHERE tipo = 'comprador'")->fetchColumn();
$totalVendedores = (int)$pdo->query("SELECT COUNT(*) FROM usuarios WHERE tipo = 'vendedor'")->fetchColumn();
$totalPendentes = (int)$pdo->query("SELECT COUNT(*) FROM usuarios WHERE status = 'pendente'")->fetchColumn();

// Últimos 10 cadastros
$stmtRecentes = $pdo->query('SELECT nome, email, tipo, status, criado_em FROM usuarios ORDER BY criado_em DESC LIMIT 10');
$recentes = $stmtRecentes->fetchAll();

$pageActive = 'dashboard';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard · Admin Nexum Games</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<?php include __DIR__ . '/php/sidebar.php'; ?>

<main class="admin-main">
    <div class="admin-topbar">
        <span class="admin-topbar__title">Dashboard</span>
        <div class="admin-topbar__actions">
            <a href="../index.php" class="btn-admin btn-sm btn-ghost" target="_blank">Ver site →</a>
            <a href="logout.php" class="btn-admin btn-sm btn-danger">Sair</a>
        </div>
    </div>

    <div class="admin-content">
        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card stat-card--accent">
                <div class="stat-card__label">Total de usuários</div>
                <div class="stat-card__value"><?= $totalUsuarios ?></div>
                <div class="stat-card__sub">Cadastros na plataforma</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__label">Compradores</div>
                <div class="stat-card__value"><?= $totalCompradores ?></div>
                <div class="stat-card__sub">Contas de comprador</div>
            </div>
            <div class="stat-card stat-card--warning">
                <div class="stat-card__label">Vendedores</div>
                <div class="stat-card__value"><?= $totalVendedores ?></div>
                <div class="stat-card__sub">Contas de vendedor</div>
            </div>
            <div class="stat-card stat-card--success">
                <div class="stat-card__label">Pendentes</div>
                <div class="stat-card__value"><?= $totalPendentes ?></div>
                <div class="stat-card__sub">Aguardando aprovação</div>
            </div>
        </div>

        <!-- Últimos cadastros -->
        <div class="admin-table-wrap">
            <div class="admin-table-header">
                <h3>Últimos cadastros</h3>
                <a href="usuarios.php" class="btn-admin btn-sm btn-ghost">Ver todos →</a>
            </div>

            <?php if (empty($recentes)): ?>
                <div class="empty-state">
                    <div class="empty-state__icon">👤</div>
                    <p class="empty-state__text">Nenhum usuário cadastrado ainda.</p>
                </div>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentes as $u): ?>
                        <tr>
                            <td style="color:var(--silver);font-weight:500;"><?= htmlspecialchars($u['nome']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td>
                                <span class="badge badge--<?= $u['tipo'] ?>"><?= ucfirst($u['tipo']) ?></span>
                            </td>
                            <td>
                                <?php
                                $statusClass = match($u['status']) {
                                    'ativo'   => 'active',
                                    'inativo' => 'inactive',
                                    default   => 'pending'
                                };
                                ?>
                                <span class="badge badge--<?= $statusClass ?>"><?= ucfirst($u['status']) ?></span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($u['criado_em'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div><!-- /admin-content -->
</main>

</body>
</html>
