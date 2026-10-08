# Migrate Red Hat Enterprise Linux to Oracle Linux on OCI

## Introduction

This workshop takes you through a complete migration lifecycle in your own Oracle Cloud Infrastructure (OCI) tenancy. You launch an OCI-provided Red Hat Enterprise Linux (RHEL) 9.8 image, deploy a test workload, protect the boot volume, and convert that same VM in place to Oracle Linux 9.8.

The source uses OCI-hosted Red Hat Update Infrastructure (RHUI) for RHEL packages and updates. The direct-image path does not require an image download, custom-image import, or personal Red Hat subscription registration. Confirm image availability and usage charges in your selected region.

Estimated Workshop Time: 4 hours 45 minutes

### Objectives

In this workshop, you will:

- Prepare the workshop compartment, VCN, public subnet, and security rules before Compute launch.
- Select an OCI-provided RHEL 9.8 image during launch and record its name and OCID.
- Launch, verify RHUI access, prepare, and baseline an actual RHEL source VM.
- Deploy a small Apache workload and capture migration evidence.
- Assess migration readiness and create a boot-volume recovery point.
- Convert RHEL 9.8 to Oracle Linux 9.8 within the same VM.
- Review Oracle Linux repositories and apply standard package updates.
- Apply Oracle Ksplice live updates to the RHCK kernel without rebooting.
- Register the migrated instance with OS Management Hub, inspect package inventory, and manage package jobs and maintenance schedules.
- Validate the application, packages, services, networking, SELinux, and firewall configuration.
- Remove the OCI resources and OS Management Hub registration created for the workshop.

### Prerequisites

This workshop requires:

- A paid OCI tenancy where you can create networking, Compute instances, and boot-volume backups.
- Administrator assistance to configure OS Management Hub IAM access for the workshop compartment during Lab 7, including access to vendor software sources in the root compartment.
- Access to an OCI-provided RHEL 9.8 x86_64 image in your selected region.
- Internet connectivity and outbound access from the VM to RHUI repositories and Oracle services.
- A modern browser and an SSH client.
- Sufficient OCI quota for one `VM.Standard.E5.Flex` VM with 1 OCPU, 12 GB RAM, and a 64 GB boot volume, or a tested compatible alternative. Use a larger boot volume if your image requires it.

> **Important:** Do not paste SSH private keys, OCI credentials, or other secrets into workshop fields, screenshots, or shared terminals.

## Key Terms

- **Compartment:** An OCI container used to organize resources and control access to them.
- **Platform image:** An OCI-provided operating-system image used to launch a Compute instance.
- **RHUI:** Red Hat Update Infrastructure, which provides RHEL packages and updates for this source.
- **Virtual cloud network (VCN):** A private network that you create inside OCI.
- **Subnet:** A smaller address range inside a VCN where OCI resources connect.
- **Boot volume:** The virtual disk that contains the operating system used to start a Compute instance.
- **In-place migration:** Changing the operating system on the existing VM instead of creating a replacement VM.
- **Checkpoint:** A required set of successful checks that you complete before continuing.
- **OS Management Hub:** An OCI service used to manage operating-system package inventory and update jobs.
- **Registration profile:** A configuration that assigns software sources when an instance registers with OS Management Hub.

## Workshop Architecture

You will create the following flow:

```text
Workshop compartment, VCN, public subnet, and security rules
        |
        v
OCI-provided RHEL 9.8 image
        |
        | Direct launch and RHUI repository access
        v
RHEL source VM
        |
        | Baseline, backup, dry run, in-place migration
        v
Same VM running Oracle Linux 9.8
        |
        | DNF maintenance and Ksplice live patching
        v
OS Management Hub registration, inventory, and package jobs
        |
        | Validation and management-resource removal
        v
Cleanup
```

## Workshop Conventions

- Resource names use the prefix `ol-migrate`.
- Run Linux commands using the SSH account that worked in Lab 2, normally `cloud-user`, unless a step explicitly uses `sudo`.
- Replace values enclosed in angle brackets, such as `<public-ip>`, with values from your environment.
- Complete each required checkpoint before continuing to operations that modify the system.
- A successful migration requires application and system evidence, not only a changed `/etc/os-release` file.

## Source Setup Validation

The direct launch and Apache workload were demonstrated on `rhel-oci-test` in US East (Ashburn), using image `Red-Hat-Enterprise-Linux-9.8-2026.09.15-8`, `VM.Standard.E5.Flex`, 1 OCPU, and 12 GB RAM. The selected image displayed a 64 GB boot volume. The application returned `MIGRATION_WORKLOAD_OK` without personal Red Hat subscription registration.

This confirms source setup and application deployment. The pinned script dry run, RHUI-to-Oracle Linux conversion, post-migration management, and billing behavior still require validation for this image. Confirm the estimated duration with an end-to-end rehearsal.

## Learn More

- [OCI platform images](https://docs.oracle.com/en-us/iaas/Content/Compute/References/images.htm)
- [Red Hat Update Infrastructure](https://access.redhat.com/products/red-hat-update-infrastructure/)
- [RHEL 9.8 Release Notes](https://docs.redhat.com/en/documentation/red_hat_enterprise_linux/9/html-single/9.8_release_notes/index)
- [Oracle Linux](https://www.oracle.com/linux/)
- [OS Management Hub prerequisites](https://docs.oracle.com/en-us/iaas/osmh/doc/getstarted.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
