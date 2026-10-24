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

6. Select **OCI** as the image type. The launch mode is selected from the exported metadata automatically.

7. Select **Import image** and wait until its state is Available. If it fails, inspect the work request and confirm that the PAR permits reads and has not expired.

## Task 2: Launch and verify the destination instance

1. Use an existing destination network that permits SSH and HTTP from your client IP, or follow Lab 2, Task 1 in the destination tenancy to create one.

2. Open the destination `rhel-9-8-apache-source` image and confirm that `VM.Standard.E5.Flex` is listed under compatible shapes. Add the tested shape if needed.

3. Select **Create instance** and enter `ol-migrate-rhel-source` as the instance name.

4. Configure 1 OCPU, 8 GB memory, and a boot volume of at least 50 GB or the image minimum. Select the destination network, attach the SSH/HTTP network security group, assign a public IP, and supply your SSH public key.

5. Create the instance, wait until it is Running, and record its public IP. Connect from your local terminal:

    ```bash
    <copy>
    ssh -i <private-key-path> cloud-user@<public-ip>
    </copy>
    ```

6. Verify the operating system and application:

    ```bash
    <copy>
    cat /etc/os-release
    systemctl is-enabled httpd
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

    Expected results are RHEL 9.8, `enabled`, `active`, and the workload marker.

7. Verify registration cleanup:

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

8. Open `http://<public-ip>/` in your browser and confirm that the HTML page displays `MIGRATION_WORKLOAD_OK`.

9. Continue to Lab 4, the Linux Command-Line Quickstart, on this destination instance. Capture the migration baseline afterward in Lab 5. Do not repeat the preparation registration or package installation steps.

## Learn More

- [Importing a custom image](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/custom-images-import.htm)
- [Creating an OCI Compute instance](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/launchinginstance.htm)
- [Connecting to a Linux instance](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/connect-to-linux-instance.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
