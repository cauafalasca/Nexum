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

// Gestão de Idioma (Português, English, Español)
if (isset($_GET['lang'])) {
    $allowedLangs = ['pt', 'en', 'es'];
    if (in_array($_GET['lang'], $allowedLangs)) {
        $_SESSION['nexum_lang'] = $_GET['lang'];
        setcookie('nexum_lang', $_GET['lang'], time() + (365 * 86400), '/');
    }
}

function get_active_lang(): string {
    return $_SESSION['nexum_lang'] ?? $_COOKIE['nexum_lang'] ?? 'pt';
}

function __t(string $key): string {
    static $translations = [
        'pt' => [
            'inicio' => 'Início',
            'loja' => 'Loja',
            'consoles' => 'Consoles',
            'mensagens' => 'Mensagens',
            'configuracoes' => 'Configurações',
            'sobre' => 'Sobre',
            'contato' => 'Contato',
            'crie_conta' => 'Crie sua conta',
            'entrar' => 'Entrar',
            'sair' => 'Sair',
            'modo_escuro' => 'Modo Escuro',
            'modo_claro' => 'Modo Claro',
            'idioma' => 'Idioma',
            'termos' => 'Termos de Uso',
            'avaliacoes' => 'Avaliações',
            'falar_vendedor' => 'Falar com vendedor',
        ],
        'en' => [
            'inicio' => 'Home',
            'loja' => 'Store',
            'consoles' => 'Consoles',
            'mensagens' => 'Messages',
            'configuracoes' => 'Settings',
            'sobre' => 'About',
            'contato' => 'Contact',
            'crie_conta' => 'Create account',
            'entrar' => 'Sign In',
            'sair' => 'Log Out',
            'modo_escuro' => 'Dark Mode',
            'modo_claro' => 'Light Mode',
            'idioma' => 'Language',
            'termos' => 'Terms of Service',
            'avaliacoes' => 'Reviews',
            'falar_vendedor' => 'Chat with seller',
        ],
        'es' => [
            'inicio' => 'Inicio',
            'loja' => 'Tienda',
            'consoles' => 'Consolas',
            'mensagens' => 'Mensajes',
            'configuracoes' => 'Ajustes',
            'sobre' => 'Nosotros',
            'contato' => 'Contacto',
            'crie_conta' => 'Crear cuenta',
            'entrar' => 'Iniciar sesión',
            'sair' => 'Salir',
            'modo_escuro' => 'Modo Oscuro',
            'modo_claro' => 'Modo Claro',
            'idioma' => 'Idioma',
            'termos' => 'Términos de Uso',
            'avaliacoes' => 'Reseñas',
            'falar_vendedor' => 'Hablar con el vendedor',
        ]
    ];
    $lang = get_active_lang();
    return $translations[$lang][$key] ?? $translations['pt'][$key] ?? $key;
}

// Itens de navegação — centralizados para não duplicar HTML em cada página
$NAV_ITEMS = [
    ['key' => 'inicio',        'label' => __t('inicio'),        'href' => 'index.php'],
    ['key' => 'loja',          'label' => __t('loja'),          'href' => 'loja.php'],
    ['key' => 'consoles',      'label' => __t('consoles'),      'href' => 'consoles.php'],
    ['key' => 'mensagens',     'label' => __t('mensagens'),     'href' => 'mensagens.php'],
    ['key' => 'configuracoes', 'label' => __t('configuracoes'), 'href' => 'configuracoes.php'],
    ['key' => 'sobre',         'label' => __t('sobre'),         'href' => 'sobre.php'],
    ['key' => 'contato',       'label' => __t('contato'),       'href' => 'contato.php'],
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

/**
 * Retorna dados do usuário atualmente logado na sessão ou null.
 */
function get_logged_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    return [
        'id'    => (int)$_SESSION['user_id'],
        'nome'  => $_SESSION['user_nome'] ?? 'Usuário',
        'email' => $_SESSION['user_email'] ?? '',
        'tipo'  => $_SESSION['user_tipo'] ?? 'comprador',
    ];
}

/**
 * Retorna a contagem de mensagens não lidas para o usuário fornecido.
 */
function get_unread_messages_count(int $userId): int
{
    try {
        $pdo = get_db();
        $stmt = $pdo->prepare("
            SELECT COUNT(m.id) 
            FROM mensagens m
            INNER JOIN conversas c ON m.conversa_id = c.id
            WHERE (c.comprador_id = ? OR c.vendedor_id = ?)
              AND m.remetente_id != ?
              AND m.lida = 0
        ");
        $stmt->execute([$userId, $userId, $userId]);
        return (int)$stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

