# Lab 3: Import and Verify the RHEL Application Image

## Introduction

Use the prepared RHEL image from Lab 2 to create a working source instance in the destination tenancy. Import the OCI export with its object-read PAR URL, launch a new VM, and verify Apache and the registration cleanup. Red Hat registration and package installation are not repeated in the destination tenancy.

### Objectives

In this lab, you will:

- Import the prepared image into the destination tenancy.
- Launch a destination VM with the required networking and SSH access.
- Verify RHEL 9.8, Apache, and the HTML application.
- Confirm that registration certificates are absent and subscription plugins are disabled.

Estimated Lab Time: 25 minutes

### Prerequisites

Complete Lab 2, or obtain the authorized `rhel-9-8-apache-source` OCI export and a valid object-read PAR URL from the image owner. You need access to the destination tenancy and region, permission to import images and create a Compute instance, and an SSH key pair. No Red Hat credentials are needed for this lab.

## Task 1: Import into the destination tenancy

1. Sign in to the destination OCI tenancy and select its destination region and compartment. An existing authorized compartment is sufficient.

2. Open **Compute**, then **Custom images**, and select **Import image**.

3. Enter `rhel-9-8-apache-source` as the image name.

4. Select **Red Hat Enterprise Linux** if offered, or **Linux** in a Console layout with generic operating-system choices.

5. Choose **Import from an Object Storage URL** and paste the complete PAR URL from Lab 2, Task 8.

    Keep the PAR URL private and hide it in any screenshots you share.

6. Select **OCI** as the image type. The launch mode is selected from the exported metadata automatically.

    ![Import image settings show the prepared RHEL image name, Object Storage URL source, and OCI image type, with the PAR URL redacted.](./images/import-prepared-rhel-image.png)

7. Select **Import image** and wait until its state is Available. If it fails, inspect the work request and confirm that the PAR permits reads and has not expired.

## Task 2: Launch and verify the destination instance

1. Use an existing destination network that permits SSH and HTTP from your client IP, or follow Lab 2, Task 1 in the destination tenancy to create one.

2. Open the destination `rhel-9-8-apache-source` image and confirm that `VM.Standard.E5.Flex` is listed under compatible shapes. Add the tested shape if needed.

3. Select **Create instance** and enter `ol-migrate-rhel-source` as the instance name.

    The wizard may initially use the image name, `rhel-9-8-apache-source`, as the instance name. Replace it with `ol-migrate-rhel-source`; keep the selected image unchanged.

4. Under **Basic information**, select an availability domain and confirm that the selected image is `rhel-9-8-apache-source`. Select `VM.Standard.E5.Flex` and configure 1 OCPU and at least 8 GB of memory. A 12 GB configuration is also sufficient.

    The example shows the wizard's default instance name. Use `ol-migrate-rhel-source` for your instance.

    ![Basic information shows the default instance name, availability domain selection, and prepared RHEL image.](./images/create-instance-basic-info.png)

5. Under **Networking**, select the destination VCN and public subnet. Keep automatic private IPv4 assignment and attach the SSH/HTTP network security group.

    ![Networking settings select the workshop VCN, public subnet, and automatic private IPv4 assignment.](./images/create-instance-networking.png)

6. Enable **Automatically assign public IPv4 address** and leave IPv6 disabled for this workshop. Under **Add SSH keys**, upload or paste your SSH public key. If you select **Generate a key pair for me**, download and securely save the private key before continuing; it will not be shown again.

    ![Public IPv4 assignment is enabled, and the generated SSH key option provides a Download private key button.](./images/create-instance-add-ssh.png)

7. Under **Storage**, enable **Specify a custom volume size and performance setting**. Set the boot volume to at least 50 GB or the image minimum, whichever is larger.

    ![Storage settings enable a custom boot volume size of 50 GB.](./images/create-instance-storage.png)

8. Under **Review**, confirm the instance name, selected image, shape, memory, networking, and SSH key. If the name still matches the image name shown in the example, select **Edit** and change it to `ol-migrate-rhel-source`. Select **Create**.

    ![The review page shows the prepared RHEL image, E5 Flex shape with 1 OCPU and 12 GB of memory, and the Create button.](./images/create-instance-review.png)

9. Wait until the instance is Running and record its public IP. Connect from your local terminal:

    ```bash
    <copy>
    ssh -i <private-key-path> cloud-user@<public-ip>
    </copy>
    ```

10. Verify the operating system and application:

    ```bash
    <copy>
    cat /etc/os-release
    systemctl is-enabled httpd
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

    Expected results are RHEL 9.8, `enabled`, `active`, and the workload marker.

11. Verify registration cleanup:

    ```bash
    <copy>
    sudo find /etc/pki/consumer /etc/pki/entitlement \
      -type f -name '*.pem' -print 2>/dev/null
    sudo grep '^enabled=' \
      /etc/dnf/plugins/subscription-manager.conf \
      /etc/dnf/plugins/product-id.conf
    </copy>
    ```

    No registration certificate files should be listed, and both plugins must show `enabled=0`. The login banner suggesting `rhc connect` is not proof of registration. Do not run that command.

12. Open `http://<public-ip>/` in your browser and confirm that the HTML page displays `MIGRATION_WORKLOAD_OK`.

13. Continue to Lab 4, the Linux Command-Line Quickstart, on this destination instance. Capture the migration baseline afterward in Lab 5. Do not repeat the preparation registration or package installation steps.

## Learn More

- [Importing a custom image](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/custom-images-import.htm)
- [Creating an OCI Compute instance](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/launchinginstance.htm)
- [Connecting to a Linux instance](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/connect-to-linux-instance.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
