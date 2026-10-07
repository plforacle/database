# Lab 7: Manage the Migrated Instance with OS Management Hub

## Introduction

In this lab, you register the migrated Oracle Linux VM with Oracle OS Management Hub, inspect its package inventory, perform a managed package operation, and schedule maintenance. You continue using `ol-migrate-rhel-source` and its Apache workload.

OS Management Hub brings package inventory and update jobs into the OCI Console. A registration profile assigns software sources to the instance. This extends the local DNF maintenance you practiced in Lab 5 with centralized management and job history.

Estimated Lab Time: 60 minutes

### Objectives

- Prepare Oracle Cloud Agent and check service connectivity.
- Create an Oracle Linux 9 registration profile and register the existing VM.
- Review software sources, installed packages, and available updates.
- Complete a package operation and inspect its job results.
- Schedule maintenance and validate the Apache workload.

### Prerequisites

- Completed Lab 6: Apply Live Updates with Oracle Ksplice.
- A paid OCI tenancy and the migrated Oracle Linux 9 x86_64 VM running RHCK.
- SSH access using the account and private key that worked in Lab 2.
- OS Management Hub IAM setup completed for `ol-migrate-lab`, including access to vendor software sources in the root compartment.
- Permission to manage profiles and jobs, enable Compute plugins, and unregister the instance during cleanup.
- A network path to OCI services and access to Oracle Linux repositories.

> **Note:** The source VM was imported from a RHEL image. Verify agent installation and plugin support on this converted image before continuing. A paid tenancy alone does not prove that registration will succeed. If the plugin reports `NOT_SUPPORTED`, preserve the details and stop this lab for instructor review.

## Task 1: Verify the system and IAM setup

1. Connect to the migrated VM. Replace both placeholders, using the SSH account selected in Lab 2:

    ```bash
    <copy>ssh -i "<private-key-path>" <ssh-user>@<public-ip></copy>
    ```

2. Check the operating system, kernel, and application:

    ```bash
    <copy>
    grep -E '^(ID|VERSION_ID)=' /etc/os-release
    uname -m
    uname -r
    rpm -qf "/boot/vmlinuz-$(uname -r)" --qf '%{VENDOR}\n'
    systemctl is-active httpd
    curl --fail --silent --show-error http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

    Confirm Oracle Linux major version 9, `x86_64`, an Oracle kernel without `uek` in its release, an active Apache service, and the workload marker.

3. In the OCI Console, open **Observability & Management**, then **OS Management Hub**, then **Overview**. Select the workshop region and `ol-migrate-lab` compartment.

4. Confirm that your administrator completed the [policy advisor setup](https://docs.oracle.com/en-us/iaas/osmh/doc/policy-advisor.htm) introduced in Lab 1. The instance must match the dynamic group's rule for this exact compartment. Child compartments are not included automatically.

5. Confirm that your user can create a profile and a job and read vendor sources in the root compartment. Read-only operator access is insufficient. If any permission is missing, have your tenancy administrator resolve it before continuing.

## Task 2: Prepare the agent and network

1. Inspect available repositories and install preparation tools:

    ```bash
    <copy>
    sudo dnf repolist --all
    sudo dnf install -y dnf-plugins-core jq
    </copy>
    ```

2. Confirm that `ol9_oci_included` appears in the repository list. Enable it and install or update Oracle Cloud Agent:

    ```bash
    <copy>
    sudo dnf config-manager --set-enabled ol9_oci_included
    sudo dnf install -y oracle-cloud-agent
    sudo dnf upgrade -y oracle-cloud-agent
    rpm -q oracle-cloud-agent
    sudo systemctl enable --now oracle-cloud-agent
    sudo systemctl status oracle-cloud-agent --no-pager
    </copy>
    ```

    The installed agent must be version 1.40 or later and its service must be active. If the repository or package is unavailable, stop and follow [Oracle Cloud Agent installation guidance](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/manage-plugins.htm). Resolve installation before enabling the plugin.

3. In **Networking**, open `ol-migrate-vcn` and inspect the route table associated with the VM's public subnet. Confirm a service gateway route for **All <region> Services in Oracle Services Network**. If missing, create a service gateway in this VCN using that service label and add a route with destination type **Service** to the subnet's route table, targeting the gateway. Keep the existing internet-gateway route for SSH, HTTP, and internet downloads.

4. Confirm that subnet security rules and any network security groups permit outbound HTTPS to OCI services. Follow the [networking requirements](https://docs.oracle.com/en-us/iaas/osmh/doc/getstarted.htm#networking-requirements) if your administrator controls these rules.

5. Test the regional service endpoint using OCI instance metadata:

    ```bash
    <copy>
    REGION_INFO=$(curl --fail --silent --show-error --connect-timeout 5 \
      -H 'Authorization: Bearer Oracle' \
      http://169.254.169.254/opc/v2/instance/regionInfo)
    OSMH_REGION=$(printf '%s' "$REGION_INFO" | jq -er '.regionIdentifier')
    OSMH_DOMAIN=$(printf '%s' "$REGION_INFO" | jq -er '.realmDomainComponent')
    curl --silent --show-error --connect-timeout 10 --max-time 30 \
      --output /dev/null --write-out 'HTTP response: %{http_code}\n' \
      "https://osmh.${OSMH_REGION}.oci.${OSMH_DOMAIN}/"
    </copy>
    ```

    A service response such as HTTP 404 confirms transport connectivity; this unsigned request does not prove IAM authorization. DNS errors, timeouts, TLS errors, or HTTP `000` require investigation. Do not disable certificate validation. See [connectivity troubleshooting](https://docs.oracle.com/en-us/iaas/osmh/doc/troubleshoot-connection.htm).

6. Save the pre-registration repository configuration for comparison:

    ```bash
    <copy>
    mkdir -p "$HOME/ol-migration-evidence/osmh"
    sudo dnf repolist --enabled > "$HOME/ol-migration-evidence/osmh/repositories-before.txt"
    sudo tar -czf "$HOME/ol-migration-evidence/osmh/yum-repos-before.tar.gz" \
      -C /etc yum.repos.d
    </copy>
    ```

## Task 3: Add software sources and create a profile

1. Under **OS Management Hub**, open **Software Sources** and select the root compartment. Vendor sources reside there; the profile will reside in `ol-migrate-lab`.

2. Select **Add vendor software source**. Set the vendor to **Oracle**, version to **Oracle Linux 9**, and architecture to **x86_64**.

3. Add the following sources, or confirm they are already selected for the service:

    - `ol9_baseos_latest-x86_64`
    - `ol9_appstream-x86_64`
    - `ol9_addons-x86_64`
    - `ol9_oci_included-x86_64`

    BaseOS and AppStream provide core OS and application packages. Addons and OCI Included support the workshop's management tools. Keep RHCK for this workshop; do not add a UEK source to change the kernel.

4. Review the source selection and select **Add vendor software source**. Record which sources were already present. They can be shared by other managed instances.

5. Open **Profiles**, select `ol-migrate-lab`, and select **Create profile**.

6. Enter the following values:

    - Name: `ol-migrate-ol9-profile`. Add your workshop identifier if this name already exists.
    - Description: `Register the migrated Oracle Linux workshop VM`
    - Compartment: `ol-migrate-lab`
    - Instance location: **Oracle Cloud Infrastructure**
    - OS vendor: **Oracle**
    - OS version: **Oracle Linux 9**
    - Architecture: **x86_64**
    - Profile type: **Software source**

7. Select the root compartment in the software-source selector and attach the four sources from Step 3. Review the profile, select **Create**, and record its name and OCID.

    If sources are missing, check their region, compartment, availability, and your access. See [adding sources](https://docs.oracle.com/en-us/iaas/osmh/doc/add-vendor-software-sources.htm) and [creating profiles](https://docs.oracle.com/en-us/iaas/osmh/doc/create-profile.htm).

## Task 4: Register and inspect the migrated instance

1. Open **Compute**, then **Instances**, and select `ol-migrate-rhel-source`.

2. Select **Management**. Under **Oracle Cloud Agent**, locate **OS Management Hub Agent**, open its actions menu, and select **Enable**.

3. Select the profile created in Task 3. Because this VM uses a custom image, explicitly select **Oracle Linux 9** and **x86_64** when prompted to identify compatible profiles. Use the migrated OS values, even if the original image record still says RHEL or Custom.

4. Allow up to ten minutes for registration. In **OS Management Hub**, open **Instances**, filter to `ol-migrate-lab`, and confirm the instance is **Active**. Do not continue with a stopped, unsupported, or failed registration.

5. Open the managed instance. Check the registration profile and assigned software sources, then open **Packages** and inspect the installed-package inventory. Allow time for the initial inventory to arrive.

6. Locate `httpd` in the installed packages and compare its version with SSH output:

    ```bash
    <copy>rpm -q httpd</copy>
    ```

7. Record the managed-instance OCID, status, and inventory time. Capture a screenshot of the Active instance and package inventory for your evidence, excluding personal identifiers.

    Registration changes the instance's repository management. Use OS Management Hub for the following package jobs and avoid simultaneous DNF transactions. See [registering OCI instances](https://docs.oracle.com/en-us/iaas/osmh/doc/register-oci-instance.htm).

## Task 5: Complete a managed package operation

1. Review available updates on the managed instance. If package updates are available, select **Create update job**.

2. Name the job `ol-migrate-update-now`, choose **Immediately**, and select **Apply specific update categories**. Select package categories such as security and bug fixes. Exclude Ksplice categories in this lab, review the job, and select **Submit**.

    Lab 6 used Uptrack. OS Management Hub's Ksplice operations require a different client configuration; registering the instance does not convert that setup. See [Ksplice client requirements](https://docs.oracle.com/en-us/iaas/osmh/doc/linux-package-management.htm#using-ksplice-for-oracle-linux). This lab exercises package management.

3. If no updates are available, complete a package-installation exercise instead. In SSH, check whether the example tools are already installed:

    ```bash
    <copy>rpm -q tree dos2unix</copy>
    ```

    A not-installed message is expected for an absent package. In **Packages**, under **Available packages**, search for an absent tool, select its package version, and select **Install**. Choose **Immediately**, review the transaction, and submit the installation. If both tools are installed, select another available command-line utility that your instructor approves. Do not remove existing application packages to manufacture an update.

4. Open the instance's job history and inspect the operation until it finishes. Review its status and log, including the package transaction. For a failed job, record the error and resolve it before retrying. Do not submit overlapping jobs.

5. Verify the result in SSH. For a package installation, substitute the package you selected:

    ```bash
    <copy>rpm -q <installed-package-name></copy>
    ```

6. Record the job OCID, completion status, and packages changed in your evidence. See [update jobs](https://docs.oracle.com/en-us/iaas/osmh/doc/create-scheduled-job-instance.htm) and [package installation](https://docs.oracle.com/en-us/iaas/osmh/doc/install-packages-instance.htm).

## Task 6: Schedule maintenance

1. On the managed instance, select **Create update job** and enter `ol-migrate-update-scheduled` as its name.

2. Choose **Schedule run time** and a date at least one day in the future. Confirm the time zone displayed by the Console and select **Once**. This exercise demonstrates scheduling without starting another transaction during the workshop.

3. Select specific package update categories, excluding Ksplice categories. Review the instance, time, frequency, and categories, then submit the job.

4. In **Jobs**, filter to `ol-migrate-lab` and open **Scheduled jobs**. Confirm the job's target and future execution time. Record its name and OCID.

5. Delete this practice schedule before leaving the lab. Open its details, select **Delete** from **Actions**, and confirm. Verify that it no longer appears in **Scheduled jobs**. See [deleting a scheduled job](https://docs.oracle.com/en-us/iaas/osmh/doc/delete-scheduled-job.htm).

## Task 7: Validate the workload and record the checkpoint

1. Once package jobs have finished, check whether maintenance requires a reboot:

    ```bash
    <copy>sudo dnf needs-restarting -r</copy>
    ```

    A return code of 1 means a reboot is recommended. If required, reboot, reconnect using the same account, and wait for the managed instance to become Active again. Recheck that the running kernel is Oracle RHCK.

2. Validate Apache and the security configuration:

    ```bash
    <copy>
    systemctl is-enabled httpd
    systemctl is-active httpd
    curl --fail --silent --show-error http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    getenforce
    sudo firewall-cmd --query-service=http
    systemctl --failed --no-pager
    </copy>
    ```

    Expected results include `enabled`, `active`, the workload marker, `Enforcing`, and `yes`. Investigate any failed systemd unit.

3. Open `http://<public-ip>/` in your browser and verify the same application page remains available.

4. Capture repository and package state after registration:

    ```bash
    <copy>
    sudo dnf repolist --enabled > "$HOME/ol-migration-evidence/osmh/repositories-managed.txt"
    rpm -qa --qf '%{NAME}\t%{VERSION}-%{RELEASE}.%{ARCH}\n' | sort \
      > "$HOME/ol-migration-evidence/osmh/packages-managed.tsv"
    </copy>
    ```

5. Confirm the checkpoint: the instance is Active, inventory is populated, a package operation completed successfully, the practice schedule was deleted, and the application remains healthy. Continue to **Lab 8: Validate and Clean Up**, where you unregister the VM and delete its workshop profile.

## Troubleshooting

- **No profile appears:** Check Oracle Linux 9, x86_64, region, profile compartment, source availability, and permissions to read the profile and sources.
- **Agent missing or unsupported:** Check the installed version and service status. Preserve errors with `sudo journalctl -u oracle-cloud-agent -n 100 --no-pager`. Do not alter image metadata to bypass a support check.
- **Registration does not become Active:** Check the exact compartment's dynamic-group rule, agent status, service-gateway route, DNS, and HTTPS connectivity. Use [registration troubleshooting](https://docs.oracle.com/en-us/iaas/osmh/doc/register-oci-instance.htm).
- **Package job fails:** Open its log and verify assigned software sources and dependency errors. Wait for existing package transactions to finish.
- **No updates remain:** Use the installation branch in Task 5. Lab 5 may already have applied all current updates.

## Learn More

- [OS Management Hub overview](https://docs.oracle.com/en-us/iaas/osmh/doc/overview.htm)
- [OS Management Hub policies](https://docs.oracle.com/en-us/iaas/osmh/doc/policies.htm)
- [Understanding software sources](https://docs.oracle.com/en-us/iaas/osmh/doc/understand-software-sources.htm)
- [Unregistering an instance](https://docs.oracle.com/en-us/iaas/osmh/doc/unregister-instance.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
