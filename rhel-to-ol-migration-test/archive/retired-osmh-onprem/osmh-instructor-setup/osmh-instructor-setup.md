# Prepare the On-Premises OSMH Practice Environment

## Introduction

Prepare the shared station and on-premises clients before Student Labs 7 through 9. This setup guide supplements the two instructor image-preparation labs. It follows the SME-provided osmh-onprem-lab example and uses a real on-premises environment.

Students retain their OCI migration VMs from Labs 1 through 6. They use separate on-premises Oracle Linux clients for the OSMH exercises. Do not present these clients as the OCI instances that were migrated earlier.

Estimated Time: 45 minutes of instructor activity, plus provisioning and mirror synchronization

### Objectives

- Prepare service access, support entitlement, and required software sources.
- Register and synchronize one shared management station.
- Provide each student with an unregistered on-premises client and the required private handoff.

### Prerequisites

- An existing OCI tenancy and a tenancy administrator for IAM setup.
- Valid Oracle Linux Basic or Premier Support for the on-premises hosts.
- One on-premises Oracle Linux 9 x86_64 station with at least four CPU cores, 16 GB RAM, and a separate mirror volume mounted at `/mnt/mirror`.
- Mirror and `/var/cache/dnf` capacity based on the selected source sizes. Several hundred GB or more may be required; the ordinary migration VM's boot volume is not a station storage plan.
- One separate on-premises Oracle Linux 9 x86_64 client per student, with SSH, sudo, and a working Apache application. The SME example suggests two vCPUs, 4 GB RAM, and 40 GB disk for a disposable client; these are planning values.
- DNS, synchronized time, an agent-install bootstrap path, station HTTPS access to regional OCI endpoints, and client access to the station proxy and mirror ports.

## Task 1: Prepare service permissions and content

1. Select the OSMH region, station compartment, and student compartments. Record each host's assigned location. Do not use an OCI Compute instance as the on-premises station or client for this guide.

2. Have a tenancy administrator run **OS Management Hub > Overview > Run Policy Advisor** in every compartment used by the exercise. Review its proposed IAM changes before applying them.

3. Assign learners permissions to create their client profiles and manage their assigned clients and jobs. The advisor's operator group is read-only. Verify that the dynamic-group rules include Management Agent resources in each relevant compartment, not only OCI Compute instances.

4. Provide visibility and the necessary permissions for vendor sources in the root compartment or replicate approved sources into the lab compartment. Where the shared station is in another compartment, verify that each student's profile can select it. Do not grant blanket tenancy administration to bypass a missing permission.

5. Open **OS Management Hub > Software sources > Add vendor software source**. Select Oracle Linux 9, `x86_64`, BaseOS Latest, and AppStream. Confirm eligibility and entitlement for non-OCI use. Limit this lab to those sources; additional kernel and Ksplice mirrors are not required for its non-kernel update exercise.

6. Open **Observability & Management > Management Agent > Downloads and Keys**. Create a lab install key for each compartment used by a station or client. Set expiration to cover the class and keep the keys private.

7. Download a Linux x86_64 Management Agent RPM at version `260828.3213` or later. Transfer it securely to the station and each client. Verify the package version before distribution; the selected agent includes its private Java runtime.

## Task 2: Prepare the station host and connectivity

1. On the station, verify its identity, time, mounted mirror volume, and free capacity:

    ```bash
    <copy>
    hostname
    cat /etc/oracle-release
    uname -m
    timedatectl status
    findmnt /mnt/mirror
    df -h /mnt/mirror /var/cache/dnf
    </copy>
    ```

    Do not continue if the intended data volume is absent. Allocate and mount storage through the approved provisioning process; this guide does not format disks.

2. Permit outbound HTTPS on TCP 443 to the required regional endpoints. Replace `REGION` with the chosen OCI region identifier:

    ```text
    osmh.REGION.oci.oraclecloud.com
    osmh.yum.REGION.oci.oraclecloud.com
    management-agent.REGION.oci.oraclecloud.com
    identity.REGION.oci.oraclecloud.com
    auth.REGION.oci.oraclecloud.com
    objectstorage.REGION.oraclecloud.com
    telemetry-ingestion.REGION.oraclecloud.com
    ```

    Account for DNS, the corporate proxy, and its trusted CA or SSL passthrough settings. Console browser access requires additional endpoints beyond this service list.

3. Choose a client-reachable station hostname, client subnet, proxy port, and mirror port. The example uses TCP `16080` for the proxy and TCP `17080` for an HTTP mirror in an isolated lab network. Permit these ports only from the assigned client network. Use trusted HTTPS with certificate verification for a production mirror.

4. Install and enable the station prerequisites:

    ```bash
    <copy>
    sudo dnf install -y httpd mod_ssl openssl policycoreutils-python-utils firewalld
    sudo systemctl enable --now httpd firewalld
    </copy>
    ```

## Task 3: Create and register the shared station

1. Open **OS Management Hub > Management stations > Create management station** in the station compartment. Use `ol-migrate-osmh-station` as its name.

2. Enter the client-reachable hostname or IP. Enable its proxy, set the permitted addresses to the actual client subnet, and set proxy port `16080`, or your chosen value. Do not allow all source addresses.

3. Set mirror storage to `/mnt/mirror` and the HTTP mirror port to `17080`, or your chosen value. For the isolated HTTP lab path, leave the HTTPS mirror port blank. If using HTTPS instead, configure the agreed certificate and port and give students those values.

4. Select **Automatically configure station** for this lab and create the resource. Review the resulting firewall and SELinux configuration as part of station verification; upstream network controls still need the client-port rules.

5. Open the station's generated profile, select **View**, and copy its contents. On the station, save them using `sudo vi /etc/osmh-profile`. This profile registers a single station and is not a client profile.

6. Complete the [Management Agent installation reference](../osmh-agent-install/osmh-agent-install.md) on the station. Use the **station** connection settings: direct OCI access or the corporate forwarding proxy, never the station's own proxy during bootstrap.

7. If automatic station configuration was disabled, wait for plugin deployment, review the generated setup script, and run it on the station:

    ```bash
    <copy>
    sudo /opt/oracle/mgmt_agent/plugins/osmh/stateDir/station-setup.sh
    </copy>
    ```

8. Confirm the station health becomes **Healthy**, the associated managed instance is **Active**, and `systemctl is-active httpd` returns `active`. Capture the station configuration and health without revealing keys.

## Task 4: Synchronize the class content before students begin

1. Create an OSMH software-source profile named `ol-migrate-osmh-warmup`. Select **On premises or third-party cloud**, enable the shared station, and select Oracle Linux 9, `x86_64`, BaseOS Latest, and AppStream.

2. Open the station's **Mirrored status** tab. A newly registered station can have an empty mirror list until a client profile references it. Refresh the source list if needed after creating the warm-up profile.

3. Select the required sources and choose **Actions > Sync software sources now**, then **Sync now**. Monitor the jobs and station storage until all selected mirrors are synchronized.

4. Keep the warm-up profile and shared station in place throughout the class. Finish the initial download before Student Lab 7; transfer time can exceed the workshop's active time estimates.

## Task 5: Prepare and assign the on-premises clients

1. Provide a clean Oracle Linux 9 x86_64 VM per student. It must not already be registered with OSMH, ULN, or another content management system. Create any reusable VM template before agent activation; do not clone a registered VM.

2. On each client, prepare the same static Apache workload used in the migration labs. Transfer the existing [workload helper](../launch-baseline-rhel/files/create-workload.sh) to the client and run it from its local directory:

    ```bash
    <copy>
    bash create-workload.sh
    systemctl is-enabled httpd
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    getenforce
    </copy>
    ```

    Confirm the helper reported success, Apache is `enabled` and `active`, the marker appears, and SELinux is `Enforcing`. Do not run RHEL registration steps on these Oracle Linux clients.

3. Verify SSH and browser access from the student's network and client access to the station proxy and mirror. Supply a trusted bootstrap repository path for Management Agent dependencies as well as the local RPM.

4. Assign each student a unique client hostname and profile suffix. Provide the client SSH account, private connection details, OSMH region and compartment, station hostname and ports, approved source list, install key, RPM path, and maintenance-window plan.

5. Pilot Student Labs 7 through 9 on one assigned client. Confirm registration, source delivery, job results, and application checks before class. An absence of available updates must be recorded accurately; do not downgrade systems to create an exercise result.

6. Capture station health, synchronized mirrors, profile association, client Active status, and update-job evidence during the pilot. Retain the station, clients, profiles, keys, and all OCI resources. Cleanup is outside this setup guide.

## Learn More

- [OSMH prerequisites and network endpoints](https://docs.oracle.com/en-us/iaas/osmh/doc/getstarted.htm)
- [Policy Advisor and Management Agent matching](https://docs.oracle.com/en-us/iaas/osmh/doc/policy-advisor.htm)
- [Station requirements](https://docs.oracle.com/en-us/iaas/osmh/doc/understand-management-stations.htm)
- [Creating a management station](https://docs.oracle.com/en-us/iaas/osmh/doc/create-management-station.htm)
- [Registering a management station](https://docs.oracle.com/en-us/iaas/osmh/doc/register-management-station.htm)
- [Synchronizing mirrors](https://docs.oracle.com/en-us/iaas/osmh/doc/sync-mirrors.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Contributors** - Oracle Linux SMEs; adapted from the supplied osmh-onprem-lab example
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
