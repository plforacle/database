# Lab 9: Explore Rebootless Patching with Ksplice

## Introduction

Evaluate Ksplice after migration validation and the kernel-choice exercise. The Uptrack client can apply available supported kernel fixes to the running system without a reboot. You will record the boot identity, sample the original application's HTTP responses, inspect available live patches, and verify the outcome.

This lab uses the standalone command-line client, not Cloud Agent. The OCI installation path below applies to the migrated BYOI instance used in this workshop. On OCI, Oracle documents access without ULN registration. Outside OCI, arrange the required Oracle Linux support entitlement and use its documented client-installation path before joining Task 3; do not use the OCI installer as a general-purpose entitlement workaround.

### Objectives

In this lab, you will:

- Check kernel maintenance and Ksplice access requirements.
- Install Uptrack on the converted OCI instance.
- Inspect and apply available live kernel updates when supported.
- Compare boot identity and observed application availability.
- Distinguish live patching from on-disk package maintenance.

Estimated Lab Time: 30 minutes

### Prerequisites

Complete Labs 7 and 8, keeping the migration evidence unchanged. Use a healthy Oracle Linux 9.8 destination VM with a maintained Oracle UEK or RHCK, internet access, and sudo privileges. Verify the current maintained-kernels documentation linked below. Do not assume that a particular patch will be available on the workshop date.

If access or kernel support prevents the live exercise, record the actual error and discuss it with the instructor. Other administration labs may continue, but do not report that patches were applied. Beginners follow the guided path; experienced learners should explain the evidence that distinguishes a successful patch application from a successful no-updates check.

## Task 1: Record the starting state and monitor the application

1. Keep one SSH session open for the entire lab. Record the current boot ID, kernel, application process, and checksum:

    ```bash
    <copy>
    REPORT="$HOME/ol-practice-reports/ksplice"
    mkdir -p "$REPORT"
    cat /proc/sys/kernel/random/boot_id > "$REPORT/boot-id-before.txt"
    uname -r > "$REPORT/boot-kernel-before.txt"
    rpm -qf "/boot/vmlinuz-$(uname -r)" --qf '%{NAME} %{VERSION}-%{RELEASE} %{VENDOR}\n'
    systemctl show httpd -p MainPID --value > "$REPORT/httpd-pid-before.txt"
    sha256sum --check "$HOME/ol-migration-evidence/before/application-sha256.txt"
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

    Stop if the application is unhealthy or the kernel is not Oracle-owned. Confirm eligibility against the maintained-kernels reference. A family being maintained does not guarantee that every newly released build already has live patches.

2. Start a lightweight background HTTP monitor in this same session:

    ```bash
    <copy>
    (
      set -o pipefail
      while :; do
        printf '%s ' "$(date -Is)"
        if curl --fail --silent --show-error --max-time 5 http://127.0.0.1/ \
          | grep -q MIGRATION_WORKLOAD_OK; then
          printf 'HTTP_OK\n'
        else
          printf 'HTTP_FAIL\n'
        fi
        sleep 2
      done
    ) > "$REPORT/http-monitor.log" 2>&1 &
    MONITOR_PID=$!
    printf 'Monitor process: %s\n' "$MONITOR_PID"
    </copy>
    ```

    Keep this session and its `MONITOR_PID` variable. Do not reboot or deliberately stop Apache during this lab. The monitor starts before installation so it also covers setup activity. If you stop the lab early, stop this background job using Task 4, Step 2 before closing the session.

## Task 2: Install the client on the OCI BYOI instance

1. Check whether the Uptrack client is already available:

    ```bash
    <copy>
    command -v uptrack-show
    rpm -q uptrack
    </copy>
    ```

    A missing command/package is expected on this converted custom image. If installed, retain it and continue to Task 3. First-time installation is covered below.

2. On the OCI instance only, download the official installer into a working directory:

    ```bash
    <copy>
    mkdir -p "$HOME/ol-ksplice"
    cd "$HOME/ol-ksplice"
    curl --fail --location --output install-uptrack-oc \
      https://www.ksplice.com/uptrack/install-uptrack-oc
    </copy>
    ```

    Continue only after a successful download from the official HTTPS endpoint. If the download or installation fails, retain the error; do not disable TLS or package-signature checks or download a replacement from an unverified source.

3. Run the installer without enabling unattended updates:

    ```bash
    <copy>
    sudo sh install-uptrack-oc
    command -v uptrack-show
    rpm -q uptrack
    </copy>
    ```

    Do not pass `--autoinstall` in this exercise. Inspect the installer's output because setup may install or apply updates as part of configuring the client. The boot-ID and application observations started before setup. If client installation fails, stop the monitor, record the failure, and ask the instructor to review access and support before continuing with Ksplice commands.

## Task 3: Inspect and apply available live kernel updates

1. Inspect the applied state, effective kernel, and available updates:

    ```bash
    <copy>
    sudo uptrack-show
    sudo uptrack-uname -r | tee "$REPORT/effective-kernel-after-install.txt"
    sudo uptrack-upgrade -n > "$REPORT/available-updates.txt" 2>&1
    UPTRACK_STATUS=$?
    cat "$REPORT/available-updates.txt"
    printf "Preview exit status: %s\n" "$UPTRACK_STATUS"
    </copy>
    ```

    Interpret the message, not just the exit status. `uname -r` reports the booted kernel; `uptrack-uname -r` reports the effective version recognized by Uptrack. They need not differ when no live patches have changed the effective version.

2. If supported updates are listed, apply them and record the resulting state:

    ```bash
    <copy>
    sudo uptrack-upgrade -y
    sudo uptrack-show | tee "$REPORT/applied-updates.txt"
    sudo uptrack-uname -r | tee "$REPORT/effective-kernel-after.txt"
    </copy>
    ```

    Run the apply command only after reviewing a valid available-update result. Keep the HTTP monitor running. If application fails, preserve the output and inspect the supported-kernel and access requirements; do not blindly rerun or remove all patches as a generic fix.

3. If no updates are available, record that outcome. If setup already applied updates, record those from `uptrack-show`. If the current kernel is reported as unsupported or has no patch stream yet, check the current maintenance reference with the instructor. A newly released kernel can lack published patches even when its family is maintained. Do not downgrade the kernel to manufacture a demonstration.

    An authorization, network, TLS, or repository error is not equivalent to having no updates available. Record unresolved access/support errors as a blocked live exercise, not successful patching. Continue to the observation and monitor-stop task in every case.

## Task 4: Verify the boot and application observations

1. Compare the recorded state without rebooting:

    ```bash
    <copy>
    cat /proc/sys/kernel/random/boot_id > "$REPORT/boot-id-after.txt"
    uname -r > "$REPORT/boot-kernel-after.txt"
    systemctl show httpd -p MainPID --value > "$REPORT/httpd-pid-after.txt"
    diff -u "$REPORT/boot-id-before.txt" "$REPORT/boot-id-after.txt"
    diff -u "$REPORT/boot-kernel-before.txt" "$REPORT/boot-kernel-after.txt"
    diff -u "$REPORT/httpd-pid-before.txt" "$REPORT/httpd-pid-after.txt"
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    sha256sum --check "$HOME/ol-migration-evidence/before/application-sha256.txt"
    </copy>
    ```

    Expect no differences, active Apache, the workload marker, and checksum OK. A changed boot ID means a reboot occurred and this run does not demonstrate rebootless patching. A changed Apache PID needs investigation before claiming the service was not restarted.

2. Stop only the monitor started in this SSH session and inspect its observations:

    ```bash
    <copy>
    kill "$MONITOR_PID"
    wait "$MONITOR_PID" 2>/dev/null || true
    tail -10 "$REPORT/http-monitor.log"
    grep -c HTTP_OK "$REPORT/http-monitor.log"
    grep -c HTTP_FAIL "$REPORT/http-monitor.log"
    </copy>
    ```

    Require at least one HTTP_OK observation and investigate any HTTP_FAIL result. A count of zero matches makes `grep -c` return status 1, which is expected for a zero-failure count. These two-second samples record observed responses; they are not a continuous load test or proof about every possible request.

    If the session was lost, the monitor and shell variables may also be gone. Reconnect, inspect any remaining log, and record the observation gap rather than guessing a process ID to kill. Repeat the monitoring part in a new session if complete evidence is needed.

## Task 5: Explain the result and complete the checkpoint

1. Record one accurate outcome in `$HOME/ol-practice-reports/ksplice/result.txt`:

    - Updates applied: list actual applied updates, unchanged boot identity, and application observations.
    - No additional updates: record the client state and availability result; do not claim you observed a new patch being applied.
    - Live exercise blocked: retain the real installation, access, or kernel-support error and the instructor's follow-up decision.

2. Challenge: explain why a boot-kernel version can stay unchanged while the effective kernel changes, why the UEK exercise required a reboot but this one should not, and why live patching does not replace updating on-disk packages. This Uptrack exercise addresses kernel updates; it does not demonstrate Enhanced Client user-space patching.

3. Complete when the monitor is stopped, Apache and its checksum are healthy, and the outcome is recorded accurately. Available updates should have been applied only when the supported apply path succeeded. Follow-on package maintenance still matters so that a future boot uses current on-disk software. Ksplice reduces reboot requirements for supported fixes; it does not eliminate every reason for maintenance or reboot.

4. Continue to Lab 10. If the live exercise was blocked, carry that open issue in your notes; completing other administration labs does not resolve or validate Ksplice access.

## Learn More

- [Installing Ksplice on OCI BYOI](https://docs.oracle.com/en-us/iaas/oracle-linux/oci/install-ksplice.htm)
- [Maintained Ksplice kernels](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-MaintainedKernels.html)
- [Uptrack command reference](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-UsingtheuptrackupgradeCommandtoManageKspliceUpdates.html)
- [Ksplice availability and entitlement](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-AboutOracleKsplice.html)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
