# Set Up Networking, Storage, and VM

## Introduction

In this lab, you will create the logical network for guest traffic, add shared storage, import an Oracle Linux template, and deploy a test VM to verify the environment before you move on to the application lab.

**Estimated Lab Time:** 60-75 minutes, including OVA download and template import time.

### Objectives

In this lab, you will:

- Create the `l2-vm-network` logical network
- Assign that network to both KVM hosts
- Add a shared Fibre Channel storage domain
- Import an Oracle Linux template
- Create and validate the `ol9-vm1` test VM

### Prerequisites

This lab assumes you have:

- Completed the Lab 3 checkpoint
- Both KVM hosts showing status **Up**
- Access to the Administration Portal
- A working manager desktop session through the Lab 2 SSH tunnel

> **Important:** Do not start this lab while either host is still `Installing`, `Initializing`, or `Non Operational`.
>
> **Important:** Do not start Lab 5 until this lab's checkpoint is complete.

## Task 1: Create a Logical Network

1. In the **Administration Portal**, navigate to **Network -> Networks**.

2. Click **New**.

3. Leave **Data Center** set to **Default**.

4. For **Name**, enter:

    ```
    <copy>l2-vm-network</copy>
    ```

5. Leave **VM Network** selected.

6. Click **OK**.

## Task 2: Assign the Logical Network to `olkvm01`

1. Navigate to **Compute -> Hosts**.

2. Click the `olkvm01` host name.

3. Open the **Network Interfaces** tab.

4. Click **Setup Host Networks**.

5. Drag `l2-vm-network` from **Unassigned Logical Networks** to the physical interface box on the right, such as `ens5`.

6. Click **OK** and wait for the network setup task to finish before you continue.

## Task 3: Assign the Logical Network to `olkvm02`

1. Navigate to **Compute -> Hosts**.

2. Click the `olkvm02` host name.

3. Open the **Network Interfaces** tab.

4. Click **Setup Host Networks**.

5. Drag `l2-vm-network` from **Unassigned Logical Networks** to the physical interface box on the right.

6. Click **OK** and wait for the network setup task to finish before you continue.

## Task 4: Add a Fibre Channel Data Domain

1. Navigate to **Storage -> Domains**.

2. Click **New Domain**.

3. For **Name**, enter:

    ```
    <copy>amd-storage-domain-01</copy>
    ```

4. Set **Data Center** to **Default**.

5. Set **Domain Function** to **Data**.

6. Set **Storage Type** to **Fibre Channel**.

7. Set **Host to Use** to **olkvm01**.

8. When the available LUNs appear, click **Add** next to the first LUN ID.

9. Click **OK**.

10. Use **Tasks** in the upper-right corner to monitor progress.

11. Wait for **Cross Data Center Status** to show **Active** before you continue.

    **Expected time:** 5-10 minutes.

    If the domain is still not active after 15 minutes, or the LUN list never appears, stop and contact the instructor or workshop owner before changing the storage configuration manually.

## Task 5: Import a Virtual Machine Template

1. Navigate to **Compute -> Templates -> Import**.

2. Keep the default values for **Data Center** and **Source**, and select **olkvm01** for **Host**.

3. For **File Path**, enter:

    ```bash
    <copy>/tmp</copy>
    ```

4. Switch to the manager terminal and make sure you are on the `olvm` host.

5. Download the OVA template to `olkvm01`:

    ```bash
    <copy>ssh olkvm01 "curl -L https://yum.oracle.com/templates/OracleLinux/OL9/u5/x86_64/OL9U5_x86_64-olvm-b253.ova -o /tmp/ol95.ova"</copy>
    ```

    **Expected time:** 10-20 minutes, depending on download speed.

6. Return to the browser and click **Load**.

7. In **Virtual Machines on Source**, select the OVA template.

8. Click the **Right Arrow** to move it to **Virtual Machines to Import**.

9. Click **Next**.

10. Review the template information, then click **OK**.

11. Wait for the template import status to show **OK** before you continue.

    **Expected time:** 10-20 minutes.

    Do not create the test VM until the import finishes successfully.

## Task 6: Create a Test Virtual Machine (`ol9-vm1`)

1. Navigate to **Compute -> Virtual Machines -> New**.

2. Set **Template** to `OL9U5x8664-olvm-b253`.

3. Set **Operating System** to `Oracle Linux 9.x x64`.

4. Set **Name** to `ol9-vm1`.

5. Set `nic1` to `l2-vm-network`.

6. Click **Show Advanced Options**.

7. Open **Initial Run -> Authentication** and enter:

    - **User Name:** `opc`
    - **Password / Verify Password:** `oracle`

8. Open **Networks** and enter:

    - **DNS Servers:** `10.0.10.1`
    - Check **In-guest Network Interface Name**, then click **Add New**
    - **Name:** `eth0`
    - **IPv4 Boot Protocol:** `Static`
    - **IPv4 Address:** `10.0.10.105`
    - **IPv4 Netmask:** `255.255.255.0`
    - **IPv4 Gateway:** `10.0.10.1`

9. Click **OK**.

10. Wait for the VM status to change from **Importing** to **Down** before you continue.

## Task 7: Run the Test Virtual Machine

1. Select `ol9-vm1` and click **Run**.

2. Wait for the VM status to change to **Up**.

3. Open the console by clicking **Console -> Console Options**, selecting **noVNC**, and clicking **OK**.

4. Click **Console** to open the VM session in a new browser window.

5. Log in with the username and password defined in Task 6.

6. Verify the network settings:

    ```bash
    <copy>ip addr</copy>
    ```

    The output should show `10.0.10.105` on `eth0`.

7. Verify gateway connectivity:

    ```bash
    <copy>ping 10.0.10.1</copy>
    ```

    The ping should succeed, confirming network connectivity.

### Set Up Networking, Storage, and VM Checkpoint

At this point, you should have:

- `l2-vm-network` assigned to both hosts
- The Fibre Channel storage domain in **Active** state
- The Oracle Linux template imported successfully
- `ol9-vm1` running with IP address `10.0.10.105`
- Verified connectivity from `ol9-vm1` to `10.0.10.1`

You are ready for Lab 5 only when all checkpoint items above are complete.

## Learn More

- Oracle Linux Virtualization Manager install lab (official): https://docs.oracle.com/en/learn/olvm-install/index.html

## Acknowledgements

- **Author** - Shawn Kelley, John Priest
- **Contributors** - Perside Foster
- **Last Updated By/Date** - Perside Foster, May 6, 2026
