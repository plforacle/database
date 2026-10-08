# Lab 2: Prepare and Baseline the RHEL Workload

## Introduction

In this lab, you connect to the RHEL source VM launched in Lab 1, verify its operating system and repository access, prepare the source system, deploy an Apache workload, and capture the pre-migration baseline.

The OCI-provided RHEL image receives packages and updates through OCI-hosted Red Hat Update Infrastructure (RHUI). This path requires no personal Red Hat subscription registration. Verify repository access before installing packages or continuing to migration readiness checks.

Estimated Lab Time: 30 minutes

### Objectives

In this lab, you will:

- Connect to the source VM with SSH and verify RHEL 9.8 and its architecture.
- Verify RHUI access and prepare the source system.
- Deploy Apache and test the workload locally and from your browser.
- Record the SSH account and source-system evidence.
- Archive the baseline for comparison after migration.

### Prerequisites

Before beginning this lab, confirm that you have:

- Completed Lab 1 and have a Running RHEL 9.8 source VM.
- The instance name, public IP, source image details, and launch settings recorded in Lab 1.
- The SSH private key corresponding to the public key added during launch.
- An SSH client and permission to use `sudo` on the source VM.
- The public subnet, internet route, and SSH and HTTP rules configured in Lab 1.
- Outbound connectivity from the VM to its RHUI repositories.

> **Note:** If you already prepared the source and deployed Apache, verify the image, SSH account, RHUI access, and `MIGRATION_WORKLOAD_OK` marker before continuing with Task 4. Use your existing instance name and public IP throughout the workshop.

## Task 1: Connect and inspect the source system

1. From your local terminal, connect using the default RHEL image user. If your SSH agent or default SSH key is configured, run:

    ```bash
    <copy>ssh cloud-user@<public-ip></copy>
    ```

    If you must identify a specific private key, run:

    ```bash
    <copy>ssh -i "<private-key-path>" cloud-user@<public-ip></copy>
    ```

    Replace `<private-key-path>` with the actual path to the private key. Do not enter the placeholder literally.

    If `cloud-user` is denied, use `opc` with the same key. OCI-targeted images can use `opc` as their SSH user:

    ```bash
    <copy>ssh opc@<public-ip></copy>
    ```

    If needed, add `-i "<private-key-path>"` to the `opc` command. Use the account that works for the remaining lab steps. The steps that write to your home directory work with either account.

2. Accept the host key only after confirming that the IP address matches your OCI instance.

3. Verify the operating-system identity and architecture:

    ```bash
    <copy>
    cat /etc/os-release
    uname -m
    uname -r
    </copy>
    ```

    Confirm that `ID` is `rhel`, the version is 9.8, and the architecture is `x86_64`.

4. Confirm that `cloud-init` completed:

    ```bash
    <copy>
    cloud-init status --wait
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

## Task 2: Verify RHUI access and prepare RHEL

1. Confirm the source repositories are available:

    ```bash
    <copy>sudo dnf repolist --enabled</copy>
    ```

    Verify BaseOS and AppStream access through OCI-hosted RHUI. Repository names can vary by image. Do not run `subscription-manager register` or attach a personal Red Hat entitlement for this path.

2. Refresh metadata to verify repository access:

    ```bash
    <copy>sudo dnf makecache</copy>
    ```

    If access fails, check DNS, outbound connectivity, and the image RHUI configuration. Resolve repository access before continuing.

3. Check source disk space and kernel state:

    ```bash
    <copy>
    df -h /
    uname -r
    rpm -q kernel-core | sort -V
    sudo grubby --default-kernel
    </copy>
    ```

    If the source is already updated and running its newest installed kernel, skip the update and reboot in Steps 4 and 5. A 64 GB volume does not guarantee sufficient free space.

4. If updates are needed, update the source system:

    ```bash
    <copy>sudo dnf update -y</copy>
    ```

5. If the update installed a new kernel, reboot so it is running:

    ```bash
    <copy>sudo reboot</copy>
    ```

    The SSH session closes when the VM restarts.

6. Wait for the instance to return to the Running state, then reconnect:

    ```bash
    <copy>ssh <ssh-user>@<public-ip></copy>
    ```

    Replace `<ssh-user>` with the account you used in Task 1, either `cloud-user` or `opc`.

7. Verify the running kernel and repository access:

    ```bash
    <copy>uname -r</copy>
    ```

    ```bash
    <copy>sudo dnf repolist</copy>
    ```

## Task 3: Deploy the workshop workload

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

## Task 4: Record the RHEL system before migration

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
    whoami > "$EVIDENCE/ssh-user.txt"
    rpm -qa | grep -Ei 'rhui|subscription-manager|oracle-cloud-agent' \
      > "$EVIDENCE/source-management-packages.txt" || true
    df -h / > "$EVIDENCE/disk-free.txt"
    lsblk > "$EVIDENCE/block-devices.txt"
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

5. Record provenance in `$EVIDENCE/source-instance.txt`. Copy the source image name and OCID, instance name and OCID, region, shape, OCPUs, memory, boot-volume size, and SSH account from your notes. Do not include credentials. Then archive the baseline:

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

Continue to **Lab 3: Assess Readiness and Protect the Source** after the source checkpoint passes.

## Learn More

- [Connecting to a Linux instance](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/connect-to-linux-instance.htm)
- [Red Hat Update Infrastructure](https://access.redhat.com/products/red-hat-update-infrastructure/)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026

