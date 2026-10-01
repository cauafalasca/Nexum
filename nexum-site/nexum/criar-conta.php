<?php
$pageTitle = 'Crie sua conta';
$bodyClass = 'page-inner';
require_once __DIR__ . '/php/config.php';

$errors = [];
$success = false;
$values = ['nome' => '', 'email' => '', 'cpf' => '', 'tipo' => 'comprador'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['conta_submit'])) {
    $values['nome']  = clean_input($_POST['nome'] ?? '');
    $values['email'] = clean_input($_POST['email'] ?? '');
    $values['cpf']   = clean_input($_POST['cpf'] ?? '');
    $values['tipo']  = in_array($_POST['tipo'] ?? '', ['comprador', 'vendedor']) ? $_POST['tipo'] : 'comprador';
    $senha           = $_POST['senha'] ?? '';
    $senhaConfirma   = $_POST['senha_confirma'] ?? '';

    // Validações
    if ($values['nome'] === '') {
        $errors['nome'] = 'Informe seu nome completo.';
    }
    if ($values['email'] === '' || !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Informe um e-mail válido.';
    }
    if ($values['tipo'] === 'vendedor') {
        $cpf_limpo = preg_replace('/\D/', '', $values['cpf']);
        if (strlen($cpf_limpo) !== 11) {
            $errors['cpf'] = 'Informe um CPF válido (somente números).';
        }
    }
    if (strlen($senha) < 8) {
        $errors['senha'] = 'A senha precisa ter pelo menos 8 caracteres.';
    } elseif ($senha !== $senhaConfirma) {
        $errors['senha_confirma'] = 'As senhas não coincidem.';
    }

    if (empty($errors)) {
        try {
            $pdo = get_db();

            // Verificar e-mail duplicado
            $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
            $stmt->execute([$values['email']]);
            if ($stmt->fetch()) {
                $errors['email'] = 'Este e-mail já está cadastrado.';
            } else {
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, cpf, senha, tipo) VALUES (?, ?, ?, ?, ?)');
                $cpf_store = $values['tipo'] === 'vendedor' ? preg_replace('/\D/', '', $values['cpf']) : null;
                $stmt->execute([$values['nome'], $values['email'], $cpf_store, $hash, $values['tipo']]);

                $success = true;
                $_SESSION['ultimo_cadastro'] = $values['nome'];
                $values = ['nome' => '', 'email' => '', 'cpf' => '', 'tipo' => 'comprador'];
            }
        } catch (PDOException $e) {
            $errors['geral'] = 'Erro ao salvar cadastro. Tente novamente.';
        }
    }
}

require_once __DIR__ . '/php/header.php';
?>

    <main>
        <section class="page-hero page-hero--compact">
            <div class="page-hero__inner">
                <p class="eyebrow">Comece agora</p>
                <h1>Crie sua conta Nexum Games.</h1>
                <p class="page-hero__lead">Acesse o catálogo completo, salve favoritos e acompanhe suas compras. Grátis.</p>
            </div>
        </section>

        <section class="content-section">
            <div class="content-section__inner content-section__inner--narrow">

                <?php if ($success): ?>
                    <div class="alert alert--success" role="status">
                        🎮 Conta criada com sucesso, <?= clean_input($_SESSION['ultimo_cadastro'] ?? '') ?>!
                        Bem-vindo à Nexum Games. Você já pode fazer login e explorar o catálogo.
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors['geral'])): ?>
                    <div class="alert alert--error" role="alert"><?= $errors['geral'] ?></div>
                <?php endif; ?>

                <form class="form" method="post" action="criar-conta.php" novalidate>

                    <!-- Tipo de conta -->
                    <div class="form__row">
                        <label>Tipo de conta</label>
                        <div class="form__tipo-group">
                            <label class="tipo-option">
                                <input type="radio" name="tipo" value="comprador" <?= $values['tipo'] === 'comprador' ? 'checked' : '' ?>>
                                <span class="tipo-option__label">
                                    <span class="tipo-option__icon">🛒</span>
                                    Comprador
                                    <small style="font-size:0.72rem;color:var(--silver-dimmer);font-weight:400;">Quero comprar jogos</small>
                                </span>
                            </label>
                            <label class="tipo-option">
                                <input type="radio" name="tipo" value="vendedor" <?= $values['tipo'] === 'vendedor' ? 'checked' : '' ?>>
                                <span class="tipo-option__label">
                                    <span class="tipo-option__icon">📦</span>
                                    Vendedor
                                    <small style="font-size:0.72rem;color:var(--silver-dimmer);font-weight:400;">Quero vender jogos</small>
                                </span>
                            </label>
                        </div>
                    </div>

                    <hr class="form__divider">

                    <div class="form__row">
                        <label for="nome">Nome completo</label>
                        <input type="text" id="nome" name="nome" value="<?= $values['nome'] ?>" placeholder="Seu nome completo" required>
                        <?php if (!empty($errors['nome'])): ?><span class="form__error"><?= $errors['nome'] ?></span><?php endif; ?>
                    </div>

                    <div class="form__row">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" value="<?= $values['email'] ?>" placeholder="voce@email.com" required>
                        <?php if (!empty($errors['email'])): ?><span class="form__error"><?= $errors['email'] ?></span><?php endif; ?>
                    </div>

                    <div class="form__row">
                         <label for="date">Data de Nascimento</label>
                         <input type="date" id="date" name="date"
                            value="<?= htmlspecialchars($values['date'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        <?php if (!empty($errors['date'])): ?>
                        <span class="form__error"><?= htmlspecialchars($errors['date'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                        </div>

                    <div class="form__row">
                        <label for="telefone">Telefone</label>
                        <input type="tel" id="telefone" name="telefone"
                            value="<?= htmlspecialchars($values['telefone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="(14) 99999-9999"
                            inputmode="numeric" autocomplete="tel"
                            pattern="\(?\d{2}\)?\s?\d{4,5}-?\d{4}" required>
                        <?php if (!empty($errors['telefone'])): ?>
                            <span class="form__error"><?= htmlspecialchars($errors['telefone'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>

                    <?php
                    $ufs = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];
                    $estadoAtual = $values['estado'] ?? '';
                    ?>

                    <div class="form__row form__row--split">
                        <div class="form__col form__col--grow">
                            <label for="endereco">Endereço</label>
                            <input type="text" id="endereco" name="endereco"
                                value="<?= htmlspecialchars($values['endereco'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="Rua, número e bairro"
                                autocomplete="street-address"
                                maxlength="150" required>
                            <?php if (!empty($errors['endereco'])): ?>
                                <span class="form__error"><?= htmlspecialchars($errors['endereco'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form__col form__col--estado"> <br>
                            <label for="estado">Estado</label>
                            <select id="estado" name="estado" autocomplete="address-level1" required>
                                <option value="">UF</option>
                                <?php foreach ($ufs as $uf): ?>
                                    <option value="<?= $uf ?>" <?= $estadoAtual === $uf ? 'selected' : '' ?>><?= $uf ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!empty($errors['estado'])): ?>
                                <span class="form__error"><?= htmlspecialchars($errors['estado'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form__row-group">
                        <div class="form__row">
                            <label for="senha">Senha</label>
                            <input type="password" id="senha" name="senha" placeholder="Mínimo 8 caracteres" required>
                            <?php if (!empty($errors['senha'])): ?><span class="form__error"><?= $errors['senha'] ?></span><?php endif; ?>
                        </div>
                        <div class="form__row">
                            <label for="senha_confirma">Confirmar senha</label>
                            <input type="password" id="senha_confirma" name="senha_confirma" placeholder="Repita a senha" required>
                            <?php if (!empty($errors['senha_confirma'])): ?><span class="form__error"><?= $errors['senha_confirma'] ?></span><?php endif; ?>
                        </div>
                    </div>

                    <button type="submit" name="conta_submit" class="btn btn--primary btn--block">Criar conta</button>

                    <p style="margin-top:20px;text-align:center;font-size:0.82rem;">
                        Já tem conta? <a href="login.php" style="color:var(--accent);">Faça login →</a>
                    </p>
                </form>
            </div>
        </section>
    </main>

    <script>
    // Mostrar/esconder campo CPF baseado no tipo de conta selecionado
    (function() {
        const radios = document.querySelectorAll('input[name="tipo"]');
        const cpfField = document.getElementById('cpf-field');
        const cpfInput = document.getElementById('cpf');

        function toggleCpf() {
            const isVendedor = document.querySelector('input[name="tipo"]:checked')?.value === 'vendedor';
            cpfField.style.display = isVendedor ? 'block' : 'none';
            cpfInput.required = isVendedor;
        }

        radios.forEach(r => r.addEventListener('change', toggleCpf));
        toggleCpf(); // Rodar ao carregar caso tipo esteja pré-selecionado

        // Máscara de CPF
        cpfInput.addEventListener('input', function() {
            let v = this.value.replace(/\D/g, '');
            if (v.length > 11) v = v.slice(0, 11);
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            this.value = v;
        });
    })();
    </script>

<?php require_once __DIR__ . '/php/footer.php'; ?>
