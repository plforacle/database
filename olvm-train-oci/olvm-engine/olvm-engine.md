# Deploy OLVM Engine 

## Introduction

In this lab, you will connect to the OLVM Manager host using a secure SSH tunnel to VNC, install the required OLVM Engine packages, run `engine-setup`, and then validate access to the Engine web interface (Administration Portal).

Estimated Lab Time: 60–90 minutes

### Objectives

In this lab, you will:
- Open a VNC session to the OLVM Manager via SSH tunneling
- Install OLVM repositories and Engine packages
- Verify required repositories are enabled
- Run `engine-setup` and record the `admin@ovirt` credentials
- Log in to the Administration Portal and validate the deployment

### Prerequisites

This lab assumes you have:
- A deployed OLVM Manager instance and its IP address
- SSH connectivity to the OLVM Manager
- Access to the Luna desktop environment with TigerVNC available

### SSH Connection Reference

Throughout this lab, you'll connect to different systems. Use this reference:

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         Connection Paths                                │
│                                                                         │
│  ┌──────────────┐      ┌──────────────┐      ┌──────────────────────┐   │
│  │ Your PC      │ SSH  │    olvm      │ SSH  │   Virtual Machines   │   │
│  │ (VNC Viewer) │ ───> │   (Engine)   │ ───> │                      │   │
│  └──────────────┘      └──────────────┘      │  • ol9-mysql         │   │
│                              │               │    (10.0.10.100)     │   │
│                              │ SSH           │                      │   │
│                              ▼               │  • ol9-webapp        │   │
│                        ┌──────────────┐      │    (10.0.10.101)     │   │
│                        │ KVM Hosts    │      └──────────────────────┘   │
│                        │ • olkvm01    │                                 │
│                        │ • olkvm02    │                                 │
│                        └──────────────┘                                 │
└─────────────────────────────────────────────────────────────────────────┘
```

| From | To | Command |
|------|-----|---------|
| olvm | ol9-mysql | `ssh opc@10.0.10.100` |
| olvm | ol9-webapp | `ssh opc@10.0.10.101` |
| olvm | KVM hosts | `ssh olkvm01` or `ssh olkvm02` |

**Note:** KVM hosts use pre-configured SSH keys and hostnames. VMs require username, IP, and password for SSH login.

### Architecture Overview — What You Need to Know

Before continuing, here's a quick overview of the OLVM virtualization stack. Understanding these layers helps you troubleshoot and explain the platform:

**The Virtualization Stack (top to bottom):**

1. **oVirt Engine** — The brain. Java app on WildFly server + PostgreSQL database. Sends commands down to hosts.
2. **VDSM** — Virtual Desktop and Server Manager. A daemon running on EVERY KVM host. Acts as the agent between the engine and the hypervisor.
3. **libvirt** — The API layer. VDSM talks to libvirt, not directly to KVM.
4. **QEMU** — Quick Emulator. Emulates hardware (CPU, memory, disk, NIC) for each VM. Each VM runs as a QEMU process in **user space**.
5. **KVM** — The actual hypervisor. Runs in **kernel space**. Provides near-native performance.

> **Key concept:** If the engine goes offline, VMs keep running. The engine is management only — KVM handles execution independently.

### What You Will Build

```
┌─────────────────────────────────────────────────────────────────────────┐
│  Part 1: Infrastructure Setup                                           │
│                                                                         │
│  ┌─────────────────┐                                                    │
│  │   OLVM Engine   │                                                    │
│  │     (olvm)      │                                                    │
│  │                 │                                                    │
│  │ • Admin Portal  │                                                    │
│  │ • REST API      │                                                    │
│  │ • PostgreSQL    │                                                    │
│  └─────────────────┘                                                    │
│                                                                         │
│  olkvm01 and olkvm02 hosts provisioned but not yet configured           │
└─────────────────────────────────────────────────────────────────────────┘
```

### Steps

1. Launch the OLVM lab in Luna Labs
2. Deploy the lab environment using Ansible (provisions olvm, olkvm01, olkvm02)
3. Record IP addresses for all hosts
4. Open a VNC session to the OLVM Manager
5. Install the Oracle Linux Virtualization Manager Engine
6. Log in to the Administration Portal

## Task 1: Open a VNC Session to the Manager

1. Open a **new terminal** and connect to the olvm instance via SSH.

    The `-L` option creates an SSH tunnel to the remote VNC server.

    ```bash
    <copy>ssh -L 5914:localhost:5901 oracle@<ip_address_of_instance></copy>
    ```
    **What this does:** Creates an SSH tunnel that forwards local port 5914 to the remote VNC server on port 5901. VNC port 5901 is not directly accessible from the internet (firewalled), so SSH tunneling provides secure, encrypted access.

2. Switch to the Luna Desktop.

3. Open the **TigerVNC Viewer** by clicking the **Applications** menu → **Internet** → **TigerVNC Viewer**.

4. Log on by entering `localhost:5914` into the VNC Server text box and pressing **Connect**.

5. Enter the `oracle` user's password of **oracle** and press **OK**.

6. The Server's GUI desktop is displayed with a first-time login setup. Press **Next** three times, then **Skip**, followed by **Start Using Oracle Linux Server**. Close or minimize the Getting Started window.



## Task 2: Install the Engine

1. Open the VNC Activities Menu.

2. Open a terminal within the VNC session.

3. Enable copy and paste to the VNC session.
    ```bash
    <copy>vncconfig -nowin &</copy>
    ```

4. Install the Oracle Linux Virtualization Manager Release package.
    ```bash
    <copy>sudo dnf install -y oracle-ovirt-release-45-el8</copy>
    ```
    **What this does:** Installs the OLVM repository configuration package for release 4.5 on Oracle Linux 8. This automatically enables the required YUM/DNF repositories.

5. Clear the dnf cache.
    ```bash
    <copy>sudo dnf clean all</copy>
    ```

6. Install the Manager package.
    ```bash
    <copy>sudo dnf install -y ovirt-engine</copy>
    ```
    **What this does:** Downloads the core OLVM software and all dependencies (Java, WildFly, etc.) from Oracle Yum repositories. Does not start the manager — it only places the files on disk.

7. List the configured repositories and verify that the required repositories are enabled.
    ```bash
    <copy>sudo dnf repolist</copy>
    ```
    You must enable the following repositories:
    - ol8\_baseos\_latest
    - ol8\_appstream
    - ol8\_kvm_appstream
    - ovirt-4.5
    - ovirt-4.5-extra
    - ol8\_gluster\_appstream
    - ol8\_UEKR7

    If a required repository is not enabled:
    ```bash
    <copy>sudo dnf config-manager --enable <repository_name></copy>
    ```

8. Configure the Manager.
    ```bash
    <copy>sudo engine-setup --accept-defaults</copy>
    ```
    **What this does:** Runs the OLVM Engine configuration wizard with all default answers accepted. Behind the scenes, it configures the PostgreSQL database, installs Apache/Tomcat for the web portal, generates SSL certificates, configures the firewall, creates the admin@ovirt user, initializes the oVirt Engine, and creates the default data center and cluster.

    **Time:** Takes 5–10 minutes to complete all configuration steps.

    **Admin password:** The wizard will prompt for the admin@ovirt password even with `--accept-defaults`. This is the only interactive prompt. Password must be 8+ characters with uppercase, lowercase, number, and special character.

    > **CRITICAL:** When engine-setup completes, **WRITE DOWN the admin password!**



## Task 3: Log in to the Administration Portal

1. Get the FQDN for the manager host.
    ```bash
    <copy>hostname -f</copy>
    ```
    Example output: `olvm.examplevcn.oraclevcn.com`

2. Open **Firefox** within the VNC session.

3. Enter the following link to access the engine's Web UI:
    ```
    <copy>https://olvm.pub.olv.oraclevcn.com</copy>
    ```

4. **Security Warning:** Firefox will display "Warning: Potential Security Risk Ahead" because the engine uses a self-signed SSL certificate. Click **Advanced** → **Accept the Risk and Continue**.

5. Under **Downloads**, click **Engine CA Certificate**.

6. Import the certificate into the browser:
    - Open browser menu → **Settings**
    - Search for **cert** → Click **View Certificates…**
    - Click **Import…** → Select **All Files** from the dropdown
    - Click the **pki-resource** file → Click **Open**
    - Check **Trust this CA to identify websites** → Click **OK** twice

7. Close the Settings tab. From the engine's Web UI, click **Administration Portal**.

8. Enter `admin@ovirt` for the Username and the password you specified during engine-setup.

    The Administration Portal displays after a successful login.



### ✅Deploy OLVM Engine Checkpoint

At this point, you should have:

- ✓ OLVM Engine installed and configured
- ✓ Administration Portal accessible and logged in


## Learn More

- (Optional) Add links relevant to your workshop, such as the official install guide or product docs.

## Next Steps

- Return to the hands-on Engine deployment page: [olvm-engine.md](olvm-engine.md)

## Acknowledgements

- **Author** - Shawn Kelly 
- **Contributors** - Perside Foster
- **Last Updated By/Date** - Perside Foster , April 1, 2026
