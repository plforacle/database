# OLVM Exam Practice Quiz Bank
### 60 Questions for Training 2: OLVM Advanced + Partner Bootcamp Prep
### Mapped to Exam 1Z0-1170 — Oracle Linux Virtualization Manager Associate

---

## How to Use This Quiz Bank

This quiz bank is designed to be reviewed during Training 2, with each section mapped to a lab part. Review each section after completing the corresponding hands-on lab section.

| Quiz Section | Topic | Review After | Time |
|-------------|-------|-------------|------|
| Section 1 | Architecture & Big Picture | Part 1 — During Ansible wait | ~15 min |
| Section 2 | Engine Installation & Configuration | Part 1 — During engine-setup wait + after Portal access | ~15 min |
| Section 3 | KVM Host & Cluster Management | Part 2 — After both hosts "Up" | ~15 min |
| Section 4 | Networking, Storage & VM Administration | Part 3 — After VMs running | ~15 min |
| Section 5 | VM Lifecycle, Migration & Operations | Parts 4–5 — After migration complete | ~15 min |

---

## Section 1: Architecture & Big Picture
*Review during Ansible playbook wait time (~8 min) and after deployment completes*

**1. What is a Standalone Engine in Oracle Linux Virtualization Manager?**
- A. A management engine installed on a dedicated server separate from the virtualization hosts
- B. An engine that runs as a virtual machine within the same cluster it manages
- C. A host that only provides storage and networking services
- D. A management console that only functions when connected to the public cloud

**2. What is a Self-Hosted Engine in Oracle Linux Virtualization Manager?**
- A. An engine that requires a dedicated physical server
- B. A management engine that runs as a VM directly on the virtualization hosts
- C. An engine that runs in Oracle Cloud Infrastructure
- D. A backup engine for disaster recovery

**3. What is the primary benefit of using a Self-Hosted Engine deployment?**
- A. It requires more hardware resources
- B. It eliminates the need for a separate management server
- C. It increases the complexity of deployment
- D. It requires Oracle Linux Enterprise Kernel

**4. What component monitors and manages the Self-Hosted Engine VM on each host?**
- A. oVirt engine service
- B. VDSM daemon
- C. HA agent (ovirt-ha-agent)
- D. PostgreSQL database

**5. What is the core component that serves as the backbone of Oracle Linux Virtualization Manager?**
- A. KVM hypervisor
- B. VDSM agent
- C. oVirt engine
- D. PostgreSQL database

**6. Which three tasks does the oVirt engine perform? (Choose 3)**
- A. Discovering KVM hosts
- B. Running virtual machine processes
- C. Configuring storage for virtualized data centers
- D. Configuring networking for virtualized data centers
- E. Providing hardware emulation for VMs

**7. What is the role of VDSM in Oracle Linux Virtualization Manager?**
- A. It provides hardware emulation for virtual machines
- B. It acts as a host agent running continuously as a daemon on the KVM host
- C. It manages the PostgreSQL database
- D. It provides the web-based user interface

**8. What does QEMU (Quick Emulator) provide in the virtualization stack?**
- A. Network connectivity between VMs
- B. Hardware component emulation such as CPU, memory, network, and disk devices
- C. User authentication services
- D. Database management

**9. Where does KVM operate within the system?**
- A. In user space as an application
- B. In the kernel space
- C. On a separate management server
- D. In the cloud

**10. What is the relationship between VDSM and libvirt?**
- A. VDSM replaces libvirt completely
- B. VDSM relies on libvirt to manage the lifecycle of VMs and collect statistics
- C. libvirt runs inside VDSM
- D. They operate independently without interaction

**11. What happens to running virtual machines if the oVirt engine goes offline?**
- A. All VMs are automatically suspended
- B. VMs continue to run on the KVM hosts
- C. VMs are migrated to a standby host
- D. VMs shut down gracefully

**12. Which application server does the oVirt engine run on?**
- A. Apache HTTP Server
- B. WildFly (formerly JBoss)
- C. Oracle WebLogic
- D. Nginx

---

### Section 1 — Answer Key

| # | Answer | Explanation |
|---|--------|-------------|
| 1 | A | Standalone Engine runs on a dedicated server, separate from the KVM hosts |
| 2 | B | Self-Hosted Engine runs as a VM directly on the virtualization hosts it manages |
| 3 | B | Eliminates the need for a separate management server, reducing hardware and overhead |
| 4 | C | The HA agent (ovirt-ha-agent) on each host monitors and manages the Engine VM |
| 5 | C | The oVirt engine is the centralized management component — the "brain" of OLVM |
| 6 | A, C, D | The engine discovers hosts, configures storage, and configures networking. It does NOT run VM processes (KVM/QEMU does) or provide hardware emulation (QEMU does) |
| 7 | B | VDSM = Virtual Desktop and Server Manager; runs as a daemon on each KVM host |
| 8 | B | QEMU emulates hardware components (CPU, memory, network, disk) for each VM |
| 9 | B | KVM operates in kernel space as a loadable kernel module |
| 10 | B | VDSM uses the libvirt API to manage VM lifecycle and collect statistics |
| 11 | B | VMs continue running — KVM handles execution independently of the engine |
| 12 | B | The oVirt engine is a Java application running on WildFly (formerly JBoss) |

---

## Section 2: Engine Installation & Configuration
*Review during engine-setup wait time (~10 min) and after Administration Portal access*

**1. What is the minimum Oracle Linux version required for installing the OLVM Manager (Engine)?**
- A. Oracle Linux 7.5
- B. Oracle Linux 8.5 and higher
- C. Oracle Linux 9.0
- D. Oracle Linux 6.8

**2. Which two CPU technologies must be supported for the Engine host processor? (Choose 2)**
- A. Intel VT-x
- B. Intel SGX
- C. AMD-V
- D. ARM TrustZone

**3. What are the RECOMMENDED hardware requirements for an OLVM Engine SMALL deployment? (Choose 3)**
- A. 2 cores
- B. 4 cores
- C. 16 GB or greater RAM
- D. 32 GB RAM
- E. 50 GB or greater local writable disk

**4. What command is used to install the OLVM Engine package?**
- A. yum install olvm-engine
- B. dnf install ovirt-engine
- C. dnf install olvm-manager
- D. yum install oracle-engine

**5. What command is used to configure the OLVM Engine after installation?**
- A. ovirt-configure
- B. engine-setup
- C. olvm-setup --init
- D. dnf configure ovirt-engine

**6. Which five components does engine-setup configure automatically? (Choose 5)**
- A. PostgreSQL database
- B. Apache/Tomcat web server
- C. SSL certificates
- D. Firewall rules
- E. admin@ovirt user
- F. KVM hypervisor on the local server
- G. DNS server

**7. What is the MINIMUM RAM for a Standalone Engine in a SMALL deployment?**
- A. 2 GB
- B. 4 GB
- C. 16 GB
- D. 32 GB

**8. What is the MINIMUM RAM for a Self-Hosted Engine in a SMALL deployment?**
- A. 4 GB
- B. 8 GB
- C. 16 GB
- D. 32 GB

**9. Which open-source project is Oracle Linux Virtualization Manager based on?**
- A. Proxmox
- B. oVirt
- C. OpenStack
- D. VMware vSphere

**10. How many PostgreSQL databases does OLVM use?**
- A. One — ovirt_engine
- B. Two — ovirt_engine and ovirt_engine_history
- C. Three — one for engine, hosts, and VMs
- D. None — it uses Oracle Database

**11. Which three web-based management portals does OLVM provide? (Choose 3)**
- A. Administration Portal
- B. Command Line Portal
- C. VM Portal
- D. Monitoring Portal (Grafana)
- E. Developer Portal

**12. Where are the OLVM Engine log files located?**
- A. /var/log/olvm/
- B. /var/log/ovirt-engine/
- C. /opt/ovirt/logs/
- D. /var/log/messages

---

### Section 2 — Answer Key

| # | Answer | Explanation |
|---|--------|-------------|
| 1 | B | Oracle Linux 8.5 and higher is required for OLVM 4.5 |
| 2 | A, C | Intel VT-x and AMD-V are the hardware virtualization extensions required |
| 3 | B, C, E | Small deployment recommends 4 cores, 16 GB RAM, 50 GB disk |
| 4 | B | `dnf install ovirt-engine` installs the engine package |
| 5 | B | `engine-setup` runs the configuration wizard |
| 6 | A, B, C, D, E | engine-setup configures PostgreSQL, web server, SSL certs, firewall, and admin user. It does NOT install KVM or DNS |
| 7 | B | Minimum 4 GB RAM for Standalone Engine (Small deployment) |
| 8 | C | Minimum 16 GB RAM for Self-Hosted Engine (Small deployment) |
| 9 | B | OLVM is Oracle's supported distribution of the open-source oVirt project |
| 10 | B | Two databases: ovirt_engine (main engine) and ovirt_engine_history (Data Warehouse/DWH) |
| 11 | A, C, D | Administration Portal, VM Portal, and Monitoring Portal (Grafana) |
| 12 | B | Engine logs are in /var/log/ovirt-engine/ |

---

## Section 3: KVM Host & Cluster Management
*Review after both hosts show status "Up" in the Administration Portal*

**1. What is the minimum Oracle Linux version required for a KVM host?**
- A. Oracle Linux 7.5
- B. Oracle Linux 8.5 or later
- C. Oracle Linux 9.0
- D. Oracle Linux 8.0

**2. What is the MINIMUM CPU requirement for a KVM host?**
- A. Single-core 32-bit CPU
- B. 64-bit dual-core CPU
- C. 64-bit quad-core CPU
- D. 64-bit eight-core CPU

**3. What is the MINIMUM network interface requirement for a KVM host?**
- A. One NIC with 100 Mbps bandwidth
- B. One NIC with 1 Gbps bandwidth
- C. Two NICs with 1 Gbps bandwidth
- D. Four NICs with 1 Gbps bandwidth

**4. Where in the Administration Portal do you add a new KVM host?**
- A. Storage → Hosts
- B. Compute → Hosts
- C. Network → Hosts
- D. Configuration → Hosts

**5. Which two authentication methods can be used when adding a KVM host? (Choose 2)**
- A. Password authentication
- B. Kerberos
- C. SSH key authentication
- D. Certificate authentication

**6. For which user account must authentication credentials be provided when adding a host?**
- A. admin user
- B. root user
- C. ovirt user
- D. vdsm user

**7. What is the role of the VDSM service on a KVM host?**
- A. It manages the PostgreSQL database
- B. It acts as a host agent running continuously as a daemon on the KVM host
- C. It provides the web-based administration interface
- D. It handles SSL certificate generation

**8. What happens to a virtual machine if the oVirt engine goes offline?**
- A. The VM automatically suspends
- B. The VM continues to run on the KVM host
- C. The VM is migrated to another host
- D. The VM shuts down gracefully

**9. True or False: A single KVM host can belong to multiple Data Centers simultaneously.**
- A. True
- B. False

**10. What must a KVM host's state be before it can be moved to a different cluster within the same Data Center?**
- A. Up
- B. Maintenance
- C. Non-Operational
- D. Down

**11. Which service must be active on a KVM host for the OLVM engine to manage firewall rules?**
- A. iptables
- B. nftables
- C. firewalld
- D. UFW

**12. What is the correct order of the OLVM resource hierarchy, from highest to lowest?**
- A. Cluster → Data Center → Host → VM
- B. Data Center → Cluster → Host → VM
- C. Host → Cluster → Data Center → VM
- D. Data Center → Host → Cluster → VM

---

### Section 3 — Answer Key

| # | Answer | Explanation |
|---|--------|-------------|
| 1 | B | Oracle Linux 8.5 or later is required for KVM hosts |
| 2 | B | Minimum is a 64-bit dual-core CPU |
| 3 | B | Minimum is one NIC with 1 Gbps bandwidth |
| 4 | B | Hosts are managed under Compute → Hosts |
| 5 | A, C | Password and SSH key authentication are supported |
| 6 | B | Root credentials are required when adding a host |
| 7 | B | VDSM acts as a host agent daemon, mediating between the engine and KVM/libvirt |
| 8 | B | VMs continue running — KVM handles execution independently |
| 9 | B (False) | A host can only belong to one Data Center and one Cluster at a time |
| 10 | B | The host must be in Maintenance mode before moving to a different cluster |
| 11 | C | firewalld must be active; OLVM manages firewall rules through it |
| 12 | B | Data Center → Cluster → Host → VM |

---

## Section 4: Networking, Storage & VM Administration
*Review after VMs are running and network/storage are verified*

**1. What are logical networks in OLVM?**
- A. Physical network cables
- B. Representations of network resources that provide connectivity for KVM virtual machines
- C. Virtual switches only
- D. Network security policies

**2. What is the name of the default logical network automatically created during OLVM setup?**
- A. default_network
- B. ovirtmgmt
- C. vm_network
- D. management_bridge

**3. Which three are valid Storage Domain types in OLVM? (Choose 3)**
- A. Data
- B. Boot
- C. ISO
- D. Export
- E. Template

**4. Can a storage domain be shared between multiple Data Centers?**
- A. Yes, freely shared
- B. No, storage domains cannot be shared between different OLVM Data Centers
- C. Only ISO domains can be shared
- D. Only with special configuration

**5. What is the Storage Pool Manager (SPM)?**
- A. A physical storage device
- B. A management role assigned to one host in the Data Center
- C. A type of storage domain
- D. A backup application

**6. How many hosts can have the SPM role in a single Data Center at one time?**
- A. All hosts share the SPM role equally
- B. Only one host at a time
- C. Two hosts for redundancy
- D. Up to five hosts

**7. What storage requirement must be met for VMs to be migrated between hosts?**
- A. Each host needs local storage
- B. VMs must share the same storage domain accessible by both hosts
- C. Storage must be SSD-based
- D. VMs must use iSCSI only

**8. Which storage type does NOT support VM live migration or high availability?**
- A. NFS
- B. iSCSI
- C. Fibre Channel
- D. Local Storage

**9. What is an OVA file?**
- A. An Oracle Virtualization Agent
- B. An Open Virtualization Archive — a portable VM package containing disk images and configuration
- C. An Operating Virtual Appliance log file
- D. An OLVM Version Archive for backups

**10. What is the difference between thin provisioning and pre-allocated disk provisioning?**
- A. Thin provisioning is faster but uses more space
- B. Thin provisioning allocates disk space only as data is written; pre-allocated reserves all space upfront
- C. Pre-allocated provisioning is only available for NFS storage
- D. There is no difference — they are the same

**11. What does cloud-init do when a VM is created from a template?**
- A. Installs the hypervisor on the host
- B. Configures the VM on first boot — sets hostname, user accounts, networking, and runs custom scripts
- C. Creates a backup of the template
- D. Manages the storage domain

**12. Can a Data Center be initialized without a storage domain attached?**
- A. Yes, storage is optional
- B. No, at least one storage domain must be attached before initialization
- C. Only in test environments
- D. Only for Self-Hosted Engine deployments

---

### Section 4 — Answer Key

| # | Answer | Explanation |
|---|--------|-------------|
| 1 | B | Logical networks are virtual network representations providing VM connectivity |
| 2 | B | ovirtmgmt is the default management network created during engine-setup |
| 3 | A, C, D | Data (VM disks/templates/snapshots), ISO (installation media), Export (import/export VMs) |
| 4 | B | Storage domains are isolated per Data Center — cannot be shared |
| 5 | B | SPM is a management role assigned to one host per Data Center |
| 6 | B | Only one host holds the SPM role at a time; if it fails, another host takes over automatically |
| 7 | B | Source and destination hosts must access the same storage domain (shared storage) |
| 8 | D | Local storage does not support migration or HA — it creates a single-host cluster |
| 9 | B | OVA = Open Virtualization Archive, containing disk images, configuration, and metadata |
| 10 | B | Thin provisioning allocates space on demand; pre-allocated reserves all space immediately |
| 11 | B | Cloud-init runs on first boot to configure hostname, users, networking, and custom scripts |
| 12 | B | At least one storage domain must be attached before the Data Center can initialize |

---

## Section 5: VM Lifecycle, Migration & Operations
*Review after live migration is completed and verified*

**1. What are the prerequisites for live migrating a VM between hosts? (Choose 3)**
- A. Both hosts must be in the same cluster
- B. Both hosts must have access to the shared storage domain
- C. The VM must be powered off
- D. The logical network used by the VM must be available on both hosts
- E. The hosts must have different CPU types

**2. What is the correct order of phases during a live migration?**
- A. Stop-and-copy → Pre-copy → Activation
- B. Pre-copy → Iterative dirty page copy → Stop-and-copy → Activation on destination
- C. Activation → Pre-copy → Stop-and-copy
- D. Snapshot → Copy → Restore

**3. During live migration, does the VM experience any downtime?**
- A. Yes, the VM is offline for several minutes
- B. No downtime — the VM continues running throughout with only a brief pause during final sync
- C. The VM must be rebooted after migration
- D. Downtime depends on the storage type used

**4. VMs can be migrated between which levels of the OLVM hierarchy?**
- A. Between Data Centers
- B. Between Clusters within the same Data Center
- C. Between hosts within the same Cluster
- D. Between any hosts regardless of hierarchy

**5. What happens to a VM's IP address after live migration to a different host?**
- A. The IP address changes to match the new host
- B. The IP address stays the same — the VM retains its network identity
- C. The VM loses network connectivity
- D. A new IP must be manually assigned

**6. Which migration type requires the VM to be stopped before moving?**
- A. Live Migration
- B. Offline Migration
- C. Automatic Migration
- D. Hot Migration

**7. What should you do before performing maintenance on a KVM host that is running VMs?**
- A. Shut down all VMs manually
- B. Put the host in Maintenance mode — OLVM will automatically migrate VMs to other hosts
- C. Disconnect the storage domain first
- D. Remove the host from the cluster

**8. If the SPM host fails during a migration, what happens?**
- A. All VMs in the Data Center stop running
- B. OLVM automatically selects a new SPM from the remaining hosts
- C. The storage domain becomes read-only
- D. You must manually reinstall the storage domain

**9. Which three are valid VM "Run Options" in OLVM? (Choose 3)**
- A. Run Once
- B. Run and Pause
- C. Run as Stateless
- D. Run in Cloud-Init mode
- E. Run on High Priority

**10. What disk format allows for thin provisioning in OLVM?**
- A. Raw
- B. QCOW2
- C. VMDK
- D. ISO

**11. Which component provides the Data Warehouse (DWH) historical data used by the Monitoring Portal?**
- A. VDSM
- B. The ovirt_engine_history database
- C. The SPM host
- D. engine-config

**12. Which three are valid migration policies in OLVM Cluster settings? (Choose 3)**
- A. Minimal Downtime
- B. Suspend and Resume
- C. Post-copy
- D. Pre-copy
- E. Legacy

---

### Section 5 — Answer Key

| # | Answer | Explanation |
|---|--------|-------------|
| 1 | A, B, D | Same cluster, shared storage, and matching logical networks are required. The VM must be running (not off), and hosts must have compatible (not different) CPU types |
| 2 | B | Pre-copy memory → iteratively copy dirty pages → brief stop-and-copy → activate on destination |
| 3 | B | Live migration is designed for zero downtime — only a brief pause during the final sync |
| 4 | C | VMs can only migrate between hosts within the same Cluster (same CPU type, storage, network) |
| 5 | B | The VM retains its IP address, MAC address, and network identity after migration |
| 6 | B | Offline migration requires the VM to be stopped, then moved to the destination host |
| 7 | B | Putting a host in Maintenance mode triggers automatic migration of all VMs to other hosts in the cluster |
| 8 | B | OLVM automatically reassigns the SPM role to another available host |
| 9 | A, C, D | Run Once (one-time boot settings), Stateless (reverts to snapshot on shutdown), Cloud-Init mode (first-boot configuration) |
| 10 | B | QCOW2 format supports thin provisioning; Raw format is pre-allocated |
| 11 | B | The ovirt_engine_history database stores historical data used by Grafana dashboards |
| 12 | A, C, E | Minimal Downtime, Post-copy, and Legacy are valid migration policies |

---

## Score Interpretation

| Score | Rating | Recommendation |
|-------|--------|----------------|
| 55–60 correct | Excellent | Comprehensive mastery — ready for the exam |
| 49–54 correct | Very Good | Strong understanding — review missed topics |
| 43–48 correct | Good | Solid foundation — targeted review needed |
| 37–42 correct | Fair | Review weak sections before exam |
| Below 37 | Needs Review | Retake the lab and review all sections |

---

## Key Topics to Remember for Exam 1Z0-1170

**Architecture:**
- Engine → VDSM → libvirt → QEMU → KVM (the stack)
- KVM = kernel space; VMs = QEMU processes in user space
- Engine offline → VMs keep running

**Engine:**
- Minimum OL 8.5; runs on WildFly + PostgreSQL
- `engine-setup` configures database, web server, certs, firewall, admin user
- Standalone minimum RAM = 4 GB; Self-Hosted minimum RAM = 16 GB
- Two databases: ovirt_engine + ovirt_engine_history

**Hosts & Clusters:**
- Host added via Compute → Hosts; requires root SSH access
- VDSM is the host agent daemon
- Data Center → Cluster → Host → VM (hierarchy)
- Hosts can only belong to one Data Center/Cluster

**Storage:**
- Three domain types: Data, ISO, Export
- SPM: one host per Data Center, auto-reassigned on failure
- Shared storage required for migration/HA (NFS, iSCSI, FC)
- Local storage = single-host cluster, no migration
- Storage domains cannot be shared between Data Centers
- At least one storage domain required before Data Center initializes

**Networking:**
- ovirtmgmt = default management network
- Logical networks map to Linux bridges on hosts
- VM networks, migration networks, display networks are separate types

**VM Lifecycle & Migration:**
- Live migration: same cluster, shared storage, matching networks
- Pre-copy → dirty pages → stop-and-copy → activate
- QCOW2 = thin provisioning; Raw = pre-allocated
- Cloud-init runs on first boot only
- Maintenance mode auto-migrates VMs off a host

---

*This quiz bank is designed for use with Training 2: OLVM Advanced + Partner Bootcamp Prep. Questions are aligned with the 1Z0-1170 exam objectives and mapped to the hands-on lab sections.*
