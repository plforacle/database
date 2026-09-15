<?php
declare(strict_types=1);
/**
 * POST controller for adding one manual fallback line during Stage 4 or later.
 *
 * The route enforces the request method, CSRF token, comparison identifier, and
 * input side before inserting an unresolved line. Any prior result becomes stale,
 * so it is removed and the workbook returns to NEEDS_REVIEW. The representative
 * completes the new fields on the review page.
 */
require '/var/www/ol-value-navigator/lib/bootstrap.php';
require_stage(4);
require_post();
verify_csrf();
$id = post_id();
find_comparison($id);
$side = (string) ($_POST['side'] ?? '');
if (!in_array($side, ['RHEL', 'ORACLE_LINUX'], true)) {
    fail_page('Manual line not added', 'Select a valid input side.');
}
$inputs = comparison_inputs($id);
if (!isset($inputs[$side])) {
    fail_page('Manual line not added', 'The selected original input does not exist.');
}
// Number lines within one input so AI and manual entries have stable display order.
$statement = db()->prepare(
    'SELECT COALESCE(MAX(line_number), 0) + 1 FROM comparison_line WHERE comparison_input_id = ?'
);
$statement->execute([(int) $inputs[$side]['id']]);
$lineNumber = (int) $statement->fetchColumn();
$insert = db()->prepare(
    "INSERT INTO comparison_line
      (comparison_input_id, line_number, entry_method, review_status, representative_note)
     VALUES (?, ?, 'MANUAL', 'UNRESOLVED', 'Complete and review this manually entered line')"
);
$insert->execute([(int) $inputs[$side]['id'], $lineNumber]);
// A new cost line invalidates any previously calculated result snapshot.
db()->prepare('DELETE FROM comparison_result WHERE comparison_id = ?')->execute([$id]);
db()->prepare("UPDATE comparison SET status = 'NEEDS_REVIEW' WHERE id = ?")->execute([$id]);
record_event($id, 'MANUAL_LINE_ADDED', 'REPRESENTATIVE', 'COMPLETED', ['input_side' => $side]);
flash('success', 'A manual line was added. Complete its fields and save the review.');
redirect('/review.php?id=' . $id);
