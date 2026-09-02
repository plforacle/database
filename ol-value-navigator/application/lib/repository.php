<?php
declare(strict_types=1);

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
