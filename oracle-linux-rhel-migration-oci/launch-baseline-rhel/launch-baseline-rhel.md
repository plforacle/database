# Lab 2: Launch and Baseline the RHEL Workload

## Introduction

In this lab, you create a basic OCI network and launch the imported custom image as an actual RHEL VM. You then register and update RHEL, deploy an Apache web server, and capture evidence that will be compared after the migration.

The VCN wizard creates the VCN, public and private subnets, gateways, route tables, and security lists for you. The RHEL VM uses the public subnet so you can connect to it with SSH. A security list acts as a virtual firewall for resources in the subnet.

### Objectives

In this lab, you will:

- Create a basic OCI network.
- Launch and connect to the RHEL source VM.
- Register and update the RHEL system securely.
- Deploy an Apache workload.
- Capture a repeatable source-system baseline.

### Prerequisites

Before beginning this lab, confirm that you have:

- Completed Lab 1.
- An `ol-migrate-rhel-9.8` custom image in the Available state.
- Permission to create networking resources, Compute instances, and boot volumes in the `ol-migrate-lab` compartment.
- An SSH key pair available on your local computer.
- A Red Hat entitlement that can register and update the temporary RHEL source VM.

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

6. After the wizard completes, open `ol-migrate-vcn`.

7. Open the default security list for the VCN as follows:
    - From ol-migrate-vcn page, select **Security -> Default Security List for ol-migrate-vcn -> Security rules**"

8. Confirm that an existing stateful ingress rule permits TCP destination port 22. This rule enables SSH access to the RHEL VM.

9. Add a stateful ingress rule with the following values so you can test the Apache web page by clicking the **Add Ingress Rules** button:

    - Source type: **CIDR**
    - Source CIDR: `0.0.0.0/0`
    - IP protocol: **TCP**
    - Destination port range: `80`
    - Description: `Temporary workshop HTTP access`

    > **Important:** `0.0.0.0/0` permits HTTP connections from any internet address. Use this simple rule only for the temporary workshop VM. Lab 6 removes the network and its rules. In production, restrict the source to approved addresses.

## Task 2: Launch the RHEL source VM

1. From OCI Menu Open **Compute**, then **Instances**, and select **Create instance**.

2. Enter `ol-migrate-rhel-source` for the instance name.

3. Select the `ol-migrate-lab` compartment.

4. Under **Image and shape**, select **Change image**.

5. Select **My images**, then **Custom images**, and choose `ol-migrate-rhel-9.8`.

6. Select `VM.Standard.E5.Flex` or the tested compatible fallback shape.

7. Configure 1 OCPU and 8 GB of memory.

8. Select the `ol-migrate-vcn` VCN and its public subnet.

9. for turn on **Public IPv4 address assignment** to assign a public IPv4 address.

10. Under section **Add SSH keys** add your SSH public key. Save the corresponding private key securely.

11. Select **Create** and wait until the instance state is Running.

12. Record the public IP address.

## Task 3: Connect and inspect the imported system

1. From your local terminal, connect using the default RHEL image user. If your SSH agent or default SSH key is configured, run:

    ```bash
    <copy>ssh cloud-user@<public-ip></copy>
    ```

    If you must identify a specific private key, run:

    ```bash
    <copy>ssh -i "<private-key-path>" cloud-user@<public-ip></copy>
    ```

    Replace `<private-key-path>` with the actual path to the private key. Do not enter the placeholder literally.

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

## Task 4: Register and prepare RHEL

1. Register the system interactively:

    ```bash
    <copy>sudo subscription-manager register</copy>
    ```

    Enter Red Hat credentials only at the protected terminal prompts. Do not place credentials on the command line.

2. Confirm that the system is registered:

    ```bash
    <copy>sudo subscription-manager identity</copy>
    ```

3. Confirm that the RHEL BaseOS and AppStream repositories are available:

    ```bash
    <copy>sudo dnf repolist</copy>
    ```

    Verify that the output includes repositories whose IDs contain `baseos` and `appstream`. These repositories provide the operating-system and application packages used in this workshop.

4. Update the source system:

    ```bash
    <copy>sudo dnf update -y</copy>
    ```

5. Reboot so the newest installed source kernel is running:

    ```bash
    <copy>sudo reboot</copy>
    ```

    The SSH session closes when the VM restarts.

6. Wait for the instance to return to the Running state, then reconnect:

    ```bash
    <copy>ssh cloud-user@<public-ip></copy>
    ```

7. Verify the running kernel and repository access:

    ```bash
    <copy>uname -r</copy>
    ```

    ```bash
    <copy>sudo dnf repolist</copy>
    ```

## Task 5: Deploy the workshop workload

1. Install the required packages:

    This installs the Apache web server, the RHEL firewall service, a web-testing command, and SELinux management tools.

    ```bash
    <copy>
    sudo dnf install -y httpd firewalld curl policycoreutils
    </copy>
    ```

2. Create the static application page:

    This command creates a simple test webpage. The `MIGRATION_WORKLOAD_OK` marker makes the page easy to verify before and after migration.

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

    These commands start the firewall, open the standard HTTP port, and save the rule so it remains active after a reboot.

    ```bash
    <copy>
    sudo systemctl enable --now firewalld
    sudo firewall-cmd --permanent --add-service=http
    sudo firewall-cmd --reload
    </copy>
    ```

4. Restore the default SELinux context and start Apache:

    The first command applies the correct SELinux security labels to the webpage. The second starts Apache now and automatically after future reboots.

    ```bash
    <copy>
    sudo restorecon -Rv /var/www/html
    sudo systemctl enable --now httpd
    </copy>
    ```

5. Test locally:

    This requests the webpage directly from the RHEL VM. A successful response confirms that Apache is serving the page locally.

    ```bash
    <copy>
    curl --fail http://127.0.0.1/
    </copy>
    ```

6. Open `http://<public-ip>/` in your browser and confirm that `MIGRATION_WORKLOAD_OK` appears.

    This final test confirms that the webpage is reachable through the OCI network, security list, operating-system firewall, and Apache service.

    >**Note**: Wait about 30 seconds for the security rule to take effect, then open http://<public-ip>/. If the page does not load, refresh the browser and confirm that the TCP port 80 ingress rule is attached to the instance’s subnet.

## Task 6: Record the RHEL system before migration

In this task, you record important information about the RHEL system and its Apache workload before migration. You will compare these results with the Oracle Linux system after migration to confirm that the operating system changed and the workload still functions correctly.

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

