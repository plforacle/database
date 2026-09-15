<?php
declare(strict_types=1);
/**
 * Edit presentation context independently of SKU inputs and saved calculations.
 * The four-field update and audit event commit together. No source text, review
 * decision, calculation state, rule or result timestamp is changed by this route.
 */
require '/var/www/ol-value-navigator/lib/bootstrap.php';
$id = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' ? post_id() : request_id();
$comparison = find_comparison($id);
$values = $comparison;
$error = '';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    verify_csrf();
    try {
        $values = customer_context_values($_POST);
        db()->beginTransaction();
        $lock = db()->prepare('SELECT id FROM comparison WHERE id = ? FOR UPDATE');
        $lock->execute([$id]);
        if ($lock->fetchColumn() === false) {
            throw new RuntimeException('Comparison no longer exists.');
        }
        $update = db()->prepare('UPDATE comparison SET customer_name = ?, customer_objective = ?, comparison_scope = ?, recommended_next_step = ? WHERE id = ?');
        $update->execute([...array_values($values), $id]);
        record_event($id, 'CUSTOMER_CONTEXT_UPDATED', 'REPRESENTATIVE', 'COMPLETED');
        db()->commit();
        flash('success', 'Customer details saved. Reviewed lines and calculated results are unchanged.');
        redirect('/comparison.php?id=' . $id);
    } catch (InvalidArgumentException $exception) {
        $error = $exception->getMessage();
        // Retain only scalar input for the escaped correction form.
        foreach (customer_context_fields() as $key => $_) {
            $values[$key] = is_string($_POST[$key] ?? null) ? $_POST[$key] : '';
        }
        http_response_code(422);
    } catch (Throwable $exception) {
        if (db()->inTransaction()) { db()->rollBack(); }
        error_log('OLVN context update failed: ' . get_class($exception));
        fail_page('Customer details not saved', 'No changes were saved. Reopen the comparison and try again.', 500);
    }
}
render_header('Edit customer details: ' . $comparison['name']);
?>
<p>Use demonstration information only. These optional details appear in the application, CSV workbook and PowerPoint. They do not change subscription-cost calculations or get sent to GenAI.</p>
<?php if ($error !== ''): ?><div class="notice warning" role="alert"><?= h($error) ?></div><?php endif; ?>
<form method="post" action="<?= h(app_url('/context.php')) ?>" class="card">
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= $id ?>">
  <?php render_customer_context_fields($values); ?>
  <div class="actions">
    <button type="submit">Save customer details</button>
    <a class="button secondary" href="<?= h(app_url('/comparison.php?id=' . $id)) ?>">Cancel</a>
  </div>
</form>
<?php render_footer(); ?>
