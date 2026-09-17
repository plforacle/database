# V3 R3 Build Report

Date: September 17, 2026.
Status: private rehearsal candidate, not an end-to-end verified release.

## Source and scope

Shawn's reviewed commit is d419d24d035cabc68b219b9e98be23cd05770742. Application source, GUI, business rules, and Composer lock remain unchanged. R3 consolidates the deployment helpers, retry handling, three confirmed GenAI grants, and one Lab 3 installation path.

The package includes 33 locked production libraries, their license files, and a synthetic import example. It excludes credentials, Composer, and development tools. The September 16 production dependency audit reported no advisories or abandoned packages; it was not repeated for R3.

## User-verified OCI evidence

On September 17, Perside applied these exact grants for olvn_v3_app@10.0.0.0/255.255.255.0:

* EXECUTE on FUNCTION sys.ML_GENERATE.
* EXECUTE on PROCEDURE sys.ML_CLUSTER_CHECK.
* EXECUTE on FUNCTION sys.ML_GENAI_VARIABLE.

Both direct SQL and the earlier Lab 3 PHP helper returned READY. SQL completed in 52.02 seconds. This verifies that runtime path, not deployment of this new ZIP.

## Local verification and limits

R3 retains the same source and locked production dependencies used for local PHP 8.3.33 calculation and route tests. Local checks cover PHP and shell syntax, credential preflight with a mocked database, immutable existing credential files, catalog loading, independent synthetic legacy arithmetic, and an unknown-SKU block.

The legacy synthetic expectation uses ten source units at invented USD 150 versus ten target units at invented USD 60. Annual totals are USD 1,500 versus USD 600, savings USD 900 and 60 percent. Three-year totals are USD 4,500 versus USD 1,800; five-year totals are USD 7,500 versus USD 3,000.

Four Slim routes are checked with in-memory SQLite: /health/live, /demo, /demo/coverage, and /demo/coverage/new. This does not test Apache, MySQL persistence, or browser actions. Final archive hashes and portable ZIP paths are checked after packaging.

## Prior full-suite failures remain open

The full upstream Windows run from September 16 had 853 tests, 20,140 assertions, 37 errors, 15 failures, 6 warnings, and 1 skipped test. It was not rerun for R3. Failures included Linux permissions and symlinks, console line endings, process/repository verification, and presentation archive finalization. They were not suppressed or declared fixed.

## Remaining acceptance work

Rehearse installation and retry behavior under Oracle Linux, including SELinux and Apache/PHP-FPM. Verify live saves, reopen, confirmation, imports, and presentation exports using Lab 3. A passing command-line smoke test does not complete these browser checks. New screenshots and browser-rendered LiveLabs review are pending.

The shared demo identity is not login or multiuser isolation. Database TLS does not verify server certificate identity. Keep access private through SSH. Catalog values are not approved commercial quotes. Five-year support is not implemented in the newer 12/36-month scenario workflow.

No OCI resources, grants, migrations, or remote application files were changed by the assistant while authoring R3. Version 1 and earlier ZIPs remain untouched.
