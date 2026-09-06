<?php
declare(strict_types=1);

/*
 * Shared persistence queries for comparison records, inputs, lines, and events.
 * Centralizing these prepared statements keeps public controllers focused on
 * workflow and gives all pages consistent row shapes and not-found behavior.
 */

/**
 * Load one comparison and the label of the calculation rule it references.
 *
 * The request terminates with a user-safe HTTP 404 page when no row exists.
 *
 * @param int $id Comparison primary key.
 * @return array<string,mixed> Database row for the comparison.
 * @throws PDOException When the query fails.
 */
function find_comparison(int $id): array
{
    $statement = db()->prepare(
        'SELECT c.*, r.version_label
         FROM comparison c
         LEFT JOIN calculation_rule_version r ON r.id = c.rule_version_id
         WHERE c.id = ?'
    );
    $statement->execute([$id]);
    $comparison = $statement->fetch();
    if (!$comparison) {
        fail_page('Comparison not found', 'The requested comparison does not exist.', 404);
    }
    return $comparison;
}

/**
 * Load both original freeform inputs and index them by input side.
 *
 * @param int $comparisonId Comparison primary key.
 * @return array<string,array<string,mixed>> Rows keyed by RHEL or ORACLE_LINUX.
 * @throws PDOException When the query fails.
 */
function comparison_inputs(int $comparisonId): array
{
    $statement = db()->prepare(
        "SELECT * FROM comparison_input WHERE comparison_id = ? ORDER BY FIELD(input_side, 'RHEL', 'ORACLE_LINUX')"
    );
    $statement->execute([$comparisonId]);
    $inputs = [];
    foreach ($statement->fetchAll() as $input) {
        $inputs[$input['input_side']] = $input;
    }
    return $inputs;
}

/**
 * Load all formatted and manually entered lines in stable display order.
 *
 * Joining through comparison_input proves each line belongs to the requested
 * comparison and adds the side needed by review and calculation logic.
 *
 * @param int $comparisonId Comparison primary key.
 * @return list<array<string,mixed>> RHEL lines followed by Oracle Linux lines.
 * @throws PDOException When the query fails.
 */
function comparison_lines(int $comparisonId): array
{
    $statement = db()->prepare(
        "SELECT l.*, i.input_side
         FROM comparison_line l
         JOIN comparison_input i ON i.id = l.comparison_input_id
         WHERE i.comparison_id = ?
         ORDER BY FIELD(i.input_side, 'RHEL', 'ORACLE_LINUX'), l.line_number"
    );
    $statement->execute([$comparisonId]);
    return $statement->fetchAll();
}

/**
 * Append a workflow event to the comparison's traceable history.
 *
 * This function participates in an existing transaction when the caller has
 * started one. Structured details are encoded as JSON with exceptions enabled,
 * so malformed data cannot be silently stored.
 *
 * @param int $comparisonId Comparison that owns the event.
 * @param string $eventType Stable application event name.
 * @param string $actorType AI, REPRESENTATIVE, or APPLICATION.
 * @param string $outcome COMPLETED, CONFIRMED, REJECTED, or FAILED.
 * @param array<string,mixed>|null $details Optional nonsecret event context.
 * @return void
 * @throws JsonException When details cannot be encoded.
 * @throws PDOException When the insert fails.
 */
function record_event(
    int $comparisonId,
    string $eventType,
    string $actorType,
    string $outcome,
    ?array $details = null
): void {
    $statement = db()->prepare(
        'INSERT INTO application_event (comparison_id, event_type, actor_type, outcome, details)
         VALUES (?, ?, ?, ?, ?)'
    );
    $statement->execute([
        $comparisonId,
        $eventType,
        $actorType,
        $outcome,
        $details === null ? null : json_encode($details, JSON_THROW_ON_ERROR),
    ]);
}

/**
 * Count lines by review status for workbook status summaries.
 *
 * @param int $comparisonId Comparison primary key.
 * @return array<string,int> All four supported statuses, including zero counts.
 * @throws PDOException When the query fails.
 */
function line_counts(int $comparisonId): array
{
    $statement = db()->prepare(
        'SELECT l.review_status, COUNT(*) AS total
         FROM comparison_line l
         JOIN comparison_input i ON i.id = l.comparison_input_id
         WHERE i.comparison_id = ?
         GROUP BY l.review_status'
    );
    $statement->execute([$comparisonId]);
    $counts = ['AI_SUGGESTED' => 0, 'CONFIRMED' => 0, 'EXCLUDED' => 0, 'UNRESOLVED' => 0];
    foreach ($statement->fetchAll() as $row) {
        $counts[$row['review_status']] = (int) $row['total'];
    }
    return $counts;
}
