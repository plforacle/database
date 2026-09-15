<?php
declare(strict_types=1);
/**
 * POST controller for formatting both original inputs with MySQL HeatWave GenAI.
 *
 * Stage, method, CSRF, and comparison checks protect the operation. RHEL and
 * Oracle Linux are processed independently so a successful side remains available
 * if the other call fails. All stored model output has already passed the strict
 * GenAI contract, but it remains a suggestion that requires representative review.
 */
require '/var/www/ol-value-navigator/lib/bootstrap.php';
require_stage(4);
require_post();
verify_csrf();
$id = post_id();
find_comparison($id);
try {
    flash('info', implode(' ', format_comparison_inputs($id)));
} catch (Throwable $exception) {
    error_log('OLVN formatting failed: ' . get_class($exception));
    flash('error', 'Formatting could not finish. Your original inputs are saved. Review the available lines or use Manual fallback.');
}
redirect('/review.php?id=' . $id);
