<?php
$pageTitle = 'Contato';
$bodyClass = 'page-inner';
require_once __DIR__ . '/php/config.php';

$errors = [];
$success = false;
$values = ['nome' => '', 'email' => '', 'mensagem' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contato_submit'])) {
    $values['nome']      = clean_input($_POST['nome'] ?? '');
    $values['email']     = clean_input($_POST['email'] ?? '');
    $values['mensagem']  = clean_input($_POST['mensagem'] ?? '');

    if ($values['nome'] === '') {
        $errors['nome'] = 'Informe seu nome.';
    }
    if ($values['email'] === '' || !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Informe um e-mail válido.';
    }
    if ($values['mensagem'] === '' || mb_strlen($values['mensagem']) < 10) {
        $errors['mensagem'] = 'Sua mensagem precisa ter pelo menos 10 caracteres.';
    }

    if (empty($errors)) {
        // Em produção: enviar e-mail (mail()/PHPMailer) ou gravar em banco de dados.
        // mail('contato@nexum.com', 'Novo contato pelo site', $values['mensagem'], "From: {$values['email']}");
        $success = true;
        $values = ['nome' => '', 'email' => '', 'mensagem' => ''];
    }
}

require_once __DIR__ . '/php/header.php';
?>

    <main>
        <section class="page-hero page-hero--compact">
            <div class="page-hero__inner">
                <p class="eyebrow">Fale com a gente</p>
                <h1>Vamos conectar suas ideias.</h1>
                <p class="page-hero__lead">Dúvidas, parcerias ou suporte — responda ao formulário e nosso time retorna em até um dia útil.</p>
            </div>
        </section>

        <section class="content-section">
            <div class="content-section__inner content-section__inner--narrow">

                <?php if ($success): ?>
                    <div class="alert alert--success" role="status">
                        Mensagem enviada com sucesso. Obrigado por entrar em contato!
                    </div>
                <?php endif; ?>

                <form class="form" method="post" action="contato.php" novalidate>
                    <div class="form__row">
                        <label for="nome">Nome</label>
                        <input type="text" id="nome" name="nome" value="<?= $values['nome'] ?>" placeholder="Seu nome completo">
                        <?php if (!empty($errors['nome'])): ?><span class="form__error"><?= $errors['nome'] ?></span><?php endif; ?>
                    </div>

                    <div class="form__row">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" value="<?= $values['email'] ?>" placeholder="voce@empresa.com">
                        <?php if (!empty($errors['email'])): ?><span class="form__error"><?= $errors['email'] ?></span><?php endif; ?>
                    </div>

                    <div class="form__row">
                        <label for="mensagem">Mensagem</label>
                        <textarea id="mensagem" name="mensagem" rows="5" placeholder="Como podemos ajudar?"><?= $values['mensagem'] ?></textarea>
                        <?php if (!empty($errors['mensagem'])): ?><span class="form__error"><?= $errors['mensagem'] ?></span><?php endif; ?>
                    </div>

                    <button type="submit" name="contato_submit" class="btn btn--primary">Enviar mensagem</button>
                </form>
            </div>
        </section>
    </main>

<?php require_once __DIR__ . '/php/footer.php'; ?>
