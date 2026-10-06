# Instructor Guide: Prepare the CentOS Stream 9 OCI Environment

## Introduction

This guide is for instructors and tenancy administrators, not participants. Prepare one verified CentOS Stream 9 custom image per region, then launch a separate VM for each participant. Give participants only their assigned VM details. The published workshop begins with connecting to that running VM.

Estimated Lab Time: 90 minutes for initial image import, plus launch time per participant

### Objectives

- Verify and import a dated CentOS Stream 9 GenericCloud image.
- Provision isolated participant VMs with SSH and HTTP access.
- Rehearse the complete workshop on a separate test VM before delivery.

## Task 1: Verify the image once per region

1. Download `CentOS-Stream-GenericCloud-9-20260922.0.x86_64.qcow2` and its matching `.SHA256SUM` file from the [official CentOS Stream 9 image directory](https://cloud.centos.org/centos/9-stream/x86_64/images/). Use the dated file, not a changing `latest` link. Do not use a DVD ISO.

2. Compare the published checksum with a SHA-256 calculation on the downloaded QCOW2. On Windows PowerShell, use the actual image file path:

    ```powershell
    Get-FileHash -Algorithm SHA256 -LiteralPath 'C:\path\to\CentOS-Stream-GenericCloud-9-20260922.0.x86_64.qcow2'
    ```

    For this dated image, the expected size is **1,277,034,496 bytes** and the expected SHA-256 is `8527cb5ae91991afa1b0403fa3e0eb5a8d2690648bdbe51e7de86131b52e2763`. Stop if either check differs. Record the image filename, published checksum, calculated checksum, and download date in the event notes.

## Task 2: Import the image and prepare shared OCI resources

1. In the event region, create or select a compartment, VCN, public subnet, internet gateway, and a private Object Storage bucket. Configure egress for CentOS repositories, `raw.githubusercontent.com`, `yum.oracle.com`, and `www.ksplice.com`.

2. Upload the verified QCOW2 to the private bucket. Under **Compute** > **Custom images**, select **Import image**. Select the QCOW2 object, choose **QCOW2** as the image type and **Paravirtualized** as the launch mode. Name the image `ol-centos-stream9`. Select Linux as the operating system if the Console requires that field. Wait until the image is **Available**.

3. Confirm that the image supports the selected x86_64 VM shape. The starting size for testing is 1 OCPU, 8 GB memory, and a 50 GB boot volume. Verify actual regional shape capacity and event quota before provisioning participants.

## Task 3: Launch a separate VM for each participant

1. Collect each participant's SSH **public** key through an approved event process. Never collect, distribute, or reuse their private keys. Launch one VM per participant from `ol-centos-stream9`, using a unique name such as `ol-centos-participant-01`, the selected shape, and the workshop public subnet. Assign a public IP and add only that participant's public key. Grant each participant the scoped OCI access needed to view their instance and create its boot-volume backup, or arrange for an instructor to perform that backup during Lab 3.

2. Permit SSH on TCP 22 and the Apache page on TCP 80 from the participant's approved source CIDR. Do not open broader ingress than the event requires. Confirm that the subnet has an internet route and that the VM has outbound HTTPS access.

3. For each VM, verify **Running** state, CentOS Stream 9 identity, SSH as `cloud-user`, cloud-init completion, `sudo dnf makecache`, and access to the migration-script URL. Do not install Apache or run migration on participant VMs; those are learner tasks. Record the VM name, public IP, compartment, region, SSH user, and owner in an event assignment sheet.

4. Send each participant only their own assignment details and the workshop link. Participants begin with Lab 1, not with image import or VM creation.

## Task 4: Rehearse and clean up

1. Launch a separate rehearsal VM from the same image and run Labs 1 through 7 end to end. Confirm the pinned migration-script checksum, successful dry run, boot-volume backup, migration, reboot, Oracle Linux 9 identity, Apache marker, and Ksplice checks. Record any image-specific fixes before partner delivery.

2. After the event, collect any required evidence, then terminate participant VMs and delete their boot volumes and backups according to your retention policy. Remove the shared image, bucket object, network, and compartment only when no future session needs them. Confirm exact resource ownership before deleting anything.

## Learn More

- [CentOS Stream 9 image directory](https://cloud.centos.org/centos/9-stream/x86_64/images/)
- [Importing custom images into OCI](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/custom-images-import.htm)
- [Oracle migration script documentation](https://github.com/oracle/migrate-to-ol/blob/main/README-migrate-to-oracle-linux.md)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, September 2026
