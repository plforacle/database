<?php
declare(strict_types=1);
/**
 * Shared orchestration for the complete application and the staged lab routes.
 *
 * These helpers reuse the existing GenAI contract and money calculations. They
 * do not change the schema or infer representative decisions. Controllers must
 * check POST, CSRF and workshop stage before invoking a state-changing helper.
 */

/** Choose a read-only landing route without formatting or calculating on GET. */
function comparison_landing_path(array $comparison, int $stage): string
{
    $id = (int) $comparison['id'];
    if ($stage >= 5) {
        if ($comparison['status'] === 'CALCULATED') {
            return '/results.php?id=' . $id;
        }
        if ((int) ($comparison['line_count'] ?? 0) > 0) {
            return '/review.php?id=' . $id;
        }
    }
    return '/comparison.php?id=' . $id;
}

/**
 * Format each saved input independently, retaining a successful side on failure.
 * Original inputs must already be committed so a slow/failed model call cannot
 * undo creation. Reformatting intentionally invalidates the previous result.
 * @return list<string> Safe per-side completion messages for the review page.
 */
function format_comparison_inputs(int $id): array
{
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
    if (array_sum(line_counts($id)) > 0) {
        db()->prepare("UPDATE comparison SET status = 'NEEDS_REVIEW' WHERE id = ?")->execute([$id]);
    }
    return $messages;
}

/**
 * Persist one result using the unchanged fixed-point calculation and validators.
 * The caller commits review edits first. A blocked or failed calculation does
 * not discard that saved review. Result, status and event commit atomically.
 * @throws DomainException When a decision or comparison group is incomplete.
 * @throws Throwable When result persistence fails (transaction is rolled back).
 */
function save_calculated_results(int $id): void
{
    $comparison = find_comparison($id);
    $totals = calculate_comparison_totals($id);
    db()->beginTransaction();
    try {
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
    } catch (Throwable $exception) {
        if (db()->inTransaction()) { db()->rollBack(); }
        throw $exception;
    }
}
