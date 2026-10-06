# AlmaLinux to Oracle Linux on OCI

## Introduction

LiveLabs workshop for converting a disposable AlmaLinux 9.8 x86_64 OCI VM to Oracle Linux 9.8 with RHCK. Lab 1 creates a compartment, VCN, and AlmaLinux instance through the OCI Console. The workshop uses E5.Flex with 1 OCPU, 12 GB RAM, shielded instance disabled, and confidential computing disabled.

Estimated Workshop Time: 5 hours 45 minutes, plus backup and restore operations

### Objectives

- Run the eight labs through the tenancy manifest.
- Review source traceability and QA results before publishing.
- Test the migration and restoration flow in a disposable OCI environment.

## Launch and Review

Serve the repository root over HTTP and open `alma-to-ol-oci/workshops/tenancy/index.html`. The LiveLabs viewer uses Oracle CDN assets and needs internet access. For a local checkout, run `python -m http.server 8000` from the parent `database` directory and open `http://localhost:8000/alma-to-ol-oci/workshops/tenancy/index.html`.

The user reported successful completion of the original seven labs. The new OS Management Hub lab and revised Ksplice integration are authored from Oracle documentation and require a live rehearsal on the converted instance before publication. `qa/source-traceability.json` records sources and deliberate differences from the RHEL workshop; `qa/validation-results.json` records static checks.

## OS Management Hub Extension

Lab 6 registers the existing converted Oracle Linux 9 instance through the OCI Console, installs a utility, and schedules a one-time security update. Ksplice is now Lab 7 and final validation/cleanup is Lab 8. The extension requires an eligible instance, Oracle Cloud Agent 1.40.0 or later, IAM access, and regional service connectivity.

`qa/osmh-screenshot-plan.json` lists the genuine Console screenshots to capture during rehearsal. The lab has no fabricated Console images or references to missing image files. FreeSQL is not applicable.

## Supporting Scripts

The scripts in `baseline-workload/files` and `validate-cleanup/files` are optional helpers. The labs contain the core commands inline, so they do not depend on a future GitHub publication URL. To use a helper, download it from the checkout, transfer it to the lab VM with `scp`, inspect it, and run it with Bash. The capture helper accepts `before` or `after`; the final validator checks Oracle Linux major version 9 and RHCK after maintenance.

## Maintenance

The migration commit and SHA-256 checksum are pinned as a pair. Refresh them only after a full disposable AlmaLinux migration, reboot, application validation, and recovery test. Confirm that `--target-version 9.8` remains available from Oracle repositories. Verify the shape and security documentation when changing the launch configuration. Capture real OCI screenshots during the live rehearsal; the included SVG is an explanatory diagram.

No FreeSQL content is required because this workshop teaches Linux administration and does not run SQL.

## Learn More

- [Migration repository](https://github.com/oracle/migrate-to-ol)
- [Oracle LiveLabs](https://livelabs.oracle.com/)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Perside Foster with Codex assistance, October 2026
