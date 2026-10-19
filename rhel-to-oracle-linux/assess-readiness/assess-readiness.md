# Lab 2: Assess Migration Readiness

## Introduction

Check the source and assess the migration before changing the operating system. Your instructor stages a pinned migration script and arranges a recovery point. You verify the script and review its dry-run results.

Estimated Time: 20 minutes.

### Objectives

- Check the running kernel, storage, RPM database, and dependencies.
- Verify the staged migration script and run its dry run.
- Confirm the recovery and migration decision with the instructor.

### Prerequisites

- Completed Lab 1 and a successful baseline capture.
- The instructor-supplied version settings, script, and checksum.

## Task 1: Check source readiness

1. Check storage and package health:

    ```bash
    <copy>
    df -h /
    df -i /
    sudo rpm --verifydb
    sudo dnf check
    </copy>
    ```

    Resolve errors before continuing. The dry run will check its own storage requirements.

2. Compare the running, installed, and default kernels:

    ```bash
    <copy>
    uname -r
    rpm -q kernel-core | sort -V
    sudo grubby --default-kernel
    </copy>
    ```

    The running release must match the newest installed source kernel and the default kernel. If it does not, ask the instructor to resolve the preparation issue before the migration. Do not bypass the script's kernel check.

3. Review packages with a vendor other than Red Hat:

    ```bash
    <copy>
    rpm -qa --qf '%{NAME}\t%{VENDOR}\n' | awk -F '\t' '$2 !~ /Red Hat/ {print}' | sort
    </copy>
    ```

    Vendor metadata does not verify package signatures. Review these packages with the instructor; a different vendor is not automatically a blocker.

## Task 2: Verify the migration script

1. Copy the staged script into your working directory:

    ```bash
    <copy>
    . /etc/rhel-to-oracle-linux/workshop.env
    mkdir -p "$HOME/ol-migration"
    cd "$HOME/ol-migration"
    cp /opt/rhel-to-oracle-linux/migrate-to-oracle-linux.sh .
    printf '%s  migrate-to-oracle-linux.sh\n' "$MIGRATION_SHA256" | sha256sum --check
    </copy>
    ```

    Expected result: `migrate-to-oracle-linux.sh: OK`. Stop if the digest does not match. The checksum checks agreement with the instructor's reviewed artifact; do not replace it with a value calculated from an unexpected download.

2. Inspect the script version and options:

    ```bash
    <copy>
    chmod 0755 migrate-to-oracle-linux.sh
    ./migrate-to-oracle-linux.sh --version
    ./migrate-to-oracle-linux.sh --help | less
    </copy>
    ```

    Press **q** to exit `less`. This workshop uses the same major release and explicitly selects RHCK.

## Task 3: Run and review the dry run

1. Run the assessment:

    ```bash
    <copy>
    sudo ./migrate-to-oracle-linux.sh --dry-run --target-version "$TARGET_VERSION" --kernel rhck
    printf 'Dry-run exit status: %s\n' "$?"
    </copy>
    ```

    Require exit status `0` and a reviewed summary with no unresolved blocker. A dry run produces logs and reports without performing the OS conversion.

2. Locate the run directory and log:

    ```bash
    <copy>
    sudo ls -1dt /var/lib/migrate-to-oracle-linux/* | head -1
    sudo ls -1t /var/log/migrate-to-oracle-linux/*.log | head -1
    </copy>
    ```

    Review the log path displayed by the script using `sudo less <log-path>`. Search for errors, warnings, unavailable packages, and repository problems. Press **q** to exit. A successful application response does not prove migration compatibility with the source repositories.

3. Ask the instructor to confirm that the recovery point is available and its restore procedure has been tested. Record the instructor's checkpoint reference in your notes.

4. Continue only when the script checksum passes, the dry run succeeds, warnings have been reviewed, the application remains healthy, and the instructor confirms recovery readiness. Preserve logs and stop if any required condition fails.

## Learn More

- [Migration script documentation](https://github.com/oracle/migrate-to-ol/blob/main/README-migrate-to-oracle-linux.md).

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle.
- **Last Updated By/Date** - Workshop adaptation draft, October 19, 2026.
