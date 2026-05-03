# Lab 4: Onboard Hosts, Networks, and Storage

## Introduction

In this lab, you will prepare both KVM hosts, add them to the OLVM administration portal, create the `l2-vm-network` logical network, assign it to the VLAN interface on each host, and attach the Fibre Channel storage domains.

Estimated Time: 30 minutes

### Objectives

In this lab, you will:
* Install the OLVM release package and required kernel modules on both KVM hosts.
* Add `olvm-kvm1` and `olvm-kvm2` to the OLVM administration portal.
* Create the `l2-vm-network` logical network and attach it to `ens6` on both hosts.
* Create `1-storagedomain` and `2-storagedomain` as Fibre Channel data domains.

### Prerequisites (Optional)

This lab assumes the OLVM administration portal is reachable and both KVM hosts still have healthy `VDSM` connectivity after the VLAN VNIC was added.

## Task 1: Prepare Both KVM Hosts

1. Connect to `olvm-kvm1` over SSH and install the release package and required kernel modules.

    ```bash
    ssh opc@<olvm-kvm1-public-ip>
    sudo dnf install -y oracle-ovirt-release-45-el8
    sudo dnf install -y kernel-uek-modules-extra
    sudo dnf clean all
    sudo reboot
    ```

2. Repeat the same commands on `olvm-kvm2`.

    ```bash
    ssh opc@<olvm-kvm2-public-ip>
    sudo dnf install -y oracle-ovirt-release-45-el8
    sudo dnf install -y kernel-uek-modules-extra
    sudo dnf clean all
    sudo reboot
    ```

## Task 2: Add Both Hosts to the Administration Portal

1. Sign in to the OLVM administration portal and go to **Compute** and then **Hosts**.

2. Click **New** and add `olvm-kvm1`.

    Use these values:
    * Host Cluster: `Default`
    * Name: `olvm-kvm1`
    * Hostname: `10.0.1.11`

3. Under **Authentication**, copy the engine SSH public key from the portal.

4. On `olvm-kvm1`, switch to the root user and paste that public key into `/root/.ssh/authorized_keys`.

    ```bash
    sudo su -
    vi /root/.ssh/authorized_keys
    ```

5. Back in the portal, use the **fetch manually** link to populate the **Host ssh public key (PEM)** field, clear the **Automatically configure host firewall** option, and continue.

6. Accept the power management screen without configuring power management, because the source states OCI instances do not allow it.

7. Repeat the same flow for `olvm-kvm2` and use `10.0.1.12` as the host address.

8. Wait until both hosts move from `Installing` to `Up` before you continue. The source notes that this step can take about 15 minutes.

## Task 3: Create and Assign the Logical Network

1. In the administration portal, go to **Network** and then **Networks**.

2. Create a logical network with the values from the source.

    Use these values:
    * Name: `l2-vm-network`
    * Data Center: `Default`
    * VM Network: selected

3. Open `olvm-kvm1`, go to the **Network Interfaces** tab, click **Setup Host Networks**, and drag `l2-vm-network` from **Unassigned Logical Networks** onto the box next to the `ens6` interface.

4. Repeat the same network assignment for `olvm-kvm2`.

5. After a KVM host is added to the cluster, avoid spontaneous network changes in OCI or through `NetworkManager` until the host is fully `Up`, as directed by the source tutorial.

## Task 4: Add the Fibre Channel Storage Domains

1. In the administration portal, go to **Storage** and then **Domains**.

2. Click **New Domain** and create `1-storagedomain` with these values:

    * Data Center: `Default`
    * Domain Function: `Data`
    * Storage Type: `Fibre Channel`
    * Host to Use: `olvm-kvm1`

3. When the unused LUNs are displayed, click **Add** next to the first LUN ID and then click **OK**.

4. Wait until the cross data center status is `Active`.

5. Repeat the same process for `2-storagedomain` and use `olvm-kvm2` as the host.

## Acknowledgements
* **Author** - Codex
* **Contributors** - `seighman` (source tutorial author shown in the PDF)
* **Last Updated By/Date** - Codex, May 2026
