<?php
declare(strict_types=1);
/**
 * CSRF-protected Stage 5 download of the current saved calculation as PowerPoint.
 * Read metadata, lines and totals in one consistent database snapshot. A revised
 * or uncalculated comparison cannot export. Generate bytes before recording the
 * export event so failed generation never records a successful export.
 */
require '/var/www/ol-value-navigator/lib/bootstrap.php';
require_once '/var/www/ol-value-navigator/lib/presentation.php';
require_stage(5);
require_post();
verify_csrf();
$id = post_id();

try {
    db()->exec('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
    db()->beginTransaction();
    $comparison = find_comparison($id);
    $lines = comparison_lines($id);
    $statement = db()->prepare('SELECT * FROM comparison_result WHERE comparison_id = ?');
    $statement->execute([$id]);
    $result = $statement->fetch();
    if (!$result) {
        throw new DomainException('Calculate this comparison before downloading PowerPoint.');
    }
    $bytes = (new ResultsPresentation())->export($comparison, $result, $lines);
    record_event($id, 'COMPARISON_EXPORTED', 'REPRESENTATIVE', 'COMPLETED', [
        'format' => 'PPTX', 'slides' => 4, 'calculated_at' => $result['calculated_at'],
    ]);
    db()->commit();
} catch (DomainException $exception) {
    if (db()->inTransaction()) { db()->rollBack(); }
    flash('error', $exception->getMessage());
    redirect('/comparison.php?id=' . $id);
} catch (Throwable $exception) {
    if (db()->inTransaction()) { db()->rollBack(); }
    error_log('OLVN PowerPoint export failed: ' . get_class($exception));
    fail_page('PowerPoint unavailable', 'The presentation could not be created. Ask the workshop administrator to run the installation checks and try again.', 500);
}

// The filename uses only the validated numeric ID, never user-supplied text.
header('Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation');
header('Content-Disposition: attachment; filename="olvn-comparison-' . $id . '.pptx"');
header('Cache-Control: no-store');
header('Content-Length: ' . strlen($bytes));
echo $bytes;
