<?php
declare(strict_types=1);

/**
 * GET and POST controller for application-managed username and password login.
 *
 * Failure responses do not distinguish missing, disabled, or bad-password
 * accounts. Successful authentication rotates the session identifier before any
 * authenticated state is stored.
 */
require '/var/www/ol-value-navigator-2/lib/bootstrap.php';

if (current_user() !== null) {
    redirect('/index.php');
}

$error = null;
$username = '';
$information = null;

if (isset($_GET['logged_out'])) {
    $information = 'You have been logged out.';
} elseif (!empty($_SESSION['authentication_expired'])) {
    unset($_SESSION['authentication_expired']);
    $information = 'Your session expired. Sign in again to continue.';
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    verify_csrf();
    $remaining = login_delay_remaining();
    if ($remaining > 0) {
        $error = "Too many unsuccessful attempts. Try again in {$remaining} seconds.";
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        try {
            $normalizedUsername = validate_username($username);
        } catch (InvalidArgumentException) {
            $normalizedUsername = '';
        }

        $user = $normalizedUsername === '' ? null : find_user_for_login($normalizedUsername);
        $hash = $user === null
            ? password_hash('not-a-valid-workshop-password', PASSWORD_DEFAULT)
            : (string) $user['password_hash'];
        $passwordMatches = is_string($hash) && strlen($password) <= 128 && password_verify($password, $hash);

        if ($user === null || (int) $user['active'] !== 1 || !$passwordMatches) {
            record_failed_login();
            $error = 'The username or password is incorrect.';
        } else {
            try {
                if (password_needs_rehash((string) $user['password_hash'], PASSWORD_DEFAULT)) {
                    $replacementHash = password_hash($password, PASSWORD_DEFAULT);
                    if (!is_string($replacementHash)) {
                        throw new RuntimeException('PHP could not refresh the password hash.');
                    }
                    update_user_password_hash((int) $user['id'], $replacementHash);
                }
                record_successful_login((int) $user['id']);
                authenticate_user((int) $user['id']);
                redirect('/index.php');
            } catch (Throwable $exception) {
                error_log('OLVN login finalization failed: ' . get_class($exception));
                $error = 'Login could not be completed. Try again.';
            }
        }
    }
}

render_header('Login');
if ($information !== null): ?>
  <div class="notice info"><?= h($information) ?></div>
<?php endif; ?>
<?php if ($error !== null): ?>
  <div class="notice error"><?= h($error) ?></div>
<?php endif; ?>
<section class="card authentication-card">
  <h2>Sign in</h2>
  <form method="post" action="<?= h(app_url('/login.php')) ?>">
    <?= csrf_field() ?>
    <label for="username">Username</label>
    <input id="username" name="username" value="<?= h($username) ?>" maxlength="64" autocomplete="username" required autofocus>
    <label for="password">Password</label>
    <input id="password" name="password" type="password" maxlength="128" autocomplete="current-password" required>
    <button type="submit">Login</button>
  </form>
  <p>Need an account? <a href="<?= h(app_url('/register.php')) ?>">Register</a>.</p>
</section>
<?php render_footer(); ?>
