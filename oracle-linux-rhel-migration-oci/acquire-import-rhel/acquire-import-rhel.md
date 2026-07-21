# Lab 1: Acquire and Import the RHEL Source Image

## Introduction

OCI does not provide a directly selectable RHEL platform image in the standard image list. For the certified bring-your-own-image path, obtain a supported RHEL KVM guest image from Red Hat, upload it to Object Storage, and import it as a custom image.

QCOW2, which stands for QEMU Copy-On-Write version 2, is a virtual disk file format commonly used with QEMU (Quick Emulator), an open source machine emulator and virtualizer, and KVM (Kernel-based Virtual Machine), the Linux kernel's built-in virtualization technology. A Red Hat Enterprise Linux (RHEL) KVM guest image is a bootable QCOW2 virtual disk that contains a preinstalled RHEL operating system and cloud initialization components. It is not an installation ISO. Oracle Cloud Infrastructure (OCI) imports this virtual disk as a custom image and uses it to create the boot volume for a new RHEL Compute instance.

This lab uses RHEL 9.8 for x86_64. The OCI documentation requires the KVM guest image, QCOW2 image type, and paravirtualized launch mode for this workflow.

### Objectives

In this lab, you will:

- Download and verify an authorized RHEL 9.8 KVM guest image.
- Create a dedicated OCI compartment and Object Storage bucket.
- Upload the QCOW2 image to OCI.
- Import and verify a compatible custom image.

### Prerequisites

Before beginning this lab, confirm that you have:

- Completed the workshop introduction and reviewed the architecture and conventions.
- An OCI tenancy with permission to create compartments, Object Storage buckets, and custom Compute images.
- A Red Hat account with access to the RHEL 9.8 x86_64 KVM guest image and an entitlement authorized for this workshop VM.
- At least 15 GB of temporary local storage and a reliable internet connection.
- Access to an x86_64 OCI VM shape certified for the selected RHEL release.

Estimated Lab Time: 60 to 90 minutes

## Task 1: Confirm Red Hat and OCI readiness

1. Sign in to the [Red Hat Customer Portal](https://access.redhat.com/).

2. Confirm that your account can download RHEL 9.8 and has an entitlement that you may use for a temporary workshop VM.

3. Sign in to the [OCI Console](https://cloud.oracle.com/).

4. Select the OCI region where you will complete every lab.

5. Confirm that the region offers `VM.Standard.E5.Flex` or another RHEL-certified x86_64 flexible VM shape.

    > **Important:** Shape certification and regional capacity can change. Check the [Red Hat Ecosystem Catalog](https://catalog.redhat.com/en/cloud/detail/252697) before a production deployment.

## Task 2: Download the RHEL KVM guest image

1. In the Red Hat Customer Portal, select **Downloads** and open **Red Hat Enterprise Linux**.

2. Select the following values:

    - Product variant: Red Hat Enterprise Linux for x86_64
    - Version: 9.8
    - Image: KVM Guest Image

3. Download `rhel-9.8-x86_64-kvm.qcow2` to your local computer.

    Do not select the Boot ISO, Binary DVD ISO, or source image. OCI's documented RHEL workflow requires the KVM guest image.

4. Confirm that the downloaded file is approximately 1.29 GB. The exact published file size for the tested image is 1,383,071,744 bytes.

5. Calculate the local SHA-256 checksum and compare it with the value published by Red Hat.

    On Windows PowerShell, run:

    ```powershell
    <copy>Get-FileHash -Algorithm SHA256 "$env:USERPROFILE\Downloads\rhel-9.8-x86_64-kvm.qcow2"</copy>
    ```

    On macOS or Linux, run:

    ```bash
    <copy>sha256sum ~/Downloads/rhel-9.8-x86_64-kvm.qcow2</copy>
    ```

6. Confirm that the calculated checksum matches the tested Red Hat-published value:

    ```text
    b99091f1b4489111004d449398d9cc6aa024cb48b02c72fa99e6ca1fc48a7e4e
    ```

    Letter case does not matter. Do not continue if the checksum differs.

## Task 3: Create the workshop compartment

A compartment keeps the workshop resources together and makes them easier to find and remove later.

1. In the OCI Console, open the navigation menu and select **Identity & Security**, then **Compartments**.

2. Select **Create compartment**.

3. Enter the following values:

    - Name: `ol-migrate-lab`
    - Description: `Temporary resources for the RHEL to Oracle Linux workshop`
    - Parent compartment: Select the compartment permitted by your organization.

4. Select **Create compartment**.

5. Wait until the compartment is available, then select it and record its name.

## Task 4: Create a private Object Storage bucket

An Object Storage bucket holds files as objects. You use a private bucket so the licensed RHEL image is not publicly accessible.

1. Open the navigation menu and select **Storage**, then **Buckets**.

2. Select the `ol-migrate-lab` compartment.

3. Select **Create bucket**.

4. Enter a unique bucket name beginning with `ol-migrate-rhel-image`.

5. Keep the default storage tier as **Standard** and the visibility as **Private**.

6. Do not enable a public bucket or create a pre-authenticated request.

7. Select **Create**.

## Task 5: Upload the RHEL image

1. Open the new bucket and select **Upload**.

2. Select the downloaded RHEL 9.8 KVM guest QCOW2 file.

3. Start the upload and keep the browser tab open.

4. Wait until the upload completes successfully.

    Upload time depends on the image size and your network connection. If the upload fails, remove the incomplete object before retrying.

5. Select the object and confirm that the displayed size matches the local file size.

## Task 6: Import the custom image

A custom image is the reusable OCI Compute image created from the uploaded QCOW2 virtual disk. You will use it in Lab 2 to launch the RHEL source VM.

1. Open the navigation menu and select **Compute**, then **Custom images**.

2. Select the `ol-migrate-lab` compartment and then select **Import image**.

3. Enter `ol-migrate-rhel-9.8` for the image name.

4. Select **RHEL** as the operating system. After import, OCI might display the operating system as **Custom** because the image originated outside OCI.

5. Select **Import from an Object Storage bucket**.

6. Select your bucket and the uploaded RHEL QCOW2 object.

7. Configure the image:

    - Image type: **QCOW2**
    - Launch mode: **Paravirtualized mode**

8. Select **Import image**.

9. On the Custom images page, monitor the image until its state changes from **Importing** to **Available**.

10. If the import fails, open the associated work request and review the error before retrying.

## Task 7: Configure and verify shape compatibility

1. Open the `ol-migrate-rhel-9.8` custom image.

2. Under **Resources**, select **Compatible shapes**.

3. Confirm that VM.Standard.E5.Flex, or another compatible x86_64 VM shape available in your region, appears in the compatible-shapes list.

4. Confirm the following checkpoint:

    - Image state is Available.
    - Operating system is Linux.
    - Image type is QCOW2.
    - Launch mode is paravirtualized.
    - At least one tested x86_64 flexible shape is compatible.

5. Continue to Lab 2 to create the VCN, launch the RHEL source instance, and validate SSH access. Lab 1 is complete when the custom image is available and its compatibility settings are confirmed.

## Task 8: Review your knowledge

1. Why did you select a KVM guest image instead of a RHEL installation ISO?

    The KVM guest image is a prepared QCOW2 cloud image with components such as `cloud-init`. It matches OCI's documented RHEL custom-image workflow.

2. Why is the Object Storage bucket private?

    The RHEL image is participant-authorized software and must not be redistributed publicly.

3. What indicates that the image is ready for Compute?

    Its custom-image state is Available, its settings are correct, and a certified compatible shape is configured.

## Learn More

- [Importing Custom Linux Images](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/importingcustomimagelinux.htm)
- [Bring Your Own Image](https://docs.oracle.com/en-us/iaas/Content/Compute/References/bringyourownimage.htm)
- [Understanding RHEL image downloads](https://access.redhat.com/solutions/104063)
- [Red Hat Ecosystem Catalog for Oracle Cloud](https://catalog.redhat.com/en/cloud/detail/252697)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, July 2026

