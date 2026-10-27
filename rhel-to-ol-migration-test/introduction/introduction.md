# Migrate Red Hat Enterprise Linux to Oracle Linux: Student Workshop

## Introduction

Learn how to migrate on-premises systems from Red Hat Enterprise Linux to Oracle Linux and manage them after migration. Labs 1 through 7 use OCI-hosted practice infrastructure to convert a prepared RHEL 9.8 VM to Oracle Linux 9.8 while preserving Apache. Labs 8 through 10 use an instructor-provided on-premises Oracle Linux client and management station for OS Management Hub. Begin with Student Lab 1 and complete the labs in order.

Your instructor prepares the source image in a separate instructor workshop and supplies its object-read PAR URL. You create the destination network and VM yourself. You do not need a Red Hat account or access to the preparation tenancy.

Lab 1 builds the network, imports the prepared image, launches the VM, and verifies the application and registration cleanup. Lab 2 introduces everyday Linux commands. Labs 3 through 5 capture the baseline, assess readiness, convert the same VM, reboot, and validate the result.

Labs 6 and 7 explore UEK kernel choice and Ksplice live patching when supported updates and access are available. Labs 8 through 10 introduce OS Management Hub: verify the shared on-premises station, register an assigned on-premises client, and manage updates. Retain the OCI migration VM and its evidence while working on the separate OSMH client.

Estimated Workshop Time: 6 hours (360 minutes), including 15 minutes of orientation; additional time may be needed for provisioning, mirror synchronization, and jobs

### Objectives

- Create the destination network and import an instructor-provided RHEL application image.
- Launch and verify a RHEL VM without registering it with Red Hat.
- Practice Linux commands and capture a migration baseline.
- Convert the VM to Oracle Linux 9.8 with RHCK and verify application continuity.
- Compare evidence and review package exceptions.
- Explore UEK and supported Ksplice patching.
- Register an on-premises Oracle Linux client with OS Management Hub through a local management station.
- Apply an available package update, plan a maintenance window, and verify the application and job results.
- Preserve migration and OSMH evidence separately and retain the lab resources for review.

### Prerequisites

- An OCI tenancy, an assigned region and compartment, and permissions to create networking, import custom images, and launch Compute instances. Lab 1 includes compartment creation if permitted.
- A valid object-read PAR URL for the authorized `rhel-9-8-apache-source` OCI export from your instructor.
- A browser and SSH client, plus an SSH key pair or permission to generate one in the launch wizard.
- Quota and regional capacity for `VM.Standard.E5.Flex` with 1 OCPU and at least 8 GB memory, or an instructor-tested compatible x86_64 alternative. The boot volume must be at least 50 GB or the image minimum.
- For Ksplice: a maintained Oracle kernel, service connectivity, and eligible access. The OCI BYOI path uses Oracle's OCI installer without ULN registration. Record any access or support failure as unresolved.
- Boot-volume backup permission only if you choose the optional backup in Lab 3. Otherwise, retain the prepared image for rebuilding the disposable VM; rebuilding loses changes made after image capture.
- For Labs 8 through 10: an assigned on-premises Oracle Linux 9 x86_64 client with SSH, sudo, and the Apache test application; a separate registered management station with synchronized BaseOS and AppStream mirrors; and valid Oracle Linux Basic or Premier Support for these on-premises systems.
- OSMH user permissions, a private Management Agent install key, a current Linux x86_64 agent RPM, station connection details, and access to the assigned OCI service region and compartment. The instructor prepares IAM and the station before class.

The additional OSMH hosts are real on-premises systems, separate from the OCI migration VM. The control service is in OCI. OCI instances use Oracle Cloud Agent and are not registered through the on-premises procedure in these labs.

> **Important:** Keep PAR URLs, SSH private keys, and Management Agent install keys private. Hide them in screenshots and shared output. The instructor supplies the prepared image through an authorized organizational process.

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
        v
Preserve the migration VM and its evidence

Separate on-premises Oracle Linux client with the Apache test application
        |
        | Lab 8: verify shared station, sources, and connectivity
        | Lab 9: register the client with OS Management Hub
        | Lab 10: apply updates, plan maintenance, verify the application
        v
Retain OSMH reports and migration evidence as distinct results
```

## Workshop Conventions

- Lab numbers throughout this workshop refer to student labs.
- The prepared custom image is `rhel-9-8-apache-source`; the destination VM is `ol-migrate-rhel-source`.
- In Labs 1 through 7, `<public-ip>` means the OCI migration VM public IP. In Labs 8 through 10, use the on-premises client and station addresses assigned by the instructor.
- Use the same region and compartment for the migration exercises. For OSMH, use the assigned service region and compartment. Use unique profile names with your student suffix.
- Run migration commands as `cloud-user`. Use the assigned sudo-capable account on the on-premises OSMH client. Confirm the hostname before running installation commands.
- Keep PAR URLs private; never copy a URL from a screenshot.
- Verify registration cleanup in Lab 1. Do not register the destination VM with Red Hat.
- Complete each lab's starting checks. Lab 2 confines file practice to your home directory; Labs 6 and 7 change the migrated system after evidence is preserved. Labs 8 through 10 use the separate on-premises client.
- Complete every recovery step after a controlled fault, even if you stop a lab early.
- Preserve the original application page and migration archives. Keep the OSMH client reports separate; they do not establish that the OCI migration VM was registered or patched through OSMH.
- Independent challenges use the same completion checks as the guided path.
- A successful migration requires application and system evidence, including final package review.
- Keep the migration VM, on-premises client, shared station, profiles, keys, images, export objects, and PARs for review. Cleanup is outside this workshop and occurs only when requested by the workshop owner.

## Workshop Schedule

The agenda totals 360 minutes of planned student activity. The instructor provisions the OSMH hosts and completes initial mirror synchronization before class. Transfers, optional backup, and update-job execution can require additional elapsed time.

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
| Lab 8: Prepare OS Management Hub for On-Premises Management | 25 |
| Lab 9: Register an On-Premises Oracle Linux Client | 30 |
| Lab 10: Patch and Verify Oracle Linux with OS Management Hub | 30 |
| **Total** | **360** |

## Learn More

- [OSMH supported environments and prerequisites](https://docs.oracle.com/en-us/iaas/osmh/doc/getstarted.htm)
- [On-premises management station architecture](https://docs.oracle.com/en-us/iaas/osmh/doc/understand-management-stations.htm)

- [Creating a VCN with internet connectivity](https://docs.oracle.com/en-us/iaas/Content/Network/Tasks/quickstartnetworking.htm)
- [Importing a custom image](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/custom-images-import.htm)
- [Oracle Linux](https://www.oracle.com/linux/)
- [Oracle Linux kernel choices](https://docs.oracle.com/en/operating-systems/oracle-linux/9/boot/oracle_linux9_kernel_version_matrix.html)
- [Ksplice availability](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-AboutOracleKsplice.html)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
