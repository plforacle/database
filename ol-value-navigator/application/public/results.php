<?php
declare(strict_types=1);
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
  <a class="button secondary" href="<?= h(app_url('/comparison.php?id=' . $id)) ?>">Comparison</a>
  <a class="button secondary" href="<?= h(app_url('/review.php?id=' . $id)) ?>">Review lines</a>
</div>

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

