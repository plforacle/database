# Provision OCI Networking for a Dedicated Migration Path (Optional)

## Introduction

This optional advanced lab extends the workshop environment with a new OCI-side network path for live migration traffic. You will create a new VLAN inside `OLV-VCN`, attach a secondary VNIC from that VLAN to each KVM host, configure the Oracle Linux host OS to recognize the new VNIC, and verify both hosts have connectivity on the new network before you map it inside OLVM in the next lab.

Estimated Time: 20-30 minutes

### Objectives

In this lab, you will:

- Create a new OCI VLAN for dedicated migration traffic
- Attach one secondary VNIC from that VLAN to each KVM host
- Configure Oracle Linux on each KVM host to use the new VNIC
- Verify host-to-host connectivity on the new migration network

### Prerequisites

This lab assumes you have:

- Completed the Lab 4 checkpoint
- Both KVM hosts showing status **Up**
- Access to the OCI Console
- SSH access to the `olvm` manager and both KVM hosts

> **Important:** This optional lab changes the underlying OCI network topology for the KVM hosts. Do not perform it while any earlier provisioning step is still in progress.
>
> **Important:** This lab adds a secondary VNIC to each KVM host only. Do not add this VNIC to the `olvm` engine host.

## Task 1: Create the OCI VLAN

1. In the OCI Console, navigate to **Networking -> Virtual Cloud Networks**.

2. Open the `OLV-VCN` VCN.

3. Under **Resources**, click **VLANs**.

4. Click **Create VLAN**.

5. Enter values similar to the following:

    | Field | Value |
    |---|---|
    | Name | `migration-vlan-02` |
    | Compartment | *your compartment* |
    | CIDR Block | `10.0.20.0/24` |

6. Leave the VLAN as a regional VLAN unless your instructor specifically requires an availability-domain-specific configuration.

7. Accept the default route table selection unless your instructor explicitly tells you to use a custom route table.

8. Click **Create VLAN**.

9. Wait for the VLAN state to show **Available**.

## Task 2: Attach a Secondary VNIC to `olkvm01`

1. In the OCI Console, navigate to **Compute -> Instances**.

2. Click the `olkvm01` instance.

3. Under **Resources**, click **Attached VNICs**.

4. Click **Create VNIC**.

5. Use values similar to the following:

    | Field | Value |
    |---|---|
    | Name | `olkvm01-migration-vnic` |
    | VCN | `OLV-VCN` |
    | Network Resource Type | VLAN |
    | VLAN | `migration-vlan-02` |
    | Assign Private IP | Auto-assign |
    | Assign Public IP | No |

6. Click **Create VNIC**.

7. Wait until the VNIC attachment state shows **Attached**.

## Task 3: Attach a Secondary VNIC to `olkvm02`

1. In the OCI Console, navigate to **Compute -> Instances**.

2. Click the `olkvm02` instance.

3. Under **Resources**, click **Attached VNICs**.

4. Click **Create VNIC**.

5. Use values similar to the following:

    | Field | Value |
    |---|---|
    | Name | `olkvm02-migration-vnic` |
    | VCN | `OLV-VCN` |
    | Network Resource Type | VLAN |
    | VLAN | `migration-vlan-02` |
    | Assign Private IP | Auto-assign |
    | Assign Public IP | No |

6. Click **Create VNIC**.

7. Wait until the VNIC attachment state shows **Attached**.

## Task 4: Configure the New VNIC on `olkvm01`

1. From your local PowerShell window, connect to the `olvm` manager if you are not already connected.

    ```bash
    <copy>ssh -i C:\Users\<you>\.ssh\olvm-cluster-id_rsa oracle@<olvm-public-ip></copy>
    ```

2. From the `olvm` manager, connect to `olkvm01`.

    ```bash
    <copy>ssh olkvm01</copy>
    ```

3. Display the current OCI VNIC configuration.

    ```bash
    <copy>sudo oci-network-config show</copy>
    ```

4. Configure any newly attached VNICs that still show as `ADD`.

    ```bash
    <copy>sudo oci-network-config configure</copy>
    ```

5. Display all configured VNICs and note the private IP assigned to the new migration VNIC.

    ```bash
    <copy>sudo oci-network-config show-vnics-all
    ip addr</copy>
    ```

6. Record the new migration-network IP address for `olkvm01`.

7. Exit `olkvm01`.

    ```bash
    <copy>exit</copy>
    ```

## Task 5: Configure the New VNIC on `olkvm02`

1. From the `olvm` manager, connect to `olkvm02`.

    ```bash
    <copy>ssh olkvm02</copy>
    ```

2. Display the current OCI VNIC configuration.

    ```bash
    <copy>sudo oci-network-config show</copy>
    ```

3. Configure any newly attached VNICs that still show as `ADD`.

    ```bash
    <copy>sudo oci-network-config configure</copy>
    ```

4. Display all configured VNICs and note the private IP assigned to the new migration VNIC.

    ```bash
    <copy>sudo oci-network-config show-vnics-all
    ip addr</copy>
    ```

5. Record the new migration-network IP address for `olkvm02`.

6. Exit `olkvm02`.

    ```bash
    <copy>exit</copy>
    ```

## Task 6: Verify Host-to-Host Connectivity on the New VLAN

1. From the `olvm` manager, connect back to `olkvm01`.

    ```bash
    <copy>ssh olkvm01</copy>
    ```

2. Ping the migration-network IP you recorded for `olkvm02`.

    ```bash
    <copy>ping -c 3 <olkvm02-migration-ip></copy>
    ```

3. Confirm the ping succeeds.

4. Exit `olkvm01`.

    ```bash
    <copy>exit</copy>
    ```

## Dedicated Migration OCI Network Checkpoint

At this point, you should have:

- The `migration-vlan-02` VLAN created in `OLV-VCN`
- One secondary VNIC from that VLAN attached to `olkvm01`
- One secondary VNIC from that VLAN attached to `olkvm02`
- Oracle Linux configured to recognize the new VNIC on both hosts
- Successful host-to-host connectivity on the new migration network

You are ready for the next optional lab, where you map this new OCI-side path into OLVM as a dedicated migration logical network.

## Learn More

- Oracle Linux Virtualization Manager install lab (official): https://docs.oracle.com/en/learn/olvm-install/index.html

## Acknowledgements

- **Author** - Shawn Kelley, Perside Foster
- **Contributor** - Marvin Kim
- **Last Updated By/Date** - OpenAI Codex, June 2, 2026
