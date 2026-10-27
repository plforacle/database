# Lab 7: OS Management Hub Quickstart on OCI

## Introduction

Your RHEL VM now runs Oracle Linux. Use OS Management Hub (OSMH) to view its software inventory and manage a package update from the OCI Console. Continue using `ol-migrate-rhel-source` and the Apache application from the earlier labs.

You will set up OSMH for your compartment, register this VM, and run an update job. Oracle Cloud Agent runs on the VM and provides the OS Management Hub Agent plugin used for registration.

### Objectives

- Set up OSMH permissions, software sources, and a registration profile.
- Register the migrated OCI VM and view its inventory.
- Apply an available package update and verify the job and application.

Estimated Lab Time: 40 minutes, plus agent registration and job execution time

### Prerequisites

- Complete the migration, Ksplice, and kernel-choice exercises. Keep their results.
- Use the same OCI region, compartment, VM, SSH account, and key as the earlier labs.
- Have Console permissions to manage OSMH resources and the VM's agent plugins. Running Policy Advisor also requires permission to manage groups, dynamic groups, and policies. Console sign-in alone does not grant these permissions.
- The VM must reach the OSMH service and software sources. Use the network from Lab 1; resolve any connectivity failure before registering.

## Task 1: Check the migrated VM and Oracle Cloud Agent

1. Connect to `ol-migrate-rhel-source` using your existing SSH connection. Confirm its identity and check for Oracle Cloud Agent:

    ```bash
    <copy>
    hostname
    grep -E '^(ID|VERSION_ID)=' /etc/os-release
    uname -m
    rpm -q oracle-cloud-agent
    </copy>
    ```

    Expect the workshop hostname, `ID="ol"`, version 9.8, and `x86_64`. OSMH requires Oracle Cloud Agent 1.40 or later. A missing agent is possible on this imported image.

2. If the agent is missing or older than 1.40, check for the OCI software repository:

    ```bash
    <copy>
    sudo dnf repolist --all | grep -i oci
    </copy>
    ```

    Oracle Cloud Agent is supplied through the OCI software source. If `ol9_oci_included` is already defined but disabled, enable it with `sudo dnf config-manager --enable ol9_oci_included`.

    If the repository is absent, set `OCI_REGION` to your VM's region identifier, such as `us-phoenix-1`, and add the following repository on this OCI VM. Replace `YOUR_OCI_REGION` before running. This example uses the commercial OCI domain `oraclecloud.com`:

    ```bash
    <copy>
    OCI_REGION='YOUR_OCI_REGION'
    sudo tee /etc/yum.repos.d/oci-included-ol9.repo >/dev/null <<EOF
    [ol9_oci_included]
    name=Oracle Software for OCI users on Oracle Linux 9 (\$basearch)
    baseurl=https://yum.${OCI_REGION}.oci.oraclecloud.com/repo/OracleLinux/OL9/oci/included/\$basearch/
    gpgkey=file:///etc/pki/rpm-gpg/RPM-GPG-KEY-oracle
    gpgcheck=1
    enabled=1
    EOF
    </copy>
    ```

    Refresh the source and check package availability:

    ```bash
    <copy>
    sudo dnf --refresh --disablerepo='*' --enablerepo=ol9_oci_included list --available oracle-cloud-agent
    </copy>
    ```

    When the package is listed, install it:

    ```bash
    <copy>
    sudo dnf install oracle-cloud-agent
    </copy>
    ```

    If repository access or package lookup fails, save the error and review the [Oracle repository example](https://blogs.oracle.com/linux/ocifs-oke-and-object-storage) and [agent installation guidance](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/manage-plugins.htm) with your instructor. Resolve this before continuing.

3. Start the agent and check the application:

    ```bash
    <copy>
    sudo systemctl enable --now oracle-cloud-agent
    rpm -q oracle-cloud-agent
    systemctl is-active oracle-cloud-agent
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

    Expect a current agent, both services `active`, and the workload marker.

## Task 2: Set up OSMH in the Console

1. Sign in to the OCI Console. Select the VM's region and compartment, then open **OS Management Hub > Overview**.

2. If OSMH permissions have not been configured for this compartment, select **Run Policy Advisor**. Review the proposed groups and policies, select **Next**, and apply the setup. If the compartment is already configured, keep its existing configuration.

    Confirm your user has permission to manage OSMH, such as membership in the advisor's `osmh-admins` group. Its `osmh-operators` group is read-only. The VM's compartment must be included in the instance dynamic-group rule. If you lack IAM permissions, have the tenancy administrator grant the required lab access.

    If Setup says to use your home region for IAM changes, switch to the home region named in the error and rerun Policy Advisor for the same compartment. After successful setup, return to your VM's region before continuing. If group or policy creation still reports `Unauthorized`, a tenancy administrator must resolve the missing IAM permissions. See [Policy Advisor errors](https://docs.oracle.com/en-us/iaas/osmh/doc/troubleshoot-policies.htm).

3. Open the VM's VCN and check the route table associated with its subnet. OSMH requires connectivity to OCI services. If a service-gateway route is absent, create a service gateway for `All <region> Services in Oracle Services Network`, then add a route for that service CIDR label through the gateway. Keep the existing internet-gateway route and allow the required outbound HTTPS traffic. See the [OSMH networking requirements](https://docs.oracle.com/en-us/iaas/osmh/doc/getstarted.htm).

4. Return to OSMH. Open **Software sources**, select **Add vendor software source**, and choose **Oracle Linux 9** and **x86_64**. Add **BaseOS Latest**, **AppStream**, **UEK Release 8**, and **Oracle Software for OCI users**, where available. Reuse sources already added to the service.

    Software sources supply the packages managed by OSMH. BaseOS and AppStream provide the core packages; UEK Release 8 matches the kernel exercise. Use the source compartment shown in the Console, which can be the root compartment.

5. Open **Profiles > Create profile**. Enter a unique name such as `ol-migrate-osmh-oci-STUDENT_ID`, replacing `STUDENT_ID` with your suffix. Select your lab compartment and the following settings:

    | Setting | Value |
    | --- | --- |
    | Instance location | Oracle Cloud Infrastructure |
    | OS vendor | Oracle |
    | OS version | Oracle Linux 9 |
    | Architecture | x86_64 |
    | Profile type | Software source |
    | Software sources | The Oracle Linux 9 sources added in Step 4 |

6. Review the profile and select **Create**. Keep its name for the next task.

## Task 3: Register this VM with OSMH

1. Open **Compute > Instances** and select `ol-migrate-rhel-source`. Open **Management**, then locate **Oracle Cloud Agent**.

2. Find **OS Management Hub Agent** and select **Enable** from its actions menu. Choose the profile you created in Task 2.

    Because the VM came from a custom image, you might need to select **Oracle Linux 9** and **x86_64** manually to display matching profiles. If the plugin is missing or reports `NOT_SUPPORTED`, stop and review the image and agent with your instructor.

3. Allow up to 10 minutes for the plugin to start. Open **OS Management Hub > Instances** in the same compartment and find your VM. Confirm its status is **Active**.

4. Open its details. Confirm the hostname, Oracle Linux 9 identity, software sources, and installed-package inventory. Inventory can take a few additional minutes to appear.

## Task 4: Apply one available package update

1. On the VM's OSMH details page, open **Updates**. Choose one available update for a small, installed non-kernel package. Review its dependencies and target version.

    If no suitable update is available, record **No package update available** and continue to Task 5. Registration and inventory can still be verified, but do not claim an update was applied.

2. In the same SSH session, set the selected package name and display its current version:

    ```bash
    <copy>
    PACKAGE='YOUR_PACKAGE'
    rpm -q "$PACKAGE"
    </copy>
    ```

    Replace `YOUR_PACKAGE` before running. Note this version so you can compare it after the job.

3. In the Console, select the package update and choose **Update > Immediately**. Give the job a name such as `ol-migrate-update-STUDENT_ID`. Review the target VM and selected package, then submit the update.

4. Open the instance's **Work requests** tab and select the resulting request. Wait for successful completion and review its messages. If it fails, inspect the error messages before retrying.

## Task 5: Verify the result and application

1. If you ran an update, query the package again in the same SSH session:

    ```bash
    <copy>
    rpm -q "$PACKAGE"
    </copy>
    ```

    Compare the installed version with the earlier value and the job's target version. A submitted job alone does not confirm that the package changed.

2. Check the application:

    ```bash
    <copy>
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

    Expect `active` and the workload marker. Open `http://<public-ip>/` in your browser and confirm the workshop page still appears. Review any reboot requirement reported by the update job before planning further maintenance.

3. Confirm the instance remains **Active** in OSMH. Record the profile, job result, package versions, and application result in your notes. Keep the VM, profile, networking, and workshop results for review.

    You have continued from migration to ongoing management of the same Oracle Linux VM. The workshop's on-premises migration scenario is practiced on OCI; registering a physical on-premises server uses a different agent and setup procedure.

## Learn More

- [Getting started with OSMH](https://docs.oracle.com/en-us/iaas/osmh/doc/getstarted.htm)
- [Registering an OCI instance](https://docs.oracle.com/en-us/iaas/osmh/doc/register-oci-instance.htm)
- [Installing individual updates](https://docs.oracle.com/en-us/iaas/osmh/doc/install-updates-instance.htm)
- [Viewing job messages](https://docs.oracle.com/en-us/iaas/osmh/doc/view-job-logs-errors.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
