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
session_name('olvn2_session');
session_start([
    'cookie_lifetime' => 0,
    'cookie_path' => '/ol-value-navigator-2/',
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'cookie_secure' => request_is_https(),
    'gc_maxlifetime' => 28800,
    'use_only_cookies' => true,
    'use_strict_mode' => true,
]);

// Apply the same defensive browser policy before any route renders content.
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header("Content-Security-Policy: default-src 'self'; style-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'self'");
header('Referrer-Policy: no-referrer');
header('Cache-Control: no-store');

// Credentials remain in the private application directory and are never served by Apache.
$configPath = '/var/www/ol-value-navigator-2/config.php';
if (!is_file($configPath)) {
    throw new RuntimeException('Application configuration is missing.');
}

$config = require $configPath;
if (!is_array($config)) {
    throw new RuntimeException('Application configuration is invalid.');
}

$stagePath = '/var/www/ol-value-navigator-2/stage';
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
require_once __DIR__ . '/money.php';
require_once __DIR__ . '/genai.php';
require_once __DIR__ . '/deletion.php';

maintain_authenticated_session();

/**
 * Detect whether PHP received the request over HTTPS.
 *
 * The Version 2 workshop currently uses direct Apache access. No proxy header is
 * trusted here, so an external client cannot force an incorrect cookie setting.
 *
 * @return bool True when the web server marks the request as HTTPS.
 */
function request_is_https(): bool
{
    $https = strtolower((string) ($_SERVER['HTTPS'] ?? ''));
    return $https !== '' && $https !== 'off';
}

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
    $base = rtrim((string) app_config('base_path', '/ol-value-navigator-2'), '/');
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
 * Apply the authenticated session's inactivity, absolute, and rotation limits.
 *
 * Anonymous sessions are retained for CSRF protection on login and registration.
 * Authenticated sessions expire after 30 minutes without activity or eight hours
 * from login and receive a new identifier every 15 minutes while active.
 *
 * @return void
 */
function maintain_authenticated_session(): void
{
    if (!isset($_SESSION['authenticated_user_id'])) {
        return;
    }

    $now = time();
    $authenticatedAt = (int) ($_SESSION['authenticated_at'] ?? 0);
    $lastActivityAt = (int) ($_SESSION['last_activity_at'] ?? 0);
    $lastRegeneratedAt = (int) ($_SESSION['last_regenerated_at'] ?? 0);

    if (
        $authenticatedAt < 1
        || $lastActivityAt < 1
        || ($now - $lastActivityAt) > 1800
        || ($now - $authenticatedAt) > 28800
    ) {
        clear_authentication_state();
        session_regenerate_id(true);
        $_SESSION['authentication_expired'] = true;
        return;
    }

    if ($lastRegeneratedAt < 1 || ($now - $lastRegeneratedAt) > 900) {
        session_regenerate_id(true);
        $_SESSION['last_regenerated_at'] = $now;
    }
    $_SESSION['last_activity_at'] = $now;
}

/**
 * Remove identity and authentication timestamps from the current session.
 *
 * @return void
 */
function clear_authentication_state(): void
{
    unset(
        $_SESSION['authenticated_user_id'],
        $_SESSION['authenticated_at'],
        $_SESSION['last_activity_at'],
        $_SESSION['last_regenerated_at']
    );
}

/**
 * Load the active account represented by the current authenticated session.
 *
 * @return array<string,mixed>|null Current account or null for an anonymous or
 * disabled session.
 */
function current_user(): ?array
{
    static $loaded = false;
    static $user = null;

    if ($loaded) {
        return $user;
    }
    $loaded = true;

    $id = filter_var($_SESSION['authenticated_user_id'] ?? null, FILTER_VALIDATE_INT);
    if ($id === false || $id === null || $id < 1) {
        return null;
    }

    $user = find_active_user_by_id((int) $id);
    if ($user === null) {
        clear_authentication_state();
        session_regenerate_id(true);
    }
    return $user;
}

/**
 * Require an active application-managed account before continuing a route.
 *
 * All comparison, Help, export, and logout routes invoke this guard.
 *
 * @return void
 */
function require_login(): void
{
    if (current_user() === null) {
        redirect('/login.php');
    }
}

/**
 * Return the active account identifier or redirect an anonymous request.
 *
 * @return int Current authenticated user account primary key.
 */
function current_user_id(): int
{
    $user = current_user();
    if ($user === null) {
        redirect('/login.php');
    }
    return (int) $user['id'];
}

/**
 * Replace an anonymous session with a fresh authenticated session.
 *
 * @param int $userId Authenticated account primary key.
 * @return void
 */
function authenticate_user(int $userId): void
{
    session_regenerate_id(true);
    $_SESSION = [];
    $now = time();
    $_SESSION['authenticated_user_id'] = $userId;
    $_SESSION['authenticated_at'] = $now;
    $_SESSION['last_activity_at'] = $now;
    $_SESSION['last_regenerated_at'] = $now;
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Destroy the server-side session and expire its browser cookie.
 *
 * @return void
 */
function logout_user(): void
{
    $_SESSION = [];
    if ((bool) ini_get('session.use_cookies')) {
        $parameters = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $parameters['path'],
            'domain' => $parameters['domain'],
            'secure' => $parameters['secure'],
            'httponly' => $parameters['httponly'],
            'samesite' => $parameters['samesite'] ?? 'Lax',
        ]);
    }
    session_destroy();
}

/**
 * Normalize and validate a human-readable login name.
 *
 * @param string $value Submitted username.
 * @return string Valid username.
 * @throws InvalidArgumentException When the username format is unsupported.
 */
function validate_username(string $value): string
{
    $username = trim($value);
    if (preg_match('/\A[A-Za-z][A-Za-z0-9._-]{2,63}\z/', $username) !== 1) {
        throw new InvalidArgumentException(
            'Username must be 3 to 64 characters and start with a letter. Use letters, numbers, periods, underscores, or hyphens.'
        );
    }
    return $username;
}

/**
 * Validate a new password without imposing fragile composition rules.
 *
 * @param string $password Submitted password.
 * @return string Valid password.
 * @throws InvalidArgumentException When the password length is unsupported.
 */
function validate_new_password(string $password): string
{
    $length = strlen($password);
    if ($length < 12 || $length > 128) {
        throw new InvalidArgumentException('Password must contain between 12 and 128 characters.');
    }
    return $password;
}

/**
 * Return the remaining temporary delay after repeated login failures.
 *
 * @return int Seconds remaining, or zero when another attempt is allowed.
 */
function login_delay_remaining(): int
{
    $blockedUntil = (int) ($_SESSION['login_blocked_until'] ?? 0);
    if ($blockedUntil <= time()) {
        unset($_SESSION['login_blocked_until']);
        return 0;
    }
    return $blockedUntil - time();
}

/**
 * Record a failed login and briefly delay further attempts after five failures.
 *
 * @return void
 */
function record_failed_login(): void
{
    $failures = (int) ($_SESSION['login_failure_count'] ?? 0) + 1;
    $_SESSION['login_failure_count'] = $failures;
    if ($failures >= 5) {
        $_SESSION['login_blocked_until'] = time() + 60;
        $_SESSION['login_failure_count'] = 0;
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
    $login = h(app_url('/login.php'));
    $register = h(app_url('/register.php'));
    $logout = h(app_url('/logout.php'));
    $css = h(app_url('/style.css'));
    $user = current_user();
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>' . h($title) . ' | Oracle Linux Value Navigator</title>';
    echo '<link rel="stylesheet" href="' . $css . '"></head><body>';
    echo '<header><div class="wrap"><a class="brand" href="' . $home . '">Oracle Linux Value Navigator</a>';
    echo '<nav class="header-actions" aria-label="Application navigation">';
    if ($user === null) {
        echo '<a class="header-link" href="' . $login . '">Login</a>';
        echo '<a class="header-link" href="' . $register . '">Register</a>';
    } else {
        echo '<a class="header-link" href="' . $help . '">Help</a>';
        echo '<span class="signed-in-user">Signed in as ' . h($user['username']) . '</span>';
        echo '<form class="header-form" method="post" action="' . $logout . '">' . csrf_field();
        echo '<button class="header-link" type="submit">Logout</button></form>';
    }
    echo '<span class="badge">Workshop prototype</span></nav></div></header><main class="wrap">';
    echo '<div class="notice warning">Use demonstration information only. This prototype is not a quote, licensing determination, or complete TCO analysis.</div>';
    foreach ($flashes as $item) {
        echo '<div class="notice ' . h($item['type']) . '">' . h($item['message']) . '</div>';
    }
    echo '<h1>' . h($title) . '</h1>';
}

/**
 * Close the application page and repeat the Version 2 baseline governance boundary.
 *
 * @return void
 */
function render_footer(): void
{
    echo '</main><footer><div class="wrap">Version 2 uses application-managed login and does not maintain master SKU catalogs.</div></footer></body></html>';
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
