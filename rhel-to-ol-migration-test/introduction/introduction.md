# Migrate Red Hat Enterprise Linux to Oracle Linux: Student Workshop

## Introduction

Learn how to migrate on-premises systems from Red Hat Enterprise Linux to Oracle Linux and manage them after migration. The seven labs use one OCI-hosted practice VM to convert RHEL 9.8 to Oracle Linux 9.8, preserve Apache, apply Ksplice patches, explore kernel choices, and begin managing updates with OS Management Hub (OSMH). Begin with Student Lab 1 and complete the labs in order.

Your instructor prepares the source image in a separate instructor workshop and supplies its object-read PAR URL. You create the destination network and VM yourself. You do not need a Red Hat account or access to the preparation tenancy.

Lab 1 builds the network, imports the prepared image, launches the VM, and verifies the application and registration cleanup. Lab 2 introduces everyday Linux commands. Lab 3 assesses readiness and explains the migration dry run. Lab 4 converts the same VM, reviews migration reports, reboots, and verifies Oracle Linux and the application.

Lab 5 demonstrates Ksplice live patching on the migrated RHCK. Lab 6 then installs UEK and explores kernel choice. The instructor verifies patch availability before class. Lab 7 registers the same migrated VM with OSMH, reviews its inventory, and applies an available package update through the OCI Console.

Estimated Workshop Time: 5 hours

### Objectives

- Create the destination network and import an instructor-provided RHEL application image.
- Launch and verify a RHEL VM without registering it with Red Hat.
- Practice Linux commands and assess migration readiness.
- Convert the VM to Oracle Linux 9.8 with RHCK and verify application continuity.
- Review the migration reports and package exceptions.
- Apply supported Ksplice patches on RHCK, then install and explore UEK.
- Register the migrated VM with OSMH, inspect its inventory, and apply an available package update.
- Keep the migration, patching, kernel, and management results for review.

### Prerequisites

- An OCI tenancy, an assigned region and compartment, and permissions to create networking, import custom images, and launch Compute instances. Lab 1 includes compartment creation if permitted.
- A valid object-read PAR URL for the authorized `rhel-9-8-apache-source` OCI export from your instructor.
- A browser and SSH client, plus an SSH key pair or permission to generate one in the launch wizard.
- Quota and regional capacity for `VM.Standard.E5.Flex` with 1 OCPU and at least 8 GB memory, or an instructor-tested compatible x86_64 alternative. The boot volume must be at least 50 GB or the image minimum.
- For Ksplice: a maintained Oracle kernel, service connectivity, and eligible access. The OCI BYOI path uses Oracle's OCI installer without ULN registration. Record any access or support failure as unresolved.
- For Lab 7: Console permissions to manage OSMH resources, software sources, and the VM's agent plugins. Policy Advisor also requires IAM permissions for groups, dynamic groups, and policies. Arrange administrator assistance if those permissions are unavailable.
- Boot-volume backup permission only if you choose the optional backup in Lab 3. Otherwise, retain the prepared image for rebuilding the disposable VM; rebuilding loses changes made after image capture.

> **Important:** Keep PAR URLs and SSH private keys private. Hide them in screenshots and shared output. The instructor supplies the prepared image through an authorized organizational process.

## Workshop Architecture

```text
Instructor-provided prepared RHEL image and object-read PAR URL
        |
        | Lab 1: create VCN, import image, launch and verify VM
        v
Student VM: ol-migrate-rhel-source, running RHEL 9.8 and Apache
        |
        | Lab 2: Linux quickstart
        | Lab 3: readiness, script verification, dry run, optional backup
        | Lab 4: conversion to Oracle Linux RHCK and reboot
        v
Same VM running Oracle Linux 9.8 with a working application
        |
        | Lab 5: Ksplice live patching on RHCK
        | Lab 6: install and boot UEK
        v
Same migrated VM, now running UEK
        |
        | Lab 7: OSMH setup, registration, inventory, and package update
        v
Retain the VM, application, and workshop results for review
```

## Workshop Conventions

- Lab numbers throughout this workshop refer to student labs.
- The prepared custom image is `rhel-9-8-apache-source`; the destination VM is `ol-migrate-rhel-source`.
- `<public-ip>` means the public IP of the same workshop VM throughout all seven labs.
- Use the same region and VM compartment throughout.
- Run Linux commands as `cloud-user`, using sudo where shown. Confirm the hostname before running installation commands.
- Keep PAR URLs private; never copy a URL from a screenshot.
- Verify registration cleanup in Lab 1. Do not register the destination VM with Red Hat.
- Complete each lab's starting checks. Lab 2 confines file practice to your home directory; Labs 5 and 6 change the migrated system after the migration and reboot checks.
- Complete every recovery step after a controlled fault, even if you stop a lab early.
- Preserve the original application page and the migration reports written by the script.
- Independent challenges use the same completion checks as the guided path.
- Complete the OS, kernel, report, and webpage checks in Lab 4 before the kernel and patching exercises.
- Keep the VM, networking, images, export objects, PARs, OSMH profile, and results for review. Cleanup is outside this workshop and occurs only when requested by the workshop owner.

## Workshop Schedule

The agenda totals 290 minutes of planned student activity. Transfers, optional backup, patch downloads, reboots, agent registration, and update jobs can require additional elapsed time.

| Session | Minutes |
| --- | ---: |
| Orientation | 15 |
| Lab 1: Build and Verify the Migration Environment | 45 |
| Lab 2: Linux Command-Line Quickstart | 60 |
| Lab 3: Assess Readiness and Protect the Source | 30 |
| Lab 4: Migrate to Oracle Linux | 40 |
| Lab 5: Explore Rebootless Patching with Ksplice | 30 |
| Lab 6: Explore Oracle Linux Kernel Choices | 30 |
| Lab 7: OS Management Hub Quickstart on OCI | 40 |
| **Total** | **290** |

## Learn More

- [Monitor and Update Oracle Linux with Oracle OS Management Hub](https://docs.oracle.com/en/learn/osmh-monitor/index.html): a separate introduction to ongoing OS management, with its own environment and prerequisites.

- [Creating a VCN with internet connectivity](https://docs.oracle.com/en-us/iaas/Content/Network/Tasks/quickstartnetworking.htm)
- [Importing a custom image](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/custom-images-import.htm)
- [Oracle Linux](https://www.oracle.com/linux/)
- [Oracle Linux kernel choices](https://docs.oracle.com/en/operating-systems/oracle-linux/9/boot/oracle_linux9_kernel_version_matrix.html)
- [Ksplice availability](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-AboutOracleKsplice.html)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
