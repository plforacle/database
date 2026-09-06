<?php
declare(strict_types=1);

function comparison_name_matches(string $expectedName, string $submittedName): bool
{
    return $submittedName !== '' && hash_equals($expectedName, $submittedName);
}

function delete_comparison_and_record_audit(
    PDO $pdo,
    int $comparisonId,
    string $comparisonName
): void {
    $pdo->beginTransaction();
    try {
        $audit = $pdo->prepare(
            'INSERT INTO comparison_deletion_audit
             (deleted_comparison_id, comparison_name)
             VALUES (?, ?)'
        );
        $audit->execute([$comparisonId, $comparisonName]);

        $delete = $pdo->prepare('DELETE FROM comparison WHERE id = ?');
        $delete->execute([$comparisonId]);
        if ($delete->rowCount() !== 1) {
            throw new RuntimeException('The comparison was not deleted.');
        }

        $pdo->commit();
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $exception;
    }
}
