# Section 3: Core Components
## Introduction

This lab contains the Section 3 practice questions for **Core Components** from the OLVM Exam 1Z0-1170 quiz bank.

Estimated Time: 65 minutes

```quiz-config
passing: 75
badge: images/badge.png
```

### Objectives

In this lab, you will review the OLVM concepts covered by this exam objective and check your understanding with practice questions.

## Practice Questions

```quiz

Q: 1. What does a Data Center represent in Oracle Linux Virtualization Manager?
- A. A physical building location
* B. The highest organizational level encapsulating clusters, hosts, storage, and network configuration
- C. A type of storage domain
- D. A virtual machine template
> A Data Center is the top-level OLVM container for the virtualization resources that work together. It groups clusters, hosts, storage domains, and network configuration into one logical management boundary.

Q: 2. Which four components does a Data Center encapsulate? (Choose 4)
* A. Clusters
- B. Applications
* C. Hosts
* D. Storage domains
* E. Network configuration
- F. User accounts
> A Data Center contains the infrastructure pieces needed to run workloads: clusters, hosts, storage domains, and network configuration. Applications and user accounts are managed separately and are not the core Data Center components.

Q: 3. Can a single oVirt engine manage multiple Data Centers?
- A. No, only one Data Center per engine
* B. Yes, multiple Data Centers per engine
- C. Only in Self-Hosted Engine mode
- D. Only with special licensing
> One oVirt engine can manage more than one Data Center. This lets a single OLVM management plane organize separate environments with different storage types, clusters, or operational boundaries.

Q: 4. What is the maximum character limit for a Data Center name?
- A. 20 characters
- B. 30 characters
* C. 40 characters
- D. 50 characters
> Data Center names are limited to 40 characters in this quiz objective. The limit is a naming constraint, not a capacity or architecture limit.

Q: 5. Which two storage types can be configured for a Data Center? (Choose 2)
* A. Local storage
- B. Cloud storage
* C. Shared storage
- D. Tape storage
> A Data Center can be configured for local storage or shared storage. Shared storage enables clustered capabilities such as migration, while local storage is intended for simpler single-host use cases.

Q: 6. Can local and shared storage domains be mixed in the same Data Center?
- A. Yes, they can be freely mixed
* B. No, they cannot be mixed
- C. Only in production environments
- D. Only with Oracle support approval
> Local and shared storage cannot be mixed in the same Data Center. The storage type is a Data Center-level design choice because it affects clustering, migration, and how hosts access VM disks.

Q: 7. Which storage protocols can be used for SHARED storage in a Data Center? (Choose 5)
* A. iSCSI
- B. SMB/CIFS
* C. NFS
* D. Fiber Channel (FC)
* E. POSIX
* F. Gluster File System
> Shared storage can use iSCSI, NFS, Fibre Channel, POSIX-compliant storage, or GlusterFS. SMB/CIFS is not one of the shared storage protocols identified for this OLVM objective.

Q: 8. When would you typically use a local storage Data Center?
- A. Production enterprise environments
* B. Testing or development environments
- C. High-availability architectures
- D. Disaster recovery sites
> Local storage Data Centers are typically used for testing or development because they are simpler but do not provide the same migration and high-availability capabilities as shared storage designs.

Q: 9. What happens when a Data Center is configured with local storage? (Choose 3)
* A. It supports a single host only
- B. It enables high-availability features
* C. It creates a single non-shareable cluster
* D. Cluster management is not available
- E. VM migration is fully supported
> A local storage Data Center is restricted to a single host and a single non-shareable cluster, so normal cluster management and migration capabilities are not available. This is why local storage is not the usual choice for HA production environments.

Q: 10. Does a default Data Center get created during OLVM installation?
* A. Yes, one default Data Center is created
- B. No, you must manually create the first one
- C. Only in Self-Hosted Engine deployments
- D. Only if shared storage is detected
> OLVM creates a default Data Center during installation. Administrators can later modify it or create additional Data Centers as the environment grows.

Q: 11. What is a Cluster in Oracle Linux Virtualization Manager?
- A. A physical rack of servers
* B. A group of hosts that share the same storage domains and network configuration
- C. A type of virtual machine
- D. A backup server group
> A cluster is a group of compatible hosts that share the same storage domains and network configuration. This shared configuration is what allows VMs to run consistently across hosts in the cluster.

Q: 12. What must all hosts within a cluster share? (Choose 2)
- A. The same CPU type
- B. The same RAM capacity
* C. The same storage domains
* D. The same network configuration
> Hosts in a cluster must share the same storage domains and network configuration so VMs can move or run predictably across those hosts. Identical RAM capacity is not required, and CPU compatibility is handled through cluster CPU settings rather than identical hardware in every detail.

Q: 13. Where do you access the Clusters management interface in the Administration Portal?
- A. Storage -> Clusters
* B. Compute -> Clusters
- C. Network -> Clusters
- D. Administration -> Clusters
> Clusters are managed from Compute -> Clusters in the Administration Portal. OLVM treats clusters as compute organization objects, not storage or network objects.

Q: 14. What information is displayed in the Cluster list view? (Choose 2)
* A. Host count
- B. Administrator names
* C. VM count
- D. IP addresses
> The cluster list view summarizes operational scale, including host count and VM count. Administrator names and IP addresses are not the main cluster summary fields tested here.

Q: 15. Does a default cluster get created during OLVM installation?
* A. Yes, in the default Data Center
- B. No, must be manually created
- C. Only for Self-Hosted Engine
- D. Only if hosts are detected
> A default cluster is created inside the default Data Center during installation. This gives administrators a starting structure before they add custom clusters or hosts.

Q: 16. Which CPU architecture types can be selected for a cluster? (Choose 3)
* A. x86-64
- B. ARM64
* C. ppc64
* D. s390x
- E. MIPS
> Cluster CPU architecture options include x86-64, ppc64, and s390x. These options define the architecture compatibility for hosts and VMs in that cluster.

Q: 17. What does the "Memory Overcommit" optimization feature allow?
- A. Using more physical memory than allocated
* B. Allocating more virtual memory than physical memory available
- C. Sharing memory between clusters
- D. Compressing VM memory
> Memory overcommit allows the platform to allocate more virtual memory to VMs than the physical memory immediately available. This increases density, but it must be managed carefully to avoid performance pressure.

Q: 18. Which three scheduling policies can be configured for a cluster? (Choose 3)
* A. Even distribution
- B. Random distribution
* C. Power saving
- D. Cost optimization
* E. Strict (No overcommit)
> Cluster scheduling policies include even distribution, power saving, and strict/no-overcommit behavior. These policies influence how OLVM places and balances VMs across hosts.

Q: 19. What is the purpose of the Fencing Policy in a cluster?
- A. To encrypt network traffic
* B. To isolate and restart a non-responsive host
- C. To limit VM creation
- D. To manage storage quotas
> Fencing isolates or restarts a non-responsive host to protect the environment from split-brain or unsafe resource ownership. It is a recovery and safety mechanism, not an encryption or quota feature.

Q: 20. What is the default time delay before fencing is initiated?
- A. 30 seconds
* B. 60 seconds
- C. 90 seconds
- D. 120 seconds
> The default fencing delay in this quiz objective is 60 seconds. The delay gives the system time to confirm the host condition before initiating disruptive fencing actions.

Q: 21. What do Migration Policies define?
- A. How VMs are created
- B. Storage allocation rules
* C. Thresholds for automatic VM migration to balance load and number of parallel migrations
- D. Network bandwidth limits
> Migration policies define thresholds and parallelism for VM migration, including automatic balancing behavior. They control when and how migration happens rather than how VMs are created.

Q: 22. What is the purpose of Positive Affinity rules for VMs?
- A. VMs are placed on different hosts
* B. VMs are placed on the same host
- C. VMs are suspended
- D. VMs are migrated frequently
> Positive affinity keeps selected VMs together on the same host. This can be useful when workloads benefit from locality or when related VMs should run near each other.

Q: 23. What is the purpose of Negative Affinity rules for VMs?
- A. VMs are placed on the same host
* B. VMs are placed on different hosts
- C. VMs cannot be started
- D. VMs share the same storage
> Negative affinity separates selected VMs onto different hosts. This improves resilience for workloads that should not fail together if one host has a problem.

Q: 24. What does the Enforcement Mode setting determine for affinity rules?
- A. How many VMs can be created
* B. How strictly the affinity rules are enforced
- C. Storage allocation limits
- D. Network bandwidth
> Enforcement mode controls whether affinity rules are hard requirements or soft preferences. This matters because strict enforcement can prevent VM placement when the rule cannot be satisfied.

Q: 25. If the Enforcement Mode for an affinity rule is DISABLED, what happens?
- A. The rules are strictly enforced
* B. The rules are preferences but not strictly enforced
- C. The rules are ignored completely
- D. VMs cannot be started
> When enforcement is disabled, affinity rules act as placement preferences. OLVM can violate the preference if needed to start or place VMs, so the rule is not treated as mandatory.

Q: 26. What are Affinity Labels used for?
- A. Encrypting VM data
* B. Tagging and categorizing VMs and hosts to group them logically
- C. Naming storage domains
- D. Configuring network VLANs
> Affinity labels tag VMs and hosts so administrators can group them logically for placement behavior. Labels simplify rule management by applying intent to categories rather than only individual objects.

Q: 27. Which two types of affinity rules can be defined? (Choose 2)
* A. VM to VM affinity rules
- B. Storage to VM affinity rules
* C. VM to host affinity rules
- D. Network to host affinity rules
> OLVM supports VM-to-VM and VM-to-host affinity rules. These rules control whether VMs should be placed together, separated, or associated with particular hosts.

Q: 28. What are the two major types of physical hosts in OLVM? (Choose 2)
* A. Engine host
- B. Storage host
- C. Network host
* D. KVM host
> The two major host roles are the Engine host and KVM hosts. The engine host provides management, while KVM hosts provide the compute capacity where VMs run.

Q: 29. What is the primary function of the Engine host?
- A. Running virtual machines
* B. Providing central management and administration tools
- C. Storing VM disk images
- D. Managing network switches
> The Engine host runs the central management and administration services for OLVM. It coordinates the environment but is not primarily the place where production VMs execute.

Q: 30. What is the primary function of KVM hosts?
- A. Managing the Administration Portal
* B. Hosting and running virtual machines
- C. Storing backup data
- D. Running the database
> KVM hosts provide the hypervisor capacity for virtual machines. They run VM workloads through QEMU/KVM and are managed by the OLVM engine through VDSM.

Q: 31. Can a single OLVM engine manage multiple KVM hosts?
- A. No, only one host per engine
* B. Yes, a single engine can manage multiple hosts
- C. Only in clustered mode
- D. Only with enterprise licensing
> A single OLVM engine can manage multiple KVM hosts. Centralized management of many hosts is one of the core reasons to use OLVM instead of managing each KVM host independently.

Q: 32. Where in the Administration Portal do you manage hosts?
- A. Storage -> Hosts
* B. Compute -> Hosts
- C. Network -> Hosts
- D. Administration -> Hosts
> Hosts are managed from Compute -> Hosts in the Administration Portal. This reflects their role as compute resources that run virtual machines.

Q: 33. What information is displayed in the host list view? (Choose 4)
* A. Host name or IP address
* B. Cluster name
* C. Data Center name
* D. Active virtual machines
- E. CPU temperature
* F. Memory used
> The host list view shows operational identity and placement details such as host name or IP, cluster, Data Center, active VMs, and memory usage. CPU temperature is not one of the standard host list fields tested here.

Q: 34. What does the "Maintenance Mode" do for a host?
- A. Shuts down the host permanently
* B. Prepares the host for maintenance by migrating VMs away
- C. Increases host performance
- D. Resets host configuration
> Maintenance mode prepares a host for planned maintenance by moving or requiring migration of running VMs away from it. This lets administrators patch or service the host without leaving workloads on it.

Q: 35. What is the purpose of converting a host to SPM (Storage Pool Manager) role?
- A. To increase VM performance
* B. To manage storage domains within the Data Center
- C. To enable backups
- D. To configure networks
> Converting or assigning a host as SPM makes it responsible for storage domain metadata coordination in the Data Center. The SPM role is about storage management, not network configuration or backups.

Q: 36. What is the Storage Pool Manager (SPM)?
- A. A physical storage device
* B. A management role assigned to one host in the Data Center
- C. A type of storage domain
- D. A backup application
> The Storage Pool Manager is a management role assigned to one host in a Data Center. It is not a separate physical device or storage domain type.

Q: 37. How many hosts can have the SPM role in a single Data Center at one time?
- A. All hosts share the SPM role
* B. Only one host at a time
- C. Two hosts for redundancy
- D. Up to five hosts
> Only one host can hold the SPM role in a Data Center at a time. Having a single SPM prevents conflicting metadata changes against shared storage.

Q: 38. What is the primary responsibility of the SPM?
- A. Running virtual machines
* B. Coordinating all metadata changes like creating disks or snapshots within the Data Center
- C. Managing network traffic
- D. User authentication
> The SPM coordinates storage metadata changes such as disk creation, snapshot operations, and other storage domain updates. This serialization protects consistency across hosts accessing shared storage.

Q: 39. Does losing SPM functionality affect running VMs?
- A. Yes, all VMs stop immediately
* B. No, VMs continue running but storage operations are affected
- C. Only new VMs are affected
- D. VMs are automatically migrated
> If SPM functionality is lost, running VMs can continue because their execution is already active on hosts. New storage operations may be blocked or delayed until SPM is recovered or reassigned.

Q: 40. Which three types of storage domains exist in OLVM? (Choose 3)
* A. Data Domain
- B. Boot Domain
* C. ISO Domain
* D. Export Domain
- E. Template Domain
> OLVM uses Data, ISO, and Export storage domain types. Data domains hold VM disks, ISO domains hold installation media, and Export domains support legacy export/import workflows.

Q: 41. Can a storage domain be shared across multiple Data Centers?
- A. Yes, freely shared
* B. No, a storage domain belongs to only one Data Center
- C. Only ISO domains can be shared
- D. Only with special configuration
> A storage domain belongs to only one Data Center at a time. This ownership model prevents conflicting metadata and access assumptions between separate Data Center boundaries.

Q: 42. What must be configured for a Data Center before it can be initialized?
- A. At least one cluster
- B. At least one host
* C. At least one storage domain (Data Domain)
- D. At least one virtual machine
> A Data Center needs at least one Data storage domain before it can be initialized for normal VM storage operations. Without a data domain, there is nowhere for VM disks and key storage metadata to reside.

Q: 43. For VMs to be migrated between hosts, what storage requirement must be met?
- A. Each host needs local storage
* B. VMs must share the same storage domain
- C. Storage must be SSD-based
- D. VMs must have pre-allocated disks
> VM migration requires the VM's disks to be available to the destination host, which means the hosts must share the same storage domain. Local-only storage prevents normal live migration between hosts.

Q: 44. What is the default provisioning method for virtual disks?
- A. Pre-allocated
* B. Thin provisioning
- C. Thick provisioning
- D. Compressed
> Thin provisioning is the default virtual disk provisioning method. It allocates storage as data is written instead of reserving the entire virtual disk size immediately.

Q: 45. What happens with thin-provisioned disks?
- A. Full disk space is allocated immediately
* B. Disk space is allocated only as data is written, starting with minimal space
- C. Disk cannot grow beyond initial size
- D. Disk is stored on local storage only
> Thin-provisioned disks start small and grow as the guest writes data. This improves storage efficiency, but administrators must monitor capacity to avoid overcommitment problems.

Q: 46. When should pre-allocated disks be used?
- A. For test and development VMs
- B. For VMs with low I/O requirements
* C. For VMs with high levels of input/output operations
- D. Never, thin provisioning is always better
> Pre-allocated disks are better suited for high-I/O workloads because space is reserved up front. That avoids some runtime allocation overhead that can affect latency-sensitive workloads.

Q: 47. What benefit do pre-allocated disks provide for high I/O workloads?
- A. They use less storage space
* B. They maintain consistent performance by reducing latency from space allocation
- C. They enable faster VM migration
- D. They require less memory
> Pre-allocation can provide more consistent performance because the storage backing is already reserved. Thin disks save capacity, but allocation during writes can introduce extra latency.

Q: 48. Which two primary log files are used in OLVM? (Choose 2)
* A. engine.log
- B. system.log
* C. vdsm.log
- D. kernel.log
> `engine.log` records engine-side activity, while `vdsm.log` records host-side VDSM activity. These two logs are primary troubleshooting sources across the management and host layers.

Q: 49. Where is the engine log file typically located?
- A. /var/log/messages
* B. /var/log/ovirt-engine/engine.log
- C. /var/log/olvm/engine.log
- D. /etc/ovirt-engine/engine.log
> The engine log is typically located at `/var/log/ovirt-engine/engine.log`. This is the main place to inspect engine-side events, errors, and management activity.

Q: 50. What does the VDSM log file contain?
- A. User login activities
* B. Events and operations related to individual hosts including VM startups, migrations, and storage activities
- C. Network traffic logs
- D. Database queries
> The VDSM log contains host-level operations such as VM starts, migrations, and storage activities. It is the key log when troubleshooting behavior on an individual KVM host.

Q: 51. What tool is used to collect logs for sharing with Oracle support?
- A. logcollector
* B. ovirt-log-collector
- C. engine-backup
- D. sosreport
> `ovirt-log-collector` gathers relevant OLVM logs and diagnostic data for support or troubleshooting. It is designed for collecting OLVM-specific information across components.

Q: 52. Where can administrators view alerts and events in the Administration Portal?
- A. Events tab
* B. Notification Drawer
- C. Dashboard only
- D. System Settings
> The Notification Drawer in the Administration Portal shows alerts and events for administrators. It provides quick visibility into issues without requiring direct log-file inspection first.

Q: 53. Which two notification methods can be configured in OLVM? (Choose 2)
* A. Email notifications
- B. SMS messages
* C. SNMP traps
- D. Slack integration
> OLVM can send notifications through email and SNMP traps. These methods allow operational events to reach administrators or external monitoring systems.

```

## Acknowledgements

- **Source** - OLVM_Practice_Tests_Combined.pdf
- **Generated From** - Oracle Linux Virtualization Manager Exam 1Z0-1170 practice test collection
