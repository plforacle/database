# Lab 5: Manage and Patch Oracle Linux

## Introduction

The converted VM originated from a RHEL custom image, so it might not contain the Oracle Cloud Agent or Ksplice software included in current Oracle Linux platform images. In this lab, you install and verify the required management components, register the instance with OS Management Hub, and examine rebootless patching with Ksplice.

Ksplice applies selected critical security updates to a supported running kernel. On Oracle Linux, the Enhanced Client can also patch Ksplice-aware `glibc` and OpenSSL libraries. You must still keep on-disk packages current, and some updates still require conventional maintenance.

Oracle Linux provides two kernel choices. RHCK is the Red Hat Compatible Kernel used for the core migration path. The Unbreakable Enterprise Kernel (UEK) is Oracle's optimized kernel for Oracle Linux. OS Management Hub is the OCI service used to monitor and manage operating-system updates across registered instances.

### Objectives

In this lab, you will:

- Compare RHCK and UEK choices.
- Install and verify Oracle Cloud Agent.
- Register the converted VM with OS Management Hub.
- Install Ksplice for an OCI bring-your-own-image instance.
- Distinguish live updates from on-disk package updates.

### Prerequisites

Before beginning this lab, confirm that you have:

- Completed Lab 4.
- SSH access to the converted Oracle Linux 9.8 instance.
- A healthy Apache workload after conversion and reboot.
- Permission to configure OCI Identity and Access Management policies and OS Management Hub resources.
- Outbound network access from the instance to Oracle repositories and OCI services.

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

## Task 2: Install and verify Oracle Cloud Agent

1. Check whether Oracle Cloud Agent is installed:

    ```bash
    <copy>
    rpm -q oracle-cloud-agent && echo 'OCA installed' || echo 'OCA not installed'
    </copy>
    ```

2. Query the package repository:

    ```bash
    <copy>
    sudo dnf info oracle-cloud-agent
    </copy>
    ```

3. Install or update the agent:

    ```bash
    <copy>
    sudo dnf install -y oracle-cloud-agent
    </copy>
    ```

    If the package is unavailable, stop this task and use the Oracle documentation's support path. Do not download an agent RPM from an unverified site.

4. Enable and start the service:

    ```bash
    <copy>
    sudo systemctl enable --now oracle-cloud-agent
    sudo systemctl enable --now oracle-cloud-agent-updater
    </copy>
    ```

5. Verify the installed version and service state:

    ```bash
    <copy>
    rpm -q oracle-cloud-agent
    systemctl is-enabled oracle-cloud-agent
    systemctl is-active oracle-cloud-agent
    </copy>
    ```

    OS Management Hub requires Oracle Cloud Agent 1.40 or later.

## Task 3: Prepare OS Management Hub

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

## Task 4: Register the converted instance

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

## Task 6: Examine Enhanced Client capabilities

1. Check whether the Ksplice Enhanced Client is available:

    ```bash
    <copy>
    sudo dnf info ksplice
    </copy>
    ```

2. If the package is available from the configured OCI repositories, install it:

    ```bash
    <copy>
    sudo dnf install -y ksplice
    </copy>
    ```

3. List Ksplice-aware kernel and user-space targets:

    ```bash
    <copy>
    sudo ksplice all list-targets
    </copy>
    ```

4. Show applied updates:

    ```bash
    <copy>
    sudo ksplice all show
    </copy>
    ```

5. Explain the difference:

    - `uname -r` reports the kernel used at boot.
    - Ksplice reports the effective live-patched kernel state.
    - Ksplice-aware user-space patching applies only to supported libraries and processes.
    - On-disk packages must still be updated so a future reboot starts from current software.

## Task 7: Review your knowledge

1. Why was RHCK selected for the core path?

    It keeps the initial kernel interface close to RHEL while still moving to an Oracle-maintained Oracle Linux kernel.

2. Does Ksplice mean that the system will never require another reboot?

    No. Ksplice live-patches selected supported components. Other updates, configuration changes, hardware operations, and on-disk maintenance can still require a reboot.

3. Why did you install Oracle Cloud Agent manually?

    The VM originated from a RHEL KVM guest custom image instead of an OCI Oracle Linux platform image.

## Learn More

- [Registering an OCI instance with OS Management Hub](https://docs.oracle.com/en-us/iaas/osmh/doc/register-oci-instance.htm)
- [Oracle Cloud Agent](https://docs.oracle.com/en-us/iaas/Content/Compute/Tasks/manage-plugins.htm)
- [Installing Ksplice on an OCI instance](https://docs.oracle.com/en-us/iaas/oracle-linux/oci/install-ksplice.htm)
- [Maintained Ksplice kernels](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-MaintainedKernels.html)
- [Choosing a Ksplice client](https://docs.oracle.com/en/operating-systems/oracle-linux/ksplice-user/ksplice-ChoosingaKspliceClientFeaturesSupportedbyEachKspliceClient.html)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, July 2026

