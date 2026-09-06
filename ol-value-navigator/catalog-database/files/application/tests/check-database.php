<?php
declare(strict_types=1);

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
$auditTable = $pdo->query(
    "SELECT COUNT(*) FROM information_schema.tables
     WHERE table_schema = DATABASE() AND table_name = 'comparison_deletion_audit'"
)->fetchColumn();
if ((int) $auditTable !== 1) {
    throw new RuntimeException('The comparison deletion audit table is missing.');
}
echo "Database connection passed. Server {$version}; rule {$rule}; deletion audit ready.\n";
