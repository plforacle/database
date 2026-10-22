# Lab 3: Assess Readiness and Protect the Source

## Introduction

Migration begins with assessment and a recoverable checkpoint. In this lab, you inspect repositories, packages, disk space, services, and the running kernel. You stage the workshop safety candidate, verify its checksums, run a dry run, and create a boot-volume backup before changing the operating system. A checksum identifies the candidate; it does not prove that it works on this source image. A dry run checks readiness and writes reports without performing package conversion. It does not execute kernel RPM scriptlets or prove that the VM will boot.

The migration script is maintained in an Oracle-owned GitHub repository, but the repository states that the scripts are community-based and are not officially supported by Oracle.

Earlier attempts exposed missing kernel artifacts, a `libattr` loader failure during package downgrade, and incomplete GRUB error detection. Use the verified workshop.5 bundle, which checks a coherent Oracle repository catalogue, requires release 9.8, rejects `libattr` and `libacl` downgrades, defers shim/GRUB work until after the explicit Oracle kernel stage, detects GRUB errors, and narrowly verifies FAT timestamp differences. Do not substitute the unmodified upstream script.

On October 22, 2026, this candidate passed conversion, independent boot inspection, reboot, SSH, security, package, and workload checks on one OCI-provided RHEL 9.8 source after its source GRUB issue was corrected. See the [controlled migration record](../qa/source4-run-20261022.md). This is a tested path for that prepared source, not a guarantee for other image versions or a replacement for this lab's checks.

Preserve every failed instance, its boot volume, all recovery backups and evidence, the successful imported-image reference, and the shared network. Do not rerun conversion or reboot a partially converted failed guest.

Pass the migration checkpoint below on the current source. It records readiness, workload health, and a Full, Available prepared-source recovery point. It does not prove that package scriptlets or a later boot will succeed on a new run.

### Objectives

In this lab, you will:

- Assess the source system for common migration risks.
- Stage and verify the workshop safety candidate.
- Run a non-changing migration dry run.
- Create and verify a boot-volume backup.
- Document the rollback decision point.

### Prerequisites

Before beginning this lab, confirm that you have:

- Completed Lab 2, including successful source GRUB probes, any approved correction, and the required RHEL reboot.
- SSH access to the running RHEL 9.8 x86_64 source VM.
- A healthy Apache workload that returns `MIGRATION_WORKLOAD_OK`.
- The final prepared-source RHEL baseline evidence bundle created in Lab 2.
- Permission to create and inspect boot-volume backups in OCI.
- Outbound HTTPS access from the source VM to `raw.githubusercontent.com` and `yum.oracle.com`.
- OCI Console access to inspect the fresh VM, its attached boot volume, and the backup. Cloud Shell and a local OCI CLI are not required.

> **Note:** This workshop uses the public Oracle Linux yum service. In a private or restricted network, use the migration script's `--yum-mirror` option with an approved internal HTTPS mirror, or its `--proxy` option with an HTTP proxy. Do not use both options together. Confirm that the selected path works during the dry run before creating the recovery point.

Estimated Lab Time: 30 minutes

## Task 1: Assess the source operating system

1. Confirm the source version and architecture:

    ```bash
    <copy>
    . /etc/os-release
    printf 'ID=%s VERSION_ID=%s ARCH=%s\n' "$ID" "$VERSION_ID" "$(uname -m)"
    </copy>
    ```

    Continue only when the source is RHEL 9.8 on x86_64.

2. Confirm that RHEL is using the newest installed kernel.

    RHEL can keep more than one kernel. A system update may install a newer kernel, but RHEL does not use it until the VM restarts. The following commands show the kernel running now, all installed kernels from oldest to newest, and the kernel selected for the next restart:

    ```bash
    <copy>
    uname -r
    rpm -q kernel-core | sort -V
    sudo grubby --default-kernel
    </copy>
    ```

    Compare the three results. The version shown by `uname -r` should match the newest installed `kernel-core` version and the version shown in the default kernel path. Older installed kernels are normal on the unconverted source. If the newest version does not match the running version, first pass Lab 2's source boot-file gate, then reboot the fresh VM, reconnect, and repeat this step.

3. Check disk space and the RPM database:

    ```bash
    <copy>
    df -h / /boot /boot/efi /var /tmp
    df -i / /boot /var /tmp
    sudo rpm --verifydb
    sudo dnf check
    </copy>
    ```

    The selected image showed a 64 GB boot volume, but migration depends on free filesystem space and inodes. Resolve any script-reported space requirement before proceeding.

4. Inventory enabled repositories:

    ```bash
    <copy>
    sudo dnf repolist --enabled
    sudo find /etc/yum.repos.d -maxdepth 1 -type f -name '*.repo' -print
    </copy>
    ```

5. Identify packages that are not signed by Red Hat or have a different vendor:

    ```bash
    <copy>
    rpm -qa --qf '%{NAME}\t%{VENDOR}\n' \
      | awk -F '\t' '$2 !~ /Red Hat/ {print}' \
      | sort | head -50
    </copy>
    ```

    Third-party packages are not automatically proof of a blocker. They require application-owner review.

6. Confirm the workshop workload remains healthy:

    ```bash
    <copy>
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    getenforce
    </copy>
    ```

## Task 2: Stage and verify the safety candidate

1. Create a working directory:

    ```bash
    <copy>
    mkdir -p "$HOME/ol-migration"
    cd "$HOME/ol-migration"
    </copy>
    ```

2. On your local computer, obtain these three files from this checkout or download them from the matching workshop revision. Save them together without editing or converting line endings:

    - [Migration safety candidate](../migrate-to-oracle-linux/files/migrate-to-oracle-linux.sh)
    - [Migration launcher](../migrate-to-oracle-linux/files/run-migration.py)
    - [Candidate checksums](../migrate-to-oracle-linux/files/SHA256SUMS)

    For an unpublished rehearsal, use the files in `oracle-linux-rhel-migration-oci/migrate-to-oracle-linux/files` from the local checkout. Public LiveLabs will not contain local changes until a later authorized publication. Do not substitute a download from `main` or the previous published revision.

3. In a local terminal opened in that files directory, transfer the bundle to the fresh VM:

    ```bash
    <copy>
    scp -i "<private-key-path>" migrate-to-oracle-linux.sh run-migration.py SHA256SUMS \
      <ssh-user>@<fresh-public-ip>:ol-migration/
    </copy>
    ```

4. Back in the VM's SSH session, verify the entire bundle:

    ```bash
    <copy>
    cd "$HOME/ol-migration"
    sha256sum --check SHA256SUMS
    </copy>
    ```

    Expected output:

    ```text
    migrate-to-oracle-linux.sh: OK
    run-migration.py: OK
    ```

5. Inspect the candidate version and help:

    ```bash
    <copy>
    bash ./migrate-to-oracle-linux.sh --version
    bash ./migrate-to-oracle-linux.sh --help | less
    </copy>
    ```

    After reviewing the help text, press the letter **q** to exit the **less** viewer and return to the shell prompt.

    The candidate derives from upstream commit `82947c960535cfbb632977c70b0fea67f8e0de7a`, whose original SHA-256 was `5435b59e2f0e672111fd1545b2b428cabe473e7c116a353716c2f7de869d0a42`. That original checksum must not match the corrected candidate. Review the [controlled migration record](../qa/source4-run-20261022.md) and its exact bundle checksums with the instructor.

## Task 3: Run the migration dry run

1. Run the non-changing assessment for an Oracle Linux 9.8 RHCK target:

    ```bash
    <copy>
    sudo bash ./migrate-to-oracle-linux.sh \
      --dry-run \
      --target-version 9.8 \
      --kernel rhck
    </copy>
    ```

2. Review the final dry-run summary.

    This source uses OCI-hosted RHUI rather than personal subscription registration. A successful launch and Apache response do not establish migration compatibility. Verify that this pinned script accepts the RHUI-backed source and reports no unresolved repository or package blocker. Preserve the dry-run log before conversion; do not remove RHUI packages or repository files manually to bypass a failure.

    Require the current safety candidate to resolve an exact Oracle kernel version and check its proposed transaction before any source-package changes. It must also check the exact and nearest package replacement plans. With a newer RHEL kernel already installed, a name-only kernel request can otherwise skip Oracle kernel installation.

    Require the unfiltered release-catalogue check to identify Oracle Linux 9.8. A newer minor release is a blocker even when ordinary package queries filter out later-minor builds. Review the `libattr` and `libacl` replacement plans: a downgrade of either library is a blocker. Earlier replacement transactions must exclude runtime kernels and the deferred shim/GRUB group. The bootloader plan is applied only after the explicit Oracle kernel passes its initial artifact checks. See the [repository and bootloader review](../qa/repository-review-20261020.md).

    These DNF checks deliberately use `--assumeno`. Their logs show the proposed transactions followed by `Operation aborted.` because nothing was applied. That expected cancellation is accepted only when dependencies and the transaction plan resolved successfully and the script's removal checks passed. The overall dry run must still return `0`. A dependency error, an unexpected removal, or a nonzero overall dry-run status is a blocker.

    Confirm that the script completed its assessment without converting the operating system. Look for messages marked as errors, failures, or blockers. A warning does not always prevent migration, but you must understand and resolve any warning that affects repositories, packages, disk space, or the running kernel before continuing. Use the [controlled source-4 readiness review](../qa/source4-readiness-20261022.md) as an example, and create a separate review of the current source's package exceptions and transaction plans. Do not reuse acceptance from another instance or an earlier candidate.

    Record the run directory displayed near the end of the output. The directory contains reports and snapshots from this assessment and helps you investigate any reported problem.

3. Locate the newest migration state directory and log:

    ```bash
    <copy>
    sudo ls -1dt /var/lib/migrate-to-oracle-linux/* | head -1
    sudo ls -1t /var/log/migrate-to-oracle-linux/*.log | head -1
    </copy>
    ```

4. Decide whether it is safe to continue.

    Do not continue if the dry run reports an unresolved error or blocker. Correct the problem and repeat the dry run before proceeding. Common blockers include:

    The script reports problems in the terminal while the dry run runs and records the same information in the log identified in Step 3. The following command opens the newest migration log:

    ```bash
    <copy>
    LATEST_LOG=$(sudo ls -1t /var/log/migrate-to-oracle-linux/*.log | head -1)
    sudo less "$LATEST_LOG"
    </copy>
    ```

    In the `less` viewer, enter `/ERROR` and press Enter to search for errors. You can also search for `/WARN` and `/FAIL`. Press the letter **q** to close the log and return to the shell prompt. The examples below are possible problems, not a separate list or screen that you must locate.

    - A RHEL version or processor architecture that the script does not support.
    - A target with a different major version, such as RHEL 8 to Oracle Linux 9.
    - A damaged or inconsistent RPM package database.
    - Insufficient free disk space.
    - A newer kernel that is installed but not currently running.
    - RHEL or Oracle Linux software repositories that the VM cannot reach.

## Task 4: Record source image and RHUI provenance

1. Confirm the baseline includes `$HOME/ol-migration-evidence/before/source-instance.txt` with the source image name and OCID, instance name and OCID, region, shape, memory, boot-volume size, and SSH account. Use the OCI instance and image details to complete any missing fields.

2. Capture the current RHUI-related package and repository state:

    ```bash
    <copy>
    sudo dnf repolist --enabled > "$HOME/ol-migration-evidence/before/repositories.txt"
    rpm -qa | grep -Ei 'rhui|subscription-manager|oracle-cloud-agent' \
      > "$HOME/ol-migration-evidence/before/source-management-packages.txt" || true
    </copy>
    ```

3. Record the successful metadata refresh from Lab 2. This OCI source did not require personal Red Hat registration, so there is no subscription consumer ID to collect. Preserve RHUI evidence to compare repository and vendor-package changes after conversion.

## Task 5: Create the pre-migration recovery point

This backup must cover the final prepared source after its updates, any approved metadata correction, and successful RHEL reboot. A backup taken before a correction protects the earlier state and remains valuable, but does not contain that correction. Keep both checkpoints when a correction was needed. No additional backup is required if an existing Full, Available backup already covers the final prepared source and meets the launcher's age limit.

1. Write pending filesystem changes to disk, then temporarily stop the Apache web server before creating the boot-volume backup:

    ```bash
    <copy>
    sync
    sudo systemctl stop httpd
    sync
    </copy>
    ```

2. In the OCI Console, open the fresh RHEL instance for this run, such as `ol-migrate-rhel-source-4`. Check its instance OCID against your notes. Do not select a preserved failed instance or the successful reference instance.

3. On the instance details page, select the **Storage** tab. Under **Boot volumes**, select the boot-volume name attached to the instance.

4. On the boot-volume details page, select **Create backup**.

5. Enter the following values:

    - Name: `ol-migrate-source-4-ready-for-migration` (replace `source-4` with your current source identifier)
    - Backup type: **Full backup**

6. Select **Create boot volume backup**.

7. Open the boot volume's **Backups** tab and wait until the backup state becomes Available.

8. Return to the SSH session and restart Apache, then verify that the workshop webpage responds:

    ```bash
    <copy>
    sudo systemctl start httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

9. In the OCI Console, record the following values from the fresh instance, its boot volume, and the backup details:

    - Fresh instance OCID
    - Currently attached boot-volume OCID
    - Backup OCID
    - Backup type: **Full**
    - Backup state: **Available**
    - Backup creation time, including the displayed timezone

    Open the backup's details to inspect its state and creation time. The backup row's **Actions** menu offers **Copy OCID** and **Copy backup source OCID** for copying the backup and source-volume identifiers. Compare the source identifier with the attached boot volume on the fresh instance's Storage tab.

    Match the backup's source boot-volume OCID to the boot volume attached to this fresh instance. Confirm that the backup was created after final source preparation and its successful RHEL reboot. Keep this backup; no CLI export or replacement backup is required merely because you do not have CLI access.

10. In the fresh VM's existing SSH session, record the Console confirmation with the verified launcher:

    ```bash
    <copy>
    cd "$HOME/ol-migration"
    sha256sum --check SHA256SUMS
    sudo python3 ./run-migration.py \
      --record-console-checkpoint "$PWD/console-checkpoint.json"
    </copy>
    ```

    The launcher displays the guest's instance OCID from OCI Instance Metadata Service v2. Match it to the fresh instance shown in the Console. At the prompts, enter the backup OCID, attached boot-volume OCID, and backup creation time. Use ISO 8601 with a timezone, such as `2026-10-20T14:00:00-04:00` for 2 PM in New York on that date, or a timestamp ending in `Z` for UTC. Enter the actual backup time, not this example.

    Type `yes` only after confirming in the Console that the backup is Full and Available, its source volume is currently attached to the displayed instance, and source preparation finished before the backup. The command records a manual confirmation and exits. It does not migrate, reboot, create a backup, or query the backup service.

    Require exit status `0` and confirmation that the checkpoint was saved. Use a new checkpoint filename for a later backup; the recorder does not overwrite evidence. Use that same filename in Lab 4. If the option is unrecognized, restage the current launcher and `SHA256SUMS` from Lab 3, Task 2. Do not bypass the launcher or handwrite fake CLI responses.

11. Keep the backup available until the migration and recovery rehearsal are accepted. This workshop's launcher requires a full backup created within the last 24 hours; that is a rehearsal checkpoint rule, not an OCI backup expiration limit. If that window expires, take a new backup and record a new checkpoint. The saved confirmation is human evidence, not authenticated service verification. Recheck the Full and Available state, source volume, and current instance in the Console immediately before conversion. No OCI credentials are copied to the guest. Existing users with authenticated CLI access may still use the launcher's original backup and attachment JSON options; this is optional.

## Task 6: Pass the migration gate

1. Confirm every required condition:

    - Dry run completed without an unresolved blocker.
    - The exact Oracle kernel plan and the package replacement solvers passed before any source-package changes. The kernel plan installs Oracle RHCK and retains the source runtime kernels.
    - The unfiltered release catalogue resolves 9.8, both library-downgrade guards pass, and shim/GRUB work is deferred until after the explicit Oracle kernel stage.
    - The verified script checksum returned OK.
    - The candidate's dependency checks passed, and the source still has its kernel image, initramfs, and boot entry. Source GRUB probes pass, any approved correction is accepted, and the prepared source passed its RHEL reboot.
    - Source image and instance provenance and RHUI evidence were recorded.
    - The Full boot-volume backup state is Available and covers the final prepared source, including any correction and the successful RHEL reboot.
    - The recorded Console checkpoint identifies this fresh VM, its current boot volume, and the Full, Available backup. Existing CLI users may supply the equivalent backup and attachment service records instead.
    - The instructor has the backup OCID and documented restoration procedure. Record any separate restore test as its own result; do not claim that Available alone proves restoration was tested.
    - Apache is active after the backup.
    - The workload marker is returned.

2. If any condition fails, stop here and correct it before continuing.

3. Review the rollback decision:

    If the migration fails and the VM cannot return to a stable boot and application state within the bootcamp recovery window, preserve the logs, stop troubleshooting, and restore from the pre-migration boot-volume backup using the documented recovery procedure. Record whether restoration was actually rehearsed; a backup marked Available does not prove a restore test was completed.

## Learn More

- [Oracle migration repository](https://github.com/oracle/migrate-to-ol)
- [Migration script documentation](https://github.com/oracle/migrate-to-ol/blob/main/README-migrate-to-oracle-linux.md)
- [Creating a boot-volume backup](https://docs.oracle.com/en-us/iaas/Content/Block/Tasks/backingupavolume.htm)
- [Viewing backups and copying their OCIDs in the Console](https://docs.oracle.com/en-us/iaas/Content/Block/Tasks/list-bv-boot-volume-backup.htm)
- [DNF command reference](https://dnf.readthedocs.io/en/stable/command_ref.html)

### Workshop maintenance note

The safety candidate is a local workshop modification, not an upstream release or an officially supported migration tool. Its October 22, 2026, controlled OCI-image migration and boot result applies to the recorded bundle and prepared source. Record each later run's exact candidate checksums, image OCID, reports, boot results, and any actual recovery test together. Do not label a dry run or mocked test as a successful migration, or an Available backup as a tested restore. Do not substitute an unpinned `main` branch script.

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026

