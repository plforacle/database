# Migrate Red Hat Enterprise Linux to Oracle Linux: Student Workshop

## Introduction

Convert a prepared RHEL 9.8 VM to Oracle Linux 9.8 while preserving a working Apache/HTML application. Begin with Student Lab 1 and complete the labs in order.

Your instructor prepares the source image in a separate instructor workshop and supplies its object-read PAR URL. You create the destination network and VM yourself. You do not need a Red Hat account or access to the preparation tenancy.

Lab 1 builds the network, imports the prepared image, launches the VM, and verifies the application and registration cleanup. Lab 2 introduces everyday Linux commands. Labs 3 through 5 capture the baseline, assess readiness, convert the same VM, reboot, and validate the result.

Labs 6 and 7 explore UEK kernel choice and Ksplice live patching when supported updates and access are available. Labs 8 through 12 cover package maintenance, Apache troubleshooting, application security, backup and restore, and automated health checks. Keep the VM through the final lab.

Estimated Workshop Time: 6 hours 45 minutes (405 minutes), including 15 minutes of orientation

### Objectives

- Create the destination network and import an instructor-provided RHEL application image.
- Launch and verify a RHEL VM without registering it with Red Hat.
- Practice Linux commands and capture a migration baseline.
- Convert the VM to Oracle Linux 9.8 with RHCK and verify application continuity.
- Compare evidence and review package exceptions.
- Explore UEK and supported Ksplice patching.
- Practice package, Apache, security, backup, restore, and automation tasks.
- Preserve evidence and decide whether to retain your student resources after the workshop.

### Prerequisites

- An OCI tenancy, an assigned region and compartment, and permissions to create networking, import custom images, and launch Compute instances. Lab 1 includes compartment creation if permitted.
- A valid object-read PAR URL for the authorized `rhel-9-8-apache-source` OCI export from your instructor.
- A browser and SSH client, plus an SSH key pair or permission to generate one in the launch wizard.
- Quota and regional capacity for `VM.Standard.E5.Flex` with 1 OCPU and at least 8 GB memory, or an instructor-tested compatible x86_64 alternative. The boot volume must be at least 50 GB or the image minimum.
- For Ksplice: a maintained Oracle kernel, service connectivity, and eligible access. The OCI BYOI path uses Oracle's OCI installer without ULN registration. Record any access or support failure as unresolved.
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
        | Lab 3: baseline, assessment, dry run, recovery choice
        | Lab 4: conversion to Oracle Linux RHCK and reboot
        | Lab 5: compare evidence and review exceptions
        v
Same VM running Oracle Linux 9.8 with a working application
        |
        | Labs 6-7: UEK and Ksplice
        | Labs 8-12: daily administration and application practice
        v
Save evidence and retain resources or choose optional student cleanup
```

## Workshop Conventions

- Lab numbers throughout this workshop refer to student labs.
- The prepared custom image is `rhel-9-8-apache-source`; the destination VM is `ol-migrate-rhel-source`.
- `<public-ip>` always means your student VM's public IP. Replace angle-bracket placeholders with your values.
- Use the same region and compartment throughout. Use unique resource names when sharing a compartment.
- Run Linux commands as `cloud-user` unless a step uses `sudo`.
- Keep PAR URLs private; never copy a URL from a screenshot.
- Verify registration cleanup in Lab 1. Do not register the destination VM with Red Hat.
- Complete each lab's starting checks. Lab 2 confines file practice to your home directory; Labs 6 through 12 change the migrated system after evidence is preserved.
- Complete every recovery step after a controlled fault, even if you stop a lab early.
- Preserve the original application page and migration archives. Use separate practice pages, reports, and backups later.
- Independent challenges use the same completion checks as the guided path.
- A successful migration requires application and system evidence, including final package review.
- Resource cleanup is optional at the end of Lab 12 and applies only to your student resources. Instructor images, export objects, PARs, and preparation resources remain under instructor control.

## Workshop Schedule

The agenda totals 405 minutes. These are planning estimates. Breaks, optional backup or cleanup, and unusually long image transfers require additional time.

| Session | Minutes |
| --- | ---: |
| Orientation | 15 |
| Lab 1: Build and Verify the Migration Environment | 45 |
| Lab 2: Linux Command-Line Quickstart | 60 |
| Lab 3: Assess Readiness and Protect the Source | 30 |
| Lab 4: Migrate to Oracle Linux | 40 |
| Lab 5: Validate the Migration | 25 |
| Lab 6: Explore Oracle Linux Kernel Choices | 30 |
| Lab 7: Explore Rebootless Patching with Ksplice | 30 |
| Lab 8: Maintain Oracle Linux Packages | 25 |
| Lab 9: Configure and Troubleshoot Apache | 25 |
| Lab 10: Manage Application Security | 30 |
| Lab 11: Back Up and Restore the Application | 20 |
| Lab 12: Automate Application Health Checks | 30 |
| **Total** | **405** |

## Learn More

- [Creating a VCN with internet connectivity](https://docs.oracle.com/en-us/iaas/Content/Network/Tasks/quickstartnetworking.htm)
- [Importing a custom image](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/custom-images-import.htm)
- [Oracle Linux](https://www.oracle.com/linux/)
- [Oracle Linux kernel choices](https://docs.oracle.com/en/operating-systems/oracle-linux/9/boot/oracle_linux9_kernel_version_matrix.html)
- [Ksplice availability](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-AboutOracleKsplice.html)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
