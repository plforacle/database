<?php
declare(strict_types=1);
/**
 * POST controller for creating a comparison from two complete freeform inputs.
 *
 * Server-side normalization and length checks complement browser validation. The
 * new comparison, its active calculation-rule reference, both source inputs, and
 * creation event are committed together. The original text is preserved exactly
 * after newline normalization so later AI suggestions remain traceable.
 */
require '/var/www/ol-value-navigator-2/lib/bootstrap.php';
require_login();
require_post();
verify_csrf();
$ownerId = current_user_id();

try {
    $maximum = (int) app_config('max_input_characters', 12000);
    $name = require_length(normalize_text((string) ($_POST['name'] ?? '')), 1, 255, 'Comparison name');
    $rhel = require_length(normalize_text((string) ($_POST['rhel_text'] ?? '')), 1, $maximum, 'RHEL input');
    $oracle = require_length(normalize_text((string) ($_POST['oracle_text'] ?? '')), 1, $maximum, 'Oracle Linux input');

    // The parent record is not useful unless both original inputs are also saved.
    db()->beginTransaction();
    $ruleId = db()->query(
        "SELECT id FROM calculation_rule_version WHERE version_label = 'workshop-v2' AND active = 1"
    )->fetchColumn();
    if ($ruleId === false) {
        throw new RuntimeException('The active workshop calculation rule was not found.');
    }
    $comparison = db()->prepare(
        'INSERT INTO comparison (owner_user_id, name, rule_version_id) VALUES (?, ?, ?)'
    );
    $comparison->execute([$ownerId, $name, (int) $ruleId]);
    $comparisonId = (int) db()->lastInsertId();

    $input = db()->prepare(
        'INSERT INTO comparison_input (comparison_id, input_side, raw_text) VALUES (?, ?, ?)'
    );
    $input->execute([$comparisonId, 'RHEL', $rhel]);
    $input->execute([$comparisonId, 'ORACLE_LINUX', $oracle]);
    record_event($comparisonId, 'COMPARISON_CREATED', 'REPRESENTATIVE', 'COMPLETED');
    db()->commit();

    flash('success', 'The complete original inputs were saved.');
    redirect('/comparison.php?id=' . $comparisonId);
} catch (InvalidArgumentException $exception) {
    if (db()->inTransaction()) {
        db()->rollBack();
    }
    fail_page('Input validation failed', $exception->getMessage());
} catch (Throwable $exception) {
    if (db()->inTransaction()) {
        db()->rollBack();
    }
    error_log('OLVN comparison creation failed: ' . get_class($exception));
    fail_page('Comparison not saved', 'The comparison could not be saved. Verify the application database and try again.', 500);
}
