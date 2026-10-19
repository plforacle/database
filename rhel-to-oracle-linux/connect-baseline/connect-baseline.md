# Lab 1: Connect and Capture the Baseline

## Introduction

Connect to your assigned RHEL server and record the application and system state. This baseline lets you verify the migration using evidence beyond the operating-system name.

Estimated Time: 20 minutes.

### Objectives

- Establish SSH access and verify sudo privileges.
- Identify the source version and running kernel.
- Verify the prepared application and capture a baseline.

### Prerequisites

- Your instructor's connection details and host-key fingerprint.
- A prepared server with the workshop tools under `/opt/rhel-to-oracle-linux`.

## Task 1: Connect to the assigned server

1. From your local terminal, connect using the supplied values:

    ```bash
    <copy>
    ssh -i "<private-key-path>" <ssh-user>@<assigned-host>
    </copy>
    ```

    Verify the displayed host-key fingerprint against the instructor's record before accepting it. Keep this connection command for use after reboot.

2. In the SSH session, verify the account and sudo access:

    ```bash
    <copy>
    whoami
    hostname
    sudo -v
    </copy>
    ```

    Ask the instructor for help if sudo is denied. Do not continue without it.

3. Start a persistent terminal session:

    ```bash
    <copy>
    tmux new -A -s migration
    </copy>
    ```

    If SSH disconnects before reboot, reconnect and repeat this command to reattach. A reboot ends tmux as well as SSH.

## Task 2: Inspect the source and application

1. Read the instructor-supplied version settings and identify the source:

    ```bash
    <copy>
    . /etc/rhel-to-oracle-linux/workshop.env
    printf 'Expected source: %s; target: %s\n' "$SOURCE_VERSION" "$TARGET_VERSION"
    cat /etc/os-release
    uname -m
    uname -r
    </copy>
    ```

    Continue only when the source is RHEL, the release matches the supplied source version, and the architecture is `x86_64`. Ask the instructor about missing or unfilled settings.

2. Verify application and security state:

    ```bash
    <copy>
    systemctl is-enabled httpd
    systemctl is-active httpd
    curl --fail --silent --show-error http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    getenforce
    sudo firewall-cmd --query-service=http
    </copy>
    ```

    Expected results include `enabled`, `active`, the application marker, `Enforcing`, and `yes`. All checks use the SSH session. You do not need an externally accessible web page.

3. Verify source repositories:

    ```bash
    <copy>
    sudo dnf repolist --enabled
    sudo dnf makecache
    </copy>
    ```

    Your instructor has arranged source repository access. Do not change registration, remove repository packages, or add personal subscription credentials.

## Task 3: Capture the baseline

1. Run the supplied capture tool:

    ```bash
    <copy>
    bash /opt/rhel-to-oracle-linux/capture-state.sh before
    ls -lh "$HOME/ol-migration-evidence/before"
    </copy>
    ```

    The capture must finish successfully. It records the OS, packages, kernel, services, network, firewall, SELinux, application response, and application checksum. Treat a missing file or failed capture as a problem to resolve before migration.

2. Confirm the baseline application response:

    ```bash
    <copy>
    grep MIGRATION_WORKLOAD_OK "$HOME/ol-migration-evidence/before/application.html"
    cat "$HOME/ol-migration-evidence/before/application-sha256.txt"
    </copy>
    ```

3. Record the assigned hostname and SSH account in your notes. Continue when the source, application, security checks, and baseline capture pass.

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle.
- **Last Updated By/Date** - Workshop adaptation draft, October 19, 2026.
