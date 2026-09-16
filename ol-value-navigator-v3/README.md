# Oracle Linux Value Navigator Version 3

## Introduction

This is the separate Version 3 LiveLabs workshop, based on Shawn's application. It reuses the Version 1 layout and Lab 1 LAMP setup sequence with separate V3 resources. The original sibling folder, `ol-value-navigator`, remains unchanged.

**Status: Lab 1 passed the user-led rehearsal; Lab 2 is ready for rehearsal; Labs 3 through 6 remain authoring drafts.** Perside supplied successful PHP and HeatWave GenAI results. Lab 2 now includes schema installation and application-account instructions; its SQL has not yet been run against HeatWave.

Estimated Time: 5 minutes to review this status.

### Objectives

* Build a reproducible LAMP workshop around Shawn's application.
* Keep the representative workflow and human maintenance straightforward.
* Protect the Version 1 demonstration environment.

## Start here

Start with [Lab 1: Create the Version 3 OCI LAMP Environment](oci-lamp-setup/oci-lamp-setup.md). You execute the OCI steps; the assistant has not accessed or changed OCI.

Open `workshops/tenancy/index.html` through the team's approved local LiveLabs preview procedure. The manifest identifies this workshop as a Version 3 draft. Browser rendering has not yet been verified.

The learner journey is: enter or import RHEL information, review, compare, save, and export. The target release adds real login and user-owned comparisons. AI suggests fields; people review them; PHP calculates.

## Source of truth

| Item | Source |
| --- | --- |
| Upstream application | Complete local repository: `C:\Users\Perside\Documents\GitHub\olvalnav` |
| Reviewed commit | `d419d24d035cabc68b219b9e98be23cd05770742` |
| Integration reference | `perside-integration-quick-start.pdf` in Perside's project folder |
| Version 3 workshop | This folder, `ol-value-navigator-v3` |
| Application candidate | Not packaged yet; no additional original ZIP is required to use the verified repository |

The supplied `shawn/app` directory contains only the application-code subtree. Use the complete repository for dependencies, public files, migrations, resources, and tests.

**Copied Version 1 files are reference material only:** `catalog-database/files/application` and `catalog-database/files/ol-value-navigator-application.zip`. They have not been converted into Version 3. Do not run their scripts, upload that ZIP as Version 3, or point their configuration at a live database. The active labs no longer direct learners to that package.

## Workshop sequence

1. Create the compartment `ol-value-navigator-3`, networking, LAMP server, and private HeatWave database.
2. Create and verify the application database and runtime account.
3. Configure the PHP application and identity.
4. Enter, import, and review subscription information.
5. Calculate, save, and export comparisons.
6. Verify, maintain, recover, and demonstrate.

Keep existing folder names during this first pass so the copied LiveLabs layout stays familiar.

## Release checkpoints

See [authoring status](authoring-status.md) for the code gaps, source references, and verification checklist. In particular, real login and integrated AI/scenario behavior remain unfinished; the newer scenario currently supports 12 and 36 months, not five years.

Use synthetic data. No OCI changes, dependency installations, migrations, or application tests have been performed during this authoring pass.

## Acknowledgements

* **Authors** - Perside Foster, Mark Atkinson, and Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, September 2026
