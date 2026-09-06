<?php
declare(strict_types=1);

session_name('olvn_session');
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
]);

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header("Content-Security-Policy: default-src 'self'; style-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'self'");
header('Referrer-Policy: no-referrer');

$configPath = '/var/www/ol-value-navigator/config.php';
if (!is_file($configPath)) {
    throw new RuntimeException('Application configuration is missing.');
}

$config = require $configPath;
if (!is_array($config)) {
    throw new RuntimeException('Application configuration is invalid.');
}

$stagePath = '/var/www/ol-value-navigator/stage';
$applicationStage = is_file($stagePath) ? (int) trim((string) file_get_contents($stagePath)) : 3;

try {
    $pdo = new PDO(
        (string) $config['dsn'],
        (string) $config['user'],
        (string) $config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        ]
    );
} catch (Throwable $exception) {
    error_log('OLVN database connection failed: ' . get_class($exception));
    http_response_code(503);
    exit('The application cannot connect to its database. Verify the private configuration and try again.');
}

require_once __DIR__ . '/repository.php';
require_once __DIR__ . '/money.php';
require_once __DIR__ . '/genai.php';
require_once __DIR__ . '/deletion.php';

function app_config(string $key, mixed $default = null): mixed
{
    global $config;
    return $config[$key] ?? $default;
}

function app_stage(): int
{
    global $applicationStage;
    return $applicationStage;
}

function require_stage(int $minimum): void
{
    if (app_stage() < $minimum) {
        flash('info', "This feature is enabled in Lab {$minimum}.");
        redirect('/index.php');
    }
}

function db(): PDO
{
    global $pdo;
    return $pdo;
}

function h(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function app_url(string $path = ''): string
{
    $base = rtrim((string) app_config('base_path', '/ol-value-navigator'), '/');
    return $base . '/' . ltrim($path, '/');
}

function redirect(string $path): void
{
    header('Location: ' . app_url($path), true, 303);
    exit;
}

function require_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit('Method not allowed.');
    }
}

function request_id(string $key = 'id'): int
{
    $value = filter_input(INPUT_GET, $key, FILTER_VALIDATE_INT);
    if ($value === false || $value === null || $value < 1) {
        http_response_code(400);
        exit('A valid comparison identifier is required.');
    }
    return $value;
}

function post_id(string $key = 'id'): int
{
    $value = filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT);
    if ($value === false || $value === null || $value < 1) {
        http_response_code(400);
        exit('A valid comparison identifier is required.');
    }
    return $value;
}

function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return (string) $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $submitted = (string) ($_POST['csrf_token'] ?? '');
    if ($submitted === '' || !hash_equals(csrf_token(), $submitted)) {
        http_response_code(403);
        exit('The form expired. Return to the previous page and try again.');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function render_header(string $title): void
{
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    $home = h(app_url('/index.php'));
    $css = h(app_url('/style.css'));
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>' . h($title) . ' | Oracle Linux Value Navigator</title>';
    echo '<link rel="stylesheet" href="' . $css . '"></head><body>';
    echo '<header><div class="wrap"><a class="brand" href="' . $home . '">Oracle Linux Value Navigator</a>';
    echo '<span class="badge">Workshop prototype</span></div></header><main class="wrap">';
    echo '<div class="notice warning">Use demonstration information only. This prototype is not a quote, licensing determination, or complete TCO analysis.</div>';
    foreach ($flashes as $item) {
        echo '<div class="notice ' . h($item['type']) . '">' . h($item['message']) . '</div>';
    }
    echo '<h1>' . h($title) . '</h1>';
}

function render_footer(): void
{
    echo '</main><footer><div class="wrap">Version 1 has no login and does not maintain master SKU catalogs.</div></footer></body></html>';
}

function fail_page(string $title, string $message, int $status = 400): void
{
    http_response_code($status);
    render_header($title);
    echo '<div class="notice error">' . h($message) . '</div>';
    echo '<p><a class="button secondary" href="' . h(app_url('/index.php')) . '">Return to comparisons</a></p>';
    render_footer();
    exit;
}

function normalize_text(string $value): string
{
    return trim(str_replace(["\r\n", "\r"], "\n", $value));
}

function require_length(string $value, int $minimum, int $maximum, string $label): string
{
    $length = strlen($value);
    if ($length < $minimum || $length > $maximum) {
        throw new InvalidArgumentException("{$label} must contain between {$minimum} and {$maximum} characters.");
    }
    return $value;
}
