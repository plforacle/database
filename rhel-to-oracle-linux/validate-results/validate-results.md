# Lab 6: Validate the Results

## Introduction

Compare the source, post-migration, and final system states. Record the outcome of each exercise separately so that an incomplete live-patching or management exercise is visible alongside a successful OS migration.

Estimated Time: 15 minutes.

### Objectives

- Validate the final system and application.
- Compare evidence and classify package exceptions.
- Preserve results and report the workshop outcome.

### Prerequisites

- The baseline and post-migration snapshots.
- Recorded Ksplice and OS Management Hub outcomes, including any limitations.

## Task 1: Capture and validate the final state

1. After any management job completes, run:

    ```bash
    <copy>
    bash /opt/rhel-to-oracle-linux/validate-system.sh
    bash /opt/rhel-to-oracle-linux/capture-state.sh final
    </copy>
    ```

    Require successful validation and capture. Investigate errors before declaring the migration successful.

2. Compare the application response and file checksum:

    ```bash
    <copy>
    diff -u "$HOME/ol-migration-evidence/before/application.html" \
      "$HOME/ol-migration-evidence/final/application.html"
    diff -u "$HOME/ol-migration-evidence/before/application-sha256.txt" \
      "$HOME/ol-migration-evidence/final/application-sha256.txt"
    </copy>
    ```

    Both comparisons should return no differences and exit status `0`.

3. Compare the operating-system and repository evidence:

    ```bash
    <copy>
    diff -u "$HOME/ol-migration-evidence/before/os-release.txt" \
      "$HOME/ol-migration-evidence/after-migration/os-release.txt"
    diff -u "$HOME/ol-migration-evidence/before/repositories.txt" \
      "$HOME/ol-migration-evidence/final/repositories.txt"
    </copy>
    ```

    Differences are expected here; `diff` returns `1` when files differ and a value greater than `1` on an error. Explain the identity change and the repository changes from migration and management registration.

## Task 2: Review exceptions and outcomes

1. Review the migration reports with the instructor. Classify retained third-party packages, unavailable replacements, and source-vendor exceptions. An unexplained required-package loss is not a successful migration.

2. Record each result in your notes:

    | Exercise | Evidence required |
    | --- | --- |
    | Migration | Oracle Linux identity at the target release, Oracle RHCK, healthy RPM database, repositories, services, security checks, and matching application files |
    | Ksplice | Successful patch command, installed patch details, unchanged boot ID, and healthy application checks; otherwise record preview-only or incomplete |
    | OS Management Hub | Healthy registration, service inventory, successful managed request, installed package, and healthy application; otherwise record incomplete or instructor-demonstrated |

3. Explain why the migration required a reboot and the later Ksplice exercise did not. An unchanged `uname -r` alone does not prove that a live patch was installed.

## Task 3: Preserve evidence and hand back the server

1. Ask the instructor to export the migration run reports and logs from the recorded privileged paths into your evidence directory. Do not include credentials or unrelated system files.

2. Archive the evidence in your SSH session:

    ```bash
    <copy>
    tar -C "$HOME" -czf "$HOME/rhel-to-oracle-linux-evidence.tar.gz" ol-migration-evidence
    ls -lh "$HOME/rhel-to-oracle-linux-evidence.tar.gz"
    </copy>
    ```

3. If your workshop permits file transfer over SSH, copy the archive from your local terminal:

    ```bash
    <copy>
    scp -i "<private-key-path>" <ssh-user>@<assigned-host>:rhel-to-oracle-linux-evidence.tar.gz ./
    </copy>
    ```

    If file transfer is restricted, follow the instructor's evidence collection process. Notify the instructor when you finish. The instructor handles unregistering, reset, and cleanup; participants do not delete infrastructure.

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle.
- **Last Updated By/Date** - Workshop adaptation draft, October 19, 2026.
