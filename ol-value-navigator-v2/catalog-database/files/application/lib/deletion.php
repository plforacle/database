<?php
declare(strict_types=1);

/*
 * Destructive-workflow helpers shared by the deletion route and unit tests.
 * Confirmation matching stays independent from PDO so its exact behavior can be
 * tested without a database; persistence is isolated in one transactional helper.
 */

/**
 * Compare a submitted deletion confirmation with the saved comparison name.
 *
 * Exact, case-sensitive matching makes the representative deliberately type the
 * name of the record being removed. hash_equals avoids a normal early-exit string
 * comparison and the empty-string check prevents a blank name from confirming.
 *
 * @param string $expectedName Name loaded from the database.
 * @param string $submittedName Name entered in the confirmation form.
 * @return bool True only when the nonempty values match exactly.
 */
function comparison_name_matches(string $expectedName, string $submittedName): bool
{
    return $submittedName !== '' && hash_equals($expectedName, $submittedName);
}

/**
 * Permanently delete one comparison while retaining a minimal audit record.
 *
 * The audit insert and parent deletion share one transaction. Deleting the
 * comparison invokes the schema's cascading foreign keys for inputs, lines, AI
 * runs, result snapshots, and application events. The independent audit table
 * intentionally has no foreign key to the deleted parent.
 *
 * @param PDO $pdo Active database connection.
 * @param int $comparisonId Comparison primary key being removed.
 * @param int $ownerUserId Authenticated owner account primary key.
 * @param string $comparisonName Name retained in the deletion audit.
 * @return void
 * @throws Throwable When either the audit insert or deletion fails. The
 *     transaction is rolled back before the original exception is rethrown.
 */
function delete_comparison_and_record_audit(
    PDO $pdo,
    int $comparisonId,
    int $ownerUserId,
    string $comparisonName
): void {
    // Audit insertion and cascading deletion succeed or fail as one database transaction.
    $pdo->beginTransaction();
    try {
        // Write the durable evidence before the parent row and its dependents disappear.
        $audit = $pdo->prepare(
            'INSERT INTO comparison_deletion_audit
             (deleted_comparison_id, owner_user_id, comparison_name)
             VALUES (?, ?, ?)'
        );
        $audit->execute([$comparisonId, $ownerUserId, $comparisonName]);

        // The affected-row check detects stale identifiers and ownership changes.
        $delete = $pdo->prepare('DELETE FROM comparison WHERE id = ? AND owner_user_id = ?');
        $delete->execute([$comparisonId, $ownerUserId]);
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
