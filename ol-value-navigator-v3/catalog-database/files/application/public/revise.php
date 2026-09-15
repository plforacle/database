<?php
declare(strict_types=1);
/**
 * GET and POST controller for revising Stage 5 comparison source data.
 *
 * GET renders the saved name and original inputs. POST validates the CSRF token,
 * normalizes and bounds all text, then resets the workbook to DRAFT. Because AI
 * suggestions and calculations were derived from the former inputs, the same
 * transaction removes result snapshots, lines, and formatting runs while retaining
 * the comparison's workflow event history and appending an INPUTS_REVISED event.
 */
require '/var/www/ol-value-navigator/lib/bootstrap.php';
require_stage(5);
$id = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' ? post_id() : request_id();
$comparison = find_comparison($id);
$inputs = comparison_inputs($id);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    verify_csrf();
    try {
        $maximum = (int) app_config('max_input_characters', 12000);
        $name = require_length(normalize_text((string) ($_POST['name'] ?? '')), 1, 255, 'Comparison name');
        $rhel = require_length(normalize_text((string) ($_POST['rhel_text'] ?? '')), 1, $maximum, 'RHEL input');
        $oracle = require_length(normalize_text((string) ($_POST['oracle_text'] ?? '')), 1, $maximum, 'Oracle Linux input');
        // Source replacement and removal of every stale derivative form one state change.
        db()->beginTransaction();
        db()->prepare("UPDATE comparison SET name = ?, status = 'DRAFT' WHERE id = ?")->execute([$name, $id]);
        $update = db()->prepare('UPDATE comparison_input SET raw_text = ? WHERE comparison_id = ? AND input_side = ?');
        $update->execute([$rhel, $id, 'RHEL']);
        $update->execute([$oracle, $id, 'ORACLE_LINUX']);
        db()->prepare('DELETE FROM comparison_result WHERE comparison_id = ?')->execute([$id]);
        // Delete lines before their referenced formatting runs to satisfy the foreign key.
        db()->prepare(
            'DELETE l FROM comparison_line l JOIN comparison_input i ON i.id = l.comparison_input_id WHERE i.comparison_id = ?'
        )->execute([$id]);
        db()->prepare(
            'DELETE a FROM ai_formatting_run a JOIN comparison_input i ON i.id = a.comparison_input_id WHERE i.comparison_id = ?'
        )->execute([$id]);
        record_event($id, 'INPUTS_REVISED', 'REPRESENTATIVE', 'COMPLETED');
        db()->commit();
        flash('success', 'The original inputs were revised. Previous lines and results were cleared so the new text can be reviewed.');
        redirect('/comparison.php?id=' . $id);
    } catch (InvalidArgumentException $exception) {
        if (db()->inTransaction()) {
            db()->rollBack();
        }
        remember_form('revise:' . $id, $_POST, ['name', 'rhel_text', 'oracle_text']);
        flash('error', $exception->getMessage() . ' Nothing was saved. Your entries are restored below.');
        redirect('/revise.php?id=' . $id);
    } catch (Throwable $exception) {
        if (db()->inTransaction()) {
            db()->rollBack();
        }
        error_log('OLVN revision failed: ' . get_class($exception));
        remember_form('revise:' . $id, $_POST, ['name', 'rhel_text', 'oracle_text']);
        flash('error', 'The comparison could not be revised. Nothing was saved. Your entries are restored below.');
        redirect('/revise.php?id=' . $id);
    }
}

$draft = take_form('revise:' . $id);
$comparison['name'] = form_value($draft, 'name', (string) $comparison['name']);
$inputs['RHEL']['raw_text'] = form_value($draft, 'rhel_text', (string) ($inputs['RHEL']['raw_text'] ?? ''));
$inputs['ORACLE_LINUX']['raw_text'] = form_value($draft, 'oracle_text', (string) ($inputs['ORACLE_LINUX']['raw_text'] ?? ''));
render_header('Revise original inputs');
?>
<div class="notice warning">Saving revised source text clears the current formatted lines and calculated result. The workflow event history remains.</div>
<form method="post" action="<?= h(app_url('/revise.php')) ?>" class="card">
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= $id ?>">
  <label for="name">Comparison name</label><input id="name" name="name" maxlength="255" required value="<?= h($comparison['name']) ?>">
  <div class="two-column">
    <div><label for="rhel_text">RHEL SKU information</label><textarea id="rhel_text" name="rhel_text" required><?= h($inputs['RHEL']['raw_text'] ?? '') ?></textarea></div>
    <div><label for="oracle_text">Oracle Linux SKU information</label><textarea id="oracle_text" name="oracle_text" required><?= h($inputs['ORACLE_LINUX']['raw_text'] ?? '') ?></textarea></div>
  </div>
  <button type="submit">Save revision and clear derived data</button>
  <a class="button secondary" href="<?= h(app_url('/comparison.php?id=' . $id)) ?>">Cancel</a>
</form>
<?php render_footer(); ?>
