<?php
declare(strict_types=1);
require '/var/www/ol-value-navigator/lib/bootstrap.php';
require_stage(5);
require_post();
verify_csrf();
$id = post_id();
$comparison = find_comparison($id);

try {
    $totals = calculate_comparison_totals($id);
    db()->beginTransaction();
    $statement = db()->prepare(
        'INSERT INTO comparison_result (
          comparison_id, rhel_annual_total, oracle_linux_annual_total, annual_difference,
          rhel_three_year_total, oracle_linux_three_year_total, three_year_difference,
          rhel_five_year_total, oracle_linux_five_year_total, five_year_difference
         ) VALUES (:comparison_id, :rhel_annual_total, :oracle_linux_annual_total, :annual_difference,
          :rhel_three_year_total, :oracle_linux_three_year_total, :three_year_difference,
          :rhel_five_year_total, :oracle_linux_five_year_total, :five_year_difference)
         ON DUPLICATE KEY UPDATE
          rhel_annual_total = VALUES(rhel_annual_total),
          oracle_linux_annual_total = VALUES(oracle_linux_annual_total),
          annual_difference = VALUES(annual_difference),
          rhel_three_year_total = VALUES(rhel_three_year_total),
          oracle_linux_three_year_total = VALUES(oracle_linux_three_year_total),
          three_year_difference = VALUES(three_year_difference),
          rhel_five_year_total = VALUES(rhel_five_year_total),
          oracle_linux_five_year_total = VALUES(oracle_linux_five_year_total),
          five_year_difference = VALUES(five_year_difference),
          calculated_at = CURRENT_TIMESTAMP'
    );
    $statement->execute(['comparison_id' => $id] + $totals);
    db()->prepare("UPDATE comparison SET status = 'CALCULATED' WHERE id = ?")->execute([$id]);
    record_event($id, 'CALCULATION_COMPLETED', 'APPLICATION', 'COMPLETED', [
        'rule_version' => $comparison['version_label'],
    ]);
    db()->commit();
    flash('success', 'Annual, three-year, and five-year results were calculated and saved.');
    redirect('/results.php?id=' . $id);
} catch (DomainException $exception) {
    if (db()->inTransaction()) {
        db()->rollBack();
    }
    flash('error', $exception->getMessage());
    redirect('/review.php?id=' . $id);
} catch (Throwable $exception) {
    if (db()->inTransaction()) {
        db()->rollBack();
    }
    error_log('OLVN calculation failed: ' . get_class($exception));
    fail_page('Calculation failed', 'The results could not be calculated or saved. Try again.', 500);
}
