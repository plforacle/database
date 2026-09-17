# Lab 2 SQL Files and Source Notes

## Introduction

Lab 2 uses `v3-schema.sql` and `v3-runtime-grants.sql`. The `application` directory and old application ZIP are copied Version 1 references. Do not deploy those reference files as Version 3.

Estimated Time: 1 minute.

### Objectives

* Identify the V3 SQL files and their source.
* Avoid running Version 1 deployment commands for Version 3.

## SQL source and adaptation

Source: `olvalnav`, commit `d419d24d035cabc68b219b9e98be23cd05770742`, migrations 001 through 008. Upstream remains unchanged.

The schema file concatenates those migrations in order, with these deliberate changes:

* Adds `CREATE DATABASE olvn_v3` and `USE olvn_v3` for a fresh, separate installation.
* Omits the tail of migration 003 starting at its first `CREATE USER IF NOT EXISTS` statement. This removes three fixture accounts and the temporary grant procedure, not the audit triggers or reconciliation procedure.
* Omits the fixture-only grant tails from migrations 006 and 007. Their tables and triggers remain unchanged.
* Adds source-section comments and a final installation checkpoint. SQL content otherwise matches the source after line-ending normalization.

There are 23 tables and 62 final triggers. Migration 004 replaces one trigger, so the script contains 63 trigger creations and one trigger drop. The retained reconciliation procedure belongs to the older analysis catalog path; Lab 2 does not load or activate that catalog.

The grants file adapts the runtime grants from migrations 003, 006, and 007 and `docs/operations/package-publication.md`. It targets only `olvn_v3_app` from the application subnet. Account creation occurs explicitly in the lab with a generated password. Publisher, catalog-import, and catalog-activation accounts are not created here.

SQL files use LF line endings so the SHA-256 values in Lab 2 survive Windows checkout. If SQL bytes change, review the change and update the documented hashes.

## Verification boundary

Local checks compare each retained SQL section with upstream, count objects, scan for fixture account creation, and check the Markdown and file hashes. These are not MySQL execution tests. The first HeatWave run, effective grants, and object-count checks remain learner rehearsal steps.

The current installation package is [ol-value-navigator-v3-r3.zip](ol-value-navigator-v3-r3.zip), with its [SHA-256 checksum](ol-value-navigator-v3-r3.zip.sha256). Follow Lab 3. R1/R2 ZIPs are retained for provenance, not current installation. R3 includes the bundled catalog and needs no publication or activation workflow. Real representative login and owner isolation remain application work.

No new screenshots or FreeSQL assets are included. The commands target the MySQL HeatWave DB System already created in Lab 1.

## Copied reference material

The copied application and ZIP remain unchanged for comparison. Replace or exclude them before packaging the Version 3 workshop. Do not run their deployment script or reuse the bundled schema against either application's live database.

## Acknowledgements

* **Authors** - Perside Foster, Mark Atkinson, and Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, September 2026
