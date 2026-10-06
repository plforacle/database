# Lab 2: Baseline CentOS Stream

## Introduction

On your assigned CentOS Stream 9 VM, deploy a small Apache page and save evidence of the system before migration. The page contains a marker you will test again after the operating system changes.

Estimated Lab Time: 40 minutes

### Objectives

- Update the CentOS Stream source and deploy an Apache workload.
- Capture operating system, package, service, and application evidence.

### Prerequisites

- Completed Lab 1.
- You are connected to the instructor-prepared CentOS Stream 9 VM as `cloud-user`.

## Task 1: Update and confirm the source

1. Check the enabled repositories and refresh metadata before installing anything:

    ```bash
    sudo dnf repolist --enabled
    sudo dnf makecache
    ```

    If metadata refresh fails, stop and give the instructor the error. Do not modify repository configuration on your assigned VM.

2. When metadata refresh succeeds, update the source:

    ```bash
    sudo dnf update -y
    ```

    The enabled repositories should provide CentOS Stream BaseOS and AppStream packages. CentOS Stream does not require Red Hat subscription registration.

3. Reboot after the update:

    ```bash
    sudo reboot
    ```

4. From your local computer, reconnect, then confirm the running kernel on the VM:

    ```bash
    ssh -i <private-key-path> cloud-user@<public-ip>
    ```

    ```bash
    uname -r
    ```

## Task 2: Deploy and test Apache

1. Install Apache, curl, firewall tooling, and SELinux utilities:

    ```bash
    sudo dnf install -y httpd curl firewalld policycoreutils
    ```

2. Create the workshop page:

    ```bash
    sudo tee /var/www/html/index.html >/dev/null <<'EOF'
    <!doctype html>
    <html lang="en"><head><meta charset="utf-8"><title>Migration Lab</title></head>
    <body><h1>Migration Lab</h1><p id="status">MIGRATION_WORKLOAD_OK</p></body></html>
    EOF
    sudo restorecon -Rv /var/www/html
    ```

3. Start the firewall and Apache, and allow HTTP through the guest firewall:

    ```bash
    sudo systemctl enable --now firewalld
    sudo firewall-cmd --permanent --add-service=http
    sudo firewall-cmd --reload
    sudo systemctl enable --now httpd
    ```

4. Test locally and open `http://<public-ip>/` in your browser. Both tests should show `MIGRATION_WORKLOAD_OK`:

    ```bash
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    ```

## Task 3: Capture the baseline

1. Save the source identity, packages, repositories, and running kernel:

    ```bash
    EVIDENCE="$HOME/ol-migration-evidence/before"
    mkdir -p "$EVIDENCE"
    cat /etc/os-release > "$EVIDENCE/os-release.txt"
    uname -a > "$EVIDENCE/kernel.txt"
    sudo dnf repolist --enabled > "$EVIDENCE/repositories.txt"
    rpm -qa --qf '%{NAME}\t%{VERSION}-%{RELEASE}.%{ARCH}\t%{VENDOR}\n' | sort > "$EVIDENCE/packages.tsv"
    ```

2. Save service, security, and application results:

    ```bash
    systemctl is-active httpd > "$EVIDENCE/httpd-active.txt"
    getenforce > "$EVIDENCE/selinux.txt"
    sudo firewall-cmd --list-all > "$EVIDENCE/firewall.txt"
    curl --fail --silent http://127.0.0.1/ > "$EVIDENCE/application.html"
    sha256sum /var/www/html/index.html > "$EVIDENCE/application-sha256.txt"
    tar -C "$HOME/ol-migration-evidence" -czf "$HOME/ol-migration-evidence/centos-before.tar.gz" before
    ```

3. Confirm that the baseline archive exists and the workload remains healthy:

    ```bash
    ls -lh "$HOME/ol-migration-evidence/centos-before.tar.gz"
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    ```

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, September 2026
