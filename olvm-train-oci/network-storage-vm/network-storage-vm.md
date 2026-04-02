# Set Up Networking, Storage, and VM

## Introduction

### Networking, Storage, and VM Setup

#### Overview

In this part, you will configure a logical network for VM traffic, add shared storage, import a VM template, and create a test virtual machine to verify your infrastructure.

Estimated Lab Time: 60–75 minutes


### What You Will Build

```
┌─────────────────────────────────────────────────────────────────────────┐
│                  Part 3: Networking, Storage, and VM                    │
│                                                                         │
│  ┌─────────────────┐      ┌─────────────────┐   ┌─────────────────┐     │
│  │   OLVM Engine   │      │    olkvm01      │   │    olkvm02      │     │
│  │     (olvm)      │      │   (KVM Host)    │   │   (KVM Host)    │     │
│  │                 │      │                 │   │                 │     │
│  │ • Admin Portal  │ ───> │ • VDSM Agent    │   │ • VDSM Agent    │     │
│  │ • REST API      │      │ • l2-vm-network │   │ • l2-vm-network │     │
│  │ • PostgreSQL    │      │                 │   │                 │     │
│  └─────────────────┘      │ ┌─────────────┐ │   │ ┌─────────────┐ │     │
│                           │ │ ol9-mysql   │ │   │ │ ol9-webapp  │ │     │
│                           │ │ 10.0.10.100 │ │   │ │ 10.0.10.101 │ │     │
│                           │ └─────────────┘ │   │ └─────────────┘ │     │
│                           └─────────┬───────┘   └────────┬────────┘     │
│                                     │                    │              │
│                                     └──────────┬─────────┘              │
│                                                │                        │
│                              ┌─────────────────┴─────────────────┐      │
│                              │     Shared Storage Domain         │      │
│                              │    (Fibre Channel / LUNs)         │      │
│                              │    • VM Disks  • Templates        │      │
│                              └───────────────────────────────────┘      │
└─────────────────────────────────────────────────────────────────────────┘
```

## Task 1: Create a Logical Network

A **logical network** is a virtual network layer in OLVM that defines how VMs communicate. The default `ovirtmgmt` network handles engine ↔ VDSM management traffic. We'll create a separate network specifically for VM traffic.

1. Using the side navigation menu, go to **Network** → **Networks**.

2. Click **New**.

3. The **Default** data center is pre-selected.

4. For the Name field, enter:

    ```
    <copy>l2-vm-network</copy>
    ```

5. Leave the **VM Network** check box selected (default). This creates a network available to virtual machines.

6. Click **OK** to create the network.



## Task 2: Assign the Logical Network to olkvm01

1. Go to **Compute** → **Hosts**.

2. Click the **olkvm01** host name.

3. Click the **Network Interfaces** tab.

4. Click **Setup Host Networks**.

5. Drag `l2-vm-network` from the **Unassigned Logical Networks** column to the physical interface box on the right (e.g., `ens5`).

    **What happens:** VDSM creates a Linux bridge on the host, attaches the physical interface to the bridge, and configures traffic rules. VMs will connect to this bridge for network access.

6. Click **OK**.



## Task 3: Assign the Logical Network to olkvm02
> Repeat the process from Task 2
1. Go to **Compute** → **Hosts**.

2. Click the **olkvm02** host name.

3. Click the **Network Interfaces** tab.

4. Click **Setup Host Networks**.

5. Drag `l2-vm-network` from **Unassigned Logical Networks** to the physical interface box on the right.

6. Click **OK**.



## Task 4: Add a Fibre Channel Data Domain

A **storage domain** is where OLVM stores VM disk images, templates, and snapshots. For shared storage (required for VM migration and HA), all hosts in the cluster must access the same storage. In this lab, OCI Block Volumes appear as Fibre Channel LUNs.

1. Using the side navigation menu, go to **Storage** → **Domains**.

2. Click **New Domain**.

3. For the Name field, enter:

    ```
    <copy>amd-storage-domain-01</copy>
    ```

4. Data Center: **Default**

5. Domain Function: **Data**

6. Storage Type: **Fibre Channel**

7. Host to Use: **olkvm01**

8. The dialog automatically displays available LUNs. Click **Add** next to the first LUN ID.

9. Click **OK**.

10. You can click **Tasks** in the upper right corner to monitor progress.

11. **Wait for the Cross Data Center Status to show as Active** before continuing.



## Task 5: Import a Virtual Machine Template

Oracle provides pre-built OVA templates that include a fully configured OS — no manual installation required.

1. Go to **Compute** → **Templates** → **Import**.

2. Keep the default selections for Data Center and Source. Select **olkvm01** for Host.

3. For File Path, enter:

    ```bash
    <copy>/tmp</copy>
    ```

4. Switch to the terminal within the VNC session. Make sure you are on the olvm instance.

5. Download the OVA template to olkvm01 (This file is large and may take several minutes to download.) 

    ```bash
    <copy>ssh olkvm01 "curl -L https://yum.oracle.com/templates/OracleLinux/OL9/u5/x86_64/OL9U5_x86_64-olvm-b253.ova -o /tmp/ol95.ova"</copy>
    ```

6. Switch back to the browser.

7. Click **Load**. The OVA template appears in the Virtual Machines on Source section.

8. Click the OVA template, then click the **Right Arrow** to move it to Virtual Machines to Import.

9. Click **Next**.

10. Review the template information, then click **OK**.

11. **Wait for the status to show as OK.**



## Task 6: Create a Test Virtual Machine (ol9-vm1)

This test VM verifies that your networking, storage, and template infrastructure is working correctly.

1. Go to **Compute** → **Virtual Machines** → **New**.

2. Template: `OL9U5x8664-olvm-b253`

3. Operating System: `Oracle Linux 9.x x64`

4. Name: `ol9-vm1`

5. nic1: `l2-vm-network`

6. Click **Show Advanced Options**.

7. Click **Initial Run** → **Authentication**.
    - User Name: `opc`
    - Password / Verify Password: *(your choice — for the lab, use `oracle`)*

8. Click **Networks**.
    - DNS Servers: `10.0.10.1`
    - Check **In-guest Network Interface Name** → Click **Add new**
    - Name: `eth0`
    - IPv4 Boot Protocol: `Static`
    - IPv4 Address: `10.0.10.105`
    - IPv4 Netmask: `255.255.255.0`
    - IPv4 Gateway: `10.0.10.1`

9. Click **OK**.

10. Wait for the status to change from **Importing** to **Down**.



## Task 7: Run the Test Virtual Machine

1. Select the virtual machine and click **Run**.

2. Click the **Console** button drop-down → **Console Options** → Select **noVNC** → **OK**.

3. Click **Console** to open a new browser window for the VM login.

4. Log in with the username and password you defined.

5. Verify the network settings:

    ```bash
    <copy>ip addr</copy>
    ```
   The output should show the `10.0.10.105` address on `eth0`.

6. Ping the network gateway:

    ```bash
    <copy>ping 10.0.10.1</copy>
    ```
    The ping should succeed, confirming network connectivity.



### ✅ Set Up Networking, Storage, and VM Checkpoint

At this point, you should have:
- ✓ `l2-vm-network` assigned to both hosts
- ✓ Fibre Channel storage domain active
- ✓ OL9 template imported
- ✓ Test VM (ol9-vm1) running with network connectivity
 


## Learn More

- Oracle Linux Virtualization Manager install lab (official): https://docs.oracle.com/en/learn/olvm-install/index.html 
- Oracle Luna Labs: https://luna.oracle.com/ 


 
## Acknowledgements

- **Author** - Shawn Kelley, John Priest  
- **Contributors** - Perside Foster
- **Last Updated By/Date** - Perside Foster , April 1, 2026
