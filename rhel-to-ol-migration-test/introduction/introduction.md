# Migrate Red Hat Enterprise Linux to Oracle Linux on OCI

## Introduction

This workshop demonstrates an in-place conversion from Red Hat Enterprise Linux (RHEL) 9.8 to Oracle Linux 9.8 while preserving a working Apache/HTML application.

Labs 1 and 2 prepare the reusable source image. The image owner imports an authorized RHEL KVM guest image, registers and updates the preparation VM, installs Apache, and removes Red Hat registration before capturing `rhel-9-8-apache-source`. Lab 2 ends with the prepared export and its object-read PAR URL. Lab 3 imports that image into the destination tenancy and verifies a new instance.

Lab 4 introduces everyday Linux commands on the prepared RHEL destination VM. Labs 5 through 7 capture the baseline, assess readiness, convert the instance, reboot, and validate the result. The destination VM is not registered with Red Hat. If your image owner supplies the prepared image, begin Lab 3, then follow the Lab 4 quickstart; the full eight-hour agenda includes the image-preparation work.

Labs 8 and 9 demonstrate Oracle Linux benefits after the RHCK conversion is validated: kernel choice with UEK, followed by Ksplice live patching when supported updates and access are available. The kernel switch requires a reboot; Ksplice observations distinguish subsequent live patching from that boot change.

Labs 10 through 14 develop daily administration skills on the migrated Oracle Linux instance: package maintenance, Apache troubleshooting, application security, backup and restore, and automated health checks. These skill exercises use terminal commands and standard Linux tools. Keep the instance through all labs; optional resource cleanup is at the end of Lab 14.

The workshop does not provide or redistribute RHEL images. The image owner must obtain and use the source software through an authorized account or organizational process.

Estimated Workshop Time: 8 hours (480 minutes), including 15 minutes of orientation

### Objectives

In this workshop, you will:

- Import an authorized RHEL 9.8 KVM guest image into OCI.
- Prepare RHEL with a working Apache/HTML application.
- Remove registration and capture a reusable custom image.
- Export and import the prepared image into the destination tenancy.
- Capture a destination baseline and assess migration readiness.
- Convert the destination VM to Oracle Linux 9.8 with RHCK.
- Validate the application, packages, services, networking, SELinux, and firewall.
- Practice everyday Linux commands with guided exercises and independent challenges.
- Explore UEK after validating the compatibility-focused RHCK migration.
- Evaluate supported rebootless kernel patching with Ksplice and record the actual outcome.
- Maintain Oracle packages and troubleshoot Apache.
- Configure file permissions, SELinux labels, and firewall rules.
- Back up and restore the application and automate health checks.
- Save evidence and choose whether to retain or delete temporary resources at the end.

### Prerequisites

This workshop requires:

- Access to preparation and destination OCI tenancies or authorized environments, with permission to create networking, Object Storage objects and object-read pre-authenticated requests, custom images, and Compute instances.
- For Labs 1 and 2 preparation: an authorized Red Hat account with RHEL 9.8 x86_64 KVM guest image access and Simple Content Access for package installation.
- For Lab 3 and the destination workflow: the prepared `rhel-9-8-apache-source` image or a valid object-read PAR URL supplied by the image owner. No Red Hat credentials are required in the destination tenancy.
- For image preparation: a local computer with at least 15 GB of temporary free space and a reliable connection for image upload.
- A modern browser and an SSH client.
- For Ksplice: a currently maintained Oracle kernel, network access to the Ksplice service, and eligible access. The OCI BYOI lab path uses Oracle's OCI installer without ULN registration. Outside OCI, arrange the required Oracle Linux support entitlement and use the corresponding documented installation path. An access/support failure must be recorded rather than treated as successful patching.
- Sufficient quota for a `VM.Standard.E5.Flex` VM with 1 OCPU and 8 GB of memory in each environment used, or a tested compatible alternative.
- Permission to create boot-volume backups only if you choose the optional backup task. Without a backup, this disposable practice VM can be rebuilt from the prepared image; work added after image capture will be lost.

> **Important:** Do not paste Red Hat passwords, activation keys, OCI private keys, or other secrets into workshop fields, screenshots, or shared terminals.

## Workshop Architecture

You will create the following flow:

```text
Authorized RHEL 9.8 KVM guest image
        |
        | Labs 1-2: import, register, update, deploy Apache
        v
RHEL preparation VM: rhel-9-8-source
        |
        | Unregister, clean subscription data, capture and export
        v
Reusable image: rhel-9-8-apache-source
        |
        | Lab 3: import and launch in destination tenancy
        v
Destination VM: ol-migrate-rhel-source
        |
        | Lab 4: Linux quickstart
        | Lab 5: baseline, assessment, dry run, optional backup
        | Lab 6: conversion, reboot, health checks
        v
Same destination VM running Oracle Linux 9.8 and Apache
        |
        | Lab 7: compare evidence, review exceptions
        | Labs 8-9: UEK kernel choice and Ksplice live patching
        | Labs 10-14: packages, Apache, security, recovery, automation
        v
Retain for further work or optionally delete temporary resources
```

## Workshop Conventions

- The raw imported image is `rhel-9-8-migration-import`; the reusable prepared image is `rhel-9-8-apache-source`.
- The preparation VM is `rhel-9-8-source`; the destination VM is `ol-migrate-rhel-source`.
- In Labs 4 through 14, `<public-ip>` always means the destination VM public IP.
- Keep PAR URLs private and use your own values rather than copying a URL from a screenshot.
- Run Linux commands as `cloud-user` unless a step explicitly uses `sudo`.
- Replace values enclosed in angle brackets, such as `<public-ip>`, with values from your environment.
- Complete each lab's starting checks before its changes. Lab 4 confines practice file changes to your home directory; Labs 8 through 14 change the migrated system after evidence is preserved.
- Complete every recovery step after a controlled fault, even if you choose to stop a lab early.
- Each new lab offers a guided path and an independent challenge. Experienced learners may use the challenge within the same time budget, but must pass the same completion checks.
- The original migration page and its evidence archives remain unchanged during later practice; use the separate practice pages and reports.
- A successful migration requires application and system evidence, not only a changed `/etc/os-release` file.

## Workshop Schedule

The learning agenda totals 480 minutes. Lab times include guided practice, investigation, independent challenges, and result review. They are planning estimates, not measured completion guarantees. Breaks, optional backup/cleanup, and unusually long image transfers require additional elapsed time.

| Session | Minutes |
| --- | ---: |
| Orientation | 15 |
| Lab 1: Acquire and Import the RHEL Source Image | 45 |
| Lab 2: Prepare and Export the RHEL Application Image | 50 |
| Lab 3: Import and Verify the RHEL Application Image | 25 |
| Lab 4: Linux Command-Line Quickstart | 60 |
| Lab 5: Assess Readiness and Protect the Source | 30 |
| Lab 6: Migrate to Oracle Linux | 40 |
| Lab 7: Validate the Migration | 25 |
| Lab 8: Explore Oracle Linux Kernel Choices | 30 |
| Lab 9: Explore Rebootless Patching with Ksplice | 30 |
| Lab 10: Maintain Oracle Linux Packages | 25 |
| Lab 11: Configure and Troubleshoot Apache | 25 |
| Lab 12: Manage Application Security | 30 |
| Lab 13: Back Up and Restore the Application | 20 |
| Lab 14: Automate Application Health Checks | 30 |
| **Total** | **480** |

## Learn More

- [Importing Custom Linux Images](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/importingcustomimagelinux.htm)
- [Bring Your Own Image](https://docs.oracle.com/en-us/iaas/Content/Compute/References/bringyourownimage.htm)
- [RHEL 9.8 Release Notes](https://docs.redhat.com/en/documentation/red_hat_enterprise_linux/9/html-single/9.8_release_notes/index)
- [Oracle Linux](https://www.oracle.com/linux/)
- [Oracle Linux kernel choices](https://docs.oracle.com/en/operating-systems/oracle-linux/9/boot/oracle_linux9_kernel_version_matrix.html)
- [Ksplice availability](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-AboutOracleKsplice.html)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026

