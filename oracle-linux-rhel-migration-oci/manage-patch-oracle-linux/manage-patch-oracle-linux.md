# Lab 5: Manage and Patch Oracle Linux

## Introduction

The converted VM originated from a RHEL custom image, so it might not contain the Oracle Cloud Agent or Ksplice software included in current Oracle Linux platform images. In this lab, you check which management components are available, use OS Management Hub when the required Oracle Cloud Agent is available, and examine rebootless patching with Ksplice.

Ksplice applies selected critical security updates to a supported running kernel. On Oracle Linux, the Enhanced Client can also patch Ksplice-aware `glibc` and OpenSSL libraries. You must still keep on-disk packages current, and some updates still require conventional maintenance.

Oracle Linux provides two kernel choices. RHCK is the Red Hat Compatible Kernel used for the core migration path. The Unbreakable Enterprise Kernel (UEK) is Oracle's optimized kernel for Oracle Linux. OS Management Hub is the OCI service used to monitor and manage operating-system updates across registered instances.

### Objectives

In this lab, you will:

- Compare RHCK and UEK choices.
- Check whether Oracle Cloud Agent is available for the converted custom image.
- Register the converted VM with OS Management Hub when the required agent is available.
- Install Ksplice for an OCI bring-your-own-image instance.
- Distinguish live updates from on-disk package updates.

### Prerequisites

Before beginning this lab, confirm that you have:

- Completed Lab 4.
- SSH access to the converted Oracle Linux 9.8 instance.
- A healthy Apache workload after conversion and reboot.
- Outbound network access from the instance to Oracle repositories and OCI services.

The optional OS Management Hub tasks also require permission to configure OCI Identity and Access Management policies and OS Management Hub resources.

Estimated Lab Time: 35 minutes

## Task 1: Compare the available kernel choices

1. Display the running kernel and its owning package:

    ```bash
    <copy>
    uname -r
    rpm -qf "/boot/vmlinuz-$(uname -r)" --qf '%{NAME} %{VENDOR}\n'
    </copy>
    ```

2. List RHCK and UEK packages visible in the configured repositories:

    ```bash
    <copy>
    sudo dnf list --available kernel kernel-uek 2>/dev/null | head -30
    </copy>
    ```

3. Keep RHCK as the running kernel for this workshop.

    RHCK minimizes kernel change during the core migration path. UEK remains an available Oracle Linux choice for workloads that benefit from its features and certifications.

## Task 2: Check for Oracle Cloud Agent

1. Check whether Oracle Cloud Agent is installed:

    ```bash
    <copy>
    rpm -q oracle-cloud-agent && echo 'OCA installed' || echo 'OCA not installed'
    </copy>
    ```

2. If the agent is not installed, query the enabled package repositories:

    ```bash
    <copy>
    sudo dnf info oracle-cloud-agent
    </copy>
    ```

3. If the result is `Error: No matching Packages to list`, check whether an OCI-specific repository is available but disabled:

    ```bash
    <copy>
    sudo dnf repolist --all | grep -i oci
    </copy>
    ```

4. Follow the result that matches your system:

    - If an approved OCI repository containing `oracle-cloud-agent` is listed but disabled, enable it according to your organization's repository policy, refresh the metadata, and query the package again.
    - If no OCI repository is listed, Oracle Cloud Agent is not available from the configured repositories. Record the result, skip Tasks 3 and 4, and continue with Task 5.
    - Do not download an Oracle Cloud Agent RPM from an unverified site. Oracle documentation directs users without access to the required Yum repository to obtain the installation file through Oracle Support.

5. Only when `dnf info` displays an available Oracle Cloud Agent package, install it:

    ```bash
    <copy>
    sudo dnf install -y oracle-cloud-agent
    </copy>
    ```

6. Enable and start the service:

    ```bash
    <copy>
    sudo systemctl enable --now oracle-cloud-agent
    sudo systemctl enable --now oracle-cloud-agent-updater
    </copy>
    ```

7. Verify the installed version and service state:

    ```bash
    <copy>
    rpm -q oracle-cloud-agent
    systemctl is-enabled oracle-cloud-agent
    systemctl is-active oracle-cloud-agent
    </copy>
    ```

    OS Management Hub requires Oracle Cloud Agent 1.40 or later.

## Task 3: Prepare OS Management Hub when the agent is available

Complete this optional task only when Task 2 confirms that Oracle Cloud Agent 1.40 or later is installed and active. Otherwise, continue with Task 5.

1. In the OCI Console, open **Observability & Management**, then **OS Management Hub**.

2. Complete the service's **Getting Started** prerequisites if your tenancy has not used OS Management Hub before.

3. Use the OS Management Hub policy advisor or your organization's approved IAM policy to grant the required permissions.

4. Under **Profiles**, select **Create profile**.

5. Configure the profile:

    - Name: `ol-migrate-ol9-rhck`
    - Location: Oracle Cloud Infrastructure
    - OS vendor: Oracle
    - OS version: Oracle Linux 9
    - Architecture: x86_64
    - Registration type: Software source

6. Select the current Oracle Linux 9 BaseOS and AppStream software sources required by your tenancy.

7. Create the profile and wait until it is available.

## Task 4: Register the converted instance when the agent is available

Complete this optional task only after successfully completing Tasks 2 and 3. Otherwise, continue with Task 5.

1. In the OCI Console, open **Compute**, then **Instances**, and select `ol-migrate-rhel-source`.

2. Select the **Management** tab.

3. Under **Oracle Cloud Agent**, locate **OS Management Hub Agent**.

4. From its actions menu, select **Enable**.

5. Select the `ol-migrate-ol9-rhck` profile and confirm the action.

6. Wait up to 10 minutes for registration.

7. In **OS Management Hub**, select **Instances** and confirm that the instance status is Active.

8. Open the instance and review its package inventory, available updates, reports, and registration profile.

9. If registration fails, verify:

    - Oracle Cloud Agent is version 1.40 or later.
    - The agent and updater services are active.
    - The selected profile matches Oracle Linux 9 and x86_64.
    - The tenancy IAM policy permits registration.
    - The VM can reach OCI services over TCP 443.

## Task 5: Install Ksplice for the converted custom image

1. Verify that Oracle Linux 9 RHCK is actively maintained by Ksplice using the maintained-kernels documentation.

2. Install `wget` if needed:

    ```bash
    <copy>
    sudo dnf install -y wget
    </copy>
    ```

3. Download the OCI Ksplice installer:

    ```bash
    <copy>
    cd "$HOME"
    sudo wget -N https://www.ksplice.com/uptrack/install-uptrack-oc
    </copy>
    ```

4. Install the client without enabling unattended updates:

    ```bash
    <copy>
    sudo sh install-uptrack-oc
    </copy>
    ```

5. Record the booted and effective kernel versions:

    ```bash
    <copy>
    uname -r
    sudo uptrack-uname -r
    </copy>
    ```

6. Display applied and available updates:

    ```bash
    <copy>
    sudo uptrack-show
    sudo uptrack-upgrade -n
    </copy>
    ```

7. Apply available Ksplice kernel updates:

    ```bash
    <copy>
    sudo uptrack-upgrade -y
    </copy>
    ```

8. Display the effective kernel version and applied updates again:

    ```bash
    <copy>
    sudo uptrack-uname -r
    sudo uptrack-show
    </copy>
    ```

    If no updates are available, record that valid result. Do not downgrade the kernel to manufacture an update.

## Task 6: Check the optional Enhanced Client capabilities

1. Check whether the Ksplice Enhanced Client is available:

    ```bash
    <copy>
    sudo dnf info ksplice
    </copy>
    ```

2. Follow the result that matches your system:

    - If `dnf info` displays the `ksplice` package, continue with Step 3.
    - If the result is `Error: No matching Packages to list`, record that the Enhanced Client is unavailable, skip Steps 3 through 5, and continue with Step 6.
    - Do not add an unapproved repository or download the package from an unverified site.

3. If the package is available from the configured OCI repositories, install it:

    ```bash
    <copy>
    sudo dnf install -y ksplice
    </copy>
    ```

4. List Ksplice-aware kernel and user-space targets:

    ```bash
    <copy>
    sudo ksplice all list-targets
    </copy>
    ```

5. Show applied updates:

    ```bash
    <copy>
    sudo ksplice all show
    </copy>
    ```

6. Explain the difference:

    - `uname -r` reports the kernel used at boot.
    - Ksplice reports the effective live-patched kernel state.
    - Ksplice-aware user-space patching applies only to supported libraries and processes.
    - On-disk packages must still be updated so a future reboot starts from current software.

## Task 7: Review your knowledge

1. Why was RHCK selected for the core path?

    It keeps the initial kernel interface close to RHEL while still moving to an Oracle-maintained Oracle Linux kernel.

2. Does Ksplice mean that the system will never require another reboot?

    No. Ksplice live-patches selected supported components. Other updates, configuration changes, hardware operations, and on-disk maintenance can still require a reboot.

3. Why might Oracle Cloud Agent require a separate installation path?

    The VM originated from a RHEL KVM guest custom image instead of an OCI Oracle Linux platform image. If the agent is not available from an approved configured repository, obtain it through the Oracle Support path or treat the OS Management Hub exercise as optional.

## Learn More

- [Registering an OCI instance with OS Management Hub](https://docs.oracle.com/en-us/iaas/osmh/doc/register-oci-instance.htm)
- [Oracle Cloud Agent](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/manage-plugins.htm)
- [Installing Ksplice on an OCI instance](https://docs.oracle.com/en-us/iaas/oracle-linux/oci/install-ksplice.htm)
- [Maintained Ksplice kernels](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-MaintainedKernels.html)
- [Choosing a Ksplice client](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-ChoosingaKspliceClientFeaturesSupportedbyEachKspliceClient.html)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, July 2026

