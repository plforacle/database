# Lab 9: Register an On-Premises Oracle Linux Client

## Introduction

Register your assigned on-premises Oracle Linux client with OSMH using a client profile and Management Agent. Verify that it receives content through the shared management station and that Apache remains healthy.

Estimated Lab Time: 30 minutes

### Objectives

- Create a profile for the client's location, OS, architecture, and station.
- Install and activate Management Agent on the assigned client.
- Verify inventory, mirrored content, and application continuity.

### Prerequisites

Complete Lab 8. Use its assigned on-premises client, shared station, install key, and agent RPM. The required mirrors must be synchronized. Keep your original OCI migration VM and evidence unchanged.

Do not run the non-OCI agent procedure on the OCI migration VM. This lab uses an on-premises client and the **On premises or third-party cloud** profile type.

## Task 1: Create the client registration profile

1. In the assigned OSMH region and compartment, open **Profiles** and select **Create profile**.

2. Name the profile `ol-migrate-osmh-STUDENT_ID`, replacing `STUDENT_ID` with your assigned suffix. Profile names must be unique within the tenancy.

3. Select **On premises or third-party cloud** as the instance location. Enable the management station option and select the shared station verified in Lab 8.

4. Select Oracle as the OS vendor, Oracle Linux 9 as the OS version, and `x86_64` as the architecture. Choose the **Software source** profile type and attach the instructor-approved BaseOS Latest and AppStream sources.

5. Review and create the profile. Verify its station association and source list. Use this client profile, not the station's automatically generated registration profile.

## Task 2: Verify content readiness for this profile

1. Open the shared station's **Mirrored status** tab. Confirm the software sources selected in your profile are listed and synchronized.

2. If a source is missing, ask the instructor to refresh the source list and synchronize it. Do not proceed with agent registration or package operations until the profile's required content is available.

3. Capture the profile's station association and the synchronized mirror status. Initial synchronization can take longer than the lab's active time.

## Task 3: Add the profile and activate the client agent

1. Open your client profile in OCI and select **View**. Copy its full contents.

2. In the on-premises client's SSH session, confirm its identity:

    ```bash
    <copy>
    hostname
    grep -E '^(ID|VERSION_ID)=' /etc/os-release
    </copy>
    ```

    Match the hostname to the client assigned in Lab 8. Stop if this is the OCI migration VM or the management station.

3. Create `/etc/osmh-profile` with no filename extension:

    ```bash
    <copy>
    sudo vi /etc/osmh-profile
    </copy>
    ```

    Paste the client profile contents and save. The profile must identify the on-premises client location and the assigned station.

4. Complete the [Management Agent installation reference](../osmh-agent-install/osmh-agent-install.md) on this client. Use the **client** connection settings, including the shared station proxy hostname and port. Return here after activation.

## Task 4: Verify registration and software delivery

1. Open **OS Management Hub**, then **Instances**, and select your assigned compartment. Allow several minutes for registration and inventory collection.

2. Find your assigned client and confirm its status is **Active**. Open its details and verify its Oracle Linux 9 identity, architecture, registration profile, attached software sources, and management station association. Record the managed-instance identifier.

3. On the on-premises client, run:

    ```bash
    <copy>
    systemctl is-active mgmt_agent
    sudo dnf repolist -v
    sudo dnf makecache
    </copy>
    ```

4. Confirm the enabled OSMH repository URLs identify the assigned station and its mirror port. Metadata retrieval must succeed. An agent process alone does not establish successful registration or package delivery.

5. If registration or metadata retrieval fails, preserve the error and use the troubleshooting table in Lab 10. Do not remove the agent or repeatedly rerun setup without identifying the cause.

## Task 5: Verify application continuity and complete the checkpoint

1. On the on-premises client, verify its baseline content and service state:

    ```bash
    <copy>
    sha256sum --check "$HOME/ol-practice-reports/osmh/before-registration/application.sha256"
    systemctl is-enabled httpd
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    getenforce
    systemctl --failed --no-pager
    </copy>
    ```

2. Expect checksum `OK`, Apache `enabled` and `active`, the workload marker, SELinux `Enforcing`, and no unexpected failed units. Open the application through the client address supplied by your instructor and verify the page.

3. Record the profile name, managed-instance identifier, Active status, repository evidence, and application results in `$HOME/ol-practice-reports/osmh/registration.txt`. Do not include the install key.

4. Complete only when registration, inventory, package metadata, and application checks pass. Continue to Lab 10 to manage updates through OSMH.

## Learn More

- [Creating a client profile](https://docs.oracle.com/en-us/iaas/osmh/doc/create-profile.htm)
- [Registering a non-OCI instance](https://docs.oracle.com/en-us/iaas/osmh/doc/register-non-oci-instance.htm)
- [Synchronizing mirrors](https://docs.oracle.com/en-us/iaas/osmh/doc/sync-mirrors.htm)
- [Registration troubleshooting](https://docs.oracle.com/en-us/iaas/osmh/doc/troubleshoot-instance-station-registration-failure.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Contributors** - Oracle Linux SMEs; adapted from the supplied osmh-onprem-lab example
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
