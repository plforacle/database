# Lab 2: Launch and Baseline the RHEL Workload

## Introduction

In this lab, you launch the imported custom image as an actual RHEL VM. You then register and update RHEL, deploy an Apache workload, and capture evidence that will be compared after the migration.

### Objectives

In this lab, you will:

- Create a restricted OCI network.
- Launch and connect to the RHEL source VM.
- Register and update the RHEL system securely.
- Deploy an Apache workload.
- Capture a repeatable source-system baseline.

Estimated Lab Time: 35 minutes

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

## Task 2: Launch the RHEL source VM

1. Open **Compute**, then **Instances**, and select **Create instance**.

2. Enter `ol-migrate-rhel-source` for the instance name.

3. Select the `ol-migrate-lab` compartment.

4. Under **Image and shape**, select **Change image**.

5. Select **My images**, then **Custom images**, and choose `ol-migrate-rhel-9-8`.

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

    Enter Red Hat credentials only at the protected terminal prompts. Do not place credentials on the command line.

2. If your organization does not use Simple Content Access and no entitlement is attached, run:

    ```bash
    <copy>
    sudo subscription-manager attach --auto
    </copy>
    ```

3. Enable the RHEL 9 BaseOS and AppStream repositories:

    ```bash
    <copy>
    sudo subscription-manager repos \
      --enable=rhel-9-for-x86_64-baseos-rpms \
      --enable=rhel-9-for-x86_64-appstream-rpms
    </copy>
    ```

4. Verify repository access:

    ```bash
    <copy>
    sudo dnf repolist
    sudo dnf makecache
    </copy>
    ```

5. Update the source system:

    ```bash
    <copy>
    sudo dnf update -y
    </copy>
    ```

6. Reboot so the newest installed source kernel is running:

    ```bash
    <copy>
    sudo reboot
    </copy>
    ```

7. Reconnect after the instance returns to Running.

8. Verify the current kernel and ensure no package transaction remains incomplete:

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

## Task 6: Create the baseline evidence bundle

1. Create an evidence directory:

    ```bash
    <copy>
    mkdir -p "$HOME/ol-migration-evidence/before"
    EVIDENCE="$HOME/ol-migration-evidence/before"
    </copy>
    ```

2. Capture the operating system, kernel, repositories, and packages:

    ```bash
    <copy>
    cat /etc/os-release > "$EVIDENCE/os-release.txt"
    uname -a > "$EVIDENCE/kernel.txt"
    sudo dnf repolist --enabled > "$EVIDENCE/repositories.txt"
    rpm -qa --qf '%{NAME}\t%{EPOCHNUM}:%{VERSION}-%{RELEASE}.%{ARCH}\t%{VENDOR}\n' \
      | sort > "$EVIDENCE/packages.tsv"
    </copy>
    ```

3. Capture services, networking, firewall, and SELinux:

    ```bash
    <copy>
    systemctl is-enabled httpd > "$EVIDENCE/httpd-enabled.txt"
    systemctl is-active httpd > "$EVIDENCE/httpd-active.txt"
    sudo ss -lntup > "$EVIDENCE/listening-ports.txt"
    ip address show > "$EVIDENCE/addresses.txt"
    ip route show > "$EVIDENCE/routes.txt"
    getenforce > "$EVIDENCE/selinux.txt"
    sudo firewall-cmd --list-all > "$EVIDENCE/firewall.txt"
    </copy>
    ```

4. Capture the workload response and checksum:

    ```bash
    <copy>
    curl --fail --silent http://127.0.0.1/ > "$EVIDENCE/application.html"
    sha256sum /var/www/html/index.html > "$EVIDENCE/application-sha256.txt"
    </copy>
    ```

5. Create an archive:

    ```bash
    <copy>
    tar -C "$HOME/ol-migration-evidence" -czf \
      "$HOME/ol-migration-evidence/rhel-before.tar.gz" before
    ls -lh "$HOME/ol-migration-evidence/rhel-before.tar.gz"
    </copy>
    ```

6. Confirm the source checkpoint:

    ```bash
    <copy>
    grep '^ID=' /etc/os-release
    systemctl is-active httpd
    getenforce
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

    Expected results are `ID="rhel"`, `active`, `Enforcing`, and the workload marker.

## Learn More

- [Creating an OCI Compute instance](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/launchinginstance.htm)
- [Connecting to a Linux instance](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/connect-to-linux-instance.htm)
- [Red Hat Subscription Management](https://access.redhat.com/articles/433903)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, July 2026

