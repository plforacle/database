<?php
declare(strict_types=1);
/**
 * GET controller and static view for the built-in Version 1 Help experience.
 *
 * The guide follows the representative's actual workflow from source entry
 * through GenAI formatting, human review, calculation, and CSV export. It also
 * explains destructive actions, recovery paths, and prototype governance limits.
 * Keeping this content in the deployed application makes help available without
 * requiring access to the LiveLabs workshop.
 */
require '/var/www/ol-value-navigator/lib/bootstrap.php';

render_header('Help and Quick Start');
?>
<nav class="card help-nav" aria-label="Help topics">
  <strong>Help topics</strong>
  <a href="#quick-start">Quick Start</a>
  <a href="#review">Review decisions</a>
  <a href="#actions">Saved comparison actions</a>
  <a href="#troubleshooting">Troubleshooting</a>
  <a href="#boundaries">Version 1 boundaries</a>
</nav>

<section id="quick-start" class="card">
  <h2>Quick Start</h2>
  <ol class="quick-steps">
    <li>Select <strong>Home</strong> in the header and enter a unique comparison name.</li>
    <li>Optionally expand <strong>Customer details</strong> and enter a demonstration customer name, objective, comparison scope, and recommended next step. These fields do not affect calculations or get sent to GenAI.</li>
    <li>Paste the complete supplied RHEL information into the RHEL input and the complete supplied Oracle Linux information into the Oracle Linux input.</li>
    <?php if (app_stage() >= 5): ?>
    <li>Select <strong>Save and format with AI</strong> once. Your original inputs are saved first. Wait for the review page; formatting can take a minute or longer.</li>
    <?php else: ?>
    <li>Select <strong>Save original inputs</strong>. In Lab 4, select <strong>Format with GenAI</strong> once and wait for review.</li>
    <?php endif; ?>
    <li>Compare every suggestion with its original input, correct the values, assign related lines the same positive group number, and give every line a final decision.</li>
    <?php if (app_stage() >= 5): ?>
    <li>Select <strong>Save review and calculate</strong>. This saves your current edits, validates every decision and group, then opens results. Use <strong>Save review for later</strong> if you are not finished.</li>
    <?php else: ?>
    <li>In Lab 4, select <strong>Save representative review</strong>. Lab 5 enables calculation.</li>
    <?php endif; ?>
    <li>Review the annual, three-year, and five-year results. On the Results page, select <strong>Download PowerPoint</strong> for four editable summary slides or <strong>Download CSV</strong> for the complete supporting data.</li>
  </ol>
  <div class="notice info">MySQL HeatWave GenAI formats supplied text into suggestions. The representative owns every correction, alignment, confirmation, and exclusion. PHP performs the calculations.</div>
</section>

<section id="review" class="card">
  <h2>Review and alignment</h2>
  <p>Expand <strong>Show complete original input</strong> for each side and verify every suggested SKU, description, quantity, and annual unit price against the supplied text.</p>
  <div class="table-scroll">
    <table>
      <thead><tr><th>Decision</th><th>Use it when</th></tr></thead>
      <tbody>
        <tr><td>Confirmed</td><td>All required fields are reviewed and the line can participate in the calculation.</td></tr>
        <tr><td>Excluded</td><td>The supplied line must not participate. Enter an exclusion reason.</td></tr>
        <tr><td>Unresolved</td><td>A supplied value or decision remains uncertain. Enter a representative note.</td></tr>
        <tr><td>Needs review</td><td>Review is not complete. This decision blocks calculation.</td></tr>
      </tbody>
    </table>
  </div>
  <p>Assign related RHEL and Oracle Linux lines the same positive group number. A group records the representative's chosen alignment and does not claim product equivalence.</p>
  <p>If GenAI misses a supplied item or formatting is unavailable, save your current edits, then expand <strong>Manual fallback</strong> to add the line to the correct side. Complete and review it using the same rules.</p>
</section>

<section id="actions" class="card">
  <h2>Saved comparison actions</h2>
  <p><strong>Home</strong> always opens the page where you create a comparison or open a saved one. <strong>Open</strong> goes directly to results for calculated comparisons and to review when lines are available. An unformatted draft opens comparison details. <strong>Back to comparison details</strong> opens the current comparison. Expand <strong>Saved inputs, customer details, and status</strong> for supporting information or <strong>More actions</strong> to duplicate, revise, or delete. Formatting again is under <strong>Replace AI suggestions</strong> and replaces reviewed lines.</p>
  <div class="table-scroll">
    <table>
      <thead><tr><th>Action</th><th>What it does</th></tr></thead>
      <tbody>
        <tr><td>Reopen</td><td>Resumes results or review without changing saved data.</td></tr>
        <tr><td>Edit customer details</td><td>Updates optional presentation context without clearing reviewed lines or calculated results. Available on the Comparison and Results pages. Download new exports after saving; previously downloaded files do not change.</td></tr>
        <tr><td>Download CSV</td><td>Downloads the complete source, review, alignment, rule, and result information.</td></tr>
        <tr><td>Download PowerPoint</td><td>Downloads four editable slides with customer context, saved totals, review counts, assumptions, and the recommended next step. Long context is shortened on slides with full text retained in speaker notes and CSV. Recalculate after changing SKU inputs or review decisions, but not after editing customer details.</td></tr>
        <tr><td>Duplicate comparison</td><td>Copies customer details, inputs and reviewed lines without copying the result snapshot. Review and calculate the copy again.</td></tr>
        <tr><td>Revise original inputs</td><td>Updates the source text and clears derived lines and results. The workflow must be repeated.</td></tr>
        <tr><td>Delete comparison</td><td>Permanently deletes the comparison and associated data after exact-name confirmation. Only its ID, name, and deletion time remain in the audit record.</td></tr>
      </tbody>
    </table>
  </div>
</section>

<section id="troubleshooting" class="card">
  <h2>Troubleshooting</h2>
  <ul>
    <li>If formatting takes several seconds, continue waiting. Do not refresh the page or select the button again.</li>
    <li>If formatting fails for either side, your original inputs remain saved. Do not create the comparison again. Expand Manual fallback and only enter information present in the original input.</li>
    <li>If calculation is blocked after a valid save, your review remains saved. Resolve every Needs review or Unresolved line and confirm that every included group contains both sides, then select Save review and calculate again.</li>
    <li>If a form reports an error, correct the named field or review line. Creation, source-revision, and review forms restore your submitted entries after a failed save. Save again before leaving the page. Restored entries are not yet saved to the comparison.</li>
    <li>If deletion is blocked, type the complete comparison name exactly, including capitalization and spaces.</li>
    <li>If the application cannot connect to the database, contact the workshop administrator. Do not place credentials in a support message.</li>
  </ul>
</section>

<section id="boundaries" class="card">
  <h2>Version 1 boundaries</h2>
  <ul>
    <li>Use demonstration information only.</li>
    <li>Version 1 has no login or individual comparison ownership.</li>
    <li>The application does not maintain master RHEL or Oracle Linux SKU catalogs.</li>
    <li>The comparison is not a quote, licensing determination, product-equivalence assessment, or complete TCO analysis.</li>
  </ul>
</section>

<p><a class="button secondary" href="<?= h(app_url('/index.php')) ?>">Home</a></p>
<?php render_footer(); ?>
