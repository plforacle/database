# Lab 12: Automate Application Health Checks

## Introduction

Build a small Bash health check, run it as an unprivileged systemd service, and schedule it with a timer. A controlled Apache stop demonstrates a failed check and recovery. The check reports problems; it does not automatically restart or reconfigure the application.

### Objectives

In this lab, you will:

- Write a script that reports PASS/FAIL and meaningful exit status.
- Install an unprivileged oneshot systemd service and timer.
- Observe a controlled failure and successful recovery.
- Review retained evidence and choose optional final cleanup.

Estimated Lab Time: 30 minutes

### Prerequisites

Complete Lab 11 on the Oracle Linux destination VM. Apache must be active and the original marker must respond. The commands use the existing `cloud-user` account and reserve `workshop-health.service`, `workshop-health.timer`, and `/usr/local/bin/workshop-health.sh`.

Follow the guided path if you are new to the tools. Experienced learners can attempt the independent challenge within the same lab time and must still pass the completion checks.

## Task 1: Create and inspect the health-check script

1. Create a home-directory source copy:

    ```bash
    <copy>
    mkdir -p "$HOME/ol-practice-reports"
    cat > "$HOME/ol-practice-reports/workshop-health.sh" <<'EOF'
    #!/usr/bin/env bash
    set -u
    set -o pipefail
    failures=0
    check() {
      local description="$1"
      shift
      if "$@"; then
        printf 'PASS: %s\n' "$description"
      else
        printf 'FAIL: %s\n' "$description"
        failures=$((failures + 1))
      fi
    }
    printf 'Health check at %s\n' "$(date -Is)"
    check 'Apache is active' systemctl is-active --quiet httpd
    check 'Original workload responds' bash -o pipefail -c \
      'curl --fail --silent --show-error --max-time 5 http://127.0.0.1/ | grep -q MIGRATION_WORKLOAD_OK'
    disk_ok() {
      df -P / | awk 'NR == 2 {gsub(/%/, "", $5); exit !($5 + 0 < 90)}'
    }
    check 'Root filesystem is below 90 percent used' disk_ok
    if (( failures > 0 )); then
      printf 'Health check failed: %s check(s).\n' "$failures"
      exit 1
    fi
    printf 'HEALTH_CHECK_OK\n'
    EOF
    chmod u+x "$HOME/ol-practice-reports/workshop-health.sh"
    bash -n "$HOME/ol-practice-reports/workshop-health.sh"
    </copy>
    ```

    `check` runs each test and increments a failure counter. Exit status 0 means every check passed; status 1 reports at least one problem. `pipefail` prevents a failed command in a pipeline from being hidden by a later command.

2. Run the script and inspect its return code:

    ```bash
    <copy>
    "$HOME/ol-practice-reports/workshop-health.sh"
    printf "Health script exit status: %s\n" "$?"
    </copy>
    ```

    Expect three PASS lines, `HEALTH_CHECK_OK`, and status 0. If disk use is already 90 percent or higher, investigate actual disk usage; do not fill the disk to manufacture this fault.

## Task 2: Install a service that runs as cloud-user

1. Install the script and define the service:

    ```bash
    <copy>
    sudo install -m 0755 "$HOME/ol-practice-reports/workshop-health.sh" \
      /usr/local/bin/workshop-health.sh
    sudo restorecon -v /usr/local/bin/workshop-health.sh
    sudo tee /etc/systemd/system/workshop-health.service >/dev/null <<'EOF'
    [Unit]
    Description=Workshop Apache health check
    After=network-online.target httpd.service
    
    [Service]
    Type=oneshot
    User=cloud-user
    ExecStart=/usr/local/bin/workshop-health.sh
    EOF
    sudo systemctl daemon-reload
    sudo systemctl start workshop-health.service
    sudo journalctl -u workshop-health.service -n 20 --no-pager
    </copy>
    ```

    Confirm the PASS results in the journal. A successful oneshot service usually becomes inactive after finishing; that does not mean the check failed. Inspect its result and exit code:

    ```bash
    <copy>
    systemctl show workshop-health.service -p User -p Result -p ExecMainStatus
    </copy>
    ```

    Expect `User=cloud-user`, `Result=success`, and `ExecMainStatus=0`. If startup fails, check the script path, syntax, permissions, and journal before continuing.

## Task 3: Schedule recurring checks

1. Create a one-minute timer:

    ```bash
    <copy>
    sudo tee /etc/systemd/system/workshop-health.timer >/dev/null <<'EOF'
    [Unit]
    Description=Run the workshop health check every minute
    
    [Timer]
    OnBootSec=2min
    OnUnitActiveSec=1min
    AccuracySec=5s
    Unit=workshop-health.service
    
    [Install]
    WantedBy=timers.target
    EOF
    sudo systemctl daemon-reload
    sudo systemctl enable --now workshop-health.timer
    systemctl list-timers --all workshop-health.timer
    </copy>
    ```

    Look for a scheduled next run. `OnBootSec` schedules the first check relative to boot; `OnUnitActiveSec` schedules later checks after previous activations. The timer runs at approximately one-minute intervals, not at a guaranteed wall-clock second.

2. After about one minute, inspect recent results:

    ```bash
    <copy>
    sudo journalctl -u workshop-health.service --since "3 minutes ago" --no-pager
    </copy>
    ```

    You can continue to Task 4 while waiting; do not paste repeated start commands merely to produce timer activity.

## Task 4: Observe failure and recover

1. Stop Apache and trigger a health check:

    ```bash
    <copy>
    sudo systemctl stop httpd
    sudo systemctl start workshop-health.service
    sudo journalctl -u workshop-health.service -n 25 --no-pager
    </copy>
    ```

    A nonzero service-start result and FAIL messages are expected. They demonstrate detection, not a migration failure. The disk check may still pass.

2. Recover Apache and run the check again:

    ```bash
    <copy>
    sudo apachectl configtest && sudo systemctl start httpd
    sudo systemctl reset-failed workshop-health.service
    sudo systemctl start workshop-health.service
    systemctl show workshop-health.service -p Result -p ExecMainStatus
    sudo journalctl -u workshop-health.service -n 20 --no-pager
    </copy>
    ```

    Expect `Result=success`, `ExecMainStatus=0`, and `HEALTH_CHECK_OK`. If interrupted after stopping Apache, run this recovery step before continuing.

3. Verify the final application and original checksum:

    ```bash
    <copy>
    systemctl is-enabled httpd
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    curl --fail --silent http://127.0.0.1/practice/ | grep ORACLE_LINUX_PRACTICE_OK
    curl --fail --silent http://127.0.0.1/secure-practice/ | grep SECURE_PRACTICE_OK
    sha256sum --check "$HOME/ol-migration-evidence/before/application-sha256.txt"
    getenforce
    systemctl --failed --no-pager
    </copy>
    ```

    All markers must appear, the checksum must be OK, SELinux Enforcing, and no unexpected failed units should remain. Resolve any failure before optional cleanup.

## Task 5: Complete the independent challenge and checkpoint

1. Challenge: explain why the timer is active while a successful oneshot service is inactive, why the check runs as `cloud-user`, and how a failed exit status helps automation. Add a read-only practice-page test to your home-directory script if you finish early, then syntax-check and run that copy. Do not change the installed service script until your new check passes.

2. Record the normal result, controlled-failure result, and recovered result in `$HOME/ol-practice-reports/health-check-results.txt`. Verify that an executed timer run appears in the journal.

3. Stop recurring practice checks when the exercise is complete:

    ```bash
    <copy>
    sudo systemctl disable --now workshop-health.timer
    systemctl is-enabled workshop-health.timer
    </copy>
    ```

    `disabled` is expected and produces a nonzero query status. Keep the unit files and script for review. The Apache service stays running.

4. Complete when the timer has run, the failure was detected, recovery returned success, all application checks pass, and recurring checks are disabled. Continue to the final evidence and optional cleanup tasks below.

## Task 6: Save the practice results

1. Confirm that the practice archives are readable by your normal user, then bundle your quickstart files, reports, and application backup. The backup archive ownership was set in Lab 11.

    ```bash
    <copy>
    tar -C "$HOME" -czf "$HOME/ol-practice-results.tar.gz" \
      linux-quickstart ol-practice-reports ol-practice-backups
    ls -lh "$HOME/ol-practice-results.tar.gz"
    </copy>
    ```

2. Download the bundle from your local terminal before deleting the instance:

    ```bash
    <copy>
    scp -i <private-key-path> cloud-user@<public-ip>:ol-practice-results.tar.gz .
    </copy>
    ```

3. Keep the before/after migration archives saved in Lab 5 as separate evidence. Record the kernel-choice, Ksplice, package, Apache, security, restore, and automation outcomes in your notes. Keep Ksplice results separate from the original migration evidence, including any unresolved eligibility or access issue.

4. Choose whether to keep the instance for further practice. Keeping it is a valid completion choice. Follow Task 7 only if you are finished and want to remove temporary resources; the following optional Console actions are outside the terminal-based skill exercises.

## Task 7: Optionally delete temporary OCI resources

1. Confirm that you no longer need the instance and have saved the evidence you want to retain. Perform only the deletions you choose below.

2. In the destination tenancy, open **Compute** and terminate `ol-migrate-rhel-source`. Select boot-volume deletion only if you no longer need that volume.

3. If you created `ol-migrate-before-conversion` and no longer need it, delete that boot-volume backup. Skip this step if no backup was created.

4. Delete network security groups and VCN resources only if they were created for this exercise and no retained instances or other resources depend on them. Keep shared networking.

5. Delete a workshop compartment only if it is empty and no longer needed. A compartment containing reusable images or image objects should be retained.

6. Review only your student resources and record what was retained. Keep your imported `rhel-9-8-apache-source` image for rebuilding if needed. Instructor preparation VMs, source images, export objects, buckets, and PARs remain under instructor control; do not delete or revoke them.

## Learn More

- [Oracle systemd timers tutorial](https://docs.oracle.com/en/learn/ol-chrony-systemd-timers/index.html)
- [Oracle systemd guide](https://docs.oracle.com/en/operating-systems/oracle-linux/9/systemd/OL9-SYSTEMD.pdf)
- [GNU Bash manual](https://www.gnu.org/software/bash/manual/bash.html)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
