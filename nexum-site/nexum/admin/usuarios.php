<?php
require_once __DIR__ . '/php/auth.php';
admin_check_auth();

$pdo = get_db();

// Ação de status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'], $_POST['user_id'])) {
    $uid = (int)$_POST['user_id'];
    $novoStatus = match($_POST['acao']) {
        'ativar'   => 'ativo',
        'inativar' => 'inativo',
        default    => null
    };
    if ($novoStatus) {
        $pdo->prepare('UPDATE usuarios SET status = ? WHERE id = ?')->execute([$novoStatus, $uid]);
    }
    header('Location: usuarios.php?updated=1');
    exit;
}

// Buscar todos usuários
$stmt = $pdo->query('SELECT id, nome, email, cpf, tipo, status, criado_em FROM usuarios ORDER BY criado_em DESC');
$usuarios = $stmt->fetchAll();

$pageActive = 'usuarios';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários · Admin Nexum Games</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<?php include __DIR__ . '/php/sidebar.php'; ?>

<main class="admin-main">
    <div class="admin-topbar">
        <span class="admin-topbar__title">Usuários</span>
        <div class="admin-topbar__actions">
            <a href="logout.php" class="btn-admin btn-sm btn-danger">Sair</a>
        </div>
    </div>

    <div class="admin-content">

        <?php if (isset($_GET['updated'])): ?>
            <div style="background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);color:#86efac;border-radius:6px;padding:12px 16px;margin-bottom:20px;font-size:0.85rem;">
                ✓ Status atualizado com sucesso.
            </div>
        <?php endif; ?>

        <div class="admin-table-wrap">
            <div class="admin-table-header">
                <h3>Todos os usuários (<?= count($usuarios) ?>)</h3>
            </div>

            <?php if (empty($usuarios)): ?>
                <div class="empty-state">
                    <div class="empty-state__icon">👤</div>
                    <p class="empty-state__text">Nenhum usuário cadastrado ainda.<br>Os cadastros feitos pelo site aparecerão aqui.</p>
                </div>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>CPF</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Data cadastro</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td style="color:var(--silver-dimmer);"><?= $u['id'] ?></td>
                            <td style="color:var(--silver);font-weight:500;"><?= htmlspecialchars($u['nome']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= $u['cpf'] ? htmlspecialchars(substr($u['cpf'], 0, 3) . '.***.***-' . substr($u['cpf'], -2)) : '—' ?></td>
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
                            <td><?= date('d/m/Y', strtotime($u['criado_em'])) ?></td>
                            <td>
                                <form method="post" action="usuarios.php" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <?php if ($u['status'] !== 'ativo'): ?>
                                        <button type="submit" name="acao" value="ativar" class="btn-admin btn-sm" style="background:var(--success-bg);color:var(--success);border:1px solid rgba(34,197,94,0.3);box-shadow:none;">Ativar</button>
                                    <?php else: ?>
                                        <button type="submit" name="acao" value="inativar" class="btn-admin btn-sm btn-ghost">Inativar</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div>
</main>

</body>
</html>
