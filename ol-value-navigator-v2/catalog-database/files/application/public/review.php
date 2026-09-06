<?php
declare(strict_types=1);
/**
 * GET controller and view for Stage 4 representative review and alignment.
 *
 * Original source text and immutable AI suggestions remain visible beside the
 * editable reviewed values. Each line must receive a decision, and confirmed
 * lines use positive group numbers to record representative-selected alignment.
 * Grouping supports cost comparison and does not claim product equivalence.
 * Manual fallback forms remain available when formatting fails or misses an item.
 */
require '/var/www/ol-value-navigator/lib/bootstrap.php';
require_stage(4);
$id = request_id();
$comparison = find_comparison($id);
$inputs = comparison_inputs($id);
$lines = comparison_lines($id);
$bySide = ['RHEL' => [], 'ORACLE_LINUX' => []];
foreach ($lines as $line) {
    $bySide[$line['input_side']][] = $line;
}

render_header('Review and align: ' . $comparison['name']);
?>
<div class="actions">
  <a class="button secondary" href="<?= h(app_url('/comparison.php?id=' . $id)) ?>">Comparison</a>
  <?php if (app_stage() >= 5): ?>
    <form method="post" action="<?= h(app_url('/calculate.php')) ?>">
      <?= csrf_field() ?><input type="hidden" name="id" value="<?= $id ?>">
      <button type="submit">Calculate confirmed results</button>
    </form>
  <?php endif; ?>
</div>

<div class="notice info">AI values are suggestions. Correct them, assign matching RHEL and Oracle Linux lines to the same positive group number, then confirm or exclude every line.</div>

<?php if ($lines === []): ?>
  <section class="card">
    <p>No formatted lines exist. Run GenAI formatting from the comparison page or add manual lines below.</p>
  </section>
<?php else: ?>
<form method="post" action="<?= h(app_url('/save-review.php')) ?>">
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= $id ?>">
  <?php foreach (['RHEL' => 'RHEL', 'ORACLE_LINUX' => 'Oracle Linux'] as $side => $label): ?>
    <section class="card">
      <h2><?= h($label) ?> lines</h2>
      <details><summary>Show complete original input</summary><pre class="source-text"><?= h($inputs[$side]['raw_text'] ?? '') ?></pre></details>
      <?php foreach ($bySide[$side] as $line): ?>
        <article class="line-card">
          <input type="hidden" name="lines[<?= (int) $line['id'] ?>][line_id]" value="<?= (int) $line['id'] ?>">
          <div><strong>Line <?= (int) $line['line_number'] ?></strong> <span class="status"><?= h($line['entry_method']) ?></span></div>
          <?php if ($line['entry_method'] === 'AI'): ?>
            <p class="suggestion">Original AI suggestion: SKU <?= h($line['suggested_sku'] ?? 'null') ?>, description <?= h($line['suggested_description'] ?? 'null') ?>, quantity <?= h($line['suggested_quantity'] ?? 'null') ?>, annual unit price <?= h($line['suggested_annual_unit_price'] ?? 'null') ?>, confidence <?= h($line['ai_confidence'] ?? 'unknown') ?></p>
          <?php endif; ?>
          <div class="line-grid">
            <div><label>SKU</label><input name="lines[<?= (int) $line['id'] ?>][sku]" maxlength="128" value="<?= h($line['sku']) ?>"></div>
            <div><label>Description</label><input name="lines[<?= (int) $line['id'] ?>][description]" maxlength="500" value="<?= h($line['description']) ?>"></div>
            <div><label>Quantity</label><input name="lines[<?= (int) $line['id'] ?>][quantity]" inputmode="decimal" value="<?= h($line['quantity']) ?>"></div>
            <div><label>Annual unit price</label><input name="lines[<?= (int) $line['id'] ?>][price]" inputmode="decimal" value="<?= h($line['annual_unit_price']) ?>"></div>
            <div><label>Group</label><input name="lines[<?= (int) $line['id'] ?>][group]" type="number" min="1" value="<?= h($line['comparison_group']) ?>"></div>
            <div><label>Decision</label><select name="lines[<?= (int) $line['id'] ?>][status]">
              <?php foreach (['AI_SUGGESTED' => 'Needs review', 'CONFIRMED' => 'Confirmed', 'EXCLUDED' => 'Excluded', 'UNRESOLVED' => 'Unresolved'] as $value => $text): ?>
                <option value="<?= h($value) ?>" <?= $line['review_status'] === $value ? 'selected' : '' ?>><?= h($text) ?></option>
              <?php endforeach; ?>
            </select></div>
          </div>
          <label>Representative note or exclusion reason</label>
          <input name="lines[<?= (int) $line['id'] ?>][note]" maxlength="500" value="<?= h($line['representative_note']) ?>">
        </article>
      <?php endforeach; ?>
    </section>
  <?php endforeach; ?>
  <button type="submit">Save representative review</button>
</form>
<?php endif; ?>

<section class="card">
  <h2>Manual fallback</h2>
  <p>Add a blank line when GenAI is unavailable or when a supplied item was not extracted.</p>
  <div class="actions">
    <?php foreach (['RHEL' => 'Add RHEL line', 'ORACLE_LINUX' => 'Add Oracle Linux line'] as $side => $label): ?>
      <form method="post" action="<?= h(app_url('/add-line.php')) ?>">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= $id ?>"><input type="hidden" name="side" value="<?= h($side) ?>">
        <button class="secondary" type="submit"><?= h($label) ?></button>
      </form>
    <?php endforeach; ?>
  </div>
</section>
<?php render_footer(); ?>
