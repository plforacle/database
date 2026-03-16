# Create a Virtual Machine 
## Introduction
## Create a Virtual Machine - ol9-mysql on Host (olkvm01)

1. Using the side navigation menu, go to Compute and then click Virtual Machines.

   The Virtual Machines pane opens.

1. Click the New button.

   The New Virtual Machine dialog box opens.

1. Select the OVA template from the Template drop-down list.
   ```
   OL9U5_x86_64-olvm-b253
   ```

1. Select the operating system from the Operating System drop-down list.
   ```
   Oracle Linux 9.x x64
   ```

1. In the Name field, enter a name for the virtual machine.
   ```
   ol9-mysql
   ```

1. Select the VM network interface from the nic1 drop-down list.
   ```
   l2-vm-network(l2-vm-network)
   ```

1. Click the Show Advanced Options button.

   The Advanced Options menu opens. This menu enables setting additional options for the virtual machine, such as host selection, a password, SSH key, or static network.
   
   **What is cloud-init:**
   
   Cloud-init is an industry-standard tool for VM initialization. It runs on first boot to:
   - Set hostname
   - Create user accounts
   - Set passwords or SSH keys
   - Configure networking (static IP, DNS, routes)
   - Run custom scripts
   - Install packages
   
   **First boot vs subsequent boots:**
   - Cloud-init only runs on FIRST boot (or when you specifically trigger "Run Once" with Initial Run)
   - After first boot, cloud-init is disabled
   - Changes to user/password must be done manually after first boot
   
   **Exam relevance (1Z0-1170):** Cloud-init configuration is tested in "VM Lifecycle Management". You must understand how to configure VMs with static networking and user accounts during creation.

1. Click Host

1. Click Start Running On

1. Click  Specific Host(s) and select olkvm01

1. Click Initial Run.

1. Click Authentication.

1. In the User Name field, enter a user name to log into the virtual machine.
    ```
    opc
    ```

1. In the Password and Verify Password fields, enter a password for the user. For Lab use oracle

    ```
    oracle
    ```

    For production systems, the recommendation is to use SSH Authorized Key.

1. Click Networks.

1. Enter the DNS server IP address in the DNS Servers field.
    ```
    8.8.8.8
    ```

1. Click the checkbox next to In-guest Network Interface Name.

1. Click the Add new button.

1. In the In-guest Network Interface ... click Add new ... In  Name field enter the name of the network interface.
    ```
    eth0
    ```

1. From the IPv4 Boot Protocol drop-down list, select `Static`.

    Due to linking the virtual machine network to the OCI VLAN network, there is no default DHCP server to assign IP addresses.

1. In the IPv4 Address field, enter an IP address in the same range as the OCI VLAN network.
    ```
   10.0.10.100
    ```

1. In the IPv4 Netmask field, enter the network netmask.
    ```
    255.255.255.0
    ```

1. In the IPv4 Gateway field, enter the network gateway.
    ```
    10.0.10.1
    ```

1. Click OK.

1. Wait for the status to change from Importing to Down.

---

## Run the Virtual Machine - ol9-mysql 

1. Switch to the terminal within the VNC session.

1. Install the Virtual Machine Viewer package.

   This package allows viewing a virtual machine using the Oracle Linux Virtualization Manager console.
   ```bash
   sudo dnf install -y virt-viewer
   ```

1. Switch to the browser within the VNC session.

1. Select the virtual machine and click the Run button.

1. Click the Console button.

   This action downloads a `console.vv` file that you can click to open the virtual machine remote viewer.

1. Click the `console.vv` file from the browser's download list.

   The virtual machine's remote viewer application opens.

1. From the virtual machine remote viewer, log into the virtual machine using the user name and password you defined.

1. Check the virtual machine network settings.
   ```bash
   ip a
   ```

   The output shows the `eth0` network interface using an IP address of `10.0.10.100`.

1. Verify internet connectivity from the MySQL VM:
   ```bash
   ping -c 3 8.8.8.8
   ```

   The virtual machine successfully pings the gateway.

1. Ping an external address (for example, `google.com`).
   ```bash
      ping -c 3 google.com
   ```

   Both pings should succeed, confirming the NAT Gateway is working.
   
1. Exit ol9-mysql to return to the olvm engine
   ```bash
      exit
   ```

---

## Create Virtual Machine 2 - ol9-webapp on Host (olkvm02)

1. In the Oracle Linux Virtualization Manager, go to Compute and then click Virtual Machines.

1. Click the New button.

1. Select the Oracle Linux 9 template from the Template drop-down list.
   ```
   OL9U5_x86_64-olvm-b253
   ```

1. Select the operating system from the Operating System drop-down list.
   ```
   Oracle Linux 9.x x64
   ```

1. In the Name field, enter a name for the virtual machine.
   ```
   ol9-webapp
   ```

1. Select the VM network interface from the nic1 drop-down list.
   ```
   l2-vm-network(l2-vm-network)
   ```

1. Click the Show Advanced Options button.

1. Click Host

1. Click Start Running On

1. Click  Specific Host(s) 

1. Unselect  olkvm01

1. Select olkvm02

1. Click Initial Run.

1. Click Authentication.

1. In the User Name field, enter a user name.
    ```
    opc
    ```

1. In the Password and Verify Password fields, enter a password for the user. For Lab use oracle

    ```
    oracle
    ```

1. Click Networks.

1. Enter the DNS server IP address in the DNS Servers field.
    ```
    8.8.8.8
    ```

1. Click the checkbox next to In-guest Network Interface Name.

1. Click the Add new button.

1. In the In-guest Network Interface section, click Add new. In the Name field, enter:
    ```
    eth0
    ```

1. From the IPv4 Boot Protocol drop-down list, select `Static`.

1. In the IPv4 Address field, enter an IP address for the web server.
    ```
    10.0.10.101
    ```

1. In the IPv4 Netmask field, enter the network netmask.
    ```
    255.255.255.0
    ```

1. In the IPv4 Gateway field, enter the network gateway.
    ```
    10.0.10.1
    ```

1. Click OK.

1. Wait for the status to change to Down.

---

## Run the  Virtual Machine - ol9-webapp

1. Select the web application virtual machine and click the Run button.

1. Wait for the VM to fully boot (about 30-60 seconds).

1. Switch to the terminal within the VNC session.

1. From olvm connect to ol9-webapp
   ```bash
   ssh opc@10.0.10.101
   ```

   Use the password you defined when creating the VM.

1. Verify the network configuration.
   ```bash
   ip a
   ```

   The output shows the `eth0` network interface using the IP address `10.0.10.101`.

1. Test connectivity from the webapp to the MySQL server
   ```bash
   ping -c 3 10.0.10.100
   ```

1. Verify DNS resolution:
   ```bash
      ping -c 3 google.com
   ```
   The ping should be successful, confirming network connectivity between the VMs.

1. Exit ol9-webapp:
   ```bash
      exit
   ```
---
## OLVM Hierarchy Review

![](images/olvm-hierarchy.png)


#### OLVM HIERARCHY - This maps exactly to what you have built in the lab.

#### LEFT SIDE: The lab architecture you have created.
- Default Data Center and Default Cluster are created by engine-setup (Part 1)
- You added olkvm01 and olkvm02 to the cluster (Part 2)
- You created the FC storage domain and VMs (Part 3)
- VMs are distributed: ol9-mysql on olkvm01, ol9-webapp on olkvm02

#### RIGHT SIDE: Somethings to remember.
- Data Center = highest level. VMs CANNOT migrate between Data Centers.
- Cluster = hosts with same CPU type. Enables live migration and HA.
- You need at least 2 hosts for HA - that's why we have olkvm01 + olkvm02.
- Storage domain MUST be attached before Data Center initializes.
- Cannot mix local and shared storage in the same Data Center.
- SPM (Storage Pool Manager) role: only ONE host at a time per Data Center.

---

## Lab Part 3: Networking, Storage, and VM Setup - Exam Practice

### LOGICAL NETWORKS

1. What are logical networks in OLVM?
- A. Physical network cables
- **B. Representations of network resources that provide connectivity for KVM virtual machines ✓**
- C. Virtual switches only
- D. Network security policies

2. What is the name of the default logical network automatically created during OLVM setup?
- A. default_network
- **B. ovirtmgmt ✓**
- C. management_net
- D. cluster_network

3. What happens if a KVM host loses connectivity to a network marked as "required"?
- A. Nothing, it continues normally
- **B. The host will be considered non-operational ✓**
- C. Only VMs on that network stop
- D. The host reboots automatically

4. For VM networks, what is created on the host for each logical network?
- A. A VLAN tag
- **B. A bridge (virtual switch) ✓**
- C. A firewall rule
- D. A routing table

5. What does a network bridge act as on a KVM host?
- A. A router
- **B. A virtual switch connecting VMs to the physical network ✓**
- C. A firewall
- D. A load balancer

### STORAGE DOMAINS

6. Can a Data Center be initialized without a storage domain attached?
- A. Yes, storage is optional
- **B. No, at least one storage domain must be attached before initialization ✓**
- C. Only in test environments
- D. Only for Self-Hosted Engine

7. For VMs to be migrated between hosts, what storage requirement must be met?
- A. Each host needs local storage
- **B. HOSTS must share the same storage domain ✓**
- C. Storage must be SSD-based
- D. VMs must use iSCSI only

8. What does LUN stand for in storage terminology?
- A. Local Unit Number
- **B. Logical Unit Number ✓**
- C. Linux Unified Node
- D. Link Universal Network

### VIRTUAL MACHINES & TEMPLATES

9. Where in the Administration Portal do you create a new VM?
- A. Storage -> VMs
- **B. Compute -> Virtual Machines ✓**
- C. Network -> VMs
- D. Administration -> VMs

10. What are the two disk allocation policies? **(Choose 2)**
- **A. Pre-allocated ✓**
- B. Compressed
- **C. Thin provisioning (sparse) ✓**
- D. Encrypted

11. What does a VM use to connect to a logical network?
- A. Physical NIC directly
- **B. VNIC (Virtual Network Interface Controller) ✓**
- C. USB adapter
- D. Serial port

