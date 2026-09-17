# Lab 3 Workshop Adapters

## Introduction

These adapters support a private installation rehearsal of Shawn's commit `d419d24d035cabc68b219b9e98be23cd05770742`. They do not replace upstream source files or implement real login.

Estimated Time: 3 minutes to review.

### Objectives

* Explain the small integration layer and its limits.
* Keep credentials private and separate from application code.

## Files and boundaries

* `workshop-db.php` injects an encrypted PDO connection into Shawn's `PackageConnectionFactory`. It uses the Lab 2 runtime account and refuses unencrypted sessions. Server certificate identity is not verified in this rehearsal.
* `workshop-cli.php` creates protected credential files without overwriting existing files. It exposes connection, GenAI, and read-only catalog-status checks. It does not publish or activate catalogs.
* `workshop-index.php` calls Shawn's bootstrap with the encrypted connection and shared demo identity. It accepts loopback requests only. The loopback environment permits local HTTP cookies for SSH-tunnel access, not public HTTP deployment.
* `olvn-v3.conf` serves only the public directory on loopback port 8008. It blocks the upstream entry point so it cannot bypass the adapter. Port 80 and the original greeting remain unchanged.

The account password stays in an Apache-owned, mode-0600 JSON file beneath a mode-0700 directory outside the document root. Do not include credentials in this workshop folder.

## Verification scope

Local syntax checks are not deployment proof. Rehearse dependency installation, SELinux labels, Apache routing, database encryption, runtime-account GenAI, and denial of source-file access on V3. Catalog activation and comparison acceptance are separate unfinished checkpoints. No OCI changes were performed while authoring these files.

## Acknowledgements

* **Authors** - Perside Foster, Mark Atkinson, and Shawn Kelley
* **Last Updated By/Date** - Perside Foster, September 2026
