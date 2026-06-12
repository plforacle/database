# Section 1: Architecture & Overview
## Introduction

This lab contains the Section 1 practice questions for **Architecture & Overview** from the OLVM Exam 1Z0-1170 quiz bank.

Estimated Time: 75 minutes

```quiz-config
passing: 75
badge: images/badge.png
```

### Objectives

In this lab, you will review the OLVM concepts covered by this exam objective and check your understanding with practice questions.

## Practice Questions

```quiz

Q: 1. What is the primary database schema name used by the oVirt engine in Oracle Linux Virtualization Manager?
- A. ovirt_db
* B. engine
- C. ovirt_engine
- D. manager_db
> The oVirt engine stores its main operational data in the `engine` PostgreSQL database/schema. Names such as `ovirt_db` or `ovirt_engine` sound plausible, but the primary database created and used by engine setup is `engine`.

Q: 2. Which two Intel hardware extensions does Oracle Linux Virtualization Manager support for enhanced performance? (Choose 2)
* A. Intel VT-x
- B. Intel SGX
* C. Intel VT-d
- D. Intel AES-NI
> Intel VT-x provides CPU virtualization support, while Intel VT-d provides directed I/O support for device assignment and I/O virtualization. SGX and AES-NI are useful processor features, but they are not the two core virtualization extensions OLVM relies on for this objective.

Q: 3. What is the function of Intel VT-x in virtualization?
- A. It handles input/output device virtualization
* B. It creates a virtual processor for each virtual machine
- C. It manages network traffic between VMs
- D. It provides encryption for virtual disks
> Intel VT-x is the processor extension that enables efficient CPU virtualization for guest operating systems. It lets the hypervisor present virtual processors to VMs while the physical CPU safely executes virtualized workloads.

Q: 4. What is the purpose of Intel VT-d in Oracle Linux Virtualization Manager?
- A. It provides processor-level virtualization
* B. It allows VMs to directly use devices like graphic cards and network adapters
- C. It manages memory allocation between VMs
- D. It handles virtual machine migration
> Intel VT-d is used for directed I/O, which allows hardware devices such as network adapters or graphics devices to be assigned more directly to a VM. VT-x handles CPU virtualization; VT-d is about I/O device virtualization and passthrough-style use cases.

Q: 5. What is "CPU pinning" (hard partitioning) used for in Oracle Linux KVM?
- A. Increasing CPU performance for all VMs
* B. Assigning specific vCPUs to certain physical CPU threads or cores
- C. Automatically balancing CPU load across hosts
- D. Reducing power consumption of physical CPUs
> CPU pinning binds specific virtual CPUs to specific physical CPU threads or cores. This is useful when a workload needs predictable CPU placement, reduced scheduling movement, or hard partitioning behavior.

Q: 6. What is the primary licensing benefit of using CPU pinning for Oracle products?
- A. It reduces the total cost of Oracle licenses
* B. It ensures licensing is based only on the number of physical cores in use
- C. It eliminates the need for Oracle licenses
- D. It allows unlimited virtual machines per core
> CPU pinning can limit an Oracle workload to a defined set of physical cores, which supports licensing based on the physical cores actually assigned to that workload. It does not remove licensing requirements; it helps establish a controlled licensing boundary.

Q: 7. What is the core component that serves as the backbone of Oracle Linux Virtualization Manager?
- A. KVM hypervisor
- B. VDSM agent
* C. oVirt engine
- D. PostgreSQL database
> The oVirt engine is the central management component in OLVM. KVM runs workloads, VDSM acts on hosts, and PostgreSQL stores data, but the engine coordinates the environment and provides the main management control plane.

Q: 8. Which three tasks does the oVirt engine perform? (Choose 3)
* A. Discovering KVM hosts
- B. Running virtual machine processes
* C. Configuring storage for virtualized data centers
* D. Configuring networking for virtualized data centers
- E. Providing hardware emulation for VMs
> The oVirt engine performs centralized orchestration tasks such as discovering hosts and coordinating storage and network configuration. VM execution and hardware emulation happen on KVM hosts through QEMU/KVM, not inside the engine process.

Q: 9. Oracle Linux Virtualization Manager is built from which open-source project?
- A. OpenStack
* B. oVirt
- C. Proxmox
- D. XenServer
> Oracle Linux Virtualization Manager is based on the oVirt open-source virtualization management project. OLVM packages and supports that management stack for Oracle Linux KVM environments.

Q: 10. What are the two main interfaces provided by Oracle Linux Virtualization Manager for management? (Choose 2)
- A. Command-line interface (CLI)
* B. Web-based UI
* C. REST API
- D. SNMP console
> OLVM provides a browser-based user interface for administrators and users, and a REST API for automation and integration. SSH and SNMP may be useful operational tools, but they are not the two primary OLVM management interfaces identified here.

Q: 11. What happens to a virtual machine if the oVirt engine goes offline?
- A. The VM automatically suspends
* B. The VM continues to run on the KVM host
- C. The VM is migrated to another host
- D. The VM shuts down gracefully
> The oVirt engine is the management layer, not the runtime process keeping a VM alive. Running VMs continue as QEMU/KVM processes on their KVM hosts even if centralized engine management is temporarily unavailable.

Q: 12. What is a Self-Hosted Engine in Oracle Linux Virtualization Manager?
- A. An engine that requires a dedicated physical server
* B. A management engine that runs as a VM directly on the virtualization hosts
- C. An engine that runs in Oracle Cloud Infrastructure
- D. A backup engine for disaster recovery
> A Self-Hosted Engine places the OLVM management engine inside a VM running on the virtualization hosts it manages. This differs from a standalone deployment where the engine runs on a separate dedicated management server.

Q: 13. What is the primary benefit of using a Self-Hosted Engine deployment?
- A. It requires more hardware resources
* B. It eliminates the need for a separate management server
- C. It increases the complexity of deployment
- D. It requires Oracle Linux Enterprise Kernel
> The main benefit is that a separate physical management server is not required. The engine VM runs on the virtualization infrastructure itself, reducing the need for dedicated management hardware.

Q: 14. How does Self-Hosted Engine enhance resilience?
- A. By requiring dedicated backup servers
* B. By eliminating single points of failure with the engine distributed across multiple hosts
- C. By automatically creating VM backupshttps://oracle.sharepoint.com/sites/s2s-mobile-phones/
- D. By using RAID storage configurations
> Self-Hosted Engine improves resilience because the engine VM can be managed across multiple eligible hosts instead of depending on one standalone server. That reduces the management plane single point of failure when shared storage and HA behavior are configured correctly.

Q: 15. In a Self-Hosted Engine deployment, where is the Engine VM stored?
- A. On the local disk of the first host
* B. On shared storage accessible by all hosts
- C. In Oracle Cloud Infrastructure
- D. On a USB drive connected to the host
> The Engine VM must be placed on shared storage so eligible hosts can access and restart it if needed. Local-only storage on one host would prevent other hosts from taking over management of the hosted engine VM.

Q: 16. What component monitors and manages the Self-Hosted Engine VM on each host?
- A. oVirt engine service
- B. VDSM daemon
* C. HA agent (ovirt-ha-agent)
- D. PostgreSQL database
> The `ovirt-ha-agent` monitors the hosted engine VM and helps manage high availability behavior on each participating host. VDSM manages host operations, but the HA agent is the component specifically responsible for hosted engine monitoring and failover coordination.

Q: 17. How many PostgreSQL databases are used by Oracle Linux Virtualization Manager?
- A. One
* B. Two
- C. Three
- D. Four
> OLVM commonly uses two PostgreSQL databases for the engine and historical reporting data. The active engine database stores current configuration and state, while the history database supports data warehouse and reporting functions.

Q: 18. What is the name of the primary database created during engine configuration?
- A. ovirt_engine
* B. engine
- C. manager
- D. ovirt_db
> The primary database created during engine configuration is named `engine`. It stores the active OLVM management data used by the oVirt engine.

Q: 19. What is the name of the second database used for historical data?
- A. engine_history
* B. ovirt_engine_history
- C. dwh_database
- D. historical_data
> `ovirt_engine_history` is the historical database used by the Data Warehouse component. It stores time-series and historical information that can be used for reporting and monitoring.

Q: 20. What must be installed to create the ovirt_engine_history database?
* A. ovirt-engine-dwh package
- B. postgresql-history package
- C. engine-history module
- D. ovirt-database-tools
> The `ovirt-engine-dwh` package provides the Data Warehouse component and creates/uses the history database. Without DWH, the historical reporting database is not part of the basic engine-only role.

Q: 21. What is the primary purpose of the Data Warehouse (DWH) component?
- A. To store virtual machine disk images
* B. To collect and store historical performance and usage data
- C. To manage the storage pool metadata
- D. To backup the engine database
> DWH collects and stores historical performance, inventory, and usage data. It is not where VM disk images live and it is not a replacement for engine database backup.

Q: 22. What information does the Oracle Linux Virtualization Manager dashboard provide? (Choose 3)
* A. VM counts and status
* B. Host counts and status
* C. Cluster information
- D. Individual user login history
* E. Storage status and performance metrics
> The OLVM dashboard summarizes infrastructure health and capacity, including VMs, hosts, clusters, and storage. Individual login history is more of an audit/security detail and is not one of the dashboard summary categories in this question.

Q: 23. Which feature allows Oracle Linux Virtualization Manager to integrate with other management systems?
- A. Web-based UI
- B. SSH access
* C. REST API
- D. SNMP traps
> The REST API is the integration surface used by external tools, scripts, and management platforms to interact with OLVM programmatically. The web UI is for human use, while REST is designed for automation and system-to-system integration.

Q: 24. What is the primary advantage of Oracle Linux Virtualization Manager being open-source?
- A. It requires no configuration
* B. It provides cost savings with no licensing fees
- C. It only runs on Oracle hardware
- D. It cannot be customized
> Because OLVM is based on open-source technology, it avoids separate virtualization management licensing costs associated with some proprietary platforms. Open source does not mean no configuration or no operational responsibility; it primarily changes cost, access, and extensibility characteristics.

Q: 25. What is the relationship between vCPUs and physical CPUs in Oracle Linux KVM?
- A. Each physical CPU can only support one vCPU
* B. The guest VM decides which tasks run on vCPUs, and the hypervisor decides which physical CPU cores the vCPUs use
- C. vCPUs must always be equal in number to physical CPUs
- D. Physical CPUs are not visible to the hypervisor
> Inside the guest, the operating system schedules work onto its vCPUs. The hypervisor then maps and schedules those vCPUs onto physical CPU cores or threads, unless stricter placement such as CPU pinning is configured.

Q: 26. What is the role of VDSM in Oracle Linux Virtualization Manager?
- A. It provides hardware emulation for virtual machines
* B. It acts as a host agent running continuously as a daemon on the KVM host
- C. It manages the PostgreSQL database
- D. It provides the web-based user interface
> VDSM is the host-side management agent that runs continuously on KVM hosts. It receives instructions from the engine and carries out host-level operations for VMs, networking, and storage.

Q: 27. How does the oVirt engine communicate with KVM hosts?
- A. Through SSH connections
* B. Through the VDSM service
- C. Through direct kernel calls
- D. Through the PostgreSQL database
> The engine communicates with KVM hosts through the VDSM service. Administrators may use SSH for setup or troubleshooting, but normal OLVM host management is coordinated through VDSM.

Q: 28. Which three tasks does VDSM perform on the KVM host? (Choose 3)
* A. Managing virtual machines
- B. Providing web browser access
* C. Managing networks
* D. Managing storage
- E. Running the oVirt engine
> VDSM performs host-level management for virtual machines, networks, and storage. It does not provide the browser UI or run the engine; those responsibilities belong to other parts of the OLVM stack.

Q: 29. What is the function of libvirt in the KVM host architecture?
- A. It emulates hardware components for VMs
* B. It provides an API for managing various hypervisors including Oracle Linux KVM
- C. It stores virtual machine configurations
- D. It manages user authentication
> libvirt provides a stable management API for hypervisors, including KVM. VDSM uses libvirt to control VM lifecycle and gather host or VM information without directly manipulating every hypervisor detail itself.

Q: 30. What does QEMU (Quick Emulator) provide in the virtualization stack?
- A. Network connectivity between VMs
* B. Hardware component emulation such as CPU, memory, network, and disk devices
- C. User authentication services
- D. Database management
> QEMU provides device emulation for guest hardware such as virtual disks, network adapters, memory, and CPU presentation. KVM accelerates virtualization in the kernel, while QEMU supplies much of the userspace VM device model.

Q: 31. Where does KVM operate within the system?
- A. In user space as an application
* B. In the kernel space
- C. On a separate management server
- D. In the cloud
> KVM is implemented as a Linux kernel virtualization facility, so it operates in kernel space. That kernel integration is what allows Linux to act as a type 1-style hypervisor for virtualized workloads.

Q: 32. Where do virtual machines run as processes?
- A. In kernel space as KVM modules
* B. As QEMU processes in user space
- C. On the oVirt engine server
- D. In the VDSM daemon
> The guest VM workload runs as a QEMU process in user space, with KVM support in the kernel providing acceleration. This split is central to understanding the OLVM/KVM architecture.

Q: 33. What is the relationship between VDSM and libvirt?
- A. VDSM replaces libvirt completely
* B. VDSM relies on libvirt to manage the lifecycle of VMs and collect statistics
- C. libvirt runs inside VDSM
- D. They operate independently without interaction
> VDSM relies on libvirt as the management layer for VM lifecycle actions and statistics collection. VDSM is the OLVM host agent; libvirt is the lower-level hypervisor management API it uses.

Q: 34. Which three portals are available in Oracle Linux Virtualization Manager? (Choose 3)
* A. Administration Portal
- B. Developer Portal
* C. VM Portal
* D. Monitoring Portal
- E. Database Portal
> OLVM provides the Administration Portal for administrators, the VM Portal for VM users, and the Monitoring Portal for health and performance visibility. Developer and database portals are not standard OLVM portals in this context.

Q: 35. What is the primary purpose of the Administration Portal?
- A. To allow end users to access their VMs only
* B. To serve as a nerve center for administrators to oversee, create, and maintain every component of the virtual ecosystem
- C. To monitor network traffic exclusively
- D. To backup virtual machines
> The Administration Portal is the main administrative console for managing data centers, clusters, hosts, storage, networks, VMs, and permissions. It is broader than an end-user VM access interface.

Q: 36. How is the Administration Portal accessed?
- A. Through a dedicated client application only
* B. Through any web browser
- C. Through SSH command line only
- D. Through the VM console
> The Administration Portal is browser based, so administrators access it through a web browser. A dedicated desktop client or SSH-only workflow is not required for normal portal access.

Q: 37. What is the primary purpose of the VM Portal?
- A. To provide full administrative control over all OLVM components
* B. To cater to users who primarily engage with virtual machines, offering a user-friendly interface for basic VM management
- C. To monitor system performance only
- D. To configure storage domains
> The VM Portal is intended for users who primarily need to interact with virtual machines rather than administer the entire platform. It exposes user-focused VM actions while leaving broader infrastructure control to administrators.

Q: 38. Which operations can users perform in the VM Portal? (Choose 3)
* A. Creating, editing, and removing virtual machines
* B. Starting, stopping, and migrating virtual machines
- C. Modifying host hardware configurations
* D. Accessing detailed VM information
- E. Installing the hypervisor kernel
> The VM Portal allows permitted users to create, edit, remove, start, stop, migrate, and inspect VMs depending on assigned permissions. Host hardware configuration and hypervisor kernel installation remain administrator and host-management responsibilities.

Q: 39. Who determines the capabilities available to users within the VM Portal?
- A. The users themselves
- B. Oracle support team
* C. System administrators
- D. The oVirt engine automatically
> System administrators assign roles and permissions that determine what VM Portal users can do. The portal does not automatically grant full capabilities to users by default.

Q: 40. Which two protocols can be used to connect to virtual machines from the VM Portal? (Choose 2)
- A. SSH
* B. VNC
* C. SPICE
- D. RDP (for Windows VMs only)
> VNC and SPICE are console protocols supported for connecting to VM graphical consoles. SSH and RDP can be used inside guest operating systems when configured, but they are not the OLVM VM Portal console protocols identified here.

Q: 41. What is the primary purpose of the Monitoring Portal in OLVM?
- A. To create new virtual machines
* B. To empower administrators with comprehensive monitoring capabilities for health and performance
- C. To manage user permissions
- D. To configure storage domains
> The Monitoring Portal is focused on health, performance, and operational visibility. It helps administrators understand resource usage and trends rather than creating VMs or configuring storage directly.

Q: 42. Which open-source analytics platform does Oracle Linux Virtualization Manager integrate with for enhanced monitoring?
- A. Kibana
- B. Prometheus
* C. Grafana
- D. Nagios
> OLVM integrates with Grafana for enhanced monitoring dashboards and analytics. Grafana is the dashboarding and visualization platform used here, distinct from tools such as Kibana or Nagios.

Q: 43. Where does Grafana retrieve its monitoring data from in OLVM?
- A. Directly from the KVM hosts
* B. From the engine data warehouse
- C. From virtual machine logs
- D. From network switches
> Grafana gets OLVM monitoring data from the engine data warehouse, which stores historical metrics. Pulling from DWH allows dashboards to show trends and history rather than only immediate host-local data.

Q: 44. What can administrators create in Grafana for monitoring?
- A. Virtual machines
- B. Storage domains
* C. Customized dashboards tailored to specific monitoring needs
- D. User accounts
> Administrators use Grafana to create customized dashboards for the metrics they care about. Grafana visualizes and organizes monitoring data; it does not create VMs, storage domains, or user accounts.

Q: 45. Which metrics can be monitored through Grafana dashboards? (Choose 3)
* A. CPU utilization
- B. User login history
* C. Memory usage
* D. Storage capacity
- E. VM creation timestamps
> Grafana dashboards commonly show infrastructure metrics such as CPU utilization, memory usage, and storage capacity. Login history and VM creation timestamps may exist in other records, but they are not the main performance metrics in this question.

Q: 46. What is Cockpit in Oracle Linux Virtualization Manager?
- A. A database management tool
* B. A web interface that empowers users to monitor KVM host resources and perform administrative tasks
- C. A virtual machine migration tool
- D. A backup solution
> Cockpit is a web-based host administration interface for Linux systems. In an OLVM environment, it can be used to monitor KVM host resources and perform selected host administration tasks.

Q: 47. How is Cockpit made available in OLVM?
- A. It is automatically installed with the oVirt engine
* B. It must be installed and activated separately
- C. It comes pre-configured in all KVM hosts
- D. It is only available through Oracle support
> Cockpit is not automatically available just because the oVirt engine is installed. It must be installed, enabled, and activated separately where it is needed.

Q: 48. Which two methods can be used to access the Cockpit web interface? (Choose 2)
* A. Through the Administration Portal
- B. Through email notifications
* C. By establishing a direct connection to the host
- D. Through the VM console
- E. Through SNMP traps
> Cockpit can be reached from integration points in the Administration Portal or by directly connecting to the host's Cockpit web service. Email, SNMP, and VM consoles are not Cockpit access methods.

Q: 49. Which metrics can users monitor through the Cockpit web interface? (Choose 3)
* A. CPU usage of the KVM host
- B. Individual VM application logs
* C. Memory utilization of the KVM host
* D. Disk space on the KVM host
* E. Network activity of the KVM host
> Cockpit reports host-level resource information such as CPU, memory, disk, and network activity. It is not designed to inspect individual application logs inside every guest VM, which remain guest operating system concerns.

Q: 50. What is "Live Migration" in Oracle Linux Virtualization Manager?
- A. Moving VM disk images between storage while the VM is powered off
* B. Moving running VMs to other servers seamlessly without downtime
- C. Backing up VMs to tape drives
- D. Converting physical servers to virtual machines
> Live migration moves a running VM from one host to another with no planned downtime or only a very brief pause. It depends on compatible hosts, networking, and storage so the VM can continue running after the move.

Q: 51. What is "Storage Live Migration" used for?
- A. Moving VMs between data centers
* B. Moving virtual disks of running VMs between storage devices without disrupting operations
- C. Backing up storage arrays
- D. Encrypting storage volumes
> Storage live migration moves a VM's virtual disks between storage locations while the VM keeps running. This is different from host live migration, which moves VM execution between hosts.

Q: 52. What technology does OLVM integrate to patch the kernel and QEMU without downtime?
- A. Ansible
- B. Puppet
* C. Ksplice
- D. Chef
> Ksplice provides zero-downtime patching capabilities for supported kernel and userspace components such as QEMU. That lets administrators apply certain critical updates without a full reboot or service outage.

Q: 53. How does OLVM provide backup capabilities for virtual machines?
- A. Using tape backup systems
- B. Through cloud synchronization only
* C. Using snapshots that capture a consistent view of running VMs at a specific moment in time
- D. Through manual file copying
> Snapshots capture the state of a VM at a point in time and can support backup-style workflows or recovery points. They are useful for consistency and rollback scenarios, but they should be understood as part of a broader data protection strategy.

Q: 54. Which two disaster recovery solutions does Oracle Linux Virtualization Manager support? (Choose 2)
* A. Active-Active
- B. Active-Standby
* C. Active-Passive
- D. Passive-Passive
> OLVM supports active-active and active-passive disaster recovery patterns. Active-active keeps both sites active, while active-passive keeps one site primarily on standby until failover is needed.

Q: 55. In an Active-Active disaster recovery configuration, how are the sites connected?
- A. Through manual failover procedures
* B. Through stretched clusters, ensuring continuous application availability
- C. Through scheduled data transfers
- D. They are not connected
> In active-active DR, stretched clusters connect sites so workloads can remain available across locations. This requires appropriate shared or replicated infrastructure and network design to support continuous availability.

Q: 56. What authentication systems can Oracle Linux Virtualization Manager integrate with? (Choose 2)
- A. Local user accounts only
* B. LDAP
* C. Active Directory
- D. OAuth only
> OLVM can integrate with directory services such as LDAP and Active Directory for external authentication. Local accounts may exist, but enterprise identity integration usually uses LDAP-compatible directories or AD.

Q: 57. What is the maximum number of logical CPUs that the Unbreakable Enterprise Kernel (UEK) can support?
- A. 256
- B. 512
- C. 1,024
* D. 2,048
> UEK supports up to 2,048 logical CPUs according to the exam objective represented by this quiz. The question is testing platform scalability limits rather than an OLVM portal setting.

Q: 58. What is the maximum amount of memory that the Unbreakable Enterprise Kernel (UEK) can support?
- A. 16 terabytes
- B. 32 terabytes
* C. 64 terabytes
- D. 128 terabytes
> UEK supports up to 64 TB of memory according to the scalability figure used in this section. This is a host operating system capability and helps frame how large OLVM/KVM hosts can be.

Q: 59. Which Java application server is the oVirt engine based on?
- A. Tomcat
- B. WebLogic
* C. WildFly
- D. JBoss EAP 6
> The oVirt engine runs on the WildFly Java application server. Tomcat, WebLogic, and JBoss EAP are Java server technologies, but WildFly is the one associated with the oVirt engine in this quiz.

Q: 60. What is the primary benefit of Oracle Linux KVM templates?
- A. They provide better security
* B. They cut down on installation time and ongoing maintenance costs
- C. They increase VM performance
- D. They reduce storage requirements
> Templates reduce repeated installation and configuration work by letting administrators create standardized VM starting points. That lowers deployment time and ongoing maintenance effort because new VMs can inherit a known baseline.

```

## Acknowledgements

- **Author** - Perside Foster
- **Source Material** - OLVM bootcamp training and Oracle Linux Virtualization Manager documentation
