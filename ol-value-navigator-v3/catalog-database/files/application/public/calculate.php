<?php
declare(strict_types=1);
/**
 * Compatibility POST endpoint for calculating already saved review decisions.
 * The complete application's review form instead saves its current fields and
 * calls the same helper. No GET request performs a calculation or changes data.
 */
require '/var/www/ol-value-navigator/lib/bootstrap.php';
require_stage(5);
require_post();
verify_csrf();
$id = post_id();
find_comparison($id);
try {
    save_calculated_results($id);
    flash('success', 'Annual, three-year, and five-year results were calculated and saved.');
    redirect('/results.php?id=' . $id);
} catch (DomainException $exception) {
    flash('error', $exception->getMessage());
    redirect('/review.php?id=' . $id);
} catch (Throwable $exception) {
    error_log('OLVN calculation failed: ' . get_class($exception));
    flash('error', 'The results could not be calculated or saved. Review the lines and try again.');
    redirect('/review.php?id=' . $id);
}
