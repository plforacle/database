# Lab 6: Verify, Maintain, and Demonstrate Version 3

## Introduction

> **Version 3 authoring draft.** This lab is not ready for deployment or a learner run. Read the prerequisites and pending checkpoints before executing anything. Version 1 remains unchanged.

A successful page load does not prove a reproducible workshop. Validate the exact candidate and have another maintainer exercise routine operations.

Estimated Time: Pending the complete rehearsal.

### Objectives

* Separate automated, environment, and manual checks.
* Verify recovery without touching Version 1.
* Prepare an evidence-based demonstration and handoff.

## Task 1: Establish the test target

1. Record the candidate identity, PHP/dependency versions, migration set, private configuration references, and selected catalog. Do not record secrets.

2. Read upstream `docs/operations/ci-verification.md`. The full wrapper expects a clean Git candidate and a checked disposable local MySQL fixture.

3. Select a supported test host. Neither Version 1's database nor the new OCI application database is automatically a compatible disposable fixture.

4. Run the required style, static, unit, contract, and integration checks only after reviewing their setup and cleanup targets. The default `composer quality` command is unit-only, not the complete suite.

5. Record passed, failed, and unrun results. Upstream results and earlier PHP 8.2 syntax checks do not validate the adapted PHP 8.3 candidate.

    **Pending checkpoint:** The supported full-suite environment is not yet selected. Do not bypass test identity protections.

## Task 2: Verify the installed application

1. Test HTTPS, database TLS/server identity, private-file denial, and actual least-privilege account behavior on approved V3 resources.

2. Run the two-user access checks from Lab 3 and the input, arithmetic, persistence, and export checks from Labs 4 and 5.

3. Verify live HeatWave extraction separately from manual fallback.

4. Agree a modest concurrent-user test and response-time target. Record actual observations instead of claiming scale from the small data volume.

## Task 3: Test human maintenance and recovery

1. Have a backup maintainer follow the documented synthetic price-update procedure. Check a new comparison and an old confirmed comparison.

2. Have that maintainer diagnose a failed comparison using safe logs and the troubleshooting notes.

3. Review backups for the matching application artifact, database, private source files, and configuration references.

4. Restore only to an explicitly authorized recovery target. Do not restore over Version 1. Verify that the recovered application can reopen and export the synthetic saved comparison.

5. Distinguish catalog activation reversal from application rollback and database recovery. One does not undo the others.

## Task 4: Rehearse and publish

1. Walk through the workshop from a clean approved V3 target. Record the actual duration and collect screenshots with synthetic data.

2. Remove draft stop notices only after their checks pass. Resolve the support alias, source distribution, and remaining feature decisions.

3. Demonstrate one simple scenario, one correction, one reopened result, and one exported presentation.

4. Report the exact deployed identity, verification outcomes, backup maintainer, and recovery location to the team. Keep credentials and customer data out of the handoff.

    **Publication condition:** Do not publish while required checks are failed or unrun. Version 1 continues to provide the existing demo.

## Acknowledgements

* **Authors** - Perside Foster, Mark Atkinson, and Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, September 2026
