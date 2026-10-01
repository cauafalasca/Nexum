<?php
require_once __DIR__ . '/php/auth.php';
admin_check_auth();

$pageActive = 'produtos';

$produtos = [
    ['id' => 1, 'titulo' => 'Metal Gear Solid', 'plataforma' => 'PS1', 'midia' => 'CD', 'preco' => 180.00, 'estoque' => 3, 'status' => 'ativo'],
    ['id' => 2, 'titulo' => 'Final Fantasy VII', 'plataforma' => 'PS1', 'midia' => 'CD', 'preco' => 320.00, 'estoque' => 1, 'status' => 'ativo'],
    ['id' => 3, 'titulo' => 'Crash Bandicoot', 'plataforma' => 'PS1', 'midia' => 'CD', 'preco' => 150.00, 'estoque' => 5, 'status' => 'ativo'],
    ['id' => 4, 'titulo' => 'God of War', 'plataforma' => 'PS2', 'midia' => 'DVD', 'preco' => 210.00, 'estoque' => 2, 'status' => 'ativo'],
    ['id' => 5, 'titulo' => 'Zelda: Ocarina of Time', 'plataforma' => 'N64', 'midia' => 'Cartucho', 'preco' => 480.00, 'estoque' => 1, 'status' => 'ativo'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos · Admin Nexum Games</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<?php include __DIR__ . '/php/sidebar.php'; ?>

<main class="admin-main">
    <div class="admin-topbar">
        <span class="admin-topbar__title">Produtos / Catálogo</span>
        <div class="admin-topbar__actions">
            <button class="btn-admin btn-sm" onclick="alert('Funcionalidade em desenvolvimento — integração com banco de dados em breve.')">+ Novo produto</button>
            <a href="logout.php" class="btn-admin btn-sm btn-danger">Sair</a>
        </div>
    </div>

    <div class="admin-content">

        <div style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.3);color:#fde68a;border-radius:6px;padding:12px 16px;margin-bottom:24px;font-size:0.85rem;">
            📋 Os produtos abaixo são dados de demonstração. A integração completa com banco de dados (tabela <code>produtos</code>) pode ser ativada quando necessário.
        </div>

        <div class="admin-table-wrap">
            <div class="admin-table-header">
                <h3>Catálogo de produtos (<?= count($produtos) ?>)</h3>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Título</th>
                        <th>Plataforma</th>
                        <th>Mídia</th>
                        <th>Preço</th>
                        <th>Estoque</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtos as $p): ?>
                    <tr>
                        <td style="color:var(--silver-dimmer);"><?= $p['id'] ?></td>
                        <td style="color:var(--silver);font-weight:500;"><?= htmlspecialchars($p['titulo']) ?></td>
                        <td><span class="badge badge--comprador"><?= htmlspecialchars($p['plataforma']) ?></span></td>
                        <td><?= htmlspecialchars($p['midia']) ?></td>
                        <td style="color:var(--silver);">R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                        <td>
                            <span style="color:<?= $p['estoque'] <= 1 ? 'var(--warning)' : 'var(--success)' ?>;font-weight:600;">
                                <?= $p['estoque'] ?> un.
                            </span>
                        </td>
                        <td><span class="badge badge--active">Ativo</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

</body>
</html>
