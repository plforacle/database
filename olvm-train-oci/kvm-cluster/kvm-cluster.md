# Configure KVM Cluster

## Introduction

In this part, you will add KVM hosts to your cluster and verify their integration with the OLVM engine. These hosts will run your virtual machines.

Estimated Lab Time: 30–45 minutes

### Objectives

In this lab, you will:
- Configure required repositories on both KVM hosts
- Add both hosts (`olkvm01`, `olkvm02`) to the **Default** cluster in the Administration Portal
- Verify both hosts reach **Up** status and know where to check logs if they do not

### Prerequisites

This lab assumes you have:
- Completed the OLVM Engine installation and can log in to the **Administration Portal**
- SSH connectivity from the Engine to both KVM hosts
- Hostnames resolvable from the Engine (for example, `ssh olkvm01`, `ssh olkvm02`)



### What You Will Build

```
┌─────────────────────────────────────────────────────────────────────────┐
│                     Part 2: Host Configuration                          │
│                                                                         │
│  ┌─────────────────┐      ┌─────────────────┐   ┌─────────────────┐     │
│  │   OLVM Engine   │      │    olkvm01      │   │    olkvm02      │     │
│  │     (olvm)      │      │   (KVM Host)    │   │   (KVM Host)    │     │
│  │                 │      │                 │   │                 │     │
│  │ • Admin Portal  │ ───> │ • VDSM Agent    │   │ • VDSM Agent    │     │
│  │ • REST API      │      │ • libvirt/KVM   │   │ • libvirt/KVM   │     │
│  │ • PostgreSQL    │      │ • Status: Up    │   │ • Status: Up    │     │
│  └─────────────────┘      └─────────────────┘   └─────────────────┘     │
│                                                                         │
│  Hosts added to Default cluster; Ready for VMs                          │
└─────────────────────────────────────────────────────────────────────────┘
```

### Steps

1. Configure the first KVM host (olkvm01)
2. Add the first KVM host to the cluster
3. Configure the second KVM host (olkvm02)
4. Add the second KVM host to the cluster



## Task 1: Configure the First KVM Host (olkvm01)

1. Open the VNC Activities Menu.

2. Switch to the terminal within the VNC session.

3. Connect via SSH to the olkvm01 instance.
   ```bash
   ssh olkvm01
   ```
   **Note:** All SSH commands using hostnames (e.g., `ssh olkvm01`) are executed from the OLVM engine, which has private DNS resolution for the cluster hosts.

4. Install the Oracle Linux Virtualization Manager Release package.
   ```bash
   sudo dnf install -y oracle-ovirt-release-45-el8
   ```

5. Clear the dnf cache.
   ```bash
   sudo dnf clean all
   ```

6. Verify repositories are enabled.
   ```bash
   sudo dnf repolist
   ```

7. Exit the session.
   ```bash
   exit
   ```
   You should now be on the Manager host.



## Task 2: Add KVM Host (olkvm01)

1. Switch to the Firefox browser within the VNC session.

2. Log in to the Administration Portal.

3. Using the side navigation menu, go to **Compute** → **Hosts**.

4. On the Hosts pane, click **New**.

5. Select the **Default** data center from the Host Cluster drop-down list.

   **OLVM hierarchy explained:**
   ```
   Data Center (physical location)
      ↓
   Cluster (group of hosts with same CPU type)
      ↓
   Hosts (physical servers running KVM)
      ↓
   Virtual Machines
   ```

   **Key points:**
   - A **Data Center** is a logical container for clusters. It defines shared storage and networking. VMs can't migrate between data centers.
   - A **Cluster** is a group of hosts that share the same CPU type, storage domains, and network configuration. It enables live migration and HA. Requires at least 2 hosts for HA features.
   - `engine-setup` creates a "Default" data center with a "Default" cluster automatically.

6. Enter a name for the host in the Name field.
   ```
   olkvm01
   ```

7. In the Hostname field, enter the fully-qualified domain name of the host.
   ```
   vdsm01.priv.olv.oraclevcn.com
   ```
   **Why this hostname:** This is the secondary VNIC (private subnet) used for OLVM management traffic, VM migration, and storage traffic. Separating management traffic from the public interface is a best practice for security and performance.

8. Under Authentication, select the **SSH Public Key** authentication method.

9. Switch to the terminal within the VNC session.

10. Copy the SSH public key to the KVM host.
    ```bash
    sudo ssh-keygen -y -f /etc/pki/ovirt-engine/keys/engine_id_rsa | ssh olkvm01 -T "sudo tee -a /root/.ssh/authorized_keys"
    ```
    **What this does:** Enables passwordless SSH access from the engine to the KVM host. The engine needs this to install VDSM, deploy configurations, and manage the host.

11. Switch back to the Firefox browser and Administration Portal.

12. Click **OK**. The Power Management Configuration screen displays.

13. Click **OK** (OCI instances do not allow configuring power management).

    The panel updates and adds the new host. The status will show as **Installing** while the engine installs VDSM and other required packages on the host.

    **What happens during host installation:** The engine automatically connects via SSH, installs VDSM and dependencies (vdsm, vdsm-client, libvirt, qemu-kvm), deploys certificates, configures the management network (ovirtmgmt bridge), starts services, configures the firewall, and verifies connectivity before marking the host as "Up".

    **Host status meanings:**
    - **Installing** — VDSM and packages being installed
    - **Initializing** — Services starting, network configuring
    - **Up** — Host is ready to run VMs
    - **Non Operational** — Host has issues (check logs)
    - **Maintenance** — Host is intentionally offline for updates

    **Troubleshooting tip:** If installation fails, check `/var/log/ovirt-engine/engine.log` on the engine and `/var/log/vdsm/vdsm.log` on the host.

    **Important:** After a KVM host is added to a cluster, avoid any spontaneous changes to the network configuration in `/etc/sysconfig/network-scripts/`, through NetworkManager (e.g., nmcli), or in OCI.

14. **Wait for the host status to show as Up** before continuing.



## Task 3: Configure the Second KVM Host (olkvm02)

Repeat the same process for the second KVM host.

1. Switch to the terminal within the VNC session.

2. Connect via SSH to olkvm02.
   ```bash
   ssh olkvm02
   ```

3. Install the OLVM Release package.
   ```bash
   sudo dnf install -y oracle-ovirt-release-45-el8
   ```

4. Clear the dnf cache.
   ```bash
   sudo dnf clean all
   ```

5. Verify repositories.
   ```bash
   sudo dnf repolist
   ```

6. Exit the session.
   ```bash
   exit
   ```



## Task 4: Add KVM Host (olkvm02)

1. From the Administration Portal, go to **Compute** → **Hosts** → **New**.

2. Select the **Default** data center from the Host Cluster drop-down list.

3. Enter the host name:
   ```
   olkvm02
   ```

4. Enter the hostname:
   ```
   vdsm02.priv.olv.oraclevcn.com
   ```

5. Under Authentication, select **SSH Public Key**.

6. Switch to the terminal and copy the SSH public key:
   ```bash
   sudo ssh-keygen -y -f /etc/pki/ovirt-engine/keys/engine_id_rsa | ssh olkvm02 -T "sudo tee -a /root/.ssh/authorized_keys"
   ```

7. Switch back to the browser. Click **OK** → **OK** (power management).

8. **Wait for the host status to show as Up** before continuing.



### ✅ Configure KVM Cluster Checkpoint

At this point, you should have:
- ✓ Both olkvm01 and olkvm02 showing status **Up** in the Hosts pane
- ✓ Both hosts in the Default cluster
- ✓ VDSM running on both hosts



## Learn More

(Optional) Add links to docs.oracle.com pages or internal references relevant to your workshop.

## Acknowledgements

- **Author** - Perside Foster
- **Contributors** - Shawn Kelly
- **Last Updated By/Date** - Perside Foster , April 1, 2026
