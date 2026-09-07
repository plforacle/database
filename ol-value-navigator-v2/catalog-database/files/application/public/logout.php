<?php
declare(strict_types=1);

/**
 * POST controller for ending an authenticated application session.
 */
require '/var/www/ol-value-navigator-2/lib/bootstrap.php';
require_login();
require_post();
verify_csrf();
logout_user();
header('Location: ' . app_url('/login.php?logged_out=1'), true, 303);
exit;
