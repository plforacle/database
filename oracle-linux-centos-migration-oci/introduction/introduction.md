# Migrate CentOS Stream 9 to Oracle Linux 9 on OCI

## Introduction

This workshop walks through an in-place migration of a CentOS Stream 9 virtual machine in Oracle Cloud Infrastructure (OCI). Your instructor prepares a separate source VM for you. You deploy a small Apache workload, record its original state, create a recovery point, migrate the same VM to the latest Oracle Linux 9 release, and verify that the workload still runs. Participants do not download, import, or launch the source image.

The migration script comes from Oracle's community-driven [migrate-to-ol repository](https://github.com/oracle/migrate-to-ol). The repository says these scripts are not officially supported by Oracle. Use a disposable training VM. This CentOS Stream 9 path has not yet been tested end to end on OCI; instructors must complete that rehearsal before delivery.

Estimated Workshop Time: 4 hours

### Objectives

In this workshop, you will:

- Connect to your assigned CentOS Stream 9 VM and verify SSH, networking, and package repositories.
- Deploy Apache and capture a before-migration baseline.
- Assess migration readiness and back up the boot volume.
- Migrate to the latest Oracle Linux 9 release on the same VM.
- Apply normal package updates and explore Ksplice on RHCK.
- Compare the final state with the baseline and return the VM to the instructor for cleanup.

### Prerequisites

- An instructor-assigned, running CentOS Stream 9 VM and either permission to create its boot-volume backup or instructor assistance with that step.
- A local computer with a browser and SSH client.
- An SSH key pair. Keep the private key on your computer.
- Outbound HTTPS from the VM to CentOS Stream repositories, `raw.githubusercontent.com`, `yum.oracle.com`, and `www.ksplice.com`.
- Approximately 50 GB of boot-volume capacity and an x86_64 VM shape such as `VM.Standard.E5.Flex` with 1 OCPU and 8 GB memory. Check regional capacity and tenancy limits before the event.

### Workshop path

```text
Instructor-prepared CentOS Stream 9 VM
        |
        v
CentOS Stream source VM -> Baseline, dry run, backup
        |
        v
In-place migration and reboot
        |
        v
Same VM on Oracle Linux 9 -> DNF, Ksplice, validation
```

CentOS Stream has no fixed minor release to preserve. The migration script therefore targets Oracle Linux **9 latest**. Do not substitute a fixed minor version. The exact Oracle Linux 9 update release shown after migration depends on what the Oracle repositories provide when you run the lab.

### Success criteria

- The same OCI VM reports `ID="ol"` after reboot.
- Its running kernel is provided by Oracle Linux.
- The Apache page still returns `MIGRATION_WORKLOAD_OK`.
- The final evidence has been compared with the CentOS baseline.
- The boot-volume backup is Available before the operating system is changed.

## Learn More

- [Oracle migration script and supported sources](https://github.com/oracle/migrate-to-ol/blob/main/README-migrate-to-oracle-linux.md)
- [Oracle Linux migration and mirror overview](https://blogs.oracle.com/scoter/announcing-oracle-linux-migration-mirror-automation-scripts)
- [CentOS Stream 9 cloud images](https://cloud.centos.org/centos/9-stream/x86_64/images/)
- [OCI custom Linux image import for instructors](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/importingcustomimagelinux.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, September 2026
