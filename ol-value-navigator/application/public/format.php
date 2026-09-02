<?php
declare(strict_types=1);
require '/var/www/ol-value-navigator/lib/bootstrap.php';
require_stage(4);
require_post();
verify_csrf();
$id = post_id();
find_comparison($id);
$inputs = comparison_inputs($id);

set_time_limit(180);
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

$counts = line_counts($id);
if (array_sum($counts) > 0) {
    db()->prepare("UPDATE comparison SET status = 'NEEDS_REVIEW' WHERE id = ?")->execute([$id]);
}
flash('info', implode(' ', $messages));
redirect('/review.php?id=' . $id);
