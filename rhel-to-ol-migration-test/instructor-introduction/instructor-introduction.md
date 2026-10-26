# Prepare the RHEL Migration Environment: Instructor Workshop

## Introduction

Prepare the reusable RHEL 9.8 Apache image before students begin the migration workshop. This instructor workshop contains two image-preparation labs and an OSMH setup guide. Students start a separate workshop at Student Lab 1, where they create their own VCN and launch a VM from your prepared image.

Obtain an authorized RHEL KVM guest image, import it into OCI, register and update the preparation VM, install Apache, remove registration, then capture and export the image. Supply the object-read PAR URL privately to authorized participants. This workshop does not provide a RHEL image or grant redistribution rights.

Estimated Workshop Time: 2 hours 30 minutes (150 minutes), including 10 minutes of orientation; additional provisioning and mirror synchronization time is required

### Objectives

- Obtain and import an authorized RHEL 9.8 x86_64 KVM guest image.
- Launch and update a preparation VM and install the workshop Apache application.
- Remove Red Hat registration and disable subscription-management plugins before image capture.
- Capture `rhel-9-8-apache-source`, export it in OCI format, and create an object-read PAR.
- Verify the student starting point and provide the image and OCI access instructions before class.
- Prepare a shared on-premises OSMH management station, mirrored content, and a separate Oracle Linux client per student for Student Labs 8 through 10.

### Prerequisites

- An authorized Red Hat account with RHEL 9.8 x86_64 KVM guest image access and Simple Content Access for package installation.
- Permission under your organization's process to use the prepared image for the intended participants.
- A preparation OCI environment with permissions for compartments, networking, private Object Storage, custom images, Compute, and object-read PARs.
- Quota and capacity for `VM.Standard.E5.Flex` with 1 OCPU and 8 GB memory, or a tested compatible x86_64 alternative, and a boot volume of at least 50 GB or the image minimum.
- A local computer with at least 15 GB of temporary free space, an SSH client, a browser, and a reliable image-upload connection.
- Assigned student OCI regions, compartments, permissions, and quotas. Students create their own destination networks in Student Lab 1.
- For Student Labs 8 through 10: on-premises Oracle Linux hosts, support entitlement, service permissions, and mirror capacity as specified in the OSMH Instructor Setup guide. Prepare and synchronize the shared station before class.

## Preparation Flow

```text
Authorized RHEL 9.8 KVM guest image
        |
        | Instructor Lab 1: download, upload, import QCOW2
        v
Custom image: rhel-9-8-migration-import
        |
        | Instructor Lab 2: launch rhel-9-8-source
        | register, update, install Apache, remove registration
        v
Prepared custom image: rhel-9-8-apache-source
        |
        | export OCI object and create object-read PAR
        v
Private instructor handoff to Student Lab 1
```

## Workshop Conventions

- Lab 1 and Lab 2 in this workshop refer to instructor labs. References prefixed with Student refer to the separate student workshop.
- The preparation VM is `rhel-9-8-source`. Students use `ol-migrate-rhel-source` in their destination environments.
- Keep the raw image, prepared image, export object, and PAR under instructor control.
- Protect Red Hat credentials, private keys, and PAR URLs. Hide them in screenshots and shared output.
- Remove Red Hat registration before capture. Students verify the cleanup and never register the destination VM.
- The preparation network does not supply student networking. Each student follows the complete network setup in Student Lab 1.
- Resource deletion is not required for instructor completion. Retain preparation and reusable assets until you decide they are no longer needed.

## Workshop Schedule

| Session | Minutes |
| --- | ---: |
| Orientation | 10 |
| Lab 1: Acquire and Import the RHEL Source Image | 45 |
| Lab 2: Prepare and Export the RHEL Application Image | 50 |
| OSMH Instructor Setup | 45 |
| **Total** | **150** |

Image transfers, updates, export, and test launches can require additional time. Complete preparation before the class begins.

## Student Handoff

Give each authorized student the following before the student workshop:

- The student workshop entry page and instructions to begin with Student Lab 1.
- A destination tenancy, region, assigned compartment or permission to create one, and the required networking, image-import, and Compute permissions and quota.
- The complete object-read PAR URL for `rhel-9-8-apache-source.oci`, with an expiration that covers the planned imports. Send it privately.
- Confirmation that the export contains RHEL 9.8 x86_64, an enabled and active Apache application serving `MIGRATION_WORKLOAD_OK`, no Red Hat registration certificates, and both subscription plugins disabled.
- The tested shape and sizing, plus a contact if the PAR expires or an import or capacity check fails.

Before teaching, follow Student Lab 1 with a permitted test account to verify that students can create a network, import the export, launch the image, and pass the application and registration checks. A successful export alone does not verify this student path.

## OSMH Readiness Before Class

Complete the [OSMH Instructor Setup guide](../osmh-instructor-setup/osmh-instructor-setup.md) before Student Lab 8. The control service runs in OCI; the station and managed clients run on premises. This is a separate environment from the OCI migration VM.

- Provide valid Oracle Linux Basic or Premier Support for the on-premises systems and a tenancy administrator for service permissions.
- Prepare one shared Oracle Linux 9 x86_64 station with at least four CPU cores, 16 GB RAM, and a separate, adequately sized mirror volume mounted at `/mnt/mirror`.
- Prepare one clean Oracle Linux 9 x86_64 client per student with SSH, sudo, and the same Apache test application. Do not clone registered agents.
- Register the station and finish BaseOS and AppStream mirror synchronization before class. Provide each student's agent RPM, private install key, station address and ports, profile suffix, service compartment, and maintenance-window plan.
- Pilot registration and an available update before class. Record no-update and blocked outcomes accurately. Retain all resources for review; no cleanup is part of these labs.

## Learn More

- [Student workshop](../workshops/tenancy/index.html)
- [Importing custom Linux images](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/importingcustomimagelinux.htm)
- [Exporting a custom image](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/custom-images-export.htm)
- [Object Storage pre-authenticated requests](https://docs.oracle.com/en-us/iaas/Content/Object/Tasks/usingpreauthenticatedrequests.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
