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
require '/var/www/ol-value-navigator/lib/bootstrap.php';
require_post();
verify_csrf();

try {
    $maximum = (int) app_config('max_input_characters', 12000);
    $context = customer_context_values($_POST);
    $name = require_length(normalize_text((string) ($_POST['name'] ?? '')), 1, 255, 'Comparison name');
    $rhel = require_length(normalize_text((string) ($_POST['rhel_text'] ?? '')), 1, $maximum, 'RHEL input');
    $oracle = require_length(normalize_text((string) ($_POST['oracle_text'] ?? '')), 1, $maximum, 'Oracle Linux input');

    // The parent record is not useful unless both original inputs are also saved.
    db()->beginTransaction();
    $ruleId = db()->query(
        "SELECT id FROM calculation_rule_version WHERE version_label = 'workshop-v1' AND active = 1"
    )->fetchColumn();
    if ($ruleId === false) {
        throw new RuntimeException('The active workshop calculation rule was not found.');
    }
    $comparison = db()->prepare('INSERT INTO comparison (name, rule_version_id, customer_name, customer_objective, comparison_scope, recommended_next_step) VALUES (?, ?, ?, ?, ?, ?)');
    $comparison->execute([$name, (int) $ruleId, ...array_values($context)]);
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
    remember_form('create', $_POST, ['name', 'rhel_text', 'oracle_text', ...array_keys(customer_context_fields())]);
    flash('error', $exception->getMessage() . ' Nothing was saved. Your entries are restored below.');
    redirect('/index.php');
} catch (Throwable $exception) {
    if (db()->inTransaction()) {
        db()->rollBack();
    }
    error_log('OLVN comparison creation failed: ' . get_class($exception));
    remember_form('create', $_POST, ['name', 'rhel_text', 'oracle_text', ...array_keys(customer_context_fields())]);
    flash('error', 'The comparison could not be saved. Your entries are restored below. Ask the workshop administrator to check the database before retrying.');
    redirect('/index.php');
}
