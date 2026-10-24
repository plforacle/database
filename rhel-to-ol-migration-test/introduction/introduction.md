# Migrate Red Hat Enterprise Linux to Oracle Linux on OCI

## Introduction

This workshop demonstrates an in-place conversion from Red Hat Enterprise Linux (RHEL) 9.8 to Oracle Linux 9.8 while preserving a working Apache/HTML application.

Labs 1 and 2 prepare the reusable source image. The image owner imports an authorized RHEL KVM guest image, registers and updates the preparation VM, installs Apache, and removes Red Hat registration before capturing `rhel-9-8-apache-source`. The prepared image is exported and imported into the destination OCI tenancy.

Labs 3 through 5 run on a new instance launched from that prepared image. Learners capture the destination baseline, assess readiness, convert the instance, reboot, and compare the application and system results. They do not register the destination VM with Red Hat. If your image owner has already supplied the prepared image, follow Lab 2 Tasks 9 and 10, then start Lab 3.

The workshop does not provide or redistribute RHEL images. The image owner must obtain and use the source software through an authorized account or organizational process.

Estimated Workshop Time: 4 to 5 hours including preparation and image transfers; 1 hour 30 minutes to 2 hours for Labs 3 through 5

### Objectives

In this workshop, you will:

- Import an authorized RHEL 9.8 KVM guest image into OCI.
- Prepare RHEL with a working Apache/HTML application.
- Remove registration and capture a reusable custom image.
- Export and import the prepared image into the destination tenancy.
- Capture a destination baseline and assess migration readiness.
- Convert the destination VM to Oracle Linux 9.8 with RHCK.
- Validate the application, packages, services, networking, SELinux, and firewall.
- Save evidence and choose whether to retain or delete temporary resources.

### Prerequisites

This workshop requires:

- Access to preparation and destination OCI tenancies or authorized environments, with permission to create networking, Object Storage objects and object-read pre-authenticated requests, custom images, and Compute instances.
- For Labs 1 and 2 preparation: an authorized Red Hat account with RHEL 9.8 x86_64 KVM guest image access and Simple Content Access for package installation.
- For the destination workflow: the prepared `rhel-9-8-apache-source` image or a valid object-read PAR URL supplied by the image owner. No Red Hat credentials are required in the destination tenancy.
- For image preparation: a local computer with at least 15 GB of temporary free space and a reliable connection for image upload.
- A modern browser and an SSH client.
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
        | Import and launch in destination tenancy
        v
Destination VM: ol-migrate-rhel-source
        |
        | Lab 3: baseline, assessment, dry run, optional backup
        | Lab 4: conversion, reboot, health checks
        v
Same destination VM running Oracle Linux 9.8 and Apache
        |
        | Lab 5: compare evidence, review exceptions
        v
Retain for further work or optionally delete temporary resources
```

## Workshop Conventions

- The raw imported image is `rhel-9-8-migration-import`; the reusable prepared image is `rhel-9-8-apache-source`.
- The preparation VM is `rhel-9-8-source`; the destination VM is `ol-migrate-rhel-source`.
- In Labs 3 through 5, `<public-ip>` always means the destination VM public IP.
- Keep PAR URLs private and use your own values rather than copying a URL from a screenshot.
- Run Linux commands as `cloud-user` unless a step explicitly uses `sudo`.
- Replace values enclosed in angle brackets, such as `<public-ip>`, with values from your environment.
- Commands that change the operating system appear only after a required checkpoint.
- A successful migration requires application and system evidence, not only a changed `/etc/os-release` file.

## Learn More

- [Importing Custom Linux Images](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/importingcustomimagelinux.htm)
- [Bring Your Own Image](https://docs.oracle.com/en-us/iaas/Content/Compute/References/bringyourownimage.htm)
- [RHEL 9.8 Release Notes](https://docs.redhat.com/en/documentation/red_hat_enterprise_linux/9/html-single/9.8_release_notes/index)
- [Oracle Linux](https://www.oracle.com/linux/)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026

