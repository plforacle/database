<?php
declare(strict_types=1);
/**
 * GET controller and view for the Version 1 comparison workspace.
 *
 * The page accepts both complete freeform inputs and lists the 50 most recently
 * updated workbooks. Version 1 has no login or per-user ownership, so the list is
 * shared by everyone with network access to the prototype. All database values
 * are escaped before rendering and creation is delegated to a CSRF-protected POST
 * controller.
 */
require '/var/www/ol-value-navigator/lib/bootstrap.php';

// Bound the shared landing page while keeping recently active workbooks visible.
$comparisons = db()->query(
    'SELECT c.id, c.name, c.status, c.updated_at,
            (SELECT COUNT(*) FROM comparison_line l JOIN comparison_input i ON i.id = l.comparison_input_id WHERE i.comparison_id = c.id) AS line_count
     FROM comparison c ORDER BY c.updated_at DESC LIMIT 50'
)->fetchAll();

$draft = take_form('create');
render_header('Home');
?>
<section class="card">
  <h2>Create a comparison</h2>
  <form method="post" action="<?= h(app_url('/create.php')) ?>">
    <?= csrf_field() ?>
    <label for="name">Comparison name</label>
    <input id="name" name="name" maxlength="255" required placeholder="Demonstration comparison" value="<?= h(form_value($draft, 'name')) ?>">
    <details class="context-details" <?= $draft !== [] ? 'open' : '' ?>>
      <summary>Customer details (optional)</summary>
      <p>Use demonstration information only. These details appear in exports and do not affect calculations.</p>
      <?php render_customer_context_fields($draft); ?>
    </details>

    <div class="two-column">
      <div>
        <label for="rhel_text">RHEL SKU information</label>
        <textarea id="rhel_text" name="rhel_text" maxlength="<?= (int) app_config('max_input_characters', 12000) ?>" required><?= h(form_value($draft, 'rhel_text')) ?></textarea>
        <small>Paste the complete supplied RHEL text, including SKUs, descriptions, quantities, annual prices, and notes.</small>
      </div>
      <div>
        <label for="oracle_text">Oracle Linux SKU information</label>
        <textarea id="oracle_text" name="oracle_text" maxlength="<?= (int) app_config('max_input_characters', 12000) ?>" required><?= h(form_value($draft, 'oracle_text')) ?></textarea>
        <small>Paste the complete supplied Oracle Linux text, including SKUs, descriptions, quantities, annual prices, and notes.</small>
      </div>
    </div>
    <?php if (app_stage() >= 5): ?>
      <p>Step 1 of 3: enter inputs. Next, review the AI suggestions, then view your results. Formatting can take a minute or longer. Select once and wait.</p>
      <button type="submit" name="next" value="format">Save and format with AI</button>
    <?php else: ?>
      <button type="submit">Save original inputs</button>
    <?php endif; ?>
  </form>
</section>

<section class="card">
  <h2>Saved comparisons</h2>
  <?php if ($comparisons === []): ?>
    <p>No comparisons have been saved.</p>
  <?php else: ?>
    <div class="table-scroll"><table>
      <thead><tr><th>Name</th><th>Status</th><th>Lines</th><th>Updated</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($comparisons as $comparison): ?>
        <tr>
          <td><?= h($comparison['name']) ?></td>
          <td><span class="status"><?= h($comparison['status']) ?></span></td>
          <td><?= (int) $comparison['line_count'] ?></td>
          <td><?= h($comparison['updated_at']) ?></td>
          <td><a class="button secondary" href="<?= h(app_url(comparison_landing_path($comparison, app_stage()))) ?>">Open</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  <?php endif; ?>
</section>
<?php render_footer(); ?>
