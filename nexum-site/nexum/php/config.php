<?php
/**
 * config.php
 * Configurações centrais do site NEXUM GAMES.
 * Reunidas aqui para serem reaproveitadas em todas as páginas PHP.
 */

// Exibição de erros (desative em produção)
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

define('SITE_NAME', 'Nexum Games');
define('SITE_URL', '/');

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'nexum_games');

/**
 * Retorna conexão PDO com o banco de dados.
 */
function get_db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // Em desenvolvimento, mostrar erro; em produção, logar silenciosamente
            die('<p style="color:#f87171;font-family:sans-serif;padding:20px">Erro de conexão com banco de dados: ' . htmlspecialchars($e->getMessage()) . '<br>Certifique-se que o MySQL está ativo no XAMPP.</p>');
        }
    }
    return $pdo;
}

// Itens de navegação — centralizados para não duplicar HTML em cada página
$NAV_ITEMS = [
    ['label' => 'Início',   'href' => 'index.php'],
    ['label' => 'Loja',     'href' => 'loja.php'],
    ['label' => 'Consoles', 'href' => 'consoles.php'],
    ['label' => 'Sobre',    'href' => 'sobre.php'],
    ['label' => 'Contato',  'href' => 'contato.php'],
];

/**
 * Marca o link de navegação ativo comparando com o script atual.
 */
function is_active_page(string $href): string
{
    $current = basename($_SERVER['PHP_SELF']);
    return $current === $href ? 'nav__link--active' : '';
}

/**
 * Sanitização simples de entrada de formulário.
 */
function clean_input(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}
