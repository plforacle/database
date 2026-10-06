# Lab 6: Manage Oracle Linux with OS Management Hub

## Introduction

Register the Oracle Linux instance that you converted from AlmaLinux with OS Management Hub. Use the OCI Console to select software sources, inspect package inventory, install a utility, and schedule a security update. Finish by checking the Apache application and saving evidence.

Keep using `alma-to-ol-source`, its public subnet, and its RHCK. Registration happens after conversion to Oracle Linux. OCI instances use Oracle Cloud Agent; they do not need a separate management station or Management Agent installation key. OS Management Hub is unavailable on Free Tier instances. See [service prerequisites](https://docs.oracle.com/en-us/iaas/osmh/doc/getstarted.htm).

Estimated Lab Time: 75 minutes, plus agent and IAM propagation

### Objectives

- Prepare Oracle Cloud Agent and verify service connectivity.
- Configure IAM access for the workshop instance.
- Create an Oracle Linux 9 registration profile and register the instance.
- Inspect inventory and run package jobs through the OCI Console.
- Schedule a security update and verify the workload.
- Save evidence for final validation and cleanup.

### Prerequisites

- You completed Lab 5 and the migrated instance is still available. If you already deleted it, repeat Labs 1 through 5 before starting.
- SSH access with sudo, the original baseline, and the resource ledger from earlier labs.
- An eligible OCI tenancy and the paid `VM.Standard.E5.Flex` lab instance.
- Administrator access to configure IAM and add regional vendor software sources, or an administrator who completes those steps for you.
- Permission to manage OS Management Hub in `alma-to-ol-lab` and enable Compute agent plugins.

Use the same OCI region throughout this lab. Run code labeled Bash on the Linux instance. IAM statements go in the Console policy editor.

## Task 1: Prepare the migrated instance and Oracle Cloud Agent

1. Connect from your local terminal. Use your actual key file, SSH username, and instance address:

    ```powershell
    <copy>
    ssh -i "<private-key-path>" <ssh-user>@<public-ip>
    </copy>
    ```

2. In the Linux SSH session, confirm the operating system and running kernel:

    ```bash
    <copy>
    . /etc/os-release
    printf 'ID=%s VERSION_ID=%s ARCH=%s\n' "$ID" "$VERSION_ID" "$(uname -m)"
    uname -r
    rpm -qf "/boot/vmlinuz-$(uname -r)" --qf '%{NAME} %{VENDOR}\n'
    </copy>
    ```

    Expect `ID=ol`, major version `9`, `x86_64`, and an Oracle-owned RHCK. Record the current minor version. Maintenance may have advanced it beyond the original 9.8 migration checkpoint.

3. Save the current repository configuration before registration:

    ```bash
    <copy>
    mkdir -p "$HOME/ol-migration-evidence/osmh"
    sudo dnf repolist --enabled > "$HOME/ol-migration-evidence/osmh/repositories-before.txt"
    sudo tar -C /etc -czf "$HOME/ol-migration-evidence/osmh/repositories-before-osmh.tar.gz" yum.repos.d
    sudo chown "$(id -u):$(id -g)" "$HOME/ol-migration-evidence/osmh/repositories-before-osmh.tar.gz"
    </copy>
    ```

4. Check Oracle Cloud Agent:

    ```bash
    <copy>
    rpm -q oracle-cloud-agent
    systemctl status oracle-cloud-agent --no-pager
    </copy>
    ```

    OS Management Hub requires version **1.40.0 or later**. An absent package is a prerequisite to resolve here, even if it was absent in the AlmaLinux baseline.

5. If the package is missing, install it. If installed but older than the required version, upgrade it instead:

    ```bash
    <copy>
    sudo dnf install oracle-cloud-agent
    </copy>
    ```

    ```bash
    <copy>
    sudo dnf upgrade --refresh oracle-cloud-agent
    </copy>
    ```

    Review the transaction before accepting. If the package cannot be found, inspect `sudo dnf repolist --all` and follow [Oracle Cloud Agent installation guidance](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/manage-plugins.htm). Resolve repository access or obtain the package through Oracle support. Do not continue with a missing or outdated agent.

6. Ensure the service is running, then repeat the version check:

    ```bash
    <copy>
    sudo systemctl enable --now oracle-cloud-agent
    rpm -q oracle-cloud-agent
    systemctl is-active oracle-cloud-agent
    </copy>
    ```

## Task 2: Verify network access to OS Management Hub

1. In the OCI Console, open **Compute**, **Instances**, and `alma-to-ol-source`. Under **Networking**, open its primary VNIC and subnet. Inspect the subnet's route table and security lists.

2. Confirm that the public subnet retains `0.0.0.0/0` through its internet gateway, DNS works, and egress permits HTTPS. Keep the workstation-only SSH and HTTP ingress from Lab 1.

    Do not add an **All Services in Oracle Services Network** service gateway route to this public subnet's internet gateway route table. Oracle documents this combination as unsupported. Private subnet designs can use a service gateway; this workshop retains the existing public instance. See [the networking known issue](https://docs.oracle.com/en-us/iaas/Content/Network/Reference/known_issues_for_networking.htm).

3. In the SSH session, set the region shown in your Console. Replace the example if needed. This endpoint pattern applies to the commercial OCI realm used by this workshop:

    ```bash
    <copy>
    OSMH_REGION='us-ashburn-1'
    printf 'Testing OS Management Hub in %s\n' "$OSMH_REGION"
    getent hosts "osmh.${OSMH_REGION}.oci.oraclecloud.com"
    curl --silent --show-error --connect-timeout 10 --max-time 30 \
      --output /dev/null --write-out 'HTTP status: %{http_code}\n' \
      "https://osmh.${OSMH_REGION}.oci.oraclecloud.com/"
    </copy>
    ```

    A returned HTTP response, including an authorization error from this unsigned request, proves endpoint reachability. DNS, TLS, and timeout errors must be resolved. This test does not prove IAM authorization. See [Oracle's connectivity check](https://docs.oracle.com/en-us/iaas/osmh/doc/troubleshoot-connection.htm).

## Task 3: Configure IAM access

1. Record the instance OCID and compartment OCID from their Console details pages. Obtain the tenancy OCID from the tenancy details page. Identify an existing user group containing your workshop user and record its identity domain.

2. As an administrator, open **Identity & Security**, **Domains**, and the identity domain used for this lab. Select **Dynamic groups**, then **Create dynamic group**. In a tenancy without identity domains, use **Identity & Security**, **Dynamic groups**.

3. Name the group `alma-to-ol-osmh-instances`. Enter this matching rule, replacing the placeholder with the **instance OCID**, then create the group:

    ```text
    instance.id = '<instance-ocid>'
    ```

    The rule selects only the migrated lab VM. Record the group's OCID and identity domain in the ledger. Follow [dynamic group creation](https://docs.oracle.com/en-us/iaas/Content/Identity/dynamicgroups/To_create_a_dynamic_group.htm) if your Console layout differs.

4. Open **Identity & Security**, **Policies**. Select the root compartment and create `alma-to-ol-osmh-policy`. Use the manual editor for these statements:

    ```text
    Allow dynamic-group '<identity-domain>'/'alma-to-ol-osmh-instances' to {OSMH_MANAGED_INSTANCE_ACCESS} in compartment alma-to-ol-lab where request.principal.id = target.managed-instance.id
    Allow dynamic-group '<identity-domain>'/'alma-to-ol-osmh-instances' to {INSTANCE_UPDATE} in compartment alma-to-ol-lab
    Allow group '<user-identity-domain>'/'<user-group>' to manage osmh-family in compartment alma-to-ol-lab
    Allow group '<user-identity-domain>'/'<user-group>' to read osmh-profiles in tenancy where target.profile.compartment.id = '<tenancy-ocid>'
    Allow group '<user-identity-domain>'/'<user-group>' to read osmh-software-sources in tenancy where target.softwareSource.compartment.id = '<tenancy-ocid>'
    ```

    Replace every placeholder. In a tenancy without identity domains, use the group name alone. These statements follow [Oracle's compartment policies](https://docs.oracle.com/en-us/iaas/osmh/doc/policies-manually-create.htm). `INSTANCE_UPDATE` enables clean unregistration in Lab 8, as described in [unregistration policy requirements](https://docs.oracle.com/en-us/iaas/osmh/doc/unregister-instance.htm).

5. Record the policy OCID and root location. Allow IAM changes to propagate before registration. Your existing Compute permissions must also allow plugin management; these OS Management Hub statements do not replace them.

## Task 4: Select software sources and create a profile

1. Open **Observability & Management**, **OS Management Hub**, **Software Sources**. Select the workshop's region and the root compartment.

2. An administrator with root software-source management access adds missing vendor sources using **Add vendor software source**. Select **Oracle**, **Oracle Linux 9**, and **x86_64**. Reuse sources already added. The learner's policy in Task 3 grants read access to root sources; it does not grant permission to add them there.

3. Select these Oracle Linux 9 sources, identifying them by repository name if display names include architecture suffixes:

    | Repository | Purpose |
    | --- | --- |
    | `ol9_baseos_latest` | Base operating system and RHCK packages |
    | `ol9_appstream` | Application packages |
    | `ol9_addons` | Additional Oracle packages |
    | `ol9_oci_included` | OCI-specific packages |
    | `ol9_ksplice` | Client and content for Lab 7 |

    Keep the RHCK path. UEK sources are not required. See [minimum sources](https://docs.oracle.com/en-us/iaas/osmh/doc/understand-software-sources.htm), [Oracle Linux 9 repository names](https://docs.oracle.com/en-us/iaas/oracle-linux/oci/oracle-linux-9.htm), and [adding vendor sources](https://docs.oracle.com/en-us/iaas/osmh/doc/add-vendor-software-sources.htm).

4. Open **Profiles**, select **Create profile**, and enter:

    | Field | Value |
    | --- | --- |
    | Name | `alma-to-ol-ol9-profile` |
    | Compartment | `alma-to-ol-lab` |
    | Instance type | Oracle Cloud Infrastructure |
    | OS vendor | Oracle |
    | OS version | Oracle Linux 9 |
    | Architecture | x86_64 |
    | Profile type | Software source |
    | Software source compartment | Root compartment |

5. Select the five sources, review, and choose **Create**. Record the profile OCID. If sources are missing, check region, source compartment, permissions, and whether the administrator added them. The profile uses major release 9. See [creating a profile](https://docs.oracle.com/en-us/iaas/osmh/doc/create-profile.htm).

## Task 5: Register and inspect the instance

1. Open **Compute**, **Instances**, `alma-to-ol-source`, and the **Management** tab. Under **Oracle Cloud Agent**, locate **OS Management Hub Agent** and select **Enable** from its actions menu.

2. Select the `alma-to-ol-lab` profile compartment and `alma-to-ol-ol9-profile`. If asked to identify the operating system, select **Oracle Linux 9** and **x86_64**. The instance started from an AlmaLinux image; use the current guest operating system when selecting the profile.

3. Allow up to ten minutes for the plugin to start. Open **OS Management Hub**, **Instances**, and filter by `alma-to-ol-lab`. Confirm the migrated VM is **Active** and reports Oracle Linux 9 and x86_64. Record its managed-instance OCID separately from the Compute OCID. See [existing instance registration](https://docs.oracle.com/en-us/iaas/osmh/doc/register-oci-instance.htm).

4. Open its **Software Sources** tab and verify the five sources. Under **Packages**, inspect installed packages and available updates after the initial inventory arrives. Search installed packages for `httpd` and compare with:

    ```bash
    <copy>
    rpm -q httpd oracle-cloud-agent
    sudo dnf repolist --enabled
    </copy>
    ```

    Registration can change the DNF repository configuration to service-managed sources. Save the result:

    ```bash
    <copy>
    sudo dnf repolist --enabled > "$HOME/ol-migration-evidence/osmh/repositories-after.txt"
    </copy>
    ```

5. If registration fails, inspect the plugin's Console message and use the troubleshooting table below. Continue only after **Active** status and package inventory are confirmed.

## Task 6: Install a package through the Console

1. Check whether `tree` is already installed:

    ```bash
    <copy>
    rpm -q tree
    </copy>
    ```

    A package-not-installed result is expected if absent. If present, try `dos2unix` instead. Choose an absent utility from the attached vendor sources; do not remove an existing package merely to repeat this exercise.

2. In the managed instance's **Packages** tab, find the chosen utility under **Available packages**. Select it and choose **Install**. Schedule **Immediately**, name the job `alma-to-ol-install-utility`, review, and select **Install**. See [package installation](https://docs.oracle.com/en-us/iaas/osmh/doc/install-packages-instance.htm).

3. Open the instance's **Jobs** tab, then **Work requests**. Open the installation request and wait for **Succeeded**. Under **Messages**, inspect logs and errors. If it is a parent request, open the child request for this VM. Save the job/work-request OCIDs and result. See [job messages](https://docs.oracle.com/en-us/iaas/osmh/doc/view-job-logs-errors.htm).

4. In SSH, confirm the selected package is installed. For `tree`, run:

    ```bash
    <copy>
    rpm -q tree
    tree --version
    </copy>
    ```

    If you selected another package, use its name and version command. Confirm the installation also appears in the Console inventory.

## Task 7: Schedule a security update

1. From the managed instance details page, select **Create update job**. Name it `alma-to-ol-security-once`.

2. Select **Schedule run time**, choose a time approximately ten minutes ahead, and set **Frequency** to **Once**. Record the time zone shown by the Console and the planned execution time.

3. Select **Apply specific update categories**, then **Security**. Review the target instance and submit. Keep Ksplice updates for Lab 7. See [scheduling updates](https://docs.oracle.com/en-us/iaas/osmh/doc/create-scheduled-job-instance.htm).

4. In **Jobs**, **Scheduled jobs**, verify the target, category, and next execution time. Leave the instance running. After execution, inspect its **Work requests** and **Messages**. Wait for a terminal result before continuing.

5. Record whether updates were applied. After Lab 5, a successful job with no applicable updates is valid. A failed job, missing inventory, or an offline agent is not a successful no-update result. If updates require a reboot, record it and perform the controlled reboot and workload checks from Lab 5 before Lab 7.

## Task 8: Validate the workload and save evidence

1. Check the application and services in SSH:

    ```bash
    <copy>
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    sha256sum /var/www/html/index.html
    systemctl --failed --no-pager
    </copy>
    ```

    Expect active Apache, the health marker, the original application checksum, and no unexpected failed services. Check the public application URL from your workstation too.

2. Save Linux evidence:

    ```bash
    <copy>
    rpm -q httpd oracle-cloud-agent > "$HOME/ol-migration-evidence/osmh/packages.txt"
    systemctl is-active httpd > "$HOME/ol-migration-evidence/osmh/httpd-active.txt"
    curl --fail --silent http://127.0.0.1/ > "$HOME/ol-migration-evidence/osmh/application.html"
    sha256sum /var/www/html/index.html > "$HOME/ol-migration-evidence/osmh/application-sha256.txt"
    </copy>
    ```

3. Save Console screenshots on your workstation showing the profile, Active instance and inventory, successful installation request, and scheduled security job result. Record the region, OCIDs, source names, installed utility, update count, and any reboot in your ledger. Export these records before unregistration removes service history.

4. Keep the instance registered for **Lab 7: Apply Live Updates with Oracle Ksplice**. Cleanup occurs in Lab 8, after evidence is saved.

## Troubleshooting

| Symptom | Check |
| --- | --- |
| Plugin missing or agent too old | Verify Oracle Cloud Agent 1.40.0 or later. Agent updater propagation can take time. |
| No profile available | Select the correct compartment, Oracle Linux 9, and x86_64 manually. |
| Registration authorization error | Verify the instance OCID in the matching rule, group domains, and policy propagation. |
| DNS, TLS, or connection timeout | Repeat Task 2 and review the public subnet route, egress, and DNS. |
| Active instance with empty inventory | Allow initial inventory collection to finish and inspect agent logs if it remains empty. |
| Job failed or package unavailable | Open the child request's Messages and verify the attached sources. |

For registration failures, consult [Oracle troubleshooting](https://docs.oracle.com/en-us/iaas/osmh/doc/troubleshoot-instance-station-registration-failure.htm). Do not change the OS identity files or disable SELinux to work around registration.

## Learn More

- [OS Management Hub prerequisites](https://docs.oracle.com/en-us/iaas/osmh/doc/getstarted.htm)
- [No profile available](https://docs.oracle.com/en-us/iaas/osmh/doc/troubleshoot-no-profile-available.htm)
- [Known issues](https://docs.oracle.com/en-us/iaas/osmh/doc/known-issues.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Perside Foster with Codex assistance, October 2026
