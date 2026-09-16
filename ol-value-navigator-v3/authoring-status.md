# Version 3 Authoring Status

## Introduction

Updated September 15, 2026: Lab 1 now contains the Version 1 resource-creation sequence adapted for a user-led Version 3 rehearsal. Mode: `how-to-guide`, draft maturity. The workshop structure comes from the copied Version 1 workshop; application behavior comes from Shawn's reviewed code.

Estimated Time: 5 minutes to review.

### Objectives

* Track source evidence and unfinished steps without claiming deployment readiness.
* Keep the learner instructions separate from copied reference files.

## Source and conversion record

| Area | Source | Treatment |
| --- | --- | --- |
| Workshop layout | Original `ol-value-navigator` | Reused folders and LiveLabs launcher; original untouched |
| Lab 1 procedure | Version 1 `oci-lamp-setup/oci-lamp-setup.md` | Preserved eight tasks; V3 resource names, restricted SSH/HTTP sources, and rehearsal checkpoints |
| PHP and MySQL client streams | Oracle Linux 9.6 release notes linked in Lab 1 | PHP 8.3 and MySQL 8.4 client; no Version 1 upgrade commands |
| HeatWave setup and model | Official GenAI requirements and ML_GENERATE reference linked in Lab 1, checked September 15, 2026 | Retained the recommended cluster shape; clarified cost, model version, and unexecuted checks |
| Complete application | Local `C:\Users\Perside\Documents\GitHub\olvalnav`, commit `d419d24d035cabc68b219b9e98be23cd05770742` | Read-only upstream; not yet bundled as an adapted V3 package |
| Source handoff | Complete repository now available | Original source ZIP no longer required; generated candidate must retain provenance |
| Integration sequence | Perside integration quick-start guide | Preserved review, isolation, exact-candidate verification, and approval boundaries |
| Routing and identity | `app/Bootstrap.php`, `config/app.php` | Documented shared demo identity and separate routes |
| Periods and exports | `ScenarioTerm.php`, scenario view, scenario presentation content | Documented 12/36 limitation and pending five-year work |
| Import | `ScenarioImport.php`, `CoverageSnapshotStore.php` | Documented supported formats, review limits, and private evidence |
| Installation and tests | `docs/operations/package-publication.md`, `ci-verification.md` | No blind migration or live-database test instructions |

## Remaining release work

* Rehearse Lab 1 in compartment `ol-value-navigator-3`. The user chooses the region and reviews costs in the creation steps. The assistant does not provision OCI resources.
* Prepare and verify the separate PHP 8.3 application candidate and dependencies.
* Implement real identity, coherent AI/scenario input, and 1/3/5-year scenario results.
* Verify owner-filtered saved work, CSV behavior, and user-facing simplicity.
* Prepare safe migrations, dedicated grants, catalog activation, and recovery instructions.
* Validate live HeatWave extraction and all acceptance checks.
* Rehearse Lab 1 and record results. Complete the later labs, then replace their draft stop notices with verified instructions and screenshots.
* Confirm the support alias and backup maintainer before publication.

## Copied material

The `catalog-database/files/application` directory and old application ZIP remain unchanged Version 1 references. No active lab instructs learners to run or download them. They must be replaced or excluded before a Version 3 publication package is built.

Copied images also remain as reference assets. None is claimed as a Version 3 screenshot in the active draft. New screenshots are pending candidate execution.

## QA scope

Run the installed LiveLabs workshop validator and inspect the manifest paths, required sections, task sequence, copy-tag balance, and stale Version 1 deployment references. Record actual results in `VALIDATION-RESULT.md`.

This check assesses document structure, not functional readiness. No OCI provisioning, dependency installation, application execution, browser rehearsal, or recovery test occurred in this authoring pass.

FreeSQL is not used: the labs target MySQL HeatWave. No second database platform or FreeSQL embed is needed.

## Document QA results

* Twelve authored Markdown files passed the installed validator's structural checks. Eight local manifest targets resolve inside this workshop.
* Additional checks passed for local links, sequential task numbers, required objectives, and the no-em-dash rule.
* The stale-term scan found Version 1 identifiers only in the retained reference application. Active guides no longer include its demo address or old login-delivery promise.
* All 38 copied application files and the reference ZIP still match Version 1. The original workshop has no Git changes from this task.
* Lab 1 now preserves the original eight-task setup sequence. Later labs still contain authoring checkpoints, not complete application deployment procedures.
* Screenshot directories exist, but new screenshots and browser-render verification remain pending. Automated prose warnings also remain for the next editing pass.
* The skill wrapper encountered the Windows Python alias. The same installed validator ran through the bundled Python runtime without changing global settings.

## Acknowledgements

* **Authors** - Perside Foster, Mark Atkinson, and Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, September 2026
