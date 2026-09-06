<?php
declare(strict_types=1);
/**
 * POST controller for creating an editable copy of a Stage 5 workbook.
 *
 * The transaction copies lineage, rule version, original inputs, AI suggestions,
 * representative edits, groups, decisions, and notes. It deliberately omits the
 * prior result snapshot and AI-run foreign keys because those describe processing
 * performed on the source workbook. The copy starts in NEEDS_REVIEW and must be
 * reviewed and calculated independently.
 */
require '/var/www/ol-value-navigator/lib/bootstrap.php';
require_stage(5);
require_post();
verify_csrf();
$id = post_id();
$source = find_comparison($id);
$inputs = comparison_inputs($id);

try {
    // Every copied input and line belongs to the new parent or nothing is committed.
    db()->beginTransaction();
    $copy = db()->prepare(
        "INSERT INTO comparison (source_comparison_id, name, status, rule_version_id)
         VALUES (?, ?, 'NEEDS_REVIEW', ?)"
    );
    $copy->execute([$id, $source['name'] . ' copy', $source['rule_version_id']]);
    $newId = (int) db()->lastInsertId();

    $inputInsert = db()->prepare(
        'INSERT INTO comparison_input (comparison_id, input_side, raw_text) VALUES (?, ?, ?)'
    );
    $lineInsert = db()->prepare(
        'INSERT INTO comparison_line (
          comparison_input_id, line_number, comparison_group, entry_method,
          suggested_sku, suggested_description, suggested_quantity, suggested_annual_unit_price,
          ai_confidence, ai_warnings, sku, description, quantity, annual_unit_price,
          review_status, representative_note
         ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    foreach ($inputs as $input) {
        $inputInsert->execute([$newId, $input['input_side'], $input['raw_text']]);
        $newInputId = (int) db()->lastInsertId();
        $lineQuery = db()->prepare('SELECT * FROM comparison_line WHERE comparison_input_id = ? ORDER BY line_number');
        $lineQuery->execute([(int) $input['id']]);
        foreach ($lineQuery->fetchAll() as $line) {
            // Preserve suggestion and review evidence without linking to the source AI run.
            $lineInsert->execute([
                $newInputId, $line['line_number'], $line['comparison_group'], $line['entry_method'],
                $line['suggested_sku'], $line['suggested_description'], $line['suggested_quantity'],
                $line['suggested_annual_unit_price'], $line['ai_confidence'], $line['ai_warnings'],
                $line['sku'], $line['description'], $line['quantity'], $line['annual_unit_price'],
                $line['review_status'], $line['representative_note'],
            ]);
        }
    }
    record_event($newId, 'COMPARISON_DUPLICATED', 'REPRESENTATIVE', 'COMPLETED', ['source_comparison_id' => $id]);
    db()->commit();
    flash('success', 'The comparison was duplicated without a result snapshot. Review and calculate the copy.');
    redirect('/comparison.php?id=' . $newId);
} catch (Throwable $exception) {
    if (db()->inTransaction()) {
        db()->rollBack();
    }
    error_log('OLVN duplication failed: ' . get_class($exception));
    fail_page('Duplicate failed', 'The comparison could not be duplicated.', 500);
}
