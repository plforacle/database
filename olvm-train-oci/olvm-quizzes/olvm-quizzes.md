# OLVM Exam Practice Quiz Bank
## Introduction

60 Questions Mapped to Exam 1Z0-1170 — Oracle Linux Virtualization Manager Associate

### How to Use This Quiz Bank

This quiz bank is designed to be reviewed during OLVM training, with each section mapped to a lab part. Review each section after completing the corresponding hands-on lab section.

| Quiz Section | Topic | Review After | Time |
|-------------|-------|-------------|------|
| Section 1 | Architecture & Big Picture | Part 1 — During Ansible wait | ~15 min |
| Section 2 | Engine Installation & Configuration | Part 1 — During engine-setup wait + after Portal access | ~15 min |
| Section 3 | KVM Host & Cluster Management | Part 2 — After both hosts "Up" | ~15 min |
| Section 4 | Networking, Storage & VM Administration | Part 3 — After VMs running | ~15 min |
| Section 5 | VM Lifecycle, Migration & Operations | Parts 4–5 — After migration complete | ~15 min |

 
## Section 1: Architecture & Big Picture
*Review during Ansible playbook wait time (~8 min) and after deployment completes*

```quiz
Q: 1. What is a Standalone Engine in Oracle Linux Virtualization Manager?
* A. A management engine installed on a dedicated server separate from the virtualization hosts
- B. An engine that runs as a virtual machine within the same cluster it manages
- C. A host that only provides storage and networking services
- D. A management console that only functions when connected to the public cloud

Q: 2. What is a Self-Hosted Engine in Oracle Linux Virtualization Manager?
- A. An engine that requires a dedicated physical server
*  B. A management engine that runs as a VM directly on the virtualization hosts
- C. An engine that runs in Oracle Cloud Infrastructure
- D. A backup engine for disaster recovery

Q: 3. What is the primary benefit of using a Self-Hosted Engine deployment?
* A. It eliminates the need for a separate management server
- B. It requires more hardware resources
- C. It increases the complexity of deployment
- D. It requires Oracle Linux Enterprise Kernel

Q: 4. What component monitors and manages the Self-Hosted Engine VM on each host?
- A. oVirt engine service
- B. VDSM daemon
- C. PostgreSQL database
* D. HA agent (ovirt-ha-agent)

Q: 5. What is the core component that serves as the backbone of Oracle Linux Virtualization Manager?
* A. oVirt engine
- B. VDSM agent
- C. KVM hypervisor
- D. PostgreSQL database

Q: 6. Which three tasks does the oVirt engine perform? (Choose 3)
- A. Providing hardware emulation for VMs
- B. Running virtual machine processes
* C. Configuring storage for virtualized data centers
* D. Configuring networking for virtualized data centers
* E. Discovering KVM hosts

Q: 7. What is the role of VDSM in Oracle Linux Virtualization Manager?
- A. It provides hardware emulation for virtual machines
- B. It manages the PostgreSQL database
* C. It acts as a host agent running continuously as a daemon on the KVM host
- D. It provides the web-based user interface

Q: 8. What does QEMU (Quick Emulator) provide in the virtualization stack?
- A. Network connectivity between VMs
- B. User authentication services
*  C. Hardware component emulation such as CPU, memory, network, and disk devices
- D. Database management

Q: 9. Where does KVM operate within the system?
*  A. In the kernel space
- B. In user space as an application
- C. On a separate management server
- D. In the cloud

Q: 10. What is the relationship between VDSM and libvirt?
- A. VDSM replaces libvirt completely
- B. libvirt runs inside VDSM
- C. They operate independently without interaction
*  D. VDSM relies on libvirt to manage the lifecycle of VMs and  collect statistics
Q: 11. What happens to running virtual machines if the oVirt engine goes offline?
- A. All VMs are automatically suspended
- B. VMs shut down gracefully
- C. VMs are migrated to a standby host
*  D. VMs continue to run on the KVM hosts

Q: 12. Which application server does the oVirt engine run on?
- A. Apache HTTP Server
*  B. WildFly (formerly JBoss)
- C. Oracle WebLogic
- D. Nginx
```


## Section 2: Engine Installation & Configuration
*Review during engine-setup wait time (~10 min) and after Administration Portal access*

```quiz
Q: 1. What is the minimum Oracle Linux version required for installing the OLVM Manager (Engine)?
- A. Oracle Linux 7.5
* B. Oracle Linux 8.5 and higher
- C. Oracle Linux 9.0
- D. Oracle Linux 6.8

Q: 2. Which two CPU technologies must be supported for the Engine host processor? (Choose 2)
* A. Intel VT-x
- B. Intel SGX
- C. AMD-V
- D. ARM TrustZone

Q: 3. What are the RECOMMENDED hardware requirements for an OLVM Engine SMALL deployment? (Choose 3)
- A. 2 cores
* B. 4 cores
- C. 16 GB or greater RAM
- D. 32 GB RAM
- E. 50 GB or greater local writable disk

Q: 4. What command is used to install the OLVM Engine package?
- A. yum install olvm-engine
* B. dnf install ovirt-engine
- C. dnf install olvm-manager
- D. yum install oracle-engine

Q: 5. What command is used to configure the OLVM Engine after installation?
- A. ovirt-configure
* B. engine-setup
- C. olvm-setup --init
- D. dnf configure ovirt-engine

Q: 6. Which five components does engine-setup configure automatically? (Choose 5)
* A. PostgreSQL database
* B. Apache/Tomcat web server
* C. SSL certificates
* D. Firewall rules
* E. admin@ovirt user
- F. KVM hypervisor on the local server
- G. DNS server

Q: 7. What is the MINIMUM RAM for a Standalone Engine in a SMALL deployment?
- A. 2 GB
* B. 4 GB
- C. 16 GB
- D. 32 GB

Q: 8. What is the MINIMUM RAM for a Self-Hosted Engine in a SMALL deployment?
- A. 4 GB
- B. 8 GB
* C. 16 GB
- D. 32 GB

Q: 9. Which open-source project is Oracle Linux Virtualization Manager based on?
- A. Proxmox
* B. oVirt
- C. OpenStack
- D. VMware vSphere

Q: 10. How many PostgreSQL databases does OLVM use?
- A. One — ovirt_engine
* B. Two — ovirt_engine and ovirt_engine_history
- C. Three — one for engine, hosts, and VMs
- D. None — it uses Oracle Database

Q: 11. Which three web-based management portals does OLVM provide? (Choose 3)
* A. Administration Portal
- B. Command Line Portal
* C. VM Portal
* D. Monitoring Portal (Grafana)
- E. Developer Portal

Q: 12. Where are the OLVM Engine log files located?
- A. /var/log/olvm/
* B. /var/log/ovirt-engine/
- C. /opt/ovirt/logs/
- D. /var/log/messages
```

## Section 3: KVM Host & Cluster Management
*Review after both hosts show status "Up" in the Administration Portal*

```quiz
Q: 1. What is the minimum Oracle Linux version required for a KVM host?
* A. Oracle Linux 7.5
- B. Oracle Linux 8.5 or later
- C. Oracle Linux 9.0
- D. Oracle Linux 8.0

Q: 2. What is the MINIMUM CPU requirement for a KVM host?
- A. Single-core 32-bit CPU
* B. 64-bit dual-core CPU
- C. 64-bit quad-core CPU
- D. 64-bit eight-core CPU

Q: 3. What is the MINIMUM network interface requirement for a KVM host?
- A. One NIC with 100 Mbps bandwidth
* B. One NIC with 1 Gbps bandwidth
- C. Two NICs with 1 Gbps bandwidth
- D. Four NICs with 1 Gbps bandwidth

Q: 4. Where in the Administration Portal do you add a new KVM host?
- A. Storage → Hosts
* B. Compute → Hosts
- C. Network → Hosts
- D. Configuration → Hosts

Q: 5. Which two authentication methods can be used when adding a KVM host? (Choose 2)
* A. Password authentication
- B. Kerberos
* C. SSH key authentication
- D. Certificate authentication

Q: 6. For which user account must authentication credentials be provided when adding a host?
- A. admin user
* B. root user
- C. ovirt user
- D. vdsm user

Q: 7. What is the role of the VDSM service on a KVM host?
- A. It manages the PostgreSQL database
* B. It acts as a host agent running continuously as a daemon on the KVM host
- C. It provides the web-based administration interface
- D. It handles SSL certificate generation

Q: 8. What happens to a virtual machine if the oVirt engine goes offline?
- A. The VM automatically suspends
* B. The VM continues to run on the KVM host
- C. The VM is migrated to another host
- D. The VM shuts down gracefully

Q: 9. True or False: A single KVM host can belong to multiple Data Centers simultaneously.
- A. True
* B. False

Q: 10. What must a KVM host's state be before it can be moved to a different cluster within the same Data Center?
- A. Up
* B. Maintenance
- C. Non-Operational
- D. Down

Q: 11. Which service must be active on a KVM host for the OLVM engine to manage firewall rules?
- A. iptables
- B. nftables
* C. firewalld
- D. UFW

Q: 12. What is the correct order of the OLVM resource hierarchy, from highest to lowest?
- A. Cluster → Data Center → Host → VM
* B. Data Center → Cluster → Host → VM
- C. Host → Cluster → Data Center → VM
- D. Data Center → Host → Cluster → VM
```


## Section 4: Networking, Storage & VM Administration
*Review after VMs are running and network/storage are verified*

```quiz
Q: 1. What are logical networks in OLVM?
- A. Physical network cables
* B. Representations of network resources that provide connectivity for KVM virtual machines
- C. Virtual switches only
- D. Network security policies

Q: 2. What is the name of the default logical network automatically created during OLVM setup?
- A. default_network
* B. ovirtmgmt
- C. vm_network
- D. management_bridge

Q: 3. Which three are valid Storage Domain types in OLVM? (Choose 3)
* A. Data
- B. Boot
* C. ISO
* D. Export
- E. Template

Q: 4. Can a storage domain be shared between multiple Data Centers?
- A. Yes, freely shared
* B. No, storage domains cannot be shared between different OLVM Data Centers
- C. Only ISO domains can be shared
- D. Only with special configuration

Q: 5. What is the Storage Pool Manager (SPM)?
- A. A physical storage device
* B. A management role assigned to one host in the Data Center
- C. A type of storage domain
- D. A backup application

Q: 6. How many hosts can have the SPM role in a single Data Center at one time?
- A. All hosts share the SPM role equally
* B. Only one host at a time
- C. Two hosts for redundancy
- D. Up to five hosts

Q: 7. What storage requirement must be met for VMs to be migrated between hosts?
- A. Each host needs local storage
* B. VMs must share the same storage domain accessible by both hosts
- C. Storage must be SSD-based
- D. VMs must use iSCSI only

Q: 8. Which storage type does NOT support VM live migration or high availability?
- A. NFS
- B. iSCSI
- C. Fibre Channel
* D. Local Storage

Q: 9. What is an OVA file?
- A. An Oracle Virtualization Agent
* B. An Open Virtualization Archive — a portable VM package containing disk images and configuration
- C. An Operating Virtual Appliance log file
- D. An OLVM Version Archive for backups

Q: 10. What is the difference between thin provisioning and pre-allocated disk provisioning?
- A. Thin provisioning is faster but uses more space
* B. Thin provisioning allocates disk space only as data is written; pre-allocated reserves all space upfront
- C. Pre-allocated provisioning is only available for NFS storage
- D. There is no difference — they are the same

Q: 11. What does cloud-init do when a VM is created from a template?
- A. Installs the hypervisor on the host
* B. Configures the VM on first boot — sets hostname, user accounts, networking, and runs custom scripts
- C. Creates a backup of the template
- D. Manages the storage domain

Q: 12. Can a Data Center be initialized without a storage domain attached?
- A. Yes, storage is optional
* B. No, at least one storage domain must be attached before initialization
- C. Only in test environments
- D. Only for Self-Hosted Engine deployments
```


## Section 5: VM Lifecycle, Migration & Operations
*Review after live migration is completed and verified*

```quiz
Q: 1. What are the prerequisites for live migrating a VM between hosts? (Choose 3)
* A. Both hosts must be in the same cluster
* B. Both hosts must have access to the shared storage domain
- C. The VM must be powered off
* D. The logical network used by the VM must be available on both hosts
- E. The hosts must have different CPU types

Q: 2. What is the correct order of phases during a live migration?
- A. Stop-and-copy → Pre-copy → Activation
* B. Pre-copy → Iterative dirty page copy → Stop-and-copy → Activation on destination
- C. Activation → Pre-copy → Stop-and-copy
- D. Snapshot → Copy → Restore

Q: 3. During live migration, does the VM experience any downtime?
- A. Yes, the VM is offline for several minutes
* B. No downtime — the VM continues running throughout with only a brief pause during final sync
- C. The VM must be rebooted after migration
- D. Downtime depends on the storage type used

Q: 4. VMs can be migrated between which levels of the OLVM hierarchy?
- A. Between Data Centers
- B. Between Clusters within the same Data Center
* C. Between hosts within the same Cluster
- D. Between any hosts regardless of hierarchy

Q: 5. What happens to a VM's IP address after live migration to a different host?
- A. The IP address changes to match the new host
* B. The IP address stays the same — the VM retains its network identity
- C. The VM loses network connectivity
- D. A new IP must be manually assigned

Q: 6. Which migration type requires the VM to be stopped before moving?
- A. Live Migration
* B. Offline Migration
- C. Automatic Migration
- D. Hot Migration

Q: 7. What should you do before performing maintenance on a KVM host that is running VMs?
- A. Shut down all VMs manually
* B. Put the host in Maintenance mode — OLVM will automatically migrate VMs to other hosts
- C. Disconnect the storage domain first
- D. Remove the host from the cluster

Q: 8. If the SPM host fails during a migration, what happens?
- A. All VMs in the Data Center stop running
* B. OLVM automatically selects a new SPM from the remaining hosts
- C. The storage domain becomes read-only
- D. You must manually reinstall the storage domain

Q: 9. Which three are valid VM "Run Options" in OLVM? (Choose 3)
* A. Run Once
- B. Run and Pause
* C. Run as Stateless
- D. Run in Cloud-Init mode
* E. Run on High Priority

Q: 10. What disk format allows for thin provisioning in OLVM?
- A. Raw
* B. QCOW2
- C. VMDK
- D. ISO

Q: 11. Which component provides the Data Warehouse (DWH) historical data used by the Monitoring Portal?
- A. VDSM
* B. The ovirt_engine_history database
- C. The SPM host
- D. engine-config

Q: 12. Which three are valid migration policies in OLVM Cluster settings? (Choose 3)
* A. Minimal Downtime
* B. Suspend and Resume
- C. Post-copy
- D. Pre-copy
* E. Legacy
```
 

## Key Topics to Remember for Exam 1Z0-1170

Architecture:
- Engine → VDSM → libvirt → QEMU → KVM (the stack)
- KVM = kernel space; VMs = QEMU processes in user space
- Engine offline → VMs keep running

Engine:
- Minimum OL 8.5; runs on WildFly + PostgreSQL
- `engine-setup` configures database, web server, certs, firewall, admin user
- Standalone minimum RAM = 4 GB; Self-Hosted minimum RAM = 16 GB
- Two databases: ovirt\_engine + ovirt\_engine\_history

Hosts & Clusters:
- Host added via Compute → Hosts; requires root SSH access
- VDSM is the host agent daemon
- Data Center → Cluster → Host → VM (hierarchy)
- Hosts can only belong to one Data Center/Cluster

Storage:
- Three domain types: Data, ISO, Export
- SPM: one host per Data Center, auto-reassigned on failure
- Shared storage required for migration/HA (NFS, iSCSI, FC)
- Local storage = single-host cluster, no migration
- Storage domains cannot be shared between Data Centers
- At least one storage domain required before Data Center initializes

Networking:
- ovirtmgmt = default management network
- Logical networks map to Linux bridges on hosts
- VM networks, migration networks, display networks are separate types

VM Lifecycle & Migration:
- Live migration: same cluster, shared storage, matching networks
- Pre-copy → dirty pages → stop-and-copy → activate
- QCOW2 = thin provisioning; Raw = pre-allocated
- Cloud-init runs on first boot only
- Maintenance mode auto-migrates VMs off a host

## Learn More

- Oracle Linux Virtualization Document :https://docs.oracle.com/en/virtualization/oracle-linux-virtualization-manager/

## Acknowledgements

- **Author** - Perside Foster 
- **Contributors** - Perside Foster
- **Last Updated By/Date** - Perside Foster , April 1, 2026
