<?php
declare(strict_types=1);
/** Read-only deployment gate. Never print database credentials or exception text. */
try {
    $config = require '/var/www/ol-value-navigator/config.php';
    $connection = new PDO($config['dsn'], $config['user'], $config['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $connection->query('SELECT customer_name, customer_objective, comparison_scope, recommended_next_step FROM comparison LIMIT 0');
    echo "Customer-context schema check passed.\n";
} catch (Throwable $exception) {
    fwrite(STDERR, "Customer-context schema check failed. Verify the private database configuration and complete Lab 5 Task 2 database upgrade before deploying. No application files were installed.\n");
    exit(1);
}
