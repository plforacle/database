# Lab 2: Prepare and Export the RHEL Application Image

## Introduction

In this lab, you launch the imported custom image as an actual RHEL VM. You then register and update RHEL, deploy an Apache workload, and remove Red Hat registration before capturing and exporting the reusable image. This is an instructor preparation lab. Students create their own network, import the prepared image, and verify their instance in Student Lab 1. They capture baseline evidence in Student Lab 3.

### Objectives

In this lab, you will:

- Create a restricted OCI network.
- Launch and connect to the RHEL source VM.
- Register and update the RHEL system securely.
- Deploy an Apache workload.
- Remove Red Hat registration and disable subscription plugins before image capture.
- Capture and export the prepared image.

Estimated Lab Time: 50 minutes

## Task 1: Create the workshop network

1. Open the OCI navigation menu and select **Networking**, then **Virtual cloud networks**.

2. Select the `ol-migrate-lab` compartment.

3. Select **Start VCN Wizard**, then **Create VCN with Internet Connectivity**.

4. Enter the following values:

    - VCN name: `ol-migrate-vcn`
    - VCN IPv4 CIDR: `10.20.0.0/16`
    - Public subnet CIDR: `10.20.10.0/24`
    - Private subnet CIDR: `10.20.20.0/24`

5. Complete the wizard.

6. Create a network security group named `ol-migrate-nsg` in the VCN.

7. Determine your current public client IP address using an approved method from your organization.

8. Add stateful ingress rules to the network security group:

    - TCP port 22 from `<your-public-ip>/32`
    - TCP port 80 from `<your-public-ip>/32`

    Do not allow SSH from `0.0.0.0/0`.

9. Inspect the public subnet's associated security lists. On this new preparation network, restrict any broader SSH or HTTP ingress rule to `<your-public-ip>/32`. Keep the existing ICMP and outbound rules. Security-list and NSG rules combine; an NSG does not restrict a broader security-list allowance.

## Task 2: Launch the RHEL source VM

1. Open **Compute**, then **Instances**, and select **Create instance**.

2. Enter `rhel-9-8-source` for the instance name.

3. Select the `ol-migrate-lab` compartment.

4. Under **Image and shape**, select **Change image**.

5. Select **My images**, then **Custom images**, and choose `rhel-9-8-migration-import`.

6. Select `VM.Standard.E5.Flex` or the tested compatible fallback shape.

7. Configure 1 OCPU and 8 GB of memory.

8. Select the `ol-migrate-vcn` VCN and its public subnet.

9. Assign a public IPv4 address and attach `ol-migrate-nsg`.

10. Add your SSH public key. Save the corresponding private key securely.

11. Set the boot volume to at least 50 GB or the minimum required by the imported image.

12. Select **Create** and wait until the instance state is Running.

13. Record the public IP address.

## Task 3: Connect and inspect the imported system

1. From your local terminal, connect using the default RHEL image user:

    ```bash
    <copy>
    ssh -i <private-key-path> cloud-user@<public-ip>
    </copy>
    ```

2. Accept the host key only after confirming that the IP address matches your OCI instance.

3. Verify the operating-system identity and architecture:

    ```bash
    <copy>
    cat /etc/os-release
    uname -m
    uname -r
    </copy>
    ```

    Confirm that `ID` is `rhel`, the major version is 9, and the architecture is `x86_64`.

4. Confirm that `cloud-init` completed:

    ```bash
    <copy>
    sudo cloud-init status --wait
    </copy>
    ```

5. Inspect storage and networking:

    ```bash
    <copy>
    lsblk
    ip address show
    ip route show
    </copy>
    ```

## Task 4: Register and update RHEL

1. Register the system interactively:

    ```bash
    <copy>
    sudo subscription-manager register
    </copy>
    ```

    Enter Red Hat credentials only at the protected terminal prompts. Do not place credentials on the command line. This workshop uses Simple Content Access, so no subscription attachment command is needed.

2. Enable the RHEL 9 BaseOS and AppStream repositories:

    ```bash
    <copy>
    sudo subscription-manager repos \
      --enable=rhel-9-for-x86_64-baseos-rpms \
      --enable=rhel-9-for-x86_64-appstream-rpms
    </copy>
    ```

3. Verify repository access:

    ```bash
    <copy>
    sudo dnf repolist
    sudo dnf makecache
    </copy>
    ```

4. Update the source system:

    ```bash
    <copy>
    sudo dnf update -y
    </copy>
    ```

5. Reboot so the newest installed source kernel is running:

    ```bash
    <copy>
    sudo reboot
    </copy>
    ```

6. Reconnect after the instance returns to Running.

7. Verify the current kernel and ensure no package transaction remains incomplete:

    ```bash
    <copy>
    uname -r
    sudo dnf history list | head
    sudo rpm --verifydb
    </copy>
    ```

## Task 5: Deploy the workshop workload

1. Install the required packages:

    ```bash
    <copy>
    sudo dnf install -y httpd firewalld curl policycoreutils
    </copy>
    ```

2. Create the static application page:

    ```bash
    <copy>
    sudo tee /var/www/html/index.html >/dev/null <<'EOF'
    <!doctype html>
    <html lang="en">
    <head><meta charset="utf-8"><title>Oracle Linux Migration Workshop</title></head>
    <body>
    <h1>Oracle Linux Migration Workshop</h1>
    <p id="status">MIGRATION_WORKLOAD_OK</p>
    <p>This page must remain available before and after the operating system migration.</p>
    </body>
    </html>
    EOF
    </copy>
    ```

3. Enable the guest firewall and permit HTTP:

    ```bash
    <copy>
    sudo systemctl enable --now firewalld
    sudo firewall-cmd --permanent --add-service=http
    sudo firewall-cmd --reload
    </copy>
    ```

4. Restore the default SELinux context and start Apache:

    ```bash
    <copy>
    sudo restorecon -Rv /var/www/html
    sudo systemctl enable --now httpd
    </copy>
    ```

5. Test locally:

    ```bash
    <copy>
    curl --fail http://127.0.0.1/
    </copy>
    ```

6. Open `http://<public-ip>/` in your browser and confirm that `MIGRATION_WORKLOAD_OK` appears.

## Task 6: Remove Red Hat registration before saving the image

1. Unregister the preparation VM from Red Hat:

    ```bash
    <copy>
    sudo subscription-manager unregister
    </copy>
    ```

    Expected output includes `System has been unregistered.` If unregistration fails, stop and resolve it before cleaning local data.

2. Remove local subscription data:

    ```bash
    <copy>
    sudo subscription-manager clean
    </copy>
    ```

    Expected output is `All local data removed`.

3. Verify that no registration certificates remain and the application still works:

    ```bash
    <copy>
    sudo find /etc/pki/consumer /etc/pki/entitlement \
      -type f -name '*.pem' -print 2>/dev/null
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

    Expected results are no certificate files, `active`, and the workload marker.

4. Inspect the subscription plugins and remaining repository files:

    ```bash
    <copy>
    sudo cat /etc/dnf/plugins/subscription-manager.conf \
      /etc/dnf/plugins/product-id.conf
    sudo find /etc/yum.repos.d -maxdepth 1 -type f \
      -name '*.repo' -exec cat {} +
    </copy>
    ```

    Registration can enable both plugins. After cleanup, no enabled Red Hat repository definitions should remain. If any remain enabled, stop and resolve them before saving the image.

5. Disable the two subscription-management plugins:

    ```bash
    <copy>
    sudo sed -i 's/^enabled=1$/enabled=0/' \
      /etc/dnf/plugins/subscription-manager.conf \
      /etc/dnf/plugins/product-id.conf
    </copy>
    ```

6. Verify that both plugins are disabled:

    ```bash
    <copy>
    sudo grep '^enabled=' \
      /etc/dnf/plugins/subscription-manager.conf \
      /etc/dnf/plugins/product-id.conf
    </copy>
    ```

    Both files must show `enabled=0`. These changes leave Apache and the HTML application installed.

7. Confirm that the application still opens at `http://<public-ip>/` before saving the prepared custom image.

    Continue with Tasks 7 and 8 to capture and export the image. Student Lab 1 creates the destination network, imports the image, and verifies a new instance. Students capture their baseline in Student Lab 3 and do not register the destination VM with Red Hat.

## Task 7: Capture the prepared source image

1. In the preparation tenancy, open **Compute**, then **Instances**, and select `rhel-9-8-source`.

2. Select **More actions**, then **Create custom image**. Depending on the Console layout, this option is under **Actions**.

3. Select the preparation compartment and enter `rhel-9-8-apache-source` as the image name.

4. Select **Create custom image** and wait until the image becomes Available.

    The instance is briefly unavailable during capture. This image contains the installed RHEL workload and the registration cleanup completed in Task 6. Keep it for future workshops.

## Task 8: Export the prepared image and create an object-read PAR

1. Open `rhel-9-8-apache-source` under **Compute**, then **Custom images** in the preparation tenancy.

2. From **Actions**, select **Export**.

3. Choose **Export to an Object Storage bucket** and select your private preparation bucket.

4. Enter `rhel-9-8-apache-source.oci` as the object name and choose **OCI** as the image format.

    OCI format includes the disk and OCI launch metadata. The raw image imported in Lab 1 used QCOW2; this prepared export uses OCI format.

5. Select **Export image**. Wait for the image to return to Available and confirm the exported object is present in the bucket.

6. In the bucket, open **Management**, then **Pre-authenticated requests**, and select **Create pre-authenticated request**.

7. Configure the request:

    - Name: `rhel-9-8-apache-source-read`
    - Target: **Object**
    - Object name: `rhel-9-8-apache-source.oci`
    - Access type: **Permit object reads**
    - Expiration: A time that covers the planned destination import

8. Create the request and copy the complete PAR URL into your private notes. Supply it privately for Student Lab 1, Task 3.

    The bucket stays private. The PAR grants access to this object to anyone holding the URL, so do not publish it in the workshop or screenshots.

The instructor workshop is complete. Follow the Student Handoff checklist in the instructor introduction and verify Student Lab 1 before class. Students begin with Student Lab 1 in the separate student workshop.

## Learn More

- [Creating an OCI Compute instance](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/launchinginstance.htm)
- [Connecting to a Linux instance](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/connect-to-linux-instance.htm)
- [Creating a custom image](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/custom-images-create.htm)
- [Exporting a custom image](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/custom-images-export.htm)
- [Creating an Object Storage PAR](https://docs.oracle.com/en-us/iaas/Content/Object/Tasks/usingpreauthenticatedrequests_topic-To_create_a_preauthenticated_request_for_all_objects_in_a_bucket.htm)
- [Red Hat Subscription Management](https://access.redhat.com/articles/433903)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
