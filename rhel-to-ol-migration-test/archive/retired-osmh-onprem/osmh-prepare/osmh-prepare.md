# Lab 7: Prepare OS Management Hub for On-Premises Management

## Introduction

Use OS Management Hub (OSMH) to manage Oracle Linux after migration. The service runs in OCI; the managed server and its management station run on premises. This lab follows the station-based architecture in the SME example.

Labs 1 through 6 use an OCI-hosted migration VM. For Labs 7 through 9, use the separate on-premises Oracle Linux client assigned by your instructor. The client has the same Apache test application. Its registration and update results are separate evidence from the migration of your OCI VM.

Estimated Lab Time: 25 minutes

### Objectives

- Identify the on-premises client, shared station, and OCI service compartment.
- Verify service access, software sources, and station readiness.
- Check client connectivity and capture its application baseline.

### Prerequisites

Complete Labs 5 and 6. Retain the Ksplice results from Lab 5 and the UEK verification from Lab 6, including any unresolved issue. Your instructor must complete the OSMH Instructor Setup guide before this lab and provide the following:

- An on-premises Oracle Linux 9 x86_64 client, its SSH details, and a sudo-capable account.
- A separate, registered management station with synchronized Oracle Linux 9 BaseOS and AppStream mirrors, a client-reachable hostname, and its proxy and mirror ports.
- Valid Oracle Linux Basic or Premier Support for the on-premises systems and an OCI region and compartment enabled for OSMH.
- Permission to create your client profile, register your assigned client, and manage its update jobs. IAM setup requires a tenancy administrator.
- A current Management Agent RPM, a private install key for the assigned compartment, and a unique student suffix for resource names.

The instructor may share the station across students. Do not reconfigure it, register it again, or use its station profile for your client. OCI Compute instances use a different OSMH agent path and do not use management stations.

## Task 1: Identify the hosts and service scope

1. Record your OSMH region, compartment, student suffix, assigned client hostname, station hostname, and station ports. Use the instructor's actual values throughout these labs.

2. Keep the migration VM `ol-migrate-rhel-source` and its evidence archives intact. Open a separate SSH session to your assigned on-premises client. Verify the hostname before running any OSMH installation command.

3. On the on-premises client, run:

    ```bash
    <copy>
    hostname
    grep -E '^(ID|VERSION_ID)=' /etc/os-release
    uname -m
    uname -r
    timedatectl status
    </copy>
    ```

    Confirm the assigned hostname, `ID="ol"`, Oracle Linux 9, `x86_64`, and synchronized time. The client must not already be registered with OSMH, ULN, or another content management system. Stop and contact the instructor if a different host or an existing registration is found.

## Task 2: Verify OCI permissions and software sources

1. Sign in to the OCI Console and select the OSMH region and compartment provided by the instructor. Open **OS Management Hub**, then **Overview**.

2. Confirm that your assigned user can view the shared station and software sources and create a client profile. The Policy Advisor operator group is read-only; it cannot perform the registration and patching exercises. Ask the instructor to resolve missing permissions in the assigned compartment.

3. Open **Software sources**. Confirm that the instructor has added Oracle Linux 9 x86_64 BaseOS Latest and AppStream and made them visible to your profile. Vendor sources can reside in the root compartment or be replicated into the lab compartment.

4. Check source eligibility for non-OCI systems. Use the instructor's entitled sources; do not select sources marked unavailable for non-OCI use. This exercise uses non-kernel package updates. Do not add Ksplice sources or additional kernel mirrors for this lab.

## Task 3: Verify the shared management station

1. Open **Management stations** and select the station assigned to your class. Confirm its health is **Healthy** and its associated managed instance is **Active**.

2. Open its configuration and record the hostname, permitted client subnet, proxy port, and HTTP or HTTPS mirror port. The example uses proxy port `16080` and HTTP mirror port `17080`; use the values configured by the instructor.

3. Open **Mirrored status**. Confirm the required BaseOS and AppStream mirrors are synchronized. The instructor preloads these sources through a warm-up client profile. If a mirror is missing, syncing, or failed, ask the instructor to finish synchronization before proceeding.

4. Capture the station health and mirror status for your notes. Hide install keys and private response-file contents. Do not change shared station settings or run competing mirror jobs.

## Task 4: Check client-to-station connectivity

1. On your on-premises client, set the station values supplied by the instructor:

    ```bash
    <copy>
    STATION_HOST='YOUR_STATION_HOSTNAME'
    STATION_PROXY_PORT=16080
    STATION_MIRROR_PORT=17080
    getent hosts "$STATION_HOST"
    </copy>
    ```

    Replace the hostname and ports before running. If the station uses an HTTPS mirror, set its configured HTTPS port instead of `17080`.

2. Test both listening ports:

    ```bash
    <copy>
    timeout 5 bash -c 'exec 3<>"/dev/tcp/$1/$2"' _ "$STATION_HOST" "$STATION_PROXY_PORT" \
      && echo 'PASS: station proxy reachable'
    timeout 5 bash -c 'exec 3<>"/dev/tcp/$1/$2"' _ "$STATION_HOST" "$STATION_MIRROR_PORT" \
      && echo 'PASS: station mirror reachable'
    </copy>
    ```

    Both PASS messages must appear. These checks establish TCP reachability; repository access is verified after registration. Resolve failures with the instructor rather than opening the station to all network sources.

3. Confirm with the instructor that the station can reach the required regional OSMH, identity, Management Agent, Object Storage, and telemetry endpoints over HTTPS. The client also needs the agreed bootstrap path for its agent RPM and package dependencies.

## Task 5: Capture the on-premises client baseline

1. On the assigned client, create a separate evidence directory and capture its identity and application:

    ```bash
    <copy>
    mkdir -p "$HOME/ol-practice-reports/osmh/before-registration"
    OSMH_BEFORE="$HOME/ol-practice-reports/osmh/before-registration"
    hostname > "$OSMH_BEFORE/hostname.txt"
    cat /etc/os-release > "$OSMH_BEFORE/os-release.txt"
    uname -r > "$OSMH_BEFORE/kernel.txt"
    sha256sum /var/www/html/index.html > "$OSMH_BEFORE/application.sha256"
    systemctl is-enabled httpd
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    getenforce
    </copy>
    ```

2. Complete when the assigned client is Oracle Linux 9 x86_64, the station and mirrors are ready, both ports are reachable, Apache is `enabled` and `active`, the workload marker appears, and SELinux is `Enforcing`.

3. Record your client and station details in `$HOME/ol-practice-reports/osmh/environment.txt`, excluding secrets. Continue to Lab 8. Preserve the earlier OCI migration evidence separately.

## Learn More

- [OSMH supported environments, subscriptions, and networking](https://docs.oracle.com/en-us/iaas/osmh/doc/getstarted.htm)
- [Management station architecture and requirements](https://docs.oracle.com/en-us/iaas/osmh/doc/understand-management-stations.htm)
- [Policy Advisor](https://docs.oracle.com/en-us/iaas/osmh/doc/policy-advisor.htm)
- [Vendor software source eligibility](https://docs.oracle.com/en-us/iaas/osmh/doc/add-vendor-software-sources.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Contributors** - Oracle Linux SMEs; adapted from the supplied osmh-onprem-lab example
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
