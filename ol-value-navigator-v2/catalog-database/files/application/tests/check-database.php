<?php
declare(strict_types=1);

/**
 * Deployed database smoke test used by the LiveLabs installation workflow.
 *
 * The script runs as the Apache account against the private configuration path.
 * It proves credentials and private endpoint settings work, an active calculation
 * rule exists, the authentication and ownership schema was installed, and two
 * temporary users cannot select each other's comparisons through the application's
 * owner-filter pattern. All temporary rows are rolled back. Output is limited to
 * server and rule metadata and never prints credentials or the DSN.
 */
$config = require '/var/www/ol-value-navigator-2/config.php';
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
$requiredTables = [
    'user_account',
    'comparison',
    'comparison_deletion_audit',
];
$tableCheck = $pdo->prepare(
    'SELECT COUNT(*) FROM information_schema.tables
     WHERE table_schema = DATABASE() AND table_name = ?'
);
foreach ($requiredTables as $tableName) {
    $tableCheck->execute([$tableName]);
    if ((int) $tableCheck->fetchColumn() !== 1) {
        throw new RuntimeException("The required {$tableName} table is missing.");
    }
}

$columnCheck = $pdo->prepare(
    'SELECT COUNT(*) FROM information_schema.columns
     WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?'
);
foreach ([
    ['comparison', 'owner_user_id'],
    ['comparison_deletion_audit', 'owner_user_id'],
] as [$tableName, $columnName]) {
    $columnCheck->execute([$tableName, $columnName]);
    if ((int) $columnCheck->fetchColumn() !== 1) {
        throw new RuntimeException("The required {$tableName}.{$columnName} column is missing.");
    }
}

$foreignKeyCheck = $pdo->prepare(
    "SELECT COUNT(*) FROM information_schema.key_column_usage
     WHERE table_schema = DATABASE()
       AND table_name = ?
       AND column_name = 'owner_user_id'
       AND referenced_table_name = 'user_account'
       AND referenced_column_name = 'id'"
);
foreach (['comparison', 'comparison_deletion_audit'] as $tableName) {
    $foreignKeyCheck->execute([$tableName]);
    if ((int) $foreignKeyCheck->fetchColumn() !== 1) {
        throw new RuntimeException("The required {$tableName} ownership foreign key is missing.");
    }
}

if ($rule === false) {
    throw new RuntimeException('No active calculation rule is available.');
}

try {
    $pdo->beginTransaction();
    $suffix = bin2hex(random_bytes(8));
    $createUser = $pdo->prepare(
        'INSERT INTO user_account (username, password_hash) VALUES (?, ?)'
    );
    $passwordHash = password_hash('Temporary-test-passphrase-1', PASSWORD_DEFAULT);
    if (!is_string($passwordHash) || !password_verify('Temporary-test-passphrase-1', $passwordHash)) {
        throw new RuntimeException('PHP password hashing is unavailable.');
    }
    $createUser->execute(["check_a_{$suffix}", $passwordHash]);
    $firstUserId = (int) $pdo->lastInsertId();
    $createUser->execute(["check_b_{$suffix}", $passwordHash]);
    $secondUserId = (int) $pdo->lastInsertId();

    $ruleId = (int) $pdo->query(
        'SELECT id FROM calculation_rule_version WHERE active = 1 ORDER BY id DESC LIMIT 1'
    )->fetchColumn();
    $createComparison = $pdo->prepare(
        'INSERT INTO comparison (owner_user_id, name, rule_version_id) VALUES (?, ?, ?)'
    );
    $createComparison->execute([$firstUserId, "Owner isolation A {$suffix}", $ruleId]);
    $firstComparisonId = (int) $pdo->lastInsertId();
    $createComparison->execute([$secondUserId, "Owner isolation B {$suffix}", $ruleId]);
    $secondComparisonId = (int) $pdo->lastInsertId();

    $ownerFilter = $pdo->prepare(
        'SELECT COUNT(*) FROM comparison WHERE id = ? AND owner_user_id = ?'
    );
    $ownerFilter->execute([$firstComparisonId, $firstUserId]);
    if ((int) $ownerFilter->fetchColumn() !== 1) {
        throw new RuntimeException('An owner could not select their own comparison.');
    }
    $ownerFilter->execute([$secondComparisonId, $firstUserId]);
    if ((int) $ownerFilter->fetchColumn() !== 0) {
        throw new RuntimeException('The owner filter exposed another user\'s comparison.');
    }

    $pdo->rollBack();
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    throw $exception;
}

echo "Database connection passed. Server {$version}; rule {$rule}; authentication schema and two-user ownership filter ready; temporary rows rolled back.\n";
