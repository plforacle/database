<?php
declare(strict_types=1);
/**
 * POST controller for deleting one comparison and all associated workbook data.
 *
 * Stage, method, CSRF, and identifier checks run before the saved name is compared
 * with the representative's exact confirmation. The deletion library records a
 * minimal independent audit and deletes the parent in one transaction; schema
 * cascades remove its inputs, lines, AI runs, results, and normal event history.
 */
require '/var/www/ol-value-navigator/lib/bootstrap.php';
require_stage(5);
require_post();
verify_csrf();

$id = post_id();
$comparison = find_comparison($id);
$confirmation = (string) ($_POST['confirmation_name'] ?? '');

if (!comparison_name_matches((string) $comparison['name'], $confirmation)) {
    flash('error', 'The comparison name did not match. Nothing was deleted.');
    redirect('/comparison.php?id=' . $id);
}

try {
    delete_comparison_and_record_audit(db(), $id, (string) $comparison['name']);
    flash('success', 'The comparison and its associated data were deleted. A minimal deletion audit record was retained.');
    redirect('/index.php');
} catch (Throwable $exception) {
    error_log('OLVN comparison deletion failed: ' . get_class($exception));
    fail_page('Comparison not deleted', 'The comparison could not be deleted. Verify the database schema and permissions, then try again.', 500);
}
