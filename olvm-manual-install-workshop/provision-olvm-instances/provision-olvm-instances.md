# Lab 2: Provision the OLVM Engine and KVM Hosts

## Introduction

In this lab, you will provision the OLVM engine and the two KVM hosts, add the private `VDSM` VNIC to all three instances, create the VLAN VNIC on both KVM hosts, and attach the shared block volumes that will become OLVM storage domains.

Estimated Time: 35 minutes

### Objectives

In this lab, you will:
* Create `olvm-engine`, `olvm-kvm1`, and `olvm-kvm2`.
* Add the `VDSM` VNIC on the `10.0.1.0/24` subnet.
* Configure `ens5` with `nmcli` on each instance and verify private connectivity.
* Add the VLAN VNIC to the KVM hosts.
* Create and attach the two block volumes used later as data domains.

### Prerequisites (Optional)

This lab assumes you completed Lab 1 and have the `OLVM-VCN` networking resources available.

## Task 1: Create the OLVM Engine Instance

1. In OCI, go to **Compute** and open **Instances** in the OLVM compartment.

2. Create the engine instance with the values from the source.

    Use these values:
    * Name: `olvm-engine`
    * Image: `Oracle Linux 8`
    * Shape: `VM.Standard.E4.Flex`
    * OCPUs: `2`
    * Memory: `32 GB`
    * Existing VCN: `OLVM-VCN`
    * Existing subnet: `public-subnet-OLVM-VCN`
    * Primary private IP: `10.0.0.10`
    * Public IP: enabled

3. Generate an SSH key pair and download both keys before you continue.

## Task 2: Create the Two KVM Hosts

1. Create `olvm-kvm1` with the values from the source.

    Use these values:
    * Name: `olvm-kvm1`
    * Image: `Oracle Linux 8`
    * Shape: `VM.Standard.E4.Flex`
    * OCPUs: `6`
    * Memory: `96 GB`
    * Existing VCN: `OLVM-VCN`
    * Existing subnet: `public-subnet-OLVM-VCN`
    * Primary private IP: `10.0.0.11`
    * Public IP: enabled

2. Upload the public SSH key that you downloaded when you created `olvm-engine`.

3. Repeat the same process for `olvm-kvm2` and set the primary private IP to `10.0.0.12`.

4. Confirm the three instances use these primary addresses:

    * `olvm-engine`: `10.0.0.10`
    * `olvm-kvm1`: `10.0.0.11`
    * `olvm-kvm2`: `10.0.0.12`

## Task 3: Add the Private VDSM VNIC to All Three Instances

1. Open `olvm-engine`, go to **Networking**, and create a VNIC named `VDSM`.

    Use these values:
    * VCN: `OLVM-VCN`
    * Setup: `Subnet`
    * Subnet: `private-subnet-OLVM-VCN`
    * Manually assigned private IP: `10.0.1.10`

2. Repeat the same VNIC creation flow for the two KVM hosts and use these addresses:

    * `olvm-kvm1`: `10.0.1.11`
    * `olvm-kvm2`: `10.0.1.12`

## Task 4: Configure the VDSM Interface on Each Instance

1. Start with `olvm-engine`, capture its public IP address, and connect with SSH.

    ```bash
    ssh opc@<olvm-engine-public-ip> -i <path-to-local-ssh-private-key>
    sudo oci-network-config show
    sudo nmcli dev
    sudo nmcli con
    ip addr
    sudo nmcli con add type ethernet con-name ens5 ip4 10.0.1.10/24 gw4 10.0.1.1
    sudo nmcli con mod ens5 connection.interface-name ens5
    sudo oci-network-config show
    ```

2. Repeat the configuration on `olvm-kvm1`.

    ```bash
    ssh opc@<olvm-kvm1-public-ip> -i <path-to-local-ssh-private-key>
    sudo oci-network-config show
    sudo nmcli dev
    sudo nmcli con
    ip addr
    sudo nmcli con add type ethernet con-name ens5 ip4 10.0.1.11/24 gw4 10.0.1.1
    sudo nmcli con mod ens5 connection.interface-name ens5
    sudo oci-network-config show
    ```

3. Repeat the configuration on `olvm-kvm2`.

    ```bash
    ssh opc@<olvm-kvm2-public-ip> -i <path-to-local-ssh-private-key>
    sudo oci-network-config show
    sudo nmcli dev
    sudo nmcli con
    ip addr
    sudo nmcli con add type ethernet con-name ens5 ip4 10.0.1.12/24 gw4 10.0.1.1
    sudo nmcli con mod ens5 connection.interface-name ens5
    sudo oci-network-config show
    ```

4. Verify that all three instances can ping one another across the `VDSM` network before you continue.

    ```bash
    ping -I ens5 10.0.1.10
    ping -I ens5 10.0.1.11
    ping -I ens5 10.0.1.12
    ```

5. If the private ping test fails, stop and correct the private subnet security list before you continue.

## Task 5: Add the VLAN VNIC to Both KVM Hosts

1. Open `olvm-kvm1`, go to **Networking**, and create a VNIC named `l2-vm-network`.

    Use these values:
    * Virtual Cloud Network: `OLVM-VCN`
    * Setup: `Advanced setup`
    * Network type: `VLAN`
    * VLAN: `VLAN-VMs`

2. Repeat the same step for `olvm-kvm2`.

3. Return to the SSH sessions for both KVM hosts and confirm `ip addr` shows a new interface for the VLAN VNIC.

    ```bash
    ip addr
    ```

4. Do not continue until both KVM hosts still support SSH access and can still ping across the `VDSM` network after the VLAN VNIC is added.

## Task 6: Create and Attach the Shared Block Volumes

1. In OCI, go to **Storage** and open **Block Volumes**.

2. Create a block volume named `1-storagedomain` in the OLVM compartment and accept the default size of `1024 GB`.

3. Create a second block volume named `2-storagedomain` with the same settings.

4. Attach both volumes to `olvm-kvm1` as `Read/Write Shareable`, accept the warning about data corruption, and do not set a device path.

5. Repeat the same attachments for `olvm-kvm2`.

6. After both hosts have both volumes attached, proceed to the OLVM installation steps.

## Acknowledgements
* **Author** - Codex
* **Contributors** - `seighman` (source tutorial author shown in the PDF)
* **Last Updated By/Date** - Codex, May 2026
