# Lab 2: Identify the Source and Prepare the Database

## Introduction

> **Version 3 authoring draft.** This lab is not ready for deployment or a learner run. Read the prerequisites and pending checkpoints before executing anything. Version 1 remains unchanged.

Shawn's application uses a different schema and catalog mechanism from Version 1. Identify the complete source before preparing a fresh Version 3 installation.

Estimated Time: Pending installation rehearsal.

### Objectives

* Identify the exact application source and dependencies.
* Separate schema installation from account provisioning.
* Explain why a catalog must be selected before coverage review.

## Task 1: Identify the source

1. Use the complete `olvalnav` repository, not only its `app` folder. The reviewed revision is `d419d24d035cabc68b219b9e98be23cd05770742`.

2. Confirm that the source contains `app`, `public`, `config`, `database`, `resources`, `tests`, `composer.json`, and `composer.lock`.

3. Record the revision, dependency-lock hash, and any adaptations in a separate candidate. Preserve the upstream repository. Do not fetch, merge, or change the baseline silently.

4. Package the accepted candidate only after review. Record the generated archive's SHA-256 and relationship to the source revision. A checksum identifies bytes; it does not independently prove a Git commit.

    **Checkpoint:** A complete local repository is available. An original downloaded ZIP is not a prerequisite.

## Task 2: Review installation responsibilities

1. Inspect migrations without executing them. Note the catalog tables, analysis tables, coverage revisions, package tables, and audit protections.

2. Separate schema work from account/grant work. Migration `003_audit_guards.sql` contains synthetic account creation and grants; later coverage grants also contain fixture-specific conditions.

3. Plan dedicated V3 runtime and catalog-maintenance credentials. Keep runtime access distinct from publication privileges.

4. Compare the reviewed fresh-install set with the intended V3 database. Do not run the copied Version 1 schema or every upstream migration blindly.

    **Pending checkpoint:** The deployment-safe migration/account procedure has not been prepared or tested.

## Task 3: Prepare the synthetic catalog

1. Read upstream `docs/operations/package-publication.md`. Distinguish publishing a validated package from selecting it for runtime use.

2. Prepare independently reviewed synthetic acceptance examples and identify the selected package.

3. Verify new comparisons use the selected package and saved confirmed comparisons retain their earlier price basis.

    **Pending checkpoint:** No package has been published or activated in a V3 environment. Creating the schema alone does not make coverage review available.

## Learn More

* Upstream source references: `database/migrations`, `docs/operations/package-publication.md`, and `app/Infrastructure/Catalog/PdoCatalogSnapshotResolver.php`.

## Acknowledgements

* **Authors** - Perside Foster, Mark Atkinson, and Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, September 2026
