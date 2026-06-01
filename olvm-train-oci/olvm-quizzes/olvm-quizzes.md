# OLVM Exam Practice Quiz Bank
## Introduction

47 curated questions mapped to Exam 1Z0-1170 - Oracle Linux Virtualization Manager Associate

Estimated Time: 75 minutes

```quiz-config
passing: 75
badge: files/badge.png
```

### Objectives

In this quiz bank, you will:

- Review OLVM architecture and core components
- Check understanding of engine installation and host management
- Reinforce networking, storage, VM lifecycle, and migration concepts
- Review detailed explanations after each quiz answer

### How to Use This Quiz Bank

This quiz bank is designed to be reviewed during OLVM training, with each section mapped to a lab part. Review each section after completing the corresponding hands-on lab section.

| Quiz Section | Topic | Review After | Time |
|-------------|-------|-------------|------|
| Section 1 | Architecture & Big Picture | Part 1 - During Ansible wait | ~15 min |
| Section 2 | Engine Installation & Configuration | Part 1 - During engine-setup wait + after Portal access | ~15 min |
| Section 3 | KVM Host & Cluster Management | Part 2 - After both hosts "Up" | ~15 min |
| Section 4 | Networking, Storage & VM Administration | Part 3 - After VMs running | ~15 min |
| Section 5 | VM Lifecycle, Migration & Operations | Parts 4-5 - After migration complete | ~15 min |

 
## Section 1: Architecture & Big Picture
*Review during Ansible playbook wait time (~8 min) and after deployment completes*

```quiz
Q: 1. What is a Standalone Engine in Oracle Linux Virtualization Manager?
* A. A management engine installed on a dedicated server separate from the virtualization hosts
- B. An engine that runs as a virtual machine within the same cluster it manages
- C. A host that only provides storage and networking services
- D. A management console that only functions when connected to the public cloud

> A standalone engine runs on a dedicated management server outside the virtualization hosts. That separation makes it different from a self-hosted engine, where the manager itself runs as a VM inside the OLVM environment.

Q: 2. What is a Self-Hosted Engine in Oracle Linux Virtualization Manager?
- A. An engine that requires a dedicated physical server
* B. A management engine that runs as a VM directly on the virtualization hosts
- C. An engine that runs in Oracle Cloud Infrastructure
- D. A backup engine for disaster recovery

> A self-hosted engine is deployed as a VM on the virtualization hosts it manages. This removes the need for a separate physical management server but depends on shared storage and hosted-engine HA behavior.

Q: 3. What is the primary benefit of using a Self-Hosted Engine deployment?
* A. It eliminates the need for a separate management server
- B. It requires more hardware resources
- C. It increases the complexity of deployment
- D. It requires Oracle Linux Enterprise Kernel


> The main benefit of self-hosted engine is eliminating a separate management server. The engine VM can run on the OLVM host infrastructure instead of requiring dedicated hardware.

Q: 4. What is the core component that serves as the backbone of Oracle Linux Virtualization Manager?
* A. oVirt engine
- B. VDSM agent
- C. KVM hypervisor
- D. PostgreSQL database

> The oVirt engine is the central OLVM management component. KVM runs workloads, VDSM acts on hosts, and PostgreSQL stores data, but the engine coordinates the environment.

Q: 5. Which three tasks does the oVirt engine perform? (Choose 3)
- A. Providing hardware emulation for VMs
- B. Running virtual machine processes
* C. Configuring storage for virtualized data centers
* D. Configuring networking for virtualized data centers
* E. Discovering KVM hosts

> The oVirt engine discovers hosts and coordinates storage and network configuration. It does not run VM processes or emulate VM hardware; those tasks happen on KVM hosts through QEMU/KVM.

Q: 6. What is the role of VDSM in Oracle Linux Virtualization Manager?
- A. It provides hardware emulation for virtual machines
- B. It manages the PostgreSQL database
* C. It acts as a host agent running continuously as a daemon on the KVM host
- D. It provides the web-based user interface

> VDSM is the host-side agent that runs as a daemon on each KVM host. The engine uses VDSM to perform host operations such as VM, network, and storage management.

Q: 7. What does QEMU (Quick Emulator) provide in the virtualization stack?
- A. Network connectivity between VMs
- B. User authentication services
* C. Hardware component emulation such as CPU, memory, network, and disk devices
- D. Database management

> QEMU provides the userspace device model for virtual machines, including emulated CPU, memory, network, and disk devices. KVM provides kernel acceleration, while QEMU presents much of the guest-visible hardware.

Q: 8. Where does KVM operate within the system?
* A. In the kernel space
- B. In user space as an application
- C. On a separate management server
- D. In the cloud

> KVM operates in Linux kernel space. That kernel integration lets Linux provide hardware-assisted virtualization for guest workloads.

Q: 9. What is the relationship between VDSM and libvirt?
- A. VDSM replaces libvirt completely
- B. libvirt runs inside VDSM
- C. They operate independently without interaction
* D. VDSM relies on libvirt to manage the lifecycle of VMs and  collect statistics

> VDSM uses libvirt to manage VM lifecycle and collect statistics from the hypervisor layer. libvirt provides the lower-level API that VDSM calls on behalf of the OLVM engine.

Q: 10. What happens to running virtual machines if the oVirt engine goes offline?
- A. All VMs are automatically suspended
- B. VMs shut down gracefully
- C. VMs are migrated to a standby host
* D. VMs continue to run on the KVM hosts

> The oVirt engine is the management layer, not the runtime process keeping VMs alive. If the engine goes offline, already-running VMs continue as QEMU/KVM processes on their hosts.

Q: 11. Which application server does the oVirt engine run on?
- A. Apache HTTP Server
* B. WildFly (formerly JBoss)
- C. Oracle WebLogic
- D. Nginx
> The oVirt engine runs on WildFly, a Java application server. WildFly hosts the engine application services that power the web interface and APIs.
```


## Section 2: Engine Installation & Configuration
*Review during engine-setup wait time (~10 min) and after Administration Portal access*

```quiz
Q: 1. What is the minimum Oracle Linux version required for installing the OLVM Manager (Engine)?
- A. Oracle Linux 7.5
* B. Oracle Linux 8.5 and higher
- C. Oracle Linux 9.0
- D. Oracle Linux 6.8

> The OLVM Manager installation path in this quiz requires Oracle Linux 8.5 or later. Older Oracle Linux releases do not match the supported engine platform for this workshop.

Q: 2. Which two CPU technologies must be supported for the Engine host processor? (Choose 2)
* A. Intel VT-x
- B. Intel SGX
* C. AMD-V
- D. ARM TrustZone


> Intel VT-x and AMD-V are CPU hardware virtualization extensions. They allow KVM to run virtualized workloads efficiently on Intel or AMD processors.

Q: 3. What command is used to install the OLVM Engine package?
- A. yum install olvm-engine
* B. dnf install ovirt-engine
- C. dnf install olvm-manager
- D. yum install oracle-engine

> The OLVM Engine package name is `ovirt-engine`, installed with DNF. OLVM is based on the oVirt stack, so the package retains the upstream oVirt naming.

Q: 4. What command is used to configure the OLVM Engine after installation?
- A. ovirt-configure
* B. engine-setup
- C. olvm-setup --init
- D. dnf configure ovirt-engine

> `engine-setup` performs the interactive post-install configuration for the engine. Installing the package alone does not create the database, certificates, services, and portal configuration.

Q: 5. Which five components does engine-setup configure automatically? (Choose 5)
* A. PostgreSQL database
* B. Apache/Tomcat web server
* C. SSL certificates
* D. Firewall rules
* E. admin@ovirt user
- F. KVM hypervisor on the local server
- G. DNS server



> `engine-setup` configures key manager components such as the database, web services, SSL certificates, firewall rules, and the initial admin user. It does not turn the local server into a KVM hypervisor or DNS server.

Q: 6. Which open-source project is Oracle Linux Virtualization Manager based on?
- A. Proxmox
* B. oVirt
- C. OpenStack
- D. VMware vSphere

> OLVM is based on the oVirt open-source virtualization management project. Oracle packages and supports that stack for Oracle Linux KVM.

Q: 7. How many PostgreSQL databases does OLVM use?
- A. One - ovirt_engine
* B. Two - ovirt_engine and ovirt_engine_history
- C. Three - one for engine, hosts, and VMs
- D. None - it uses Oracle Database

> OLVM uses two PostgreSQL databases: one for active engine data and one for historical Data Warehouse data. The history database supports monitoring and reporting.

Q: 8. Which three web-based management portals does OLVM provide? (Choose 3)
* A. Administration Portal
- B. Command Line Portal
* C. VM Portal
* D. Monitoring Portal (Grafana)
- E. Developer Portal

> The three web portals are the Administration Portal, VM Portal, and Monitoring Portal. They serve administrators, VM users, and monitoring/analytics workflows respectively.

Q: 9. Where are the OLVM Engine log files located?
- A. /var/log/olvm/
* B. /var/log/ovirt-engine/
- C. /opt/ovirt/logs/
- D. /var/log/messages
> OLVM engine logs are stored under `/var/log/ovirt-engine/`. That directory is the primary place to inspect manager-side setup and runtime issues.
```

## Section 3: KVM Host & Cluster Management
*Review after both hosts show status "Up" in the Administration Portal*

```quiz

Q: 1. Where in the Administration Portal do you add a new KVM host?
- A. Storage -> Hosts
* B. Compute -> Hosts
- C. Network -> Hosts
- D. Configuration -> Hosts

> KVM hosts are added from Compute -> Hosts because hosts are compute resources that provide capacity for VMs. The Add Host wizard starts from that Administration Portal area.

Q: 2. Which two authentication methods can be used when adding a KVM host? (Choose 2)
* A. Password authentication
- B. Kerberos
* C. SSH key authentication
- D. Certificate authentication

> A host can be added using password authentication or SSH key authentication. In both cases, the engine needs privileged access to install and configure host components.

Q: 3. For which user account must authentication credentials be provided when adding a host?
- A. admin user
* B. root user
- C. ovirt user
- D. vdsm user

> Root credentials are required because host registration performs system-level actions such as installing packages, configuring services, and applying firewall settings.

Q: 4. What is the role of the VDSM service on a KVM host?
- A. It manages the PostgreSQL database
* B. It acts as a host agent running continuously as a daemon on the KVM host
- C. It provides the web-based administration interface
- D. It handles SSL certificate generation


> VDSM is the daemon on the KVM host that receives instructions from the engine. It performs host-side VM, network, and storage operations.

Q: 5. True or False: A single KVM host can belong to multiple Data Centers simultaneously.
- A. True
* B. False

> A KVM host cannot belong to multiple Data Centers at the same time. Data Center membership defines the host's storage and network boundaries.

Q: 6. What must a KVM host's state be before it can be moved to a different cluster within the same Data Center?
- A. Up
* B. Maintenance
- C. Non-Operational
- D. Down

> A host must be in Maintenance mode before it is moved to another cluster. Maintenance mode removes active workloads and makes configuration changes safer.

Q: 7. Which service must be active on a KVM host for the OLVM engine to manage firewall rules?
- A. iptables
- B. nftables
* C. firewalld
- D. UFW

> `firewalld` must be active for the engine to manage host firewall rules automatically. If it is disabled, automatic firewall configuration cannot apply those rules normally.

Q: 8. What is the correct order of the OLVM resource hierarchy, from highest to lowest?
- A. Cluster -> Data Center -> Host -> VM
* B. Data Center -> Cluster -> Host -> VM
- C. Host -> Cluster -> Data Center -> VM
- D. Data Center -> Host -> Cluster -> VM
> The OLVM hierarchy is Data Center -> Cluster -> Host -> VM. Data Centers contain clusters, clusters contain hosts, and hosts run VMs.
```


## Section 4: Networking, Storage & VM Administration
*Review after VMs are running and network/storage are verified*

```quiz
Q: 1. What are logical networks in OLVM?
- A. Physical network cables
* B. Representations of network resources that provide connectivity for KVM virtual machines
- C. Virtual switches only
- D. Network security policies

> Logical networks represent managed network resources used by hosts and VMs. They abstract the physical network into OLVM-managed connectivity objects.

Q: 2. What is the name of the default logical network automatically created during OLVM setup?
- A. default_network
* B. ovirtmgmt
- C. vm_network
- D. management_bridge


> `ovirtmgmt` is the default logical network created during setup. It is normally assigned the management role for engine-to-host communication.

Q: 3. Can a storage domain be shared between multiple Data Centers?
- A. Yes, freely shared
* B. No, storage domains cannot be shared between different OLVM Data Centers
- C. Only ISO domains can be shared
- D. Only with special configuration

> A storage domain belongs to only one Data Center at a time. Sharing one storage domain across multiple Data Centers would create conflicting ownership and metadata assumptions.

Q: 4. What is the Storage Pool Manager (SPM)?
- A. A physical storage device
* B. A management role assigned to one host in the Data Center
- C. A type of storage domain
- D. A backup application

> The SPM is a role assigned to one host in a Data Center to coordinate storage metadata operations. It is not a physical storage device.

Q: 5. How many hosts can have the SPM role in a single Data Center at one time?
- A. All hosts share the SPM role equally
* B. Only one host at a time
- C. Two hosts for redundancy
- D. Up to five hosts

> Only one host can hold the SPM role in a Data Center at a time. A single SPM prevents conflicting storage metadata changes.

Q: 6. What storage requirement must be met for VMs to be migrated between hosts?
- A. Each host needs local storage
* B. VMs must share the same storage domain accessible by both hosts
- C. Storage must be SSD-based
- D. VMs must use iSCSI only

> VM migration requires both source and destination hosts to access the same VM disks. That means the VM must reside on storage shared by both hosts.

Q: 7. Which storage type does NOT support VM live migration or high availability?
- A. NFS
- B. iSCSI
- C. Fibre Channel
* D. Local Storage

> Local storage is tied to one host, so it does not support normal live migration or high availability across hosts. Shared storage is required for those cluster features.

Q: 8. What is an OVA file?
- A. An Oracle Virtualization Agent
* B. An Open Virtualization Archive - a portable VM package containing disk images and configuration
- C. An Operating Virtual Appliance log file
- D. An OLVM Version Archive for backups

> An OVA is an Open Virtualization Archive, a portable package containing a VM's disk images and configuration. It is commonly used for VM export and import workflows.

Q: 9. What is the difference between thin provisioning and pre-allocated disk provisioning?
- A. Thin provisioning is faster but uses more space
* B. Thin provisioning allocates disk space only as data is written; pre-allocated reserves all space upfront
- C. Pre-allocated provisioning is only available for NFS storage
- D. There is no difference - they are the same

> Thin provisioning consumes physical storage as data is written, while pre-allocated provisioning reserves the full disk space up front. Thin saves capacity; pre-allocation improves predictability.

Q: 10. What does cloud-init do when a VM is created from a template?
- A. Installs the hypervisor on the host
* B. Configures the VM on first boot - sets hostname, user accounts, networking, and runs custom scripts
- C. Creates a backup of the template
- D. Manages the storage domain

> Cloud-init customizes a VM on first boot, including hostname, users, networking, and startup scripts. It is especially useful when deploying VMs from templates.

Q: 11. Can a Data Center be initialized without a storage domain attached?
- A. Yes, storage is optional
* B. No, at least one storage domain must be attached before initialization
- C. Only in test environments
- D. Only for Self-Hosted Engine deployments

> A Data Center requires at least one storage domain before initialization because VM disks and storage metadata need a backing storage location.
```


## Section 5: VM Lifecycle, Migration & Operations
*Review after live migration is completed and verified*

```quiz

Q: 1. What is the correct order of phases during a live migration?
- A. Stop-and-copy -> Pre-copy -> Activation
* B. Pre-copy -> Iterative dirty page copy -> Stop-and-copy -> Activation on destination
- C. Activation -> Pre-copy -> Stop-and-copy
- D. Snapshot -> Copy -> Restore

> Live migration starts with pre-copy, repeatedly copies dirty pages, briefly pauses for stop-and-copy, and then activates the VM on the destination. This sequence minimizes downtime.

Q: 2. During live migration, does the VM experience any downtime?
- A. Yes, the VM is offline for several minutes
* B. No downtime - the VM continues running throughout with only a brief pause during final sync
- C. The VM must be rebooted after migration
- D. Downtime depends on the storage type used

> Live migration is designed to keep the VM running, with only a brief final pause during synchronization. Users typically should not see a long outage.

Q: 3. VMs can be migrated between which levels of the OLVM hierarchy?
- A. Between Data Centers
- B. Between Clusters within the same Data Center
* C. Between hosts within the same Cluster
- D. Between any hosts regardless of hierarchy

> VMs migrate between hosts in the same cluster because those hosts share compatible CPU, network, and storage configuration. Migration across arbitrary hosts or Data Centers is not the normal boundary.

Q: 4. What happens to a VM's IP address after live migration to a different host?
- A. The IP address changes to match the new host
* B. The IP address stays the same - the VM retains its network identity
- C. The VM loses network connectivity
- D. A new IP must be manually assigned

> A VM keeps its IP address after live migration because the guest OS and VNIC identity remain the same. The VM moves hosts, but its network identity follows it.

Q: 5. Which migration type requires the VM to be stopped before moving?
- A. Live Migration
* B. Offline Migration
- C. Automatic Migration
- D. Hot Migration

> Offline migration requires the VM to be stopped before it is moved. Live migration is the method that moves a running VM.

Q: 6. What should you do before performing maintenance on a KVM host that is running VMs?
- A. Shut down all VMs manually
* B. Put the host in Maintenance mode - OLVM will automatically migrate VMs to other hosts
- C. Disconnect the storage domain first
- D. Remove the host from the cluster


> Putting a host into Maintenance mode lets OLVM migrate or shut down VMs before host work begins. This avoids manually disrupting workloads one by one.

Q: 7. Which three are valid VM "Run Options" in OLVM? (Choose 3)
* A. Run Once
- B. Run and Pause
* C. Run as Stateless
- D. Run in Cloud-Init mode
* E. Run on High Priority


> Run Once, Run as Stateless, and Run on High Priority are valid run options. These options change how a VM starts or behaves for a specific run operation.

Q: 8. Which component provides the Data Warehouse (DWH) historical data used by the Monitoring Portal?
- A. VDSM
* B. The ovirt_engine_history database
- C. The SPM host
- D. engine-config

> `ovirt_engine_history` stores Data Warehouse historical data used by monitoring. Grafana and the Monitoring Portal use this historical database for metrics and trends.
```
 

## Key Topics to Remember for Exam 1Z0-1170

Architecture:
- Engine -> VDSM -> libvirt -> QEMU -> KVM (the stack)
- KVM = kernel space; VMs = QEMU processes in user space
- Engine offline -> VMs keep running

Engine:
- Minimum OL 8.5; runs on WildFly + PostgreSQL
- `engine-setup` configures database, web server, certs, firewall, admin user
- Standalone minimum RAM = 4 GB; Self-Hosted minimum RAM = 16 GB
- Two databases: ovirt_engine + ovirt_engine_history

Hosts & Clusters:
- Host added via Compute -> Hosts; requires root SSH access
- VDSM is the host agent daemon
- Data Center -> Cluster -> Host -> VM (hierarchy)
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
- Pre-copy -> dirty pages -> stop-and-copy -> activate
- QCOW2 = thin provisioning; Raw = pre-allocated
- Cloud-init runs on first boot only
- Maintenance mode auto-migrates VMs off a host

## Learn More

- Oracle Linux Virtualization Document :https://docs.oracle.com/en/virtualization/oracle-linux-virtualization-manager/

## Acknowledgements

- **Author** - Perside Foster
- **Source Material** - OLVM bootcamp training and Oracle Linux Virtualization Manager documentation
