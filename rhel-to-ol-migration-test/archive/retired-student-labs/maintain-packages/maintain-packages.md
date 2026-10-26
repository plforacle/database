# Lab 8: Maintain Oracle Linux Packages

## Introduction

Use Oracle repositories to maintain the converted VM. Preserve the migration evidence first. This lab checks available updates and updates only the selected package, keeping the selected running kernel unchanged.

### Objectives

In this lab, you will:

- Inspect package repositories and installed package metadata.
- Check advisories and preview a selected update.
- Install a small utility and inspect the transaction history.
- Retest the original application after maintenance.

Estimated Lab Time: 25 minutes

### Prerequisites

Complete Lab 5 with a successful migration decision, complete the kernel-choice exercise in Lab 6, and record the Ksplice outcome from Lab 7. A recorded Ksplice access/support issue can remain open while you practice package maintenance; it must not be reported as successful live patching. Run on the converted Oracle Linux destination VM with internet access and sudo privileges. Do not repeat Red Hat registration.

Use the guided steps if you are new to Linux. Experienced learners can attempt the independent challenge within the same lab time, then verify every completion check.

## Task 1: Preserve the migration result and check repositories

1. Confirm that the before/after archives exist and record the current kernel:

    ```bash
    <copy>
    ls -lh "$HOME/ol-migration-evidence/rhel-before.tar.gz" \
      "$HOME/ol-migration-evidence/oracle-linux-after.tar.gz"
    mkdir -p "$HOME/ol-practice-reports"
    uname -r | tee "$HOME/ol-practice-reports/kernel-before-maintenance.txt"
    cat /etc/os-release
    </copy>
    ```

    Stop if migration validation or the evidence archives are missing. Keep them unchanged; later exercises have separate reports.

2. Inspect and refresh Oracle repository metadata:

    ```bash
    <copy>
    sudo dnf repolist --enabled
    sudo dnf makecache
    sudo dnf info --installed httpd curl
    </copy>
    ```

    Confirm that BaseOS and AppStream identify Oracle repositories. Repository access failures must be resolved before installing packages.

## Task 2: Check available updates and advisories

1. Query updates and inspect the return code:

    ```bash
    <copy>
    sudo dnf check-update
    update_status=$?
    printf "dnf check-update exit status: %s\n" "$update_status"
    sudo dnf updateinfo list --security
    </copy>
    ```

    Status 100 means updates are available, 0 means none are available, and 1 indicates an error. An empty security-advisory list is a valid result; it is not a reason to downgrade software. Neither query installs packages.

2. Inspect the installed curl package and preview its update:

    ```bash
    <copy>
    rpm -q curl --qf "%{NAME} %{VERSION}-%{RELEASE} %{VENDOR}\n"
    sudo dnf upgrade --assumeno curl
    </copy>
    ```

    Review every proposed package and dependency. `Operation aborted` is expected with `--assumeno`. If the proposal includes kernel packages or unrelated removals, stop and ask the instructor to review it.

3. Apply the selected curl update interactively:

    ```bash
    <copy>
    sudo dnf upgrade curl
    </copy>
    ```

    Confirm with `y` only when the transaction matches the reviewed preview. If DNF reports `Nothing to do`, record that valid result and continue. Do not run a full distribution update just to create a transaction. If installation fails, retain the DNF error and inspect history before retrying.

## Task 3: Search for and install a small utility

1. Search for the tree utility and check whether it is already installed:

    ```bash
    <copy>
    dnf search tree
    dnf info tree
    rpm -q tree
    </copy>
    ```

    `package tree is not installed` is expected on a minimal source. If already present, retain it and use it without removing another package.

2. Install it from the configured repositories and examine the practice reports:

    ```bash
    <copy>
    sudo dnf install tree
    tree "$HOME/ol-practice-reports"
    </copy>
    ```

    Review the transaction, then confirm with `y`. This utility is not required by the application and can remain installed for workshop use.

## Task 4: Review history and validate the application

1. Inspect the most recent transactions:

    ```bash
    <copy>
    sudo dnf history list | head -12
    sudo dnf history info last
    rpm -q curl tree
    sudo dnf check
    </copy>
    ```

    Explain which transaction installed a new package and whether curl actually changed. Avoid blindly undoing transactions; package availability and dependencies can change.

2. Verify the kernel, service, and original application content:

    ```bash
    <copy>
    uname -r > "$HOME/ol-practice-reports/kernel-after-maintenance.txt"
    diff -u "$HOME/ol-practice-reports/kernel-before-maintenance.txt" \
      "$HOME/ol-practice-reports/kernel-after-maintenance.txt"
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    sha256sum --check "$HOME/ol-migration-evidence/before/application-sha256.txt"
    </copy>
    ```

    The kernel diff must be empty, Apache active, the marker present, and the checksum OK. If any check fails, inspect the transaction and application logs before moving on.

## Task 5: Complete the independent challenge and checkpoint

1. Challenge: use DNF/RPM output to distinguish an installed version from an available repository version, identify a transaction ID, and explain why `check-update` status 100 is not an error. Beginners can use Tasks 1 through 4 as their evidence.

2. Record the selected update result, tree package version, repository names, and application verification results in `$HOME/ol-practice-reports/package-maintenance.txt`. No specific update count or package version is required.

3. Complete when Oracle repositories respond, tree runs, package checks pass, and the original page checksum is unchanged. Continue to Lab 9.

## Learn More

- [Use DNF on Oracle Linux](https://docs.oracle.com/en/learn/ol-dnf/index.html)
- [Installing software on Oracle Linux](https://docs.oracle.com/en/operating-systems/oracle-linux/software-management/sfw-mgmt-InstallSoftwareonEnterpriseLinux.html)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
