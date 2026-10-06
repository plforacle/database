# Lab 2: Create and Baseline the Workload

## Introduction

Install a small Apache application on the disposable AlmaLinux VM and record its state. After conversion, compare the same application, network settings, security controls, and package inventory with this baseline.

Estimated Lab Time: 35 minutes

### Objectives

- Prepare AlmaLinux packages and boot the newest installed kernel.
- Create an Apache test page with a stable marker.
- Capture the source state and download the evidence.

### Prerequisites

- You passed Lab 1's checkpoint.
- The VM is dedicated to this workshop and does not serve an existing website.
- AlmaLinux repositories are reachable.

## Task 1: Prepare AlmaLinux

1. Connect to `alma-to-ol-source` and confirm its identity:

    ```bash
    <copy>
    . /etc/os-release
    test "$ID" = almalinux && test "$VERSION_ID" = 9.8 && test "$(uname -m)" = x86_64
    </copy>
    ```

    Continue only when the command returns status 0. It produces no output on success.

2. Install the tools used in this workshop:

    ```bash
    <copy>
    sudo dnf install -y httpd firewalld curl policycoreutils dnf-plugins-core grubby tmux
    sudo dnf upgrade -y
    </copy>
    ```

    Recheck `/etc/os-release` after the update. If AlmaLinux has advanced beyond 9.8, use a matching 9.8 lab source or revise and retest the target version before following this workshop.

3. Reboot, then reconnect with the same SSH account:

    ```bash
    <copy>
    sudo reboot
    </copy>
    ```

4. Verify the running and default kernels:

    ```bash
    <copy>
    uname -r
    rpm -q kernel-core | sort -V
    sudo grubby --default-kernel
    </copy>
    ```

    The running version must match the newest installed `kernel-core` version. Check free space in both `/` and `/boot` before proceeding.

## Task 2: Deploy the test page

1. Create the page on the disposable lab VM:

    ```bash
    <copy>
    sudo tee /var/www/html/index.html >/dev/null <<'EOF'
    <!doctype html>
    <html lang="en">
    <head><meta charset="utf-8"><title>AlmaLinux to Oracle Linux</title></head>
    <body>
    <h1>AlmaLinux to Oracle Linux Migration</h1>
    <p id="status">MIGRATION_WORKLOAD_OK</p>
    </body>
    </html>
    EOF
    sudo restorecon -Rv /var/www/html
    sudo systemctl enable --now firewalld
    sudo firewall-cmd --permanent --add-service=http
    sudo firewall-cmd --reload
    sudo systemctl enable --now httpd
    </copy>
    ```

2. Check the application and security state:

    ```bash
    <copy>
    systemctl is-enabled httpd
    systemctl is-active httpd
    getenforce
    sudo firewall-cmd --query-service=http
    curl --fail --silent --show-error http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

    Expected results include `enabled`, `active`, `Enforcing`, `yes`, and the application marker. Investigate any difference before capturing the baseline.

3. Open `http://<public-ip>/` from the workstation allowed by the lab ingress rules. Confirm the marker appears. This test covers both Apache and OCI network access.

## Task 3: Capture the AlmaLinux baseline

1. Create the evidence directory and record OS, kernel, repositories, and packages:

    ```bash
    <copy>
    mkdir -p "$HOME/ol-migration-evidence/before"
    EVIDENCE="$HOME/ol-migration-evidence/before"
    cat /etc/os-release > "$EVIDENCE/os-release.txt"
    uname -a > "$EVIDENCE/kernel.txt"
    sudo dnf repolist --enabled > "$EVIDENCE/repositories.txt"
    rpm -qa --qf '%{NAME}\t%{EPOCHNUM}:%{VERSION}-%{RELEASE}.%{ARCH}\t%{VENDOR}\n'       | sort > "$EVIDENCE/packages.tsv"
    </copy>
    ```

2. Record services, networking, security, application content, and its checksum:

    ```bash
    <copy>
    systemctl is-enabled httpd > "$EVIDENCE/httpd-enabled.txt"
    systemctl is-active httpd > "$EVIDENCE/httpd-active.txt"
    systemctl --failed --no-pager > "$EVIDENCE/failed-services.txt"
    sudo ss -lntup > "$EVIDENCE/listening-ports.txt"
    ip address show > "$EVIDENCE/addresses.txt"
    ip route show > "$EVIDENCE/routes.txt"
    rpm -q cloud-init oracle-cloud-agent > "$EVIDENCE/cloud-packages.txt" 2>&1 || true
    systemctl status cloud-init oracle-cloud-agent --no-pager > "$EVIDENCE/cloud-services.txt" 2>&1 || true
    getenforce > "$EVIDENCE/selinux.txt"
    sudo firewall-cmd --list-all > "$EVIDENCE/firewall.txt"
    curl --fail --silent --show-error http://127.0.0.1/ > "$EVIDENCE/application.html"
    sha256sum /var/www/html/index.html > "$EVIDENCE/application-sha256.txt"
    tar -C "$HOME/ol-migration-evidence" -czf       "$HOME/ol-migration-evidence/almalinux-before.tar.gz" before
    </copy>
    ```

3. In the SSH session on the Linux VM, confirm that the archive exists:

    ```bash
    <copy>
    ls -lh "$HOME/ol-migration-evidence/almalinux-before.tar.gz"
    </copy>
    ```

    Open a separate local PowerShell window for the download. Use the same private-key file that you used for SSH. The `-i` option requires the full Windows path to that file, including its filename; a folder path is insufficient.

    ```powershell
    <copy>
    scp -i "<private-key-path>" "<ssh-user>@<public-ip>:ol-migration-evidence/almalinux-before.tar.gz" .
    </copy>
    ```

    Replace `<private-key-path>` with a local file path such as `C:\Users\YourName\Downloads\ssh-key.key`. Replace `<ssh-user>` and `<public-ip>` with the VM's SSH account and public address. The remote path after the colon is relative to that Linux account's home directory. Do not place a Windows path there. The final `.` saves the archive in PowerShell's current directory.
4. Review failed services and confirm that the baseline archive exists locally. Complete this checkpoint before Lab 3.

## Learn More

- [AlmaLinux documentation](https://wiki.almalinux.org/)
- [OCI network security rules](https://docs.oracle.com/en-us/iaas/Content/Network/Concepts/securityrules.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Perside Foster with Codex assistance, October 2026
