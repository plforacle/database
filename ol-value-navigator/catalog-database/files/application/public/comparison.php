<?php
declare(strict_types=1);
/**
 * GET controller and view for one saved comparison workbook.
 *
 * The page renders escaped source inputs, current line counts, and only the
 * actions enabled by the deployed workshop stage. State-changing actions use
 * POST forms with CSRF tokens. Stage 5 also exposes revise, duplicate, export,
 * and exact-name-confirmed deletion controls.
 */
require '/var/www/ol-value-navigator/lib/bootstrap.php';
$id = request_id();
$comparison = find_comparison($id);
$inputs = comparison_inputs($id);
$counts = line_counts($id);

render_header('Comparison: ' . $comparison['name']);
?>
<div class="actions">
  <a class="button secondary" href="<?= h(app_url('/index.php')) ?>">Home</a>
  <?php if (app_stage() >= 4): ?>
    <a class="button <?= $comparison['status'] === 'CALCULATED' || array_sum($counts) === 0 ? 'secondary' : '' ?>" href="<?= h(app_url('/review.php?id=' . $id)) ?>">Review lines</a>
  <?php endif; ?>
  <?php if (app_stage() >= 5 && $comparison['status'] === 'CALCULATED'): ?>
    <a class="button" href="<?= h(app_url('/results.php?id=' . $id)) ?>">View results</a>
  <?php endif; ?>
</div>

<?php if (app_stage() >= 5): ?>
<details class="card"><summary>Saved inputs, customer details, and status</summary>
<?php endif; ?>
<section class="card">
  <h2>Workbook status</h2>
  <dl class="summary">
    <div><dt>Status</dt><dd><?= h($comparison['status']) ?></dd></div>
    <div><dt>Rule version</dt><dd><?= h($comparison['version_label'] ?? 'Not assigned') ?></dd></div>
    <div><dt>Confirmed</dt><dd><?= $counts['CONFIRMED'] ?></dd></div>
    <div><dt>Needs action</dt><dd><?= $counts['AI_SUGGESTED'] + $counts['UNRESOLVED'] ?></dd></div>
  </dl>
</section>

<?php render_customer_context($comparison); ?>
<section class="two-column">
  <?php foreach (['RHEL' => 'RHEL original input', 'ORACLE_LINUX' => 'Oracle Linux original input'] as $side => $label): ?>
    <article class="card">
      <h2><?= h($label) ?></h2>
      <pre class="source-text"><?= h($inputs[$side]['raw_text'] ?? 'Input not found') ?></pre>
    </article>
  <?php endforeach; ?>
</section>

<?php if (app_stage() >= 5): ?></details><?php endif; ?>

<?php if (app_stage() === 3): ?>
  <div class="notice info">The original inputs are saved. Lab 4 enables MySQL HeatWave GenAI formatting and representative review.</div>
<?php else: ?>
  <?php if (app_stage() >= 5 && array_sum($counts) > 0): ?>
  <details class="card"><summary>Replace AI suggestions</summary>
  <?php endif; ?>
  <form method="post" action="<?= h(app_url('/format.php')) ?>" class="card">
    <?= csrf_field() ?><input type="hidden" name="id" value="<?= $id ?>">
    <h2><?= array_sum($counts) === 0 ? 'Format both inputs' : 'Format both inputs again' ?></h2>
    <p>MySQL HeatWave GenAI will create suggestions. You must review every line before calculation. Formatting again replaces existing reviewed lines and clears the saved result.</p>
    <button type="submit"><?= array_sum($counts) === 0 ? 'Format with GenAI' : 'Replace lines with new suggestions' ?></button>
  </form>
  <?php if (app_stage() >= 5 && array_sum($counts) > 0): ?></details><?php endif; ?>
<?php endif; ?>

<?php if (app_stage() >= 5): ?>
  <details class="card"><summary>More actions</summary>
  <section class="card">
    <h2>Workbook actions</h2>
    <div class="actions">
      <a class="button secondary" href="<?= h(app_url('/revise.php?id=' . $id)) ?>">Revise original inputs</a>
      <form method="post" action="<?= h(app_url('/duplicate.php')) ?>">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= $id ?>">
        <button class="secondary" type="submit">Duplicate comparison</button>
      </form>
      <form method="post" action="<?= h(app_url('/export.php')) ?>">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= $id ?>">
        <button class="secondary" type="submit">Download CSV</button>
      </form>
    </div>
  </section>

  <section class="card danger-zone">
    <h2>Delete comparison</h2>
    <p>This permanently deletes the comparison and all associated inputs, formatted lines, results, and workflow events. A minimal audit record containing the comparison ID, name, and deletion time is retained.</p>
    <form method="post" action="<?= h(app_url('/delete.php')) ?>">
      <?= csrf_field() ?><input type="hidden" name="id" value="<?= $id ?>">
      <label for="confirmation-name">Type <strong><?= h($comparison['name']) ?></strong> to confirm</label>
      <input id="confirmation-name" name="confirmation_name" type="text" required autocomplete="off" spellcheck="false">
      <button class="danger" type="submit">Delete comparison and associated data</button>
    </form>
  </section>
  </details>
<?php endif; ?>
<?php render_footer(); ?>
