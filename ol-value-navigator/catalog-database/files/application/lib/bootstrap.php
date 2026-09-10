<?php
declare(strict_types=1);

/**
 * Shared runtime bootstrap loaded by every public controller.
 *
 * Startup creates a strict session, sends browser security headers, loads the
 * private configuration and workshop feature stage, opens the shared PDO
 * connection, and loads all application libraries. The helper functions below
 * centralize escaping, URLs, request validation, CSRF protection, flash messages,
 * and page rendering so individual routes cannot accidentally use weaker rules.
 */
session_name('olvn_session');
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
]);

// Apply the same defensive browser policy before any route renders content.
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header("Content-Security-Policy: default-src 'self'; style-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'self'");
header('Referrer-Policy: no-referrer');

// Credentials remain in the private application directory and are never served by Apache.
$configPath = '/var/www/ol-value-navigator/config.php';
if (!is_file($configPath)) {
    throw new RuntimeException('Application configuration is missing.');
}

$config = require $configPath;
if (!is_array($config)) {
    throw new RuntimeException('Application configuration is invalid.');
}

$stagePath = '/var/www/ol-value-navigator/stage';
// Defaulting to stage 3 keeps advanced actions unavailable if the stage file is absent.
$applicationStage = is_file($stagePath) ? (int) trim((string) file_get_contents($stagePath)) : 3;

try {
    // Native prepared statements prevent PDO from rewriting parameterized SQL. The
    // workshop does not distribute a CA bundle, so server-certificate verification
    // remains a documented production-readiness control rather than a prototype claim.
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
require_once __DIR__ . '/context.php';
require_once __DIR__ . '/money.php';
require_once __DIR__ . '/genai.php';
require_once __DIR__ . '/deletion.php';

/**
 * Read one private configuration value without exposing the configuration array.
 *
 * @param string $key Configuration-array key.
 * @param mixed $default Value returned when the key is absent.
 * @return mixed Configured value or the supplied default.
 */
function app_config(string $key, mixed $default = null): mixed
{
    global $config;
    return $config[$key] ?? $default;
}

/**
 * Return the deployed workshop feature stage.
 *
 * @return int Stage 3, 4, or 5 as written by deploy.sh.
 */
function app_stage(): int
{
    global $applicationStage;
    return $applicationStage;
}

/**
 * Prevent a route from running before its workshop stage is enabled.
 *
 * On failure this stores an informational flash message and redirects to the
 * comparison list, so callers must invoke it before producing output.
 *
 * @param int $minimum Lowest stage allowed to use the route.
 * @return void
 */
function require_stage(int $minimum): void
{
    if (app_stage() < $minimum) {
        flash('info', "This feature is enabled in Lab {$minimum}.");
        redirect('/index.php');
    }
}

/**
 * Return the shared PDO connection configured during bootstrap.
 *
 * @return PDO Shared exception-mode database connection.
 */
function db(): PDO
{
    global $pdo;
    return $pdo;
}

/**
 * Escape an untrusted value for safe placement in HTML text or attributes.
 *
 * @param mixed $value Value to convert to UTF-8 HTML-safe text.
 * @return string Escaped text safe for HTML text and quoted attributes.
 */
function h(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Build an application-relative URL from the configured deployment base path.
 *
 * @param string $path Route or asset path beneath the application root.
 * @return string URL rooted at the configured application base path.
 */
function app_url(string $path = ''): string
{
    $base = rtrim((string) app_config('base_path', '/ol-value-navigator'), '/');
    return $base . '/' . ltrim($path, '/');
}

/**
 * Complete a POST-redirect-GET transition with an HTTP 303 response.
 *
 * @param string $path Application-relative redirect destination.
 * @return never
 */
function redirect(string $path): void
{
    header('Location: ' . app_url($path), true, 303);
    exit;
}

/**
 * Reject non-POST access to a state-changing controller.
 *
 * @return void The request terminates with HTTP 405 when the method is not POST.
 */
function require_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit('Method not allowed.');
    }
}

/**
 * Read and validate a positive integer identifier from the query string.
 *
 * The request terminates with HTTP 400 when the value is absent, nonnumeric,
 * or less than one.
 *
 * @param string $key Query-string key containing the identifier.
 * @return int Valid positive identifier.
 */
function request_id(string $key = 'id'): int
{
    $value = filter_input(INPUT_GET, $key, FILTER_VALIDATE_INT);
    if ($value === false || $value === null || $value < 1) {
        http_response_code(400);
        exit('A valid comparison identifier is required.');
    }
    return $value;
}

/**
 * Read and validate a positive integer identifier from submitted form data.
 *
 * The request terminates with HTTP 400 when the value is absent, nonnumeric,
 * or less than one.
 *
 * @param string $key Form key containing the identifier.
 * @return int Valid positive identifier.
 */
function post_id(string $key = 'id'): int
{
    $value = filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT);
    if ($value === false || $value === null || $value < 1) {
        http_response_code(400);
        exit('A valid comparison identifier is required.');
    }
    return $value;
}

/**
 * Return the session-bound CSRF token, creating it with cryptographic randomness.
 *
 * @return string Hex-encoded 256-bit token stored in the current session.
 * @throws Throwable When the operating system cannot provide secure randomness.
 */
function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return (string) $_SESSION['csrf_token'];
}

/**
 * Render the current CSRF token as an escaped hidden form field.
 *
 * @return string Ready-to-render hidden input element.
 * @throws Throwable When a new token is needed and secure randomness fails.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}

/**
 * Compare the submitted CSRF token with the session token in constant time.
 *
 * @return void The request terminates with HTTP 403 when validation fails.
 */
function verify_csrf(): void
{
    $submitted = (string) ($_POST['csrf_token'] ?? '');
    if ($submitted === '' || !hash_equals(csrf_token(), $submitted)) {
        http_response_code(403);
        exit('The form expired. Return to the previous page and try again.');
    }
}

/**
 * Queue a one-request status message for display after a redirect.
 *
 * @param string $type CSS notice type such as success, info, warning, or error.
 * @param string $message User-safe message that contains no secrets or source text.
 * @return void
 */
function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

/**
 * Start a complete application page and render queued flash messages safely.
 *
 * This consumes the session flash queue and writes the opening HTML response.
 *
 * @param string $title Page-specific browser and H1 title.
 * @return void
 */
function render_header(string $title): void
{
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    $home = h(app_url('/index.php'));
    $help = h(app_url('/help.php'));
    $css = h(app_url('/style.css'));
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>' . h($title) . ' | Oracle Linux Value Navigator</title>';
    echo '<link rel="stylesheet" href="' . $css . '"></head><body>';
    echo '<header><div class="wrap"><a class="brand" href="' . $home . '">Oracle Linux Value Navigator</a>';
    echo '<nav class="header-actions" aria-label="Application navigation"><a class="header-link" href="' . $help . '">Help</a>';
    echo '<span class="badge">Workshop prototype</span></nav></div></header><main class="wrap">';
    echo '<div class="notice warning">Use demonstration information only. This prototype is not a quote, licensing determination, or complete TCO analysis.</div>';
    foreach ($flashes as $item) {
        echo '<div class="notice ' . h($item['type']) . '">' . h($item['message']) . '</div>';
    }
    echo '<h1>' . h($title) . '</h1>';
}

/**
 * Close the application page and repeat the Version 1 governance boundary.
 *
 * @return void
 */
function render_footer(): void
{
    echo '</main><footer><div class="wrap">Version 1 has no login and does not maintain master SKU catalogs.</div></footer></body></html>';
}

/**
 * Render a user-safe error page without disclosing SQL, credentials, or exceptions.
 *
 * @param string $title Error-page title.
 * @param string $message Message safe to display to the representative.
 * @param int $status HTTP status code.
 * @return never
 */
function fail_page(string $title, string $message, int $status = 400): void
{
    http_response_code($status);
    render_header($title);
    echo '<div class="notice error">' . h($message) . '</div>';
    echo '<p><a class="button secondary" href="' . h(app_url('/index.php')) . '">Return to comparisons</a></p>';
    render_footer();
    exit;
}

/**
 * Normalize browser line endings and remove surrounding whitespace.
 *
 * @param string $value Submitted text.
 * @return string Trimmed text using LF line endings.
 */
function normalize_text(string $value): string
{
    return trim(str_replace(["\r\n", "\r"], "\n", $value));
}

/**
 * Enforce server-side text length limits after normalization.
 *
 * @param string $value Text to validate.
 * @param int $minimum Minimum byte length accepted by this prototype.
 * @param int $maximum Maximum byte length accepted by this prototype.
 * @param string $label Field name used in the validation message.
 * @return string The unchanged validated value.
 * @throws InvalidArgumentException When the value falls outside the allowed range.
 */
function require_length(string $value, int $minimum, int $maximum, string $label): string
{
    $length = strlen($value);
    if ($length < $minimum || $length > $maximum) {
        throw new InvalidArgumentException("{$label} must contain between {$minimum} and {$maximum} characters.");
    }
    return $value;
}
