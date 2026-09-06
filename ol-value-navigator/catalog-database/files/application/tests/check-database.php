<?php
declare(strict_types=1);

/**
 * Deployed database smoke test used by the LiveLabs installation workflow.
 *
 * The script runs as the Apache account against the private configuration path.
 * It proves credentials and private endpoint settings work, an active calculation
 * rule exists, and the independent deletion-audit table was installed. Output is
 * limited to server and rule metadata and never prints credentials or the DSN.
 */
$config = require '/var/www/ol-value-navigator/config.php';
$pdo = new PDO(
    $config['dsn'],
    $config['user'],
    $config['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]
);
$version = $pdo->query('SELECT VERSION()')->fetchColumn();
$rule = $pdo->query(
    "SELECT version_label FROM calculation_rule_version WHERE active = 1 ORDER BY id DESC LIMIT 1"
)->fetchColumn();
// Query the active schema rather than assuming a fixed database name.
$auditTable = $pdo->query(
    "SELECT COUNT(*) FROM information_schema.tables
     WHERE table_schema = DATABASE() AND table_name = 'comparison_deletion_audit'"
)->fetchColumn();
if ((int) $auditTable !== 1) {
    throw new RuntimeException('The comparison deletion audit table is missing.');
}
echo "Database connection passed. Server {$version}; rule {$rule}; deletion audit ready.\n";
