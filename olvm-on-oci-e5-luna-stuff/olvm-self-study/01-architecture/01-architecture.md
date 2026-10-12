# OLVM Self-Study: Architecture and Core Components

## Introduction

This module contains **85 scored practice questions** for the architecture and core components objective, which accounts for **17%** of the exam weighting supplied for this training. Use it after the instructor-led Lab 7 session and while reviewing the supplied course transcripts.

Estimated Time: 85-119 minutes, including explanation review

```quiz-config
passing: 80
badge: images/badge.png
```

### Objectives

- Describe Oracle Virtualization Architecture
- Describe Hosted-Engine
- Describe Data Warehouse and Databases
- Describe Administration Interfaces
- Explain Data Centers, Clusters and Hosts
- Explain Storage Pool Manager, Networks and Storage
- Explain Event Logging and Notifications

### How to Practice

Choose **one option per question**, including questions whose choices contain a set of items. Click **Check Answer** to see feedback and the explanation. Answer all 85 questions; at least **68 correct answers** unlock this module's badge. The 80% target is a study milestone, not an official exam passing score.

Record your first-attempt score before using **Try Again**. Review the corresponding transcript clip or Lab 7 task, explain the reasoning, and retry after studying. Scores are tracked within this module; keep your own first-attempt record before navigating away.

The recorded course uses older OL8 and OLVM examples than Lab 7. Questions explicitly referring to the recorded course use that context. Match package, identity, and UI procedures to the actual practice environment.

[Study guide and coverage map](../../workshops/olvm-study/index.html?lab=study-guide)

## Task 1: Describe Oracle Virtualization Architecture

```quiz score
Q: 1.001. Which PostgreSQL database holds the engine's operational data?
- ovirt_engine_history
- The browser's local database
- The guest database inside every VM
* engine
> The engine database stores operational management data. The separate history database supports historical reporting; a database and its schemas are distinct concepts.
```

```quiz score
Q: 1.002. Which two Intel hardware extensions does Oracle Linux Virtualization Manager support for enhanced performance? Select the option listing the complete correct set.
- Intel VT-x; Intel SGX
- Intel SGX; Intel VT-d
- Intel AES-NI; Intel VT-d
* Intel VT-x; Intel VT-d
> Intel VT-x provides CPU virtualization support, while Intel VT-d provides directed I/O support for device assignment and I/O virtualization. SGX and AES-NI are useful processor features, but they are not the two core virtualization extensions OLVM relies on for this objective.
```

```quiz score
Q: 1.003. Why must a KVM host support a processor virtualization extension such as Intel VT-x?
- It automatically assigns every VM a dedicated physical core
* It allows hardware-assisted execution of guest CPU workloads
- It configures VLAN tags on the physical switch
- It supplies shared storage for every VM disk
> VT-x supports CPU virtualization. Assigning and scheduling vCPUs is a separate host and hypervisor responsibility.
```

```quiz score
Q: 1.004. What is the purpose of Intel VT-d in Oracle Linux Virtualization Manager?
- It manages memory allocation between VMs
- It provides processor-level virtualization
- It handles virtual machine migration
* It allows VMs to directly use devices like graphic cards and network adapters
> Intel VT-d is used for directed I/O, which allows hardware devices such as network adapters or graphics devices to be assigned more directly to a VM. VT-x handles CPU virtualization; VT-d is about I/O device virtualization and passthrough-style use cases.
```

```quiz score
Q: 1.005. What is the core component that serves as the backbone of Oracle Linux Virtualization Manager?
- PostgreSQL database
- KVM hypervisor
* oVirt engine
- VDSM agent
> The oVirt engine is the central management component in OLVM. KVM runs workloads, VDSM acts on hosts, and PostgreSQL stores data, but the engine coordinates the environment and provides the main management control plane.
```

```quiz score
Q: 1.006. Which three tasks does the oVirt engine perform? Select the option listing the complete correct set.
- Running virtual machine processes; Configuring storage for virtualized data centers; Configuring networking for virtualized data centers
- Discovering KVM hosts; Running virtual machine processes; Configuring networking for virtualized data centers
- Discovering KVM hosts; Configuring storage for virtualized data centers; Running virtual machine processes
* Discovering KVM hosts; Configuring storage for virtualized data centers; Configuring networking for virtualized data centers
> The oVirt engine performs centralized orchestration tasks such as discovering hosts and coordinating storage and network configuration. VM execution and hardware emulation happen on KVM hosts through QEMU/KVM, not inside the engine process.
```

```quiz score
Q: 1.007. Oracle Linux Virtualization Manager is built from which open-source project?
- OpenStack
- XenServer
* oVirt
- Proxmox
> Oracle Linux Virtualization Manager is based on the oVirt open-source virtualization management project. OLVM packages and supports that management stack for Oracle Linux KVM environments.
```

```quiz score
Q: 1.008. What are the two main interfaces provided by Oracle Linux Virtualization Manager for management? Select the option listing the complete correct set.
* Web-based UI; REST API
- Command-line interface (CLI); REST API
- Web-based UI; Command-line interface (CLI)
- SNMP console; REST API
> OLVM provides a browser-based user interface for administrators and users, and a REST API for automation and integration. SSH and SNMP may be useful operational tools, but they are not the two primary OLVM management interfaces identified here.
```

```quiz score
Q: 1.009. What happens to a virtual machine if the oVirt engine goes offline?
* The VM continues to run on the KVM host
- The VM shuts down gracefully
- The VM is migrated to another host
- The VM automatically suspends
> The oVirt engine is the management layer, not the runtime process keeping a VM alive. Running VMs continue as QEMU/KVM processes on their KVM hosts even if centralized engine management is temporarily unavailable.
```

```quiz score
Q: 1.010. Which dashboard view best helps an administrator assess infrastructure health?
- Only the manager's browser history
- A list of guest passwords without resource status
* A view of VM and host status together with cluster and storage information
- Only the names of downloaded ISO files
> The administration dashboard summarizes resources and status. Counts alone do not show whether the infrastructure is healthy.
```

```quiz score
Q: 1.011. Which feature allows Oracle Linux Virtualization Manager to integrate with other management systems?
* REST API
- Web-based UI
- SSH access
- SNMP traps
> The REST API is the integration surface used by external tools, scripts, and management platforms to interact with OLVM programmatically. The web UI is for human use, while REST is designed for automation and system-to-system integration.
```

```quiz score
Q: 1.012. What is the relationship between vCPUs and physical CPUs in Oracle Linux KVM?
- vCPUs must always be equal in number to physical CPUs
- Physical CPUs are not visible to the hypervisor
* The guest VM decides which tasks run on vCPUs, and the hypervisor decides which physical CPU cores the vCPUs use
- Each physical CPU can only support one vCPU
> Inside the guest, the operating system schedules work onto its vCPUs. The hypervisor then maps and schedules those vCPUs onto physical CPU cores or threads, unless stricter placement such as CPU pinning is configured.
```

```quiz score
Q: 1.013. What is the role of VDSM in Oracle Linux Virtualization Manager?
- It provides the web-based user interface
- It manages the PostgreSQL database
- It provides hardware emulation for virtual machines
* It acts as a host agent running continuously as a daemon on the KVM host
> VDSM is the host-side management agent that runs continuously on KVM hosts. It receives instructions from the engine and carries out host-level operations for VMs, networking, and storage.
```

```quiz score
Q: 1.014. How does the oVirt engine communicate with KVM hosts?
- Through direct kernel calls
- Through the PostgreSQL database
- Through SSH connections
* Through the VDSM service
> The engine communicates with KVM hosts through the VDSM service. Administrators may use SSH for setup or troubleshooting, but normal OLVM host management is coordinated through VDSM.
```

```quiz score
Q: 1.015. Which three tasks does VDSM perform on the KVM host? Select the option listing the complete correct set.
- Managing virtual machines; Providing web browser access; Managing storage
* Managing virtual machines; Managing networks; Managing storage
- Managing virtual machines; Managing networks; Providing web browser access
- Providing web browser access; Managing networks; Managing storage
> VDSM performs host-level management for virtual machines, networks, and storage. It does not provide the browser UI or run the engine; those responsibilities belong to other parts of the OLVM stack.
```

```quiz score
Q: 1.016. What is the function of libvirt in the KVM host architecture?
* It provides an API for managing various hypervisors including Oracle Linux KVM
- It stores virtual machine configurations
- It manages user authentication
- It emulates hardware components for VMs
> libvirt provides a stable management API for hypervisors, including KVM. VDSM uses libvirt to control VM lifecycle and gather host or VM information without directly manipulating every hypervisor detail itself.
```

```quiz score
Q: 1.017. What does QEMU (Quick Emulator) provide in the virtualization stack?
* Hardware component emulation such as CPU, memory, network, and disk devices
- Network connectivity between VMs
- User authentication services
- Database management
> QEMU provides device emulation for guest hardware such as virtual disks, network adapters, memory, and CPU presentation. KVM accelerates virtualization in the kernel, while QEMU supplies much of the userspace VM device model.
```

```quiz score
Q: 1.018. Where does KVM operate within the system?
* In the kernel space
- In user space as an application
- On a separate management server
- In the cloud
> KVM is implemented as a Linux kernel virtualization facility, so it operates in kernel space. That kernel integration is what allows Linux to act as a type 1-style hypervisor for virtualized workloads.
```

```quiz score
Q: 1.019. Where do virtual machines run as processes?
* As QEMU processes in user space
- In the VDSM daemon
- On the oVirt engine server
- In kernel space as KVM modules
> The guest VM workload runs as a QEMU process in user space, with KVM support in the kernel providing acceleration. This split is central to understanding the OLVM/KVM architecture.
```

```quiz score
Q: 1.020. What is the relationship between VDSM and libvirt?
- VDSM replaces libvirt completely
- libvirt runs inside VDSM
* VDSM relies on libvirt to manage the lifecycle of VMs and collect statistics
- They operate independently without interaction
> VDSM relies on libvirt as the management layer for VM lifecycle actions and statistics collection. VDSM is the OLVM host agent; libvirt is the lower-level hypervisor management API it uses.
```

## Task 2: Describe Hosted-Engine

```quiz score
Q: 1.021. What is a Self-Hosted Engine in Oracle Linux Virtualization Manager?
- An engine that requires a dedicated physical server
* A management engine that runs as a VM directly on the virtualization hosts
- A backup engine for disaster recovery
- An engine that runs in Oracle Cloud Infrastructure
> A Self-Hosted Engine places the OLVM management engine inside a VM running on the virtualization hosts it manages. This differs from a standalone deployment where the engine runs on a separate dedicated management server.
```

```quiz score
Q: 1.022. What is the primary benefit of using a Self-Hosted Engine deployment?
- It requires Oracle Linux Enterprise Kernel
* It eliminates the need for a separate management server
- It increases the complexity of deployment
- It requires more hardware resources
> The main benefit is that a separate physical management server is not required. The engine VM runs on the virtualization infrastructure itself, reducing the need for dedicated management hardware.
```

```quiz score
Q: 1.023. A self-hosted engine VM's host fails. What provides the opportunity to recover management on another host?
- The browser reconstructs the management database from its cache
* HA agents can restart the engine VM on an eligible host with access to its shared storage
- All application VMs automatically become replacement engine servers
- A complete independent engine is always running on every host
> The architecture describes an engine VM on shared storage and host-side HA agents. Resilience depends on another eligible host and accessible storage; the engine is not duplicated across all hosts.
```

```quiz score
Q: 1.024. In a Self-Hosted Engine deployment, where is the Engine VM stored?
* On shared storage accessible by all hosts
- On the local disk of the first host
- In Oracle Cloud Infrastructure
- On a USB drive connected to the host
> The Engine VM must be placed on shared storage so eligible hosts can access and restart it if needed. Local-only storage on one host would prevent other hosts from taking over management of the hosted engine VM.
```

```quiz score
Q: 1.025. What component monitors and manages the Self-Hosted Engine VM on each host?
- PostgreSQL database
- oVirt engine service
- VDSM daemon
* HA agent (ovirt-ha-agent)
> The `ovirt-ha-agent` monitors the hosted engine VM and helps manage high availability behavior on each participating host. VDSM manages host operations, but the HA agent is the component specifically responsible for hosted engine monitoring and failover coordination.
```

## Task 3: Describe Data Warehouse and Databases

```quiz score
Q: 1.026. What is the name of the second database used for historical data?
- engine_history
- dwh_database
* ovirt_engine_history
- historical_data
> `ovirt_engine_history` is the historical database used by the Data Warehouse component. It stores time-series and historical information that can be used for reporting and monitoring.
```

```quiz score
Q: 1.027. What must be installed to create the ovirt_engine_history database?
- postgresql-history package
- engine-history module
* ovirt-engine-dwh package
- ovirt-database-tools
> The `ovirt-engine-dwh` package provides the Data Warehouse component and creates/uses the history database. Without DWH, the historical reporting database is not part of the basic engine-only role.
```

```quiz score
Q: 1.028. What is the primary purpose of the Data Warehouse (DWH) component?
- To manage the storage pool metadata
- To backup the engine database
* To collect and store historical performance and usage data
- To store virtual machine disk images
> DWH collects and stores historical performance, inventory, and usage data. It is not where VM disk images live and it is not a replacement for engine database backup.
```

```quiz score
Q: 1.029. Why should the operational engine database and DWH be treated as different data sources?
* The operational database serves management state while DWH supports collected historical reporting
- The operational database is only a browser cache
- Both are copies of every VM disk sector
- DWH automatically contains every guest application's live tables
> The architecture course separates management data from historical reporting. Choose the data source that fits the operational or monitoring question.
```

## Task 4: Describe Administration Interfaces

```quiz score
Q: 1.030. Which three portals are available in Oracle Linux Virtualization Manager? Select the option listing the complete correct set.
- Administration Portal; Developer Portal; Monitoring Portal
* Administration Portal; VM Portal; Monitoring Portal
- Administration Portal; VM Portal; Developer Portal
- Developer Portal; VM Portal; Monitoring Portal
> OLVM provides the Administration Portal for administrators, the VM Portal for VM users, and the Monitoring Portal for health and performance visibility. Developer and database portals are not standard OLVM portals in this context.
```

```quiz score
Q: 1.031. Which interface should an infrastructure administrator use to manage clusters, hosts, storage, and permissions?
- A guest application's login page
* Administration Portal
- The guest operating system's disk manager alone
- The VM console alone
> The Administration Portal manages the virtualization infrastructure and its permissions.
```

```quiz score
Q: 1.032. What is needed to access the Administration Portal from a workstation?
- Direct attachment to the VM storage LUN
* A supported browser with network access to the engine's portal URL
- Membership in every guest operating system's local administrator group
- A QEMU process running locally on the workstation
> Portal access uses the engine's web interface. Network reachability, browser support, authentication, and certificate trust must be considered.
```

```quiz score
Q: 1.033. What is the primary purpose of the VM Portal?
- To provide full administrative control over all OLVM components
- To configure storage domains
* To cater to users who primarily engage with virtual machines, offering a user-friendly interface for basic VM management
- To monitor system performance only
> The VM Portal is intended for users who primarily need to interact with virtual machines rather than administer the entire platform. It exposes user-focused VM actions while leaving broader infrastructure control to administrators.
```

```quiz score
Q: 1.034. A user opens VM Portal but cannot perform an expected VM operation. What should the administrator check first?
- Whether every physical switch has been restarted
- Whether the user's browser has a copy of the engine database
* The user's effective role and permissions on the relevant resource
- Whether the VM has been made the SPM
> VM Portal capabilities depend on the permissions assigned by administrators. Merely reaching the portal does not grant all VM operations.
```

```quiz score
Q: 1.035. Who determines the capabilities available to users within the VM Portal?
- Oracle support team
- The oVirt engine automatically
- The users themselves
* System administrators
> System administrators assign roles and permissions that determine what VM Portal users can do. The portal does not automatically grant full capabilities to users by default.
```

```quiz score
Q: 1.036. Which pair names the graphical console transports described in the recorded VM Portal training?
* SPICE and VNC
- NFS and CHAP
- SSH and iSCSI
- SNMP and SMTP
> The recorded course describes SPICE and VNC for console access. Connecting to an RDP service inside a guest is a separate access method and depends on guest configuration.
```

```quiz score
Q: 1.037. What is the primary purpose of the Monitoring Portal in OLVM?
* To empower administrators with comprehensive monitoring capabilities for health and performance
- To manage user permissions
- To create new virtual machines
- To configure storage domains
> The Monitoring Portal is focused on health, performance, and operational visibility. It helps administrators understand resource usage and trends rather than creating VMs or configuring storage directly.
```

```quiz score
Q: 1.038. Which open-source analytics platform does Oracle Linux Virtualization Manager integrate with for enhanced monitoring?
- Kibana
- Nagios
- Prometheus
* Grafana
> OLVM integrates with Grafana for enhanced monitoring dashboards and analytics. Grafana is the dashboarding and visualization platform used here, distinct from tools such as Kibana or Nagios.
```

```quiz score
Q: 1.039. Where does Grafana retrieve its monitoring data from in OLVM?
* From the engine data warehouse
- Directly from the KVM hosts
- From virtual machine logs
- From network switches
> Grafana gets OLVM monitoring data from the engine data warehouse, which stores historical metrics. Pulling from DWH allows dashboards to show trends and history rather than only immediate host-local data.
```

```quiz score
Q: 1.040. What can administrators create in Grafana for monitoring?
- User accounts
* Customized dashboards tailored to specific monitoring needs
- Virtual machines
- Storage domains
> Administrators use Grafana to create customized dashboards for the metrics they care about. Grafana visualizes and organizes monitoring data; it does not create VMs, storage domains, or user accounts.
```

```quiz score
Q: 1.041. Which metrics can be monitored through Grafana dashboards? Select the option listing the complete correct set.
- User login history; Memory usage; Storage capacity
- CPU utilization; User login history; Storage capacity
- CPU utilization; Memory usage; User login history
* CPU utilization; Memory usage; Storage capacity
> Grafana dashboards commonly show infrastructure metrics such as CPU utilization, memory usage, and storage capacity. Login history and VM creation timestamps may exist in other records, but they are not the main performance metrics in this question.
```

```quiz score
Q: 1.042. What is Cockpit in Oracle Linux Virtualization Manager?
* A web interface that empowers users to monitor KVM host resources and perform administrative tasks
- A backup solution
- A database management tool
- A virtual machine migration tool
> Cockpit is a web-based host administration interface for Linux systems. In an OLVM environment, it can be used to monitor KVM host resources and perform selected host administration tasks.
```

```quiz score
Q: 1.043. Cockpit is unavailable on a KVM host. Which initial check is useful?
* Verify that Cockpit is installed, enabled, and reachable on that host
- Recreate the engine history database immediately
- Delete the host's VM disks
- Make the host the only SPM permanently
> Cockpit is a host management web service. Check its availability and connectivity rather than assuming every installation exposes it automatically.
```

```quiz score
Q: 1.044. Which two methods can be used to access the Cockpit web interface? Select the option listing the complete correct set.
- Through email notifications; By establishing a direct connection to the host
* Through the Administration Portal; By establishing a direct connection to the host
- Through the Administration Portal; Through email notifications
- Through the VM console; By establishing a direct connection to the host
> Cockpit can be reached from integration points in the Administration Portal or by directly connecting to the host's Cockpit web service. Email, SNMP, and VM consoles are not Cockpit access methods.
```

```quiz score
Q: 1.045. Which Cockpit information is most relevant when troubleshooting a busy KVM host?
- Only the names of external user groups
- Only the engine's certificate subject
- Only the guest template descriptions
* Host CPU, memory, disk, and network activity
> Cockpit shows host resource activity. It complements engine-wide views and can help identify host-level resource pressure.
```

```quiz score
Q: 1.046. What authentication systems can Oracle Linux Virtualization Manager integrate with? Select the option listing the complete correct set.
- OAuth only; Active Directory
- Local user accounts only; Active Directory
* LDAP; Active Directory
- LDAP; Local user accounts only
> OLVM can integrate with directory services such as LDAP and Active Directory for external authentication. Local accounts may exist, but enterprise identity integration usually uses LDAP-compatible directories or AD.
```

## Task 5: Explain Data Centers, Clusters and Hosts

```quiz score
Q: 1.047. What does a Data Center represent in Oracle Linux Virtualization Manager?
- A type of storage domain
* The highest organizational level encapsulating clusters, hosts, storage, and network configuration
- A physical building location
- A virtual machine template
> A Data Center is the top-level OLVM container for the virtualization resources that work together. It groups clusters, hosts, storage domains, and network configuration into one logical management boundary.
```

```quiz score
Q: 1.048. Which four components does a Data Center encapsulate? Select the option listing the complete correct set.
- Clusters; Applications; Storage domains; Network configuration
- Clusters; Hosts; Applications; Network configuration
* Clusters; Hosts; Storage domains; Network configuration
- Applications; Hosts; Storage domains; Network configuration
> A Data Center contains the infrastructure pieces needed to run workloads: clusters, hosts, storage domains, and network configuration. Applications and user accounts are managed separately and are not the core Data Center components.
```

```quiz score
Q: 1.049. Can a single oVirt engine manage multiple Data Centers?
- Only with special licensing
- No, only one Data Center per engine
- Only in Self-Hosted Engine mode
* Yes, multiple Data Centers per engine
> One oVirt engine can manage more than one Data Center. This lets a single OLVM management plane organize separate environments with different storage types, clusters, or operational boundaries.
```

```quiz score
Q: 1.050. Which two storage types can be configured for a Data Center? Select the option listing the complete correct set.
* Local storage; Shared storage
- Tape storage; Shared storage
- Cloud storage; Shared storage
- Local storage; Cloud storage
> A Data Center can be configured for local storage or shared storage. Shared storage enables clustered capabilities such as migration, while local storage is intended for simpler single-host use cases.
```

```quiz score
Q: 1.051. Can local and shared storage domains be mixed in the same Data Center?
- Only with Oracle support approval
- Only in production environments
* No, they cannot be mixed
- Yes, they can be freely mixed
> Local and shared storage cannot be mixed in the same Data Center. The storage type is a Data Center-level design choice because it affects clustering, migration, and how hosts access VM disks.
```

```quiz score
Q: 1.052. In the recorded course, which storage protocols can be used for SHARED storage in a Data Center? Select the option listing the complete correct set.
- iSCSI; NFS; SMB/CIFS; POSIX; Gluster File System
* iSCSI; NFS; Fiber Channel (FC); POSIX; Gluster File System
- iSCSI; SMB/CIFS; Fiber Channel (FC); POSIX; Gluster File System
- SMB/CIFS; NFS; Fiber Channel (FC); POSIX; Gluster File System
> Shared storage can use iSCSI, NFS, Fibre Channel, POSIX-compliant storage, or GlusterFS. SMB/CIFS is not one of the shared storage protocols identified for this OLVM objective. These examples describe the recorded training baseline; verify applicable capabilities in the practice environment.
```

```quiz score
Q: 1.053. When would you typically use a local storage Data Center?
* Testing or development environments
- Production enterprise environments
- Disaster recovery sites
- High-availability architectures
> Local storage Data Centers are typically used for testing or development because they are simpler but do not provide the same migration and high-availability capabilities as shared storage designs.
```

```quiz score
Q: 1.054. What is the important architectural restriction of a local-storage data center in the course?
- It can freely share its local disks with every remote host
- It removes the engine's need to manage the host
* It uses a single-host cluster rather than shared storage across multiple hosts
- It provides cross-host migration without shared disk access
> Local storage is tied to one host. The course contrasts this limited arrangement with shared-storage data centers used for multi-host operation.
```

```quiz score
Q: 1.055. Does a default Data Center get created during OLVM installation?
- Only if shared storage is detected
* Yes, one default Data Center is created
- Only in Self-Hosted Engine deployments
- No, you must manually create the first one
> OLVM creates a default Data Center during installation. Administrators can later modify it or create additional Data Centers as the environment grows.
```

```quiz score
Q: 1.056. What is a Cluster in Oracle Linux Virtualization Manager?
- A type of virtual machine
- A physical rack of servers
- A backup server group
* A group of hosts that share the same storage domains and network configuration
> A cluster is a group of compatible hosts that share the same storage domains and network configuration. This shared configuration is what allows VMs to run consistently across hosts in the cluster.
```

```quiz score
Q: 1.057. What must all hosts within a cluster share? Select the option listing the complete correct set.
- The same RAM capacity; The same network configuration
* The same storage domains; The same network configuration
- The same storage domains; The same CPU type
- The same CPU type; The same network configuration
> Hosts in a cluster must share the same storage domains and network configuration so VMs can move or run predictably across those hosts. Identical RAM capacity is not required, and CPU compatibility is handled through cluster CPU settings rather than identical hardware in every detail.
```

```quiz score
Q: 1.058. Where do you access the Clusters management interface in the Administration Portal?
- Administration -> Clusters
* Compute -> Clusters
- Storage -> Clusters
- Network -> Clusters
> Clusters are managed from Compute -> Clusters in the Administration Portal. OLVM treats clusters as compute organization objects, not storage or network objects.
```

```quiz score
Q: 1.059. What information is displayed in the Cluster list view? Select the option listing the complete correct set.
* Host count; VM count
- Host count; Administrator names
- IP addresses; VM count
- Administrator names; VM count
> The cluster list view summarizes operational scale, including host count and VM count. Administrator names and IP addresses are not the main cluster summary fields tested here.
```

```quiz score
Q: 1.060. Does a default cluster get created during OLVM installation?
- Only if hosts are detected
- No, must be manually created
- Only for Self-Hosted Engine
* Yes, in the default Data Center
> A default cluster is created inside the default Data Center during installation. This gives administrators a starting structure before they add custom clusters or hosts.
```

```quiz score
Q: 1.061. Why is cluster CPU compatibility important when planning live migration?
- A VLAN ID must match the processor core count
- The destination browser must use the same graphics card as the source browser
- All VM disk names must equal the physical CPU model
* Eligible hosts must provide compatible CPU capabilities for the VM's cluster configuration
> Cluster CPU configuration provides a compatibility baseline. Host membership alone does not remove migration compatibility requirements.
```

```quiz score
Q: 1.062. What are the two major types of physical hosts in OLVM? Select the option listing the complete correct set.
- Network host; KVM host
- Engine host; Storage host
- Storage host; KVM host
* Engine host; KVM host
> The two major host roles are the Engine host and KVM hosts. The engine host provides management, while KVM hosts provide the compute capacity where VMs run.
```

```quiz score
Q: 1.063. What is the primary function of the Engine host?
- Managing network switches
* Providing central management and administration tools
- Running virtual machines
- Storing VM disk images
> The Engine host runs the central management and administration services for OLVM. It coordinates the environment but is not primarily the place where production VMs execute.
```

```quiz score
Q: 1.064. What is the primary function of KVM hosts?
- Managing the Administration Portal
- Running the database
* Hosting and running virtual machines
- Storing backup data
> KVM hosts provide the hypervisor capacity for virtual machines. They run VM workloads through QEMU/KVM and are managed by the OLVM engine through VDSM.
```

```quiz score
Q: 1.065. Can a single OLVM engine manage multiple KVM hosts?
- Only in clustered mode
* Yes, a single engine can manage multiple hosts
- Only with enterprise licensing
- No, only one host per engine
> A single OLVM engine can manage multiple KVM hosts. Centralized management of many hosts is one of the core reasons to use OLVM instead of managing each KVM host independently.
```

```quiz score
Q: 1.066. Where in the Administration Portal do you manage hosts?
- Administration -> Hosts
* Compute -> Hosts
- Network -> Hosts
- Storage -> Hosts
> Hosts are managed from Compute -> Hosts in the Administration Portal. This reflects their role as compute resources that run virtual machines.
```

```quiz score
Q: 1.067. A host is slow while several VMs run on it. What information should the administrator inspect in the host view?
- Only the user's preferred portal language
- Only the template logo
* Host utilization, active VMs, and its cluster and data center context
- Only the engine's alternate hostname
> The host view relates utilization to the workloads and infrastructure context. These details are more useful than an isolated icon or name.
```

```quiz score
Q: 1.068. What does the "Maintenance Mode" do for a host?
- Increases host performance
- Resets host configuration
* Prepares the host for maintenance by migrating VMs away
- Shuts down the host permanently
> Maintenance mode prepares a host for planned maintenance by moving or requiring migration of running VMs away from it. This lets administrators patch or service the host without leaving workloads on it.
```

## Task 6: Explain Storage Pool Manager, Networks and Storage

```quiz score
Q: 1.069. What is the purpose of converting a host to SPM (Storage Pool Manager) role?
* To manage storage domains within the Data Center
- To configure networks
- To enable backups
- To increase VM performance
> Converting or assigning a host as SPM makes it responsible for storage domain metadata coordination in the Data Center. The SPM role is about storage management, not network configuration or backups.
```

```quiz score
Q: 1.070. What is the Storage Pool Manager (SPM)?
- A type of storage domain
- A backup application
- A physical storage device
* A management role assigned to one host in the Data Center
> The Storage Pool Manager is a management role assigned to one host in a Data Center. It is not a separate physical device or storage domain type.
```

```quiz score
Q: 1.071. How many hosts can have the SPM role in a single Data Center at one time?
- Two hosts for redundancy
* Only one host at a time
- All hosts share the SPM role
- Up to five hosts
> Only one host can hold the SPM role in a Data Center at a time. Having a single SPM prevents conflicting metadata changes against shared storage.
```

```quiz score
Q: 1.072. What is the primary responsibility of the SPM?
- Managing network traffic
- Running virtual machines
- User authentication
* Coordinating all metadata changes like creating disks or snapshots within the Data Center
> The SPM coordinates storage metadata changes such as disk creation, snapshot operations, and other storage domain updates. This serialization protects consistency across hosts accessing shared storage.
```

```quiz score
Q: 1.073. Only the SPM coordination role is temporarily unavailable; VM hosts and disks remain reachable. What is the likely effect?
* Running VM I/O can continue while metadata operations may wait for SPM recovery
- The engine deletes all snapshots automatically
- Every guest must immediately stop solely because the role is unavailable
- The manager becomes a replacement storage server
> SPM coordinates storage metadata changes. Losing the role is different from losing a host executing a VM or losing the storage itself.
```

```quiz score
Q: 1.074. In the recorded course, which three types of storage domains exist in OLVM? Select the option listing the complete correct set.
- Boot Domain; ISO Domain; Export Domain
- Data Domain; ISO Domain; Boot Domain
* Data Domain; ISO Domain; Export Domain
- Data Domain; Boot Domain; Export Domain
> OLVM uses Data, ISO, and Export storage domain types. Data domains hold VM disks, ISO domains hold installation media, and Export domains support legacy export/import workflows. These examples describe the recorded training baseline; verify applicable capabilities in the practice environment.
```

```quiz score
Q: 1.075. Can a storage domain be shared across multiple Data Centers?
* No, a storage domain belongs to only one Data Center
- Yes, freely shared
- Only with special configuration
- Only ISO domains can be shared
> A storage domain belongs to only one Data Center at a time. This ownership model prevents conflicting metadata and access assumptions between separate Data Center boundaries.
```

```quiz score
Q: 1.076. What must be configured for a Data Center before it can be initialized?
- At least one host
- At least one cluster
* At least one storage domain (Data Domain)
- At least one virtual machine
> A Data Center needs at least one Data storage domain before it can be initialized for normal VM storage operations. Without a data domain, there is nowhere for VM disks and key storage metadata to reside.
```

```quiz score
Q: 1.077. A running VM uses shared disks and needs to migrate to another host. What storage check matters?
- The portal users share the same password
- The engine's OS disk is larger than the VM disk
- The destination's local home directory has the same name
* The destination can access the same active storage containing the VM's disks
> Live host migration requires access to the VM's disks from the destination, alongside other compute and network prerequisites.
```

```quiz score
Q: 1.078. What happens with thin-provisioned disks?
- Full disk space is allocated immediately
- Disk cannot grow beyond initial size
* Disk space is allocated only as data is written, starting with minimal space
- Disk is stored on local storage only
> Thin-provisioned disks start small and grow as the guest writes data. This improves storage efficiency, but administrators must monitor capacity to avoid overcommitment problems.
```

```quiz score
Q: 1.079. Why might an administrator choose a pre-allocated disk for an I/O-sensitive VM?
- To enable disk access without a storage domain
- To guarantee zero storage latency in every workload
* To allocate disk space in advance and reduce allocation work during subsequent writes
- To make storage capacity monitoring unnecessary
> Pre-allocation moves allocation work earlier. It is a tradeoff involving capacity and workload behavior, not a universal performance guarantee.
```

## Task 7: Explain Event Logging and Notifications

```quiz score
Q: 1.080. Which two primary log files are used in OLVM? Select the option listing the complete correct set.
- system.log; vdsm.log
* engine.log; vdsm.log
- engine.log; system.log
- kernel.log; vdsm.log
> `engine.log` records engine-side activity, while `vdsm.log` records host-side VDSM activity. These two logs are primary troubleshooting sources across the management and host layers.
```

```quiz score
Q: 1.081. Where is the engine log file typically located?
* /var/log/ovirt-engine/engine.log
- /var/log/messages
- /var/log/olvm/engine.log
- /etc/ovirt-engine/engine.log
> The engine log is typically located at `/var/log/ovirt-engine/engine.log`. This is the main place to inspect engine-side events, errors, and management activity.
```

```quiz score
Q: 1.082. What does the VDSM log file contain?
- Database queries
- User login activities
- Network traffic logs
* Events and operations related to individual hosts including VM startups, migrations, and storage activities
> The VDSM log contains host-level operations such as VM starts, migrations, and storage activities. It is the key log when troubleshooting behavior on an individual KVM host.
```

```quiz score
Q: 1.083. What tool is used to collect logs for sharing with Oracle support?
* ovirt-log-collector
- sosreport
- logcollector
- engine-backup
> `ovirt-log-collector` gathers relevant OLVM logs and diagnostic data for support or troubleshooting. It is designed for collecting OLVM-specific information across components.
```

```quiz score
Q: 1.084. Where can administrators view alerts and events in the Administration Portal?
- Dashboard only
- System Settings
- Events tab
* Notification Drawer
> The Notification Drawer in the Administration Portal shows alerts and events for administrators. It provides quick visibility into issues without requiring direct log-file inspection first.
```

```quiz score
Q: 1.085. Which two notification methods can be configured in OLVM? Select the option listing the complete correct set.
- Email notifications; SMS messages
* Email notifications; SNMP traps
- Slack integration; SNMP traps
- SMS messages; SNMP traps
> OLVM can send notifications through email and SNMP traps. These methods allow operational events to reach administrators or external monitoring systems.
```

## Acknowledgements

- **Author** - OLVM training team
- **Sources** - User-supplied training transcripts, section quiz-bank files, and Lab 7 OLVM content
- **Last Updated** - October 2026
