# Lab 9: Patch and Verify Oracle Linux with OS Management Hub

## Introduction

Use OSMH to apply a controlled update to the on-premises client registered in Lab 8. Compare package versions, inspect job results, plan a maintenance window, and verify that Apache continues to serve the application.

Estimated Lab Time: 30 minutes, plus download and job execution time

### Objectives

- Review inventory and apply one available non-kernel package update.
- Distinguish a submitted job from a completed package change.
- Schedule a maintenance job and record its actual execution state.
- Verify application health and preserve the workshop evidence.

### Prerequisites

Complete Lab 8 with an Active client, synchronized content, working repository metadata, and passing application checks. Use the assigned on-premises client throughout. Keep the shared station available and the migration VM and its evidence intact.

## Task 1: Select an update and capture the baseline

1. In OSMH, open your client's details and select **Updates**. Review its inventory and available updates.

2. Choose a small non-kernel package suitable for this disposable client. Review its dependencies and target version. Keep kernel switching and Ksplice observations separate from this package-update exercise.

3. On the client, set the selected package name and save its current version:

    ```bash
    <copy>
    PACKAGE='YOUR_PACKAGE'
    mkdir -p "$HOME/ol-practice-reports/osmh"
    rpm -q "$PACKAGE" | tee "$HOME/ol-practice-reports/osmh/package-before.txt"
    sha256sum --check "$HOME/ol-practice-reports/osmh/before-registration/application.sha256"
    </copy>
    ```

    Replace `YOUR_PACKAGE` before running. Use an installed package with an available update, and expect the application checksum to be `OK`.

4. If no suitable update is available, record **No package update available** and skip Task 2. Do not downgrade packages to manufacture an update. Continue with maintenance planning and final health checks; the result must not claim that patching occurred.

## Task 2: Apply one immediate package update

1. In the client's **Updates** list, select the chosen package and select **Update**. Choose **Immediately** and name the job `ol-migrate-osmh-update-STUDENT_ID` using your student suffix.

2. Review the package selection, dependencies, target client, and target version. Submit the update and record its job identifier.

3. Open the resulting job and inspect its status and messages. Wait for it to finish successfully. A submitted request, a running job, or an Active client does not establish that the package changed.

4. In the same client session, capture the installed version again:

    ```bash
    <copy>
    rpm -q "$PACKAGE" | tee "$HOME/ol-practice-reports/osmh/package-after.txt"
    diff -u "$HOME/ol-practice-reports/osmh/package-before.txt" \
      "$HOME/ol-practice-reports/osmh/package-after.txt"
    </copy>
    ```

    A difference and exit status 1 from `diff` are expected when the version changed. Compare the result with the job's target version. If versions did not change, inspect the job messages and record the actual outcome.

5. Review the job's reboot requirements. If a reboot is required, use the agreed maintenance window, reconnect afterward, and repeat the health checks in Task 4. Do not assume every package update is reboot-free.

## Task 3: Plan and schedule a maintenance job

1. On the client's details page, select **Create update job** and name it `ol-migrate-osmh-maintenance-STUDENT_ID`.

2. Select **Schedule run time** and frequency **Once**. Choose a maintenance window agreed for this client. Record both the Console timezone and the intended local time.

3. Select **Apply specific update categories** and choose security updates. Review the scope, including any kernel updates and potential reboot requirements. This is a separate maintenance operation from the single-package update above.

4. Submit only a schedule you intend to run, and record its identifier and next execution time. If no maintenance execution is planned, inspect the scheduling form without submitting and record **Scheduling preview only**.

5. If the job is scheduled to run during the lab, keep the client and station available and inspect its result after execution. Record **Scheduled, not yet executed** for a future job. Creating a schedule does not prove successful patching.

## Task 4: Verify the application and managed state

1. On the on-premises client, run:

    ```bash
    <copy>
    systemctl is-enabled httpd
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    sha256sum --check "$HOME/ol-practice-reports/osmh/before-registration/application.sha256"
    getenforce
    sudo firewall-cmd --query-service=http
    systemctl --failed --no-pager
    </copy>
    ```

2. Expect Apache `enabled` and `active`, the workload marker, checksum `OK`, SELinux `Enforcing`, HTTP allowed, and no unexpected failed units. Verify the application in your browser using the on-premises client address.

3. In OSMH, confirm the client is still **Active** and review its refreshed package inventory. Capture the completed job and version evidence, or the accurate no-update or failed outcome.

## Task 5: Diagnose any failed checkpoint

1. Match the symptom to the relevant checks. Correct the identified cause before repeating the failed operation.

    | Symptom | Checks |
    | --- | --- |
    | Authorization or missing source | Assigned compartment, user permissions, Management Agent dynamic-group rule, source visibility |
    | Registration never becomes Active | Correct client profile, OS and architecture, unexpired install key, synchronized time, agent service |
    | Profile location conflict | On-premises client profile selected; OCI migration VM was not used for this procedure |
    | Client cannot reach the station | Hostname resolution, permitted client subnet, proxy and mirror ports, station health |
    | Package metadata or downloads fail | Profile source association, mirror synchronization, station storage and Apache service |
    | Job finished without a version change | Package target, selected operation, inventory refresh, job messages, availability of an update |
    | Previously unregistered host cannot register | Oracle's documented re-registration procedure; do not repeatedly rerun setup |

2. On the client, inspect the agent service and OSMH log when needed:

    ```bash
    <copy>
    systemctl status mgmt_agent --no-pager
    sudo tail -n 60 /opt/oracle/mgmt_agent/plugins/osmh/stateDir/log/osmh-agent.log
    </copy>
    ```

3. Record the job identifier, error text, and corrective action. Remove secrets from shared output. Shared-station faults must be handled by the instructor so other students are not disrupted.

## Task 6: Preserve evidence and complete the workshop

1. In `$HOME/ol-practice-reports/osmh/result.txt`, record the client and station identifiers, registration result, source readiness, immediate update outcome, package versions, maintenance-job state, and final application checks.

2. Keep the earlier migration evidence on the OCI VM separate. The OSMH client results show on-premises management and application continuity across registration and updating; they do not prove the earlier OCI VM was registered or patched through OSMH.

3. Save reports and screenshots to your approved destination. Record any future scheduled jobs and the person responsible for their maintenance windows.

4. Complete when the client and application are healthy and the results accurately distinguish **Update applied**, **No update available**, and **Blocked or failed**. Record scheduling as previewed, scheduled, or executed according to what actually happened.

5. Retain the client, station, OCI resources, profiles, keys, and software sources for review. No unregistration, resource deletion, or agent removal is part of this lab. Follow later cleanup instructions only when the workshop owner requests them.

## Learn More

- [Installing individual updates](https://docs.oracle.com/en-us/iaas/osmh/doc/install-updates-instance.htm)
- [Creating an immediate or scheduled update job](https://docs.oracle.com/en-us/iaas/osmh/doc/create-scheduled-job-instance.htm)
- [Registration troubleshooting](https://docs.oracle.com/en-us/iaas/osmh/doc/troubleshoot-instance-station-registration-failure.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Contributors** - Oracle Linux SMEs; adapted from the supplied osmh-onprem-lab example
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
