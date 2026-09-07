<?php
declare(strict_types=1);

/**
 * GET and POST controller for creating an application-managed user account.
 *
 * PHP hashes accepted passwords with PASSWORD_DEFAULT before persistence. A
 * successful registration immediately starts a fresh authenticated session.
 */
require '/var/www/ol-value-navigator-2/lib/bootstrap.php';

if (current_user() !== null) {
    redirect('/index.php');
}

$error = null;
$username = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    verify_csrf();
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirmation = (string) ($_POST['password_confirmation'] ?? '');

    try {
        $username = validate_username($username);
        $password = validate_new_password($password);
        if (!hash_equals($password, $confirmation)) {
            throw new InvalidArgumentException('Password confirmation does not match.');
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        if (!is_string($passwordHash)) {
            throw new RuntimeException('PHP could not create a password hash.');
        }

        $userId = create_user_account($username, $passwordHash);
        record_successful_login($userId);
        authenticate_user($userId);
        redirect('/index.php');
    } catch (InvalidArgumentException $exception) {
        $error = $exception->getMessage();
    } catch (PDOException $exception) {
        if ((int) ($exception->errorInfo[1] ?? 0) === 1062) {
            $error = 'That username is unavailable.';
        } else {
            error_log('OLVN registration database failure: ' . get_class($exception));
            $error = 'The account could not be created. Try again.';
        }
    } catch (Throwable $exception) {
        error_log('OLVN registration failure: ' . get_class($exception));
        $error = 'The account could not be created. Try again.';
    }
}

render_header('Register');
if ($error !== null): ?>
  <div class="notice error"><?= h($error) ?></div>
<?php endif; ?>
<section class="card authentication-card">
  <h2>Create an account</h2>
  <form method="post" action="<?= h(app_url('/register.php')) ?>">
    <?= csrf_field() ?>
    <label for="username">Username</label>
    <input id="username" name="username" value="<?= h($username) ?>" minlength="3" maxlength="64" pattern="[A-Za-z][A-Za-z0-9._-]{2,63}" autocomplete="username" required autofocus>
    <small>Use 3 to 64 characters. Start with a letter, then use letters, numbers, periods, underscores, or hyphens.</small>
    <label for="password">Password</label>
    <input id="password" name="password" type="password" minlength="12" maxlength="128" autocomplete="new-password" required>
    <small>Use 12 to 128 characters. A longer passphrase is recommended.</small>
    <label for="password-confirmation">Confirm password</label>
    <input id="password-confirmation" name="password_confirmation" type="password" minlength="12" maxlength="128" autocomplete="new-password" required>
    <button type="submit">Register</button>
  </form>
  <p>Already registered? <a href="<?= h(app_url('/login.php')) ?>">Login</a>.</p>
</section>
<?php render_footer(); ?>
