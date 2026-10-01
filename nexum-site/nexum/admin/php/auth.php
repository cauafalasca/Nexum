<?php
/**
 * admin/php/auth.php
 * Autenticação do painel administrativo.
 */

// Referenciar config principal
require_once dirname(__DIR__, 2) . '/php/config.php';

define('ADMIN_USER', 'admin');
// senha: nexum2026 — hash gerado com password_hash('nexum2026', PASSWORD_DEFAULT)
define('ADMIN_PASS_HASH', '$2y$10$DJRKqetCBd6NZyiOiOUMo.XxgFJqUft6UvsXA2ug78.Wq0nWK6JYS');

/**
 * Verifica se o admin está logado. Se não, redireciona para login.
 */
function admin_check_auth(): void
{
    if (empty($_SESSION['admin_logged_in'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Tenta fazer login com as credenciais fornecidas.
 */
function admin_try_login(string $user, string $pass): bool
{
    if ($user === ADMIN_USER && password_verify($pass, ADMIN_PASS_HASH)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $user;
        return true;
    }
    return false;
}

/**
 * Desloga o admin.
 */
function admin_logout(): void
{
    unset($_SESSION['admin_logged_in'], $_SESSION['admin_user']);
    session_destroy();
}
