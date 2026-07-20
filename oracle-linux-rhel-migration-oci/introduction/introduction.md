# Migrate Red Hat Enterprise Linux to Oracle Linux on OCI

## Introduction

This workshop takes you through a complete migration lifecycle in your own Oracle Cloud Infrastructure (OCI) tenancy. You begin with an authorized Red Hat Enterprise Linux (RHEL) 9.8 KVM guest image, import it into OCI, launch an actual RHEL source VM, deploy a test workload, protect the boot volume, and convert that same VM in place to Oracle Linux 9.8.

The workshop does not provide or redistribute a RHEL image. You download the image through your own Red Hat account and use your own subscription entitlement.

Estimated Workshop Time: 4 hours 30 minutes

### Objectives

In this workshop, you will:

- Import a RHEL 9.8 KVM guest image into OCI as a custom image.
- Launch, register, update, and baseline an actual RHEL source VM.
- Deploy a small Apache workload and capture migration evidence.
- Assess migration readiness and create a boot-volume recovery point.
- Convert RHEL 9.8 to Oracle Linux 9.8 within the same VM.
- Configure Oracle Linux management and examine Ksplice capabilities.
- Validate the application, packages, services, networking, SELinux, and firewall configuration.
- Remove the OCI resources and Red Hat registration created for the workshop.

### Prerequisites

This workshop requires:

- An OCI tenancy where you can create networking, Object Storage, custom images, Compute instances, boot-volume backups, IAM policies, and OS Management Hub resources.
- A Red Hat account with access to the RHEL 9.8 x86_64 KVM guest image and a valid RHEL entitlement.
- A local computer with at least 15 GB of temporary free space.
- A reliable internet connection for a multi-gigabyte download and upload.
- A modern browser and an SSH client.
- Sufficient OCI quota for one `VM.Standard.E5.Flex` VM with 1 OCPU and 8 GB of memory, or a tested compatible alternative.

> **Important:** Do not paste Red Hat passwords, activation keys, OCI private keys, or other secrets into workshop fields, screenshots, or shared terminals.

## Workshop Architecture

You will create the following flow:

```text
Red Hat Customer Portal
        |
        | RHEL 9.8 KVM guest QCOW2
        v
OCI Object Storage bucket
        |
        | Custom image import
        v
RHEL source VM
        |
        | Baseline, backup, dry run, in-place migration
        v
Same VM running Oracle Linux 9.8
        |
        | Management, Ksplice, and validation
        v
Cleanup
```

## Workshop Conventions

- Resource names use the prefix `ol-migrate`.
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
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, July 2026

