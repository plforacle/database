<?php
declare(strict_types=1);
/**
 * GET controller and view for a Stage 5 calculated result snapshot.
 *
 * Access is redirected to review unless both the CALCULATED state and its result
 * row exist. The view presents fixed-point totals, explains the difference
 * direction, identifies the calculation rule, and retains excluded lines in the
 * traceability table even though they do not contribute to totals.
 */
require '/var/www/ol-value-navigator/lib/bootstrap.php';
require_stage(5);
$id = request_id();
$comparison = find_comparison($id);
$lines = comparison_lines($id);
$statement = db()->prepare('SELECT * FROM comparison_result WHERE comparison_id = ?');
$statement->execute([$id]);
$result = $statement->fetch();
if (!$result || $comparison['status'] !== 'CALCULATED') {
    flash('info', 'Review and calculate the comparison before opening results.');
    redirect('/review.php?id=' . $id);
}

render_header('Results: ' . $comparison['name']);
?>
<div class="actions">
  <a class="button secondary" href="<?= h(app_url('/comparison.php?id=' . $id)) ?>">Back to comparison details</a>
  <a class="button secondary" href="<?= h(app_url('/review.php?id=' . $id)) ?>">Review lines</a>
  <form method="post" action="<?= h(app_url('/export-pptx.php')) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= h($id) ?>">
    <button type="submit">Download PowerPoint</button>
  </form>
  <form method="post" action="<?= h(app_url('/export.php')) ?>">
    <?= csrf_field() ?><input type="hidden" name="id" value="<?= h($id) ?>">
    <button class="secondary" type="submit">Export CSV workbook</button>
  </form>
  <a class="button secondary" href="<?= h(app_url('/context.php?id=' . $id)) ?>">Edit customer details</a>
</div>
<p>Download four editable PowerPoint slides or the complete CSV workbook. Both exports use this saved comparison.</p>

<section class="card">
  <h2>Confirmed subscription-cost comparison</h2>
  <p>Positive difference values mean the confirmed RHEL total is greater than the confirmed Oracle Linux total. These results use rule version <strong><?= h($comparison['version_label']) ?></strong>.</p>
  <div class="table-scroll"><table>
    <thead><tr><th>Period</th><th>RHEL</th><th>Oracle Linux</th><th>Difference</th></tr></thead>
    <tbody>
      <tr><th>Annual</th><td><?= h(money($result['rhel_annual_total'])) ?></td><td><?= h(money($result['oracle_linux_annual_total'])) ?></td><td><?= h(money($result['annual_difference'])) ?></td></tr>
      <tr><th>Three years</th><td><?= h(money($result['rhel_three_year_total'])) ?></td><td><?= h(money($result['oracle_linux_three_year_total'])) ?></td><td><?= h(money($result['three_year_difference'])) ?></td></tr>
      <tr><th>Five years</th><td><?= h(money($result['rhel_five_year_total'])) ?></td><td><?= h(money($result['oracle_linux_five_year_total'])) ?></td><td><?= h(money($result['five_year_difference'])) ?></td></tr>
    </tbody>
  </table></div>
</section>

<?php render_customer_context($comparison, false); ?>
<section class="card">
  <h2>Traceable reviewed lines</h2>
  <div class="table-scroll"><table>
    <thead><tr><th>Side</th><th>Group</th><th>SKU</th><th>Description</th><th>Quantity</th><th>Annual unit price</th><th>Decision</th><th>Note</th></tr></thead>
    <tbody>
    <?php foreach ($lines as $line): ?>
      <tr>
        <td><?= h($line['input_side']) ?></td><td><?= h($line['comparison_group']) ?></td><td><?= h($line['sku']) ?></td>
        <td><?= h($line['description']) ?></td><td><?= h($line['quantity']) ?></td>
        <td><?= $line['annual_unit_price'] === null ? '' : h(money($line['annual_unit_price'])) ?></td>
        <td><?= h($line['review_status']) ?></td><td><?= h($line['representative_note']) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</section>

<p>Calculated at <?= h($result['calculated_at']) ?>. Excluded lines remain visible but are not included in totals.</p>
<?php render_footer(); ?>
