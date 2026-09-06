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
    <li>On <strong>Comparisons</strong>, enter a unique comparison name.</li>
    <li>Paste the complete supplied RHEL information into the RHEL input and the complete supplied Oracle Linux information into the Oracle Linux input.</li>
    <li>Select <strong>Save original inputs</strong>.</li>
    <li>Select <strong>Format with GenAI</strong> once and wait for both inputs to finish.</li>
    <li>Compare every suggestion with its original input, correct the values, assign related lines the same positive group number, and give every line a final decision.</li>
    <li>Select <strong>Save representative review</strong>, then select <strong>Calculate confirmed results</strong>.</li>
    <li>Review the annual, three-year, and five-year results, then return to the comparison and select <strong>Export CSV workbook</strong>.</li>
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
  <p>If GenAI misses a supplied item or formatting is unavailable, use <strong>Manual fallback</strong> to add the line to the correct side. Complete and review it using the same rules.</p>
</section>

<section id="actions" class="card">
  <h2>Saved comparison actions</h2>
  <div class="table-scroll">
    <table>
      <thead><tr><th>Action</th><th>What it does</th></tr></thead>
      <tbody>
        <tr><td>Reopen</td><td>Returns to saved original inputs, reviewed lines, decisions, and results.</td></tr>
        <tr><td>Export CSV workbook</td><td>Downloads the complete source, review, alignment, rule, and result information.</td></tr>
        <tr><td>Duplicate comparison</td><td>Copies the inputs and reviewed lines without copying the result snapshot. Review and calculate the copy again.</td></tr>
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
    <li>If formatting fails for either side, use manual fallback and only enter information present in the original input.</li>
    <li>If calculation is blocked, resolve every Needs review or Unresolved line and confirm that every included group contains both sides.</li>
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

<p><a class="button secondary" href="<?= h(app_url('/index.php')) ?>">Return to comparisons</a></p>
<?php render_footer(); ?>
