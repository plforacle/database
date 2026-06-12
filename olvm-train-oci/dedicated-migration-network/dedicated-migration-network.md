# Map the Dedicated Migration Network in OLVM (Optional)

## Introduction

This optional lab is the OLVM-side half of the dedicated migration network workflow. After you provision the additional OCI VLAN and host VNIC attachments in the preceding optional lab, you will create a migration-role logical network in OLVM, map it to the new host-side interface on both KVM hosts, and verify both hosts accept the configuration.

Estimated Time: 10-15 minutes

### Objectives

In this lab, you will:

- Create a logical network for migration traffic
- Assign the Migration role to that logical network
- Map the logical network to both KVM hosts
- Verify both hosts remain `Up` after the network change

### Prerequisites

This lab assumes you have:

- Completed the Lab 4 checkpoint
- Completed the optional OCI networking lab for the dedicated migration path
- Both KVM hosts showing status **Up**
- Access to the Administration Portal
- A newly attached host-side interface available for migration traffic on both hosts

> **Important:** This lab assumes the new OCI VLAN and secondary VNICs are already in place on both KVM hosts.
>
> **Important:** If the new migration VNIC does not appear on both hosts, return to the OCI networking lab and complete that setup before continuing.

## Task 1: Review Host Network Interfaces

1. In the **Administration Portal**, navigate to **Compute -> Hosts**.

2. Click `olkvm01`, then open the **Network Interfaces** tab.

3. Review the available interfaces and identify the new interface created by the additional OCI VNIC attachment.

4. Repeat the same check on `olkvm02`.

5. Confirm that both hosts have a matching new interface available for the migration network mapping.

    - Do not reuse the interface that already carries `ovirtmgmt`.
    - Do not move `l2-vm-network` from the interface you configured in Lab 4.
    - If the new migration interface is missing on either host, stop here and return to the OCI networking lab.

## Task 2: Create the Migration Logical Network

1. Navigate to **Network -> Networks**.

2. Click **New**.

3. Leave **Data Center** set to **Default**.

4. For **Name**, enter:

    ```
    <copy>l2-migration-network</copy>
    ```

5. In the network role options, select **Migration Network**.

6. If **VM Network** is selected by default, clear it.

7. Leave any **Required** or default-route options unchanged unless your instructor explicitly tells you to use them.

8. Click **OK**.

## Task 3: Assign the Migration Network to `olkvm01`

1. Navigate to **Compute -> Hosts**.

2. Click the `olkvm01` host name.

3. Open the **Network Interfaces** tab.

4. Click **Setup Host Networks**.

5. Drag `l2-migration-network` from **Unassigned Logical Networks** into the new interface you identified for migration traffic in Task 1.

6. Click **OK** and wait for the network setup task to finish.

## Task 4: Assign the Migration Network to `olkvm02`

1. Navigate to **Compute -> Hosts**.

2. Click the `olkvm02` host name.

3. Open the **Network Interfaces** tab.

4. Click **Setup Host Networks**.

5. Drag `l2-migration-network` from **Unassigned Logical Networks** into the matching new interface you identified for migration traffic in Task 1.

6. Click **OK** and wait for the network setup task to finish.

## Task 5: Verify the Migration Network Configuration

1. Navigate to **Network -> Networks** and confirm `l2-migration-network` now appears in the network list.

2. Verify the network shows the **Migration** role for the cluster.

3. Navigate to **Compute -> Hosts** and confirm both `olkvm01` and `olkvm02` remain in status **Up**.

4. If either host becomes **Non Operational** or the network setup task fails, remove the optional migration-network assignment and return to the standard workshop flow before continuing.

## Dedicated Migration Network Checkpoint

At this point, you should have:

- `l2-migration-network` created in OLVM
- The Migration role assigned to that logical network
- The network mapped to both KVM hosts
- Both hosts still showing status **Up**

You can continue to the next workshop lab after this checkpoint. Live migration is still validated in the later migration lab.

## Learn More

- Oracle Linux Virtualization Manager install lab (official): https://docs.oracle.com/en/learn/olvm-install/index.html

## Acknowledgements

- **Author** - Shawn Kelley, Perside Foster
- **Contributor** - Marvin Kim
- **Last Updated By/Date** - OpenAI Codex, June 2, 2026
