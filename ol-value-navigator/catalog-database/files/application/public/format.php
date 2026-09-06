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
$inputs = comparison_inputs($id);

// Two remote model calls can take longer than a normal interactive PHP request.
set_time_limit(180);
// Reformatting changes the workbook inputs to calculation, invalidating old results.
db()->prepare('DELETE FROM comparison_result WHERE comparison_id = ?')->execute([$id]);
$messages = [];
foreach (['RHEL', 'ORACLE_LINUX'] as $side) {
    if (!isset($inputs[$side])) {
        $messages[] = "{$side}: original input is missing.";
        continue;
    }
    try {
        $count = format_input_with_genai($inputs[$side]);
        $messages[] = "{$side}: {$count} suggestion(s) created.";
    } catch (RuntimeException $exception) {
        $messages[] = "{$side}: {$exception->getMessage()}";
    }
}

// A partial success is still actionable because manual entry can replace a failed side.
$counts = line_counts($id);
if (array_sum($counts) > 0) {
    db()->prepare("UPDATE comparison SET status = 'NEEDS_REVIEW' WHERE id = ?")->execute([$id]);
}
flash('info', implode(' ', $messages));
redirect('/review.php?id=' . $id);
