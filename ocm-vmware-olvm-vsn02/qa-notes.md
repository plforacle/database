# OCM VMware to OLVM Workshop QA Notes

## Introduction

This file is a reviewer checklist for the OCM VMware-to-OLVM workshop draft. It is not included in the LiveLabs manifest.

Estimated Time: 5 minutes

### Objectives

In this checklist, reviewers will:

* Confirm the source authority for each lab area
* Review current Oracle Cloud Migrations terminology
* Identify SME review items before publication
* Track remaining validation requirements

## Source Authority

Use these source materials as the primary basis for technical review:

* `OCM-DEMO-BUILD.pdf` - Authority for the OCI foundation, OCVS SDDC, jump host, NSX and vCenter access, and source VM build.
* `vmware-olvm-ocm-confluence.pdf` - Planning source for VMware-to-OLVM migration, OLVM roles, remote agent connectivity, and OLVM credential format.
* `Oracle-Cloud-Migrations-LA-PDF.pdf` - Draft OCM source for prerequisites, remote agent, VDDK, discovery, migration assets, migration plans, Resource Manager stack deployment, and validation.

Treat the Oracle Cloud Migrations LA PDF as draft or pre-GA guidance. Confirm console labels and required fields against current Oracle documentation and a live tenancy before publishing.

## Current Oracle Documentation Cross-Check

The May 12, 2026 public Oracle Cloud Migrations documentation confirms these current workflow names and concepts:

* Cloud Migrations is under **Migration & Disaster Recovery** in the OCI Console.
* Remote connections are represented as source environments.
* Remote agent appliances are downloaded from the source environment workflow and registered on port `3000`.
* VMware discovery uses a VMware asset source, vCenter SDK endpoint, and vault-backed credentials.
* VMware replication requires a VDDK agent dependency.
* OLVM discovery uses an OLVM asset source, OLVM endpoint, and vault-backed credentials that include the OLVM engine certificate.
* OLVM migration projects are created with **Create a migration project with initial migration plan** and the **OLVM Migration Project** option enabled.
* The OLVM initial migration plan collects the target cluster, VnicProfile, and template.
* The migration workflow uses replication, target asset review, RMS stack generation/deployment, and post-migration completion.

## SME Review Items

Ask the OLVM or OCM SME to confirm these items before publication:

* The approved OLVM build method for the demo.
* Whether the demo should use `admin@internal` or a dedicated OLVM migration user.
* The exact OLVM roles required beyond `VmCreator` and `DiskOperator`, if local policy requires least-privilege hardening.
* Whether the target OLVM environment needs storage-domain selection in the current OCM console flow.
* Whether the OLVM replication management endpoint is reachable on port `54323` in the selected network design.
* Whether the source VM name should remain `OL9u7-test` or use a neutral workshop name.
* Whether screenshots should be captured from a live OCI, OCM, OCVS, vSphere, NSX, and OLVM environment.

## Validation Notes

Local structure validation has checked for one H1 per markdown file, required section fields, manifest references, image references, obvious placeholders, obvious sensitive values, and ordered-list indentation patterns.

The official LiveLabs shell validator should still be run when a bash-capable environment is available.

## Acknowledgements

* **Author** - Mark Atkinson, Master Principal Sales Consultant, Open Cloud Technologies
* **Workshop Draft** - Perside Foster, May 2026
* **Source Material** - OCM-DEMO-BUILD presentation, Confluence VMware-to-OLVM planning PDF, Oracle Cloud Migrations LA PDF, and public Oracle Cloud Migrations documentation
