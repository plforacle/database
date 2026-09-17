# Build Oracle Linux Value Navigator Version 3

## Introduction

> **Version 3 workshop in progress.** Labs 1 and 2 passed the user-led rehearsal. Lab 3 now contains private installation and connectivity checks for rehearsal. Labs 4 through 6 remain authoring drafts. Version 1 remains unchanged.

This workshop will guide you through building a subscription-cost comparison application from Shawn's PHP code. It uses the LAMP concept: Oracle Linux and Apache run PHP, and a private MySQL HeatWave DB System stores data and supports HeatWave GenAI.

The goal is a simple representative journey: enter or import RHEL information, review the lines and Oracle Linux comparison, calculate, then save or export. AI assists with input; it does not approve prices, choose authoritative mappings, or confirm results.

Use invented sample data only. This application is not a customer quote, licensing determination, or full TCO analysis.

Estimated Workshop Time: Pending measurement during the Version 3 rehearsal.

### Objectives

* Deploy one PHP application in a separate OCI environment.
* Review source information and use deterministic cost calculations.
* Protect comparisons with authenticated user ownership.
* Save and export results with their price basis.
* Maintain and recover the application using documented procedures.

### Current code versus target release

| Area | Reviewed Shawn code | Version 3 target |
| --- | --- | --- |
| Identity | One configured demo representative | Real sign-in and separate user access |
| Scenario terms | 12 or 36 months | Annual, three-year, and five-year results under reviewed assumptions |
| AI | Text extraction in a separate analysis demo | A coherent reviewed-input journey |
| Imports | CSV, TSV, XLSX, and manually transcribed images | Focused spreadsheet workflow; no PDF/OCR promise |
| Saved work | Revisions, confirmation, and PowerPoint routes | Simple owner-filtered list and agreed exports |

The target column is planned work, not a claim of implemented behavior.

### Environment boundary

Version 3 must not use Version 1's server, DB System, accounts, or deployment scripts. Lab 1 creates separate resources in compartment `ol-value-navigator-3`. Do not change shared Version 1 network rules.

No new microservice, Kubernetes platform, or separate OCI AI service is required. Lab 1 creates an Apache server and private database. Its public HTTP greeting is only a setup test. Lab 3 uses a loopback-only application listener and encrypted SSH tunnel for private rehearsal. Public application access still requires HTTPS and real login.

### Prerequisites and later-lab requirements

* OCI access, permission to create the Lab 1 resources, and an SSH client. Lab 1 creates the separate environment; it does not need to exist beforehand.
* Review of resource costs before creation.
* The complete, identified application source and supported PHP 8.3 dependencies.
* A reviewed migration/account procedure and selected synthetic catalog package.
* A working identity integration before teaching multiuser access.
* Synthetic examples, independent expected results, and tested recovery steps.

Begin with Lab 1 and record actual checkpoint results. The source, migration, identity, and recovery requirements above apply to the later application labs, not to creating the Lab 1 infrastructure.

## Acknowledgements

* **Authors** - Perside Foster, Mark Atkinson, and Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, September 2026
