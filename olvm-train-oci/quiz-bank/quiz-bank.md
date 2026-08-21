# OLVM Exam 1Z0-1170 Practice Tests
## Introduction

This LiveLabs-style quiz bank was generated from `OLVM_Practice_Tests_Combined.pdf`.

Estimated Time: 6 hours

```quiz-config
passing: 75
badge: images/badge.png
```

### Objectives

In this quiz bank, you will review the major exam objectives for Oracle Linux Virtualization Manager Administration.

### Quiz Sections

| Section | Topic | Questions |
|---------|-------|-----------|
| 1 | Architecture & Overview | 60 |
| 2 | Engine Installation | 50 |
| 3 | Core Components | 53 |
| 4 | KVM Host Installation | 50 |
| 5 | Storage Domain Management | 55 |
| 6 | Network Management | 50 |
| 7 | User Management | 50 |
| 8 | Optimization Practices | 50 |
| 9 | Virtual Machine Administration | 55 |
| 10 | Events & Logs Management | 55 |
| 11 | Recovery Management | 50 |

## Section 1: Architecture & Overview

```quiz

Q: 1. What is the primary database schema name used by the oVirt engine in Oracle Linux Virtualization Manager?
- A. ovirt_db
* B. engine
- C. ovirt_engine
- D. manager_db
> Answer key: B - engine

Q: 2. Which two Intel hardware extensions does Oracle Linux Virtualization Manager support for enhanced performance? (Choose 2)
* A. Intel VT-x
- B. Intel SGX
* C. Intel VT-d
- D. Intel AES-NI
> Answer key: A, C - Intel VT-x and Intel VT-d

Q: 3. What is the function of Intel VT-x in virtualization?
- A. It handles input/output device virtualization
* B. It creates a virtual processor for each virtual machine
- C. It manages network traffic between VMs
- D. It provides encryption for virtual disks
> Answer key: B - It creates a virtual processor for each virtual machine

Q: 4. What is the purpose of Intel VT-d in Oracle Linux Virtualization Manager?
- A. It provides processor-level virtualization
* B. It allows VMs to directly use devices like graphic cards and network adapters
- C. It manages memory allocation between VMs
- D. It handles virtual machine migration
> Answer key: B - It allows VMs to directly use devices like graphic cards and network adapters

Q: 5. What is "CPU pinning" (hard partitioning) used for in Oracle Linux KVM?
- A. Increasing CPU performance for all VMs
* B. Assigning specific vCPUs to certain physical CPU threads or cores
- C. Automatically balancing CPU load across hosts
- D. Reducing power consumption of physical CPUs
> Answer key: B - Assigning specific vCPUs to certain physical CPU threads or cores

Q: 6. What is the primary licensing benefit of using CPU pinning for Oracle products?
- A. It reduces the total cost of Oracle licenses
* B. It ensures licensing is based only on the number of physical cores in use
- C. It eliminates the need for Oracle licenses
- D. It allows unlimited virtual machines per core
> Answer key: B - It ensures licensing is based only on the number of physical cores in use

Q: 7. What is the core component that serves as the backbone of Oracle Linux Virtualization Manager?
- A. KVM hypervisor
- B. VDSM agent
* C. oVirt engine
- D. PostgreSQL database
> Answer key: C - oVirt engine

Q: 8. Which three tasks does the oVirt engine perform? (Choose 3)
* A. Discovering KVM hosts
- B. Running virtual machine processes
* C. Configuring storage for virtualized data centers
* D. Configuring networking for virtualized data centers
- E. Providing hardware emulation for VMs
> Answer key: A, C, D - Discovering KVM hosts, Configuring storage, Configuring networking

Q: 9. Oracle Linux Virtualization Manager is built from which open-source project?
- A. OpenStack
* B. oVirt
- C. Proxmox
- D. XenServer
> Answer key: B - oVirt

Q: 10. What are the two main interfaces provided by Oracle Linux Virtualization Manager for management? (Choose 2)
- A. Command-line interface (CLI)
* B. Web-based UI
* C. REST API
- D. SNMP console
> Answer key: B, C - Web-based UI and REST API

Q: 11. What happens to a virtual machine if the oVirt engine goes offline?
- A. The VM automatically suspends
* B. The VM continues to run on the KVM host
- C. The VM is migrated to another host
- D. The VM shuts down gracefully
> Answer key: B - The VM continues to run on the KVM host

Q: 12. What is a Self-Hosted Engine in Oracle Linux Virtualization Manager?
- A. An engine that requires a dedicated physical server
* B. A management engine that runs as a VM directly on the virtualization hosts
- C. An engine that runs in Oracle Cloud Infrastructure
- D. A backup engine for disaster recovery
> Answer key: B - A management engine that runs as a VM directly on the virtualization hosts

Q: 13. What is the primary benefit of using a Self-Hosted Engine deployment?
- A. It requires more hardware resources
* B. It eliminates the need for a separate management server
- C. It increases the complexity of deployment
- D. It requires Oracle Linux Enterprise Kernel
> Answer key: B - It eliminates the need for a separate management server

Q: 14. How does Self-Hosted Engine enhance resilience?
- A. By requiring dedicated backup servers
* B. By eliminating single points of failure with the engine distributed across multiple hosts
- C. By automatically creating VM backupshttps://oracle.sharepoint.com/sites/s2s-mobile-phones/
- D. By using RAID storage configurations
> Answer key: B - By eliminating single points of failure with the engine distributed across multiple hosts

Q: 15. In a Self-Hosted Engine deployment, where is the Engine VM stored?
- A. On the local disk of the first host
* B. On shared storage accessible by all hosts
- C. In Oracle Cloud Infrastructure
- D. On a USB drive connected to the host
> Answer key: B - On shared storage accessible by all hosts

Q: 16. What component monitors and manages the Self-Hosted Engine VM on each host?
- A. oVirt engine service
- B. VDSM daemon
* C. HA agent (ovirt-ha-agent)
- D. PostgreSQL database
> Answer key: C - HA agent (ovirt-ha-agent)

Q: 17. How many PostgreSQL databases are used by Oracle Linux Virtualization Manager?
- A. One
* B. Two
- C. Three
- D. Four
> Answer key: B - Two

Q: 18. What is the name of the primary database created during engine configuration?
- A. ovirt_engine
* B. engine
- C. manager
- D. ovirt_db
> Answer key: B - engine

Q: 19. What is the name of the second database used for historical data?
- A. engine_history
* B. ovirt_engine_history
- C. dwh_database
- D. historical_data
> Answer key: B - ovirt_engine_history

Q: 20. What must be installed to create the ovirt_engine_history database?
* A. ovirt-engine-dwh package
- B. postgresql-history package
- C. engine-history module
- D. ovirt-database-tools
> Answer key: A - ovirt-engine-dwh package

Q: 21. What is the primary purpose of the Data Warehouse (DWH) component?
- A. To store virtual machine disk images
* B. To collect and store historical performance and usage data
- C. To manage the storage pool metadata
- D. To backup the engine database
> Answer key: B - To collect and store historical performance and usage data

Q: 22. What information does the Oracle Linux Virtualization Manager dashboard provide? (Choose 3)
* A. VM counts and status
* B. Host counts and status
* C. Cluster information
- D. Individual user login history
* E. Storage status and performance metrics
> Answer key: A, B, C, E - VM counts/status, Host counts/status, Cluster information, Storage status/performance

Q: 23. Which feature allows Oracle Linux Virtualization Manager to integrate with other management systems?
- A. Web-based UI
- B. SSH access
* C. REST API
- D. SNMP traps
> Answer key: C - REST API

Q: 24. What is the primary advantage of Oracle Linux Virtualization Manager being open-source?
- A. It requires no configuration
* B. It provides cost savings with no licensing fees
- C. It only runs on Oracle hardware
- D. It cannot be customized
> Answer key: B - It provides cost savings with no licensing fees

Q: 25. What is the relationship between vCPUs and physical CPUs in Oracle Linux KVM?
- A. Each physical CPU can only support one vCPU
* B. The guest VM decides which tasks run on vCPUs, and the hypervisor decides which physical CPU cores the vCPUs use
- C. vCPUs must always be equal in number to physical CPUs
- D. Physical CPUs are not visible to the hypervisor
> Answer key: B - The guest VM decides which tasks run on vCPUs, and the hypervisor decides which physical CPU cores the vCPUs use

Q: 26. What is the role of VDSM in Oracle Linux Virtualization Manager?
- A. It provides hardware emulation for virtual machines
* B. It acts as a host agent running continuously as a daemon on the KVM host
- C. It manages the PostgreSQL database
- D. It provides the web-based user interface
> Answer key: B - It acts as a host agent running continuously as a daemon on the KVM host

Q: 27. How does the oVirt engine communicate with KVM hosts?
- A. Through SSH connections
* B. Through the VDSM service
- C. Through direct kernel calls
- D. Through the PostgreSQL database
> Answer key: B - Through the VDSM service

Q: 28. Which three tasks does VDSM perform on the KVM host? (Choose 3)
* A. Managing virtual machines
- B. Providing web browser access
* C. Managing networks
* D. Managing storage
- E. Running the oVirt engine
> Answer key: A, C, D - Managing virtual machines, Managing networks, Managing storage

Q: 29. What is the function of libvirt in the KVM host architecture?
- A. It emulates hardware components for VMs
* B. It provides an API for managing various hypervisors including Oracle Linux KVM
- C. It stores virtual machine configurations
- D. It manages user authentication
> Answer key: B - It provides an API for managing various hypervisors including Oracle Linux KVM

Q: 30. What does QEMU (Quick Emulator) provide in the virtualization stack?
- A. Network connectivity between VMs
* B. Hardware component emulation such as CPU, memory, network, and disk devices
- C. User authentication services
- D. Database management
> Answer key: B - Hardware component emulation such as CPU, memory, network, and disk devices

Q: 31. Where does KVM operate within the system?
- A. In user space as an application
* B. In the kernel space
- C. On a separate management server
- D. In the cloud
> Answer key: B - In the kernel space

Q: 32. Where do virtual machines run as processes?
- A. In kernel space as KVM modules
* B. As QEMU processes in user space
- C. On the oVirt engine server
- D. In the VDSM daemon
> Answer key: B - As QEMU processes in user space

Q: 33. What is the relationship between VDSM and libvirt?
- A. VDSM replaces libvirt completely
* B. VDSM relies on libvirt to manage the lifecycle of VMs and collect statistics
- C. libvirt runs inside VDSM
- D. They operate independently without interaction
> Answer key: B - VDSM relies on libvirt to manage the lifecycle of VMs and collect statistics

Q: 34. Which three portals are available in Oracle Linux Virtualization Manager? (Choose 3)
* A. Administration Portal
- B. Developer Portal
* C. VM Portal
* D. Monitoring Portal
- E. Database Portal
> Answer key: A, C, D - Administration Portal, VM Portal, Monitoring Portal

Q: 35. What is the primary purpose of the Administration Portal?
- A. To allow end users to access their VMs only
* B. To serve as a nerve center for administrators to oversee, create, and maintain every component of the virtual ecosystem
- C. To monitor network traffic exclusively
- D. To backup virtual machines
> Answer key: B - To serve as a nerve center for administrators to oversee, create, and maintain every component of the virtual ecosystem

Q: 36. How is the Administration Portal accessed?
- A. Through a dedicated client application only
* B. Through any web browser
- C. Through SSH command line only
- D. Through the VM console
> Answer key: B - Through any web browser

Q: 37. What is the primary purpose of the VM Portal?
- A. To provide full administrative control over all OLVM components
* B. To cater to users who primarily engage with virtual machines, offering a user-friendly interface for basic VM management
- C. To monitor system performance only
- D. To configure storage domains
> Answer key: B - To cater to users who primarily engage with virtual machines, offering a user-friendly interface for basic VM management

Q: 38. Which operations can users perform in the VM Portal? (Choose 3)
* A. Creating, editing, and removing virtual machines
* B. Starting, stopping, and migrating virtual machines
- C. Modifying host hardware configurations
* D. Accessing detailed VM information
- E. Installing the hypervisor kernel
> Answer key: A, B, D - Creating/editing/removing VMs, Starting/stopping/migrating VMs, Accessing detailed VM information

Q: 39. Who determines the capabilities available to users within the VM Portal?
- A. The users themselves
- B. Oracle support team
* C. System administrators
- D. The oVirt engine automatically
> Answer key: C - System administrators

Q: 40. Which two protocols can be used to connect to virtual machines from the VM Portal? (Choose 2)
- A. SSH
* B. VNC
* C. SPICE
- D. RDP (for Windows VMs only)
> Answer key: B, C - VNC, SPICE

Q: 41. What is the primary purpose of the Monitoring Portal in OLVM?
- A. To create new virtual machines
* B. To empower administrators with comprehensive monitoring capabilities for health and performance
- C. To manage user permissions
- D. To configure storage domains
> Answer key: B - To empower administrators with comprehensive monitoring capabilities for health and performance

Q: 42. Which open-source analytics platform does Oracle Linux Virtualization Manager integrate with for enhanced monitoring?
- A. Kibana
- B. Prometheus
* C. Grafana
- D. Nagios
> Answer key: C - Grafana

Q: 43. Where does Grafana retrieve its monitoring data from in OLVM?
- A. Directly from the KVM hosts
* B. From the engine data warehouse
- C. From virtual machine logs
- D. From network switches
> Answer key: B - From the engine data warehouse

Q: 44. What can administrators create in Grafana for monitoring?
- A. Virtual machines
- B. Storage domains
* C. Customized dashboards tailored to specific monitoring needs
- D. User accounts
> Answer key: C - Customized dashboards tailored to specific monitoring needs

Q: 45. Which metrics can be monitored through Grafana dashboards? (Choose 3)
* A. CPU utilization
- B. User login history
* C. Memory usage
* D. Storage capacity
- E. VM creation timestamps
> Answer key: A, C, D - CPU utilization, Memory usage, Storage capacity

Q: 46. What is Cockpit in Oracle Linux Virtualization Manager?
- A. A database management tool
* B. A web interface that empowers users to monitor KVM host resources and perform administrative tasks
- C. A virtual machine migration tool
- D. A backup solution
> Answer key: B - A web interface that empowers users to monitor KVM host resources and perform administrative tasks

Q: 47. How is Cockpit made available in OLVM?
- A. It is automatically installed with the oVirt engine
* B. It must be installed and activated separately
- C. It comes pre-configured in all KVM hosts
- D. It is only available through Oracle support
> Answer key: B - It must be installed and activated separately

Q: 48. Which two methods can be used to access the Cockpit web interface? (Choose 2)
* A. Through the Administration Portal
- B. Through email notifications
* C. By establishing a direct connection to the host
- D. Through the VM console
- E. Through SNMP traps
> Answer key: A, C - Through the Administration Portal, By establishing a direct connection to the host

Q: 49. Which metrics can users monitor through the Cockpit web interface? (Choose 3)
* A. CPU usage of the KVM host
- B. Individual VM application logs
* C. Memory utilization of the KVM host
* D. Disk space on the KVM host
* E. Network activity of the KVM host
> Answer key: A, C, D, E - CPU usage, Memory utilization, Disk space, Network activity (all of KVM host)

Q: 50. What is "Live Migration" in Oracle Linux Virtualization Manager?
- A. Moving VM disk images between storage while the VM is powered off
* B. Moving running VMs to other servers seamlessly without downtime
- C. Backing up VMs to tape drives
- D. Converting physical servers to virtual machines
> Answer key: B - Moving running VMs to other servers seamlessly without downtime

Q: 51. What is "Storage Live Migration" used for?
- A. Moving VMs between data centers
* B. Moving virtual disks of running VMs between storage devices without disrupting operations
- C. Backing up storage arrays
- D. Encrypting storage volumes
> Answer key: B - Moving virtual disks of running VMs between storage devices without disrupting operations

Q: 52. What technology does OLVM integrate to patch the kernel and QEMU without downtime?
- A. Ansible
- B. Puppet
* C. Ksplice
- D. Chef
> Answer key: C - Ksplice

Q: 53. How does OLVM provide backup capabilities for virtual machines?
- A. Using tape backup systems
- B. Through cloud synchronization only
* C. Using snapshots that capture a consistent view of running VMs at a specific moment in time
- D. Through manual file copying
> Answer key: C - Using snapshots that capture a consistent view of running VMs at a specific moment in time

Q: 54. Which two disaster recovery solutions does Oracle Linux Virtualization Manager support? (Choose 2)
* A. Active-Active
- B. Active-Standby
* C. Active-Passive
- D. Passive-Passive
> Answer key: A, C - Active-Active, Active-Passive

Q: 55. In an Active-Active disaster recovery configuration, how are the sites connected?
- A. Through manual failover procedures
* B. Through stretched clusters, ensuring continuous application availability
- C. Through scheduled data transfers
- D. They are not connected
> Answer key: B - Through stretched clusters, ensuring continuous application availability

Q: 56. What authentication systems can Oracle Linux Virtualization Manager integrate with? (Choose 2)
- A. Local user accounts only
* B. LDAP
* C. Active Directory
- D. OAuth only
> Answer key: B, C - LDAP, Active Directory

Q: 57. What is the maximum number of logical CPUs that the Unbreakable Enterprise Kernel (UEK) can support?
- A. 256
- B. 512
- C. 1,024
* D. 2,048
> Answer key: D - 2,048

Q: 58. What is the maximum amount of memory that the Unbreakable Enterprise Kernel (UEK) can support?
- A. 16 terabytes
- B. 32 terabytes
* C. 64 terabytes
- D. 128 terabytes
> Answer key: C - 64 terabytes

Q: 59. Which Java application server is the oVirt engine based on?
- A. Tomcat
- B. WebLogic
* C. WildFly
- D. JBoss EAP 6
> Answer key: C - WildFly

Q: 60. What is the primary benefit of Oracle Linux KVM templates?
- A. They provide better security
* B. They cut down on installation time and ongoing maintenance costs
- C. They increase VM performance
- D. They reduce storage requirements
> Answer key: B - They cut down on installation time and ongoing maintenance costs 50-60 correct: Excellent - Ready for this module 40-30 correct: Good - Review missed topics 30-20 correct: Fair - Study weak areas Below 20: Needs review - Re-read the training transcript

```

## Section 2: Engine Installation

```quiz

Q: 1. What is the minimum Oracle Linux version required for installing the OLVM Manager (Engine)?
- A. Oracle Linux 7.5
* B. Oracle Linux 8.5
- C. Oracle Linux 9.0
- D. Oracle Linux 6.8
> Answer key: B - Oracle Linux 8.5

Q: 2. Which two CPU technologies must be supported for the Engine host processor? (Choose 2)
* A. Intel VT-x
- B. Intel SGX
* C. AMD-V
- D. ARM TrustZone
> Answer key: A, C - Intel VT-x and AMD-V

Q: 3. What type of CPU architecture is required for the Engine host?
- A. 32-bit x86
* B. 64-bit x86
- C. ARM64
- D. PowerPC
> Answer key: B - 64-bit x86

Q: 4. Which six repositories must be enabled on the Oracle Linux system for OLVM Engine? (Choose 6)
* A. BaseOS Latest
* B. AppStream
* C. KVM AppStream
* D. oVirt 44
* E. oVirt 44 Extras
* F. Gluster AppStream
> Answer key: A, B, C, D, E, F - All six listed

Q: 5. Which additional repository channel is required specifically for configuring VDSM on the host?
- A. UEKR6
* B. UEKR7
- C. KVM Utils
- D. Gluster 9
> Answer key: B - UEKR7

Q: 6. What are the MINIMUM hardware requirements for a SMALL deployment Engine? (Choose 3)
* A. 2 core CPUs
* B. 4 GB RAM
* C. 25 GB local writable disk
- D. 8 GB RAM
- E. 50 GB local writable disk
> Answer key: A, B, C - 2 core CPUs, 4 GB RAM, 25 GB local writable disk

Q: 7. What are the RECOMMENDED hardware requirements for a SMALL deployment? (Choose 3)
- A. 2 cores
* B. 4 cores
* C. 16 GB or greater RAM
- D. 32 GB RAM
* E. 50 GB or greater local writable disk
> Answer key: B, C, E - 4 cores, 16 GB or greater RAM, 50 GB or greater local writable disk

Q: 8. How many KVM hosts can a SMALL deployment support?
* A. 1 to 5 hosts
- B. 5 to 10 hosts
- C. 10 to 20 hosts
- D. 20 to 50 hosts
> Answer key: A - 1 to 5 hosts

Q: 9. How many virtual machines can a SMALL deployment support?
- A. Up to 20 VMs
* B. Up to 50 VMs
- C. Up to 100 VMs
- D. Up to 200 VMs
> Answer key: B - Up to 50 VMs

Q: 10. What are the recommended hardware requirements for a MEDIUM deployment? (Choose 3)
- A. 4 core CPUs
* B. 8 core CPUs
* C. 32 GB or greater RAM
- D. 64 GB RAM
* E. 100 GB or greater local writable disk
> Answer key: B, C, E - 8 core CPUs, 32 GB or greater RAM, 100 GB or greater local writable disk

Q: 11. How many KVM hosts can a MEDIUM deployment support?
- A. 1 to 5 hosts
* B. 5 to 50 hosts
- C. 50 to 100 hosts
- D. 100 to 200 hosts
> Answer key: B - 5 to 50 hosts

Q: 12. How many virtual machines can a MEDIUM deployment support?
- A. 50 to 100 VMs
* B. 50 to 500 VMs
- C. 500 to 1000 VMs
- D. 1000 to 2000 VMs
> Answer key: B - 50 to 500 VMs

Q: 13. What are the recommended hardware requirements for a LARGE deployment? (Choose 3)
- A. 8 cores
* B. 16 cores or greater
* C. 64 GB or greater RAM
- D. 128 GB RAM
* E. 200 GB or greater writable disk space
> Answer key: B, C, E - 16 cores or greater, 64 GB or greater RAM, 200 GB or greater writable disk space

Q: 14. How many KVM hosts can a LARGE deployment support?
- A. 10 to 50 hosts
* B. 50 to 200 hosts
- C. 200 to 500 hosts
- D. 500 to 1000 hosts
> Answer key: B - 50 to 200 hosts

Q: 15. How many virtual machines can a LARGE deployment support?
- A. 500 to 1000 VMs
* B. 500 to 2000 VMs
- C. 2000 to 5000 VMs
- D. 5000 to 10000 VMs
> Answer key: B - 500 to 2000 VMs

Q: 16. What is the default port number for SSH access to the Manager?
- A. 21
* B. 22
- C. 23
- D. 3389
> Answer key: B - 22

Q: 17. Which two ports are used for web interface and REST API access? (Choose 2)
* A. 80 (TCP)
- B. 8080 (TCP)
* C. 443 (TCP)
- D. 8443 (TCP)
> Answer key: A, C - 80 (TCP) and 443 (TCP)

Q: 18. What is the default port number for PostgreSQL database communication?
- A. 3306
* B. 5432
- C. 5433
- D. 27017
> Answer key: B - 5432

Q: 19. Which two ports are used for VDSM communication between the Engine and hosts? (Choose 2)
- A. 54320
* B. 54321
* C. 54322
- D. 54323
> Answer key: B, C - 54321 and 54322

Q: 20. What is the port range used for SPICE protocol remote desktop connections?
- A. 3389 to 3400
- B. 5800 to 5900
* C. 5900 to 6923
- D. 8000 to 9000
> Answer key: C - 5900 to 6923

Q: 21. What port is used for iSCSI storage domain access?
- A. 111
- B. 2049
* C. 3260
- D. 32803
> Answer key: C - 3260

Q: 22. Which port is used by the portmapper service for NFS?
* A. 111 (TCP and UDP)
- B. 2049 (TCP and UDP)
- C. 32803 (TCP)
- D. 3260 (TCP)
> Answer key: A - 111 (TCP and UDP)

Q: 23. Which two appstream modules must be enabled before installing the OLVM Engine? (Choose 2)
- A. php:7.4
* B. pki-deps
* C. postgresql:13
- D. nodejs:14
> Answer key: B, C - pki-deps and postgresql:13

Q: 24. What does PKI stand for in the context of OLVM?
- A. Package Key Infrastructure
* B. Public Key Infrastructure
- C. Private Key Interface
- D. Portal Key Integration
> Answer key: B - Public Key Infrastructure

Q: 25. Which virt module should be DISABLED before installing OLVM Engine?
- A. virt:rhel
* B. virt:ol
- C. virt:kvm
- D. virt:8.5
> Answer key: B - virt:ol

Q: 26. Which virt module version should be ENABLED for OLVM Engine installation?
- A. virt:rhel
- B. virt:kvm_utils1
* C. virt:kvm_utils2
- D. virt:ol8
> Answer key: C - virt:kvm_utils2

Q: 27. Which repository should be enabled for the latest base operating system packages?
- A. ol7_baseos_latest
* B. ol8_baseos_latest
- C. ol8_appstream
- D. ol8_UEKR7
> Answer key: B - ol8_baseos_latest

Q: 28. What command is used to install the Oracle oVirt release package for Enterprise Linux 8?
- A. dnf install ovirt-release-el8
- B. dnf install oracle-ovirt-el8
* C. dnf install oracle-ovirt-release-el8
- D. dnf install ovirt-engine-release
> Answer key: C - dnf install oracle-ovirt-release-el8

Q: 29. What command is used to install the OLVM Engine package?
- A. yum install olvm-engine
* B. dnf install ovirt-engine
- C. dnf install olvm-manager
- D. yum install oracle-engine
> Answer key: B - dnf install ovirt-engine

Q: 30. What command is used to configure the OLVM Engine after package installation?
- A. ovirt-setup
* B. engine-setup
- C. olvm-configure
- D. manager-setup
> Answer key: B - engine-setup

Q: 31. What type of interface does the engine-setup command provide?
- A. Graphical user interface
- B. Web-based interface
* C. Interactive command-line with questions
- D. Silent installation with config file
> Answer key: C - Interactive command-line with questions

Q: 32. What does the engine-setup command display after all questions are answered?
- A. Error log
* B. Summary of entered values
- C. Installation progress bar
- D. Database schema
> Answer key: B - Summary of entered values

Q: 33. What information is displayed after the engine-setup configuration is complete?
- A. System resource usage
* B. Details about how to log in to the Administration Portal
- C. List of available VMs
- D. Network topology diagram
> Answer key: B - Details about how to log in to the Administration Portal

Q: 34. Which five configuration groupings are part of the engine-setup process? (Choose 5)
* A. Database configuration
- B. Hardware configuration
* C. Network configuration
* D. Administration user setup
* E. Certificates and security
* F. Service configurations
> Answer key: A, C, D, E, F - Database config, Network config, Admin user setup, Certificates/security, Service configs

Q: 35. What can the database configuration in engine-setup allow you to configure? (Choose 2)
* A. A local database instance
* B. A remote database
- C. MySQL database
- D. Oracle Database
> Answer key: A, B - A local database instance, A remote database

Q: 36. What is the default administrative username created during engine-setup?
- A. root
- B. administrator
* C. admin
- D. ovirt
> Answer key: C - admin

Q: 37. What is the name of the domain/profile for the default admin user?
- A. local
* B. internal
- C. default
- D. admin
> Answer key: B - internal

Q: 38. If you enable automatic firewall configuration during engine-setup, what happens?
- A. All ports are opened
- B. The firewall is disabled
* C. Necessary ports for OLVM are automatically allowed
- D. Only SSH port is opened
> Answer key: C - Necessary ports for OLVM are automatically allowed

Q: 39. Where do you configure an alternate hostname for the OLVM Engine?
- A. /etc/hosts
- B. /etc/ovirt-engine/engine.conf
* C. /etc/ovirt-engine/engine.conf.d/custom-sso-setup.conf
- D. /var/lib/ovirt-engine/hostname.conf
> Answer key: C - /etc/ovirt-engine/engine.conf.d/custom-sso-setup.conf

Q: 40. What command is used to restart the OLVM Engine service after configuring an alternate hostname?
- A. service ovirt-engine restart
* B. systemctl restart ovirt-engine
- C. engine-restart
- D. systemctl reload ovirt-engine
> Answer key: B - systemctl restart ovirt-engine

Q: 41. Which three benefits does configuring an alternate hostname provide? (Choose 3)
* A. Enhanced accessibility with multiple entry points
- B. Increased security
* C. Load balancing options
* D. Increased flexibility for network segmentation
- E. Faster VM performance
> Answer key: A, C, D - Enhanced accessibility, Load balancing, Increased flexibility

Q: 42. Which three portals are available in the OLVM Engine web interface? (Choose 3)
* A. Administration Portal
- B. Developer Portal
* C. VM Portal
* D. Monitoring Portal (Grafana)
- E. Storage Portal
> Answer key: A, C, D - Administration Portal, VM Portal, Monitoring Portal

Q: 43. What is the primary purpose of the Administration Portal?
- A. End users managing their VMs only
* B. Comprehensive management of all OLVM components by administrators
- C. Monitoring only
- D. Backup management
> Answer key: B - Comprehensive management of all OLVM components by administrators

Q: 44. What is the Monitoring Portal in OLVM?
- A. A custom Oracle monitoring tool
* B. Grafana for monitoring and analytics
- C. A syslog server
- D. An SNMP trap receiver
> Answer key: B - Grafana for monitoring and analytics

Q: 45. After installing the Engine CA certificate in the browser, what does it allow you to do?
- A. Access VMs directly
* B. Securely access the OLVM Administration Portal without certificate warnings
- C. Increase browser performance
- D. Enable two-factor authentication
> Answer key: B - Securely access the OLVM Administration Portal without certificate warnings

Q: 46. What command is used to enable a repository using dnf config-manager?
- A. dnf config-manager enable <repo-name>
* B. dnf config-manager --set-enabled <repo-name>
- C. dnf enable-repo <repo-name>
- D. dnf repository enable <repo-name>
> Answer key: B - dnf config-manager --set-enabled <repo-name>

Q: 47. What command cleans up the dnf repository cache?
- A. dnf clear
- B. dnf clean
* C. dnf clean all
- D. dnf cache-clean
> Answer key: C - dnf clean all

Q: 48. After installing the ovirt-engine package, what is the FIRST command you run to begin configuration?
- A. ovirt-config
* B. engine-setup
- C. systemctl start ovirt-engine
- D. engine-configure
> Answer key: B - engine-setup

Q: 49. What file extension does the Engine CA certificate have when downloaded?
- A. .crt
- B. .pem
* C. .cer
- D. .p12
> Answer key: Not explicitly stated in transcript, but based on demo: likely .cer or certificate file

Q: 50. When installing the Engine CA certificate in a browser, which certificate store location is recommended?
- A. Current User
* B. Local Machine
- C. Trusted Root
- D. Personal
> Answer key: B - Local Machine 46-50 correct: Excellent - Comprehensive mastery 41-45 correct: Very Good - Strong understanding 36-40 correct: Good - Ready with review 31-35 correct: Fair - Review weak areas Below 31: Needs comprehensive review

```

## Section 3: Core Components

```quiz

Q: 1. What does a Data Center represent in Oracle Linux Virtualization Manager?
- A. A physical building location
* B. The highest organizational level encapsulating clusters, hosts, storage, and network configuration
- C. A type of storage domain
- D. A virtual machine template
> Answer key: B - The highest organizational level encapsulating clusters, hosts, storage, and network configuration

Q: 2. Which four components does a Data Center encapsulate? (Choose 4)
* A. Clusters
- B. Applications
* C. Hosts
* D. Storage domains
* E. Network configuration
- F. User accounts
> Answer key: A, C, D, E - Clusters, Hosts, Storage domains, Network configuration

Q: 3. Can a single oVirt engine manage multiple Data Centers?
- A. No, only one Data Center per engine
* B. Yes, multiple Data Centers per engine
- C. Only in Self-Hosted Engine mode
- D. Only with special licensing
> Answer key: B - Yes, multiple Data Centers per engine

Q: 4. What is the maximum character limit for a Data Center name?
- A. 20 characters
- B. 30 characters
* C. 40 characters
- D. 50 characters
> Answer key: C - 40 characters

Q: 5. Which two storage types can be configured for a Data Center? (Choose 2)
* A. Local storage
- B. Cloud storage
* C. Shared storage
- D. Tape storage
> Answer key: A, C - Local storage, Shared storage

Q: 6. Can local and shared storage domains be mixed in the same Data Center?
- A. Yes, they can be freely mixed
* B. No, they cannot be mixed
- C. Only in production environments
- D. Only with Oracle support approval
> Answer key: B - No, they cannot be mixed

Q: 7. Which storage protocols can be used for SHARED storage in a Data Center? (Choose 5)
* A. iSCSI
- B. SMB/CIFS
* C. NFS
* D. Fiber Channel (FC)
* E. POSIX
* F. Gluster File System
> Answer key: A, C, D, E, F - iSCSI, NFS, FC, POSIX, Gluster FS

Q: 8. When would you typically use a local storage Data Center?
- A. Production enterprise environments
* B. Testing or development environments
- C. High-availability architectures
- D. Disaster recovery sites
> Answer key: B - Testing or development environments

Q: 9. What happens when a Data Center is configured with local storage? (Choose 3)
* A. It supports a single host only
- B. It enables high-availability features
* C. It creates a single non-shareable cluster
* D. Cluster management is not available
- E. VM migration is fully supported
> Answer key: A, C, D - Single host only, Single non-shareable cluster, No cluster management

Q: 10. Does a default Data Center get created during OLVM installation?
* A. Yes, one default Data Center is created
- B. No, you must manually create the first one
- C. Only in Self-Hosted Engine deployments
- D. Only if shared storage is detected
> Answer key: A - Yes, one default Data Center is created

Q: 11. What is a Cluster in Oracle Linux Virtualization Manager?
- A. A physical rack of servers
* B. A group of hosts that share the same storage domains and network configuration
- C. A type of virtual machine
- D. A backup server group
> Answer key: B - A group of hosts that share the same storage domains and network configuration

Q: 12. What must all hosts within a cluster share? (Choose 2)
- A. The same CPU type
- B. The same RAM capacity
* C. The same storage domains
* D. The same network configuration
> Answer key: C, D - Same storage domains, Same network configuration

Q: 13. Where do you access the Clusters management interface in the Administration Portal?
- A. Storage -> Clusters
* B. Compute -> Clusters
- C. Network -> Clusters
- D. Administration -> Clusters
> Answer key: B - Compute -> Clusters

Q: 14. What information is displayed in the Cluster list view? (Choose 2)
* A. Host count
- B. Administrator names
* C. VM count
- D. IP addresses
> Answer key: A, C - Host count, VM count

Q: 15. Does a default cluster get created during OLVM installation?
* A. Yes, in the default Data Center
- B. No, must be manually created
- C. Only for Self-Hosted Engine
- D. Only if hosts are detected
> Answer key: A - Yes, in the default Data Center

Q: 16. Which CPU architecture types can be selected for a cluster? (Choose 3)
* A. x86-64
- B. ARM64
* C. ppc64
* D. s390x
- E. MIPS
> Answer key: A, C, D - x86-64, ppc64, s390x

Q: 17. What does the "Memory Overcommit" optimization feature allow?
- A. Using more physical memory than allocated
* B. Allocating more virtual memory than physical memory available
- C. Sharing memory between clusters
- D. Compressing VM memory
> Answer key: B - Allocating more virtual memory than physical memory available

Q: 18. Which three scheduling policies can be configured for a cluster? (Choose 3)
* A. Even distribution
- B. Random distribution
* C. Power saving
- D. Cost optimization
* E. Strict (No overcommit)
> Answer key: A, C, E - Even distribution, Power saving, Strict

Q: 19. What is the purpose of the Fencing Policy in a cluster?
- A. To encrypt network traffic
* B. To isolate and restart a non-responsive host
- C. To limit VM creation
- D. To manage storage quotas
> Answer key: B - To isolate and restart a non-responsive host

Q: 20. What is the default time delay before fencing is initiated?
- A. 30 seconds
* B. 60 seconds
- C. 90 seconds
- D. 120 seconds
> Answer key: B - 60 seconds

Q: 21. What do Migration Policies define?
- A. How VMs are created
- B. Storage allocation rules
* C. Thresholds for automatic VM migration to balance load and number of parallel migrations
- D. Network bandwidth limits
> Answer key: C - Thresholds for automatic VM migration and parallel migrations

Q: 22. What is the purpose of Positive Affinity rules for VMs?
- A. VMs are placed on different hosts
* B. VMs are placed on the same host
- C. VMs are suspended
- D. VMs are migrated frequently
> Answer key: B - VMs are placed on the same host

Q: 23. What is the purpose of Negative Affinity rules for VMs?
- A. VMs are placed on the same host
* B. VMs are placed on different hosts
- C. VMs cannot be started
- D. VMs share the same storage
> Answer key: B - VMs are placed on different hosts

Q: 24. What does the Enforcement Mode setting determine for affinity rules?
- A. How many VMs can be created
* B. How strictly the affinity rules are enforced
- C. Storage allocation limits
- D. Network bandwidth
> Answer key: B - How strictly the affinity rules are enforced

Q: 25. If the Enforcement Mode for an affinity rule is DISABLED, what happens?
- A. The rules are strictly enforced
* B. The rules are preferences but not strictly enforced
- C. The rules are ignored completely
- D. VMs cannot be started
> Answer key: B - The rules are preferences but not strictly enforced

Q: 26. What are Affinity Labels used for?
- A. Encrypting VM data
* B. Tagging and categorizing VMs and hosts to group them logically
- C. Naming storage domains
- D. Configuring network VLANs
> Answer key: B - Tagging and categorizing VMs and hosts to group them logically

Q: 27. Which two types of affinity rules can be defined? (Choose 2)
* A. VM to VM affinity rules
- B. Storage to VM affinity rules
* C. VM to host affinity rules
- D. Network to host affinity rules
> Answer key: A, C - VM to VM affinity rules, VM to host affinity rules

Q: 28. What are the two major types of physical hosts in OLVM? (Choose 2)
* A. Engine host
- B. Storage host
- C. Network host
* D. KVM host
> Answer key: A, D - Engine host, KVM host

Q: 29. What is the primary function of the Engine host?
- A. Running virtual machines
* B. Providing central management and administration tools
- C. Storing VM disk images
- D. Managing network switches
> Answer key: B - Providing central management and administration tools

Q: 30. What is the primary function of KVM hosts?
- A. Managing the Administration Portal
* B. Hosting and running virtual machines
- C. Storing backup data
- D. Running the database
> Answer key: B - Hosting and running virtual machines

Q: 31. Can a single OLVM engine manage multiple KVM hosts?
- A. No, only one host per engine
* B. Yes, a single engine can manage multiple hosts
- C. Only in clustered mode
- D. Only with enterprise licensing
> Answer key: B - Yes, a single engine can manage multiple hosts

Q: 32. Where in the Administration Portal do you manage hosts?
- A. Storage -> Hosts
* B. Compute -> Hosts
- C. Network -> Hosts
- D. Administration -> Hosts
> Answer key: B - Compute -> Hosts

Q: 33. What information is displayed in the host list view? (Choose 4)
* A. Host name or IP address
* B. Cluster name
* C. Data Center name
* D. Active virtual machines
- E. CPU temperature
* F. Memory used
> Answer key: A, B, C, D, F - Host name/IP, Cluster name, Data Center name, Active VMs, Memory used

Q: 34. What does the "Maintenance Mode" do for a host?
- A. Shuts down the host permanently
* B. Prepares the host for maintenance by migrating VMs away
- C. Increases host performance
- D. Resets host configuration
> Answer key: B - Prepares the host for maintenance by migrating VMs away

Q: 35. What is the purpose of converting a host to SPM (Storage Pool Manager) role?
- A. To increase VM performance
* B. To manage storage domains within the Data Center
- C. To enable backups
- D. To configure networks
> Answer key: B - To manage storage domains within the Data Center

Q: 36. What is the Storage Pool Manager (SPM)?
- A. A physical storage device
* B. A management role assigned to one host in the Data Center
- C. A type of storage domain
- D. A backup application
> Answer key: B - A management role assigned to one host in the Data Center

Q: 37. How many hosts can have the SPM role in a single Data Center at one time?
- A. All hosts share the SPM role
* B. Only one host at a time
- C. Two hosts for redundancy
- D. Up to five hosts
> Answer key: B - Only one host at a time

Q: 38. What is the primary responsibility of the SPM?
- A. Running virtual machines
* B. Coordinating all metadata changes like creating disks or snapshots within the Data Center
- C. Managing network traffic
- D. User authentication
> Answer key: B - Coordinating all metadata changes like creating disks or snapshots

Q: 39. Does losing SPM functionality affect running VMs?
- A. Yes, all VMs stop immediately
* B. No, VMs continue running but storage operations are affected
- C. Only new VMs are affected
- D. VMs are automatically migrated
> Answer key: B - No, VMs continue running but storage operations are affected

Q: 40. Which three types of storage domains exist in OLVM? (Choose 3)
* A. Data Domain
- B. Boot Domain
* C. ISO Domain
* D. Export Domain
- E. Template Domain
> Answer key: A, C, D - Data Domain, ISO Domain, Export Domain

Q: 41. Can a storage domain be shared across multiple Data Centers?
- A. Yes, freely shared
* B. No, a storage domain belongs to only one Data Center
- C. Only ISO domains can be shared
- D. Only with special configuration
> Answer key: B - No, a storage domain belongs to only one Data Center

Q: 42. What must be configured for a Data Center before it can be initialized?
- A. At least one cluster
- B. At least one host
* C. At least one storage domain (Data Domain)
- D. At least one virtual machine
> Answer key: C - At least one storage domain (Data Domain)

Q: 43. For VMs to be migrated between hosts, what storage requirement must be met?
- A. Each host needs local storage
* B. VMs must share the same storage domain
- C. Storage must be SSD-based
- D. VMs must have pre-allocated disks
> Answer key: B - VMs must share the same storage domain

Q: 44. What is the default provisioning method for virtual disks?
- A. Pre-allocated
* B. Thin provisioning
- C. Thick provisioning
- D. Compressed
> Answer key: B - Thin provisioning

Q: 45. What happens with thin-provisioned disks?
- A. Full disk space is allocated immediately
* B. Disk space is allocated only as data is written, starting with minimal space
- C. Disk cannot grow beyond initial size
- D. Disk is stored on local storage only
> Answer key: B - Disk space is allocated only as data is written, starting with minimal space

Q: 46. When should pre-allocated disks be used?
- A. For test and development VMs
- B. For VMs with low I/O requirements
* C. For VMs with high levels of input/output operations
- D. Never, thin provisioning is always better
> Answer key: C - For VMs with high levels of input/output operations

Q: 47. What benefit do pre-allocated disks provide for high I/O workloads?
- A. They use less storage space
* B. They maintain consistent performance by reducing latency from space allocation
- C. They enable faster VM migration
- D. They require less memory
> Answer key: B - They maintain consistent performance by reducing latency from space allocation

Q: 48. Which two primary log files are used in OLVM? (Choose 2)
* A. engine.log
- B. system.log
* C. vdsm.log
- D. kernel.log
> Answer key: A, C - engine.log, vdsm.log

Q: 49. Where is the engine log file typically located?
- A. /var/log/messages
* B. /var/log/ovirt-engine/engine.log
- C. /var/log/olvm/engine.log
- D. /etc/ovirt-engine/engine.log
> Answer key: B - /var/log/ovirt-engine/engine.log

Q: 50. What does the VDSM log file contain?
- A. User login activities
* B. Events and operations related to individual hosts including VM startups, migrations, and storage activities
- C. Network traffic logs
- D. Database queries
> Answer key: B - Events and operations related to individual hosts including VM startups, migrations, and storage activities

Q: 51. What tool is used to collect logs for sharing with Oracle support?
- A. logcollector
* B. ovirt-log-collector
- C. engine-backup
- D. sosreport
> Answer key: B - ovirt-log-collector

Q: 52. Where can administrators view alerts and events in the Administration Portal?
- A. Events tab
* B. Notification Drawer
- C. Dashboard only
- D. System Settings
> Answer key: B - Notification Drawer

Q: 53. Which two notification methods can be configured in OLVM? (Choose 2)
* A. Email notifications
- B. SMS messages
* C. SNMP traps
- D. Slack integration
> Answer key: A, C - Email notifications, SNMP traps 48-53 correct: Excellent - Comprehensive mastery 43-47 correct: Very Good - Strong understanding 38-42 correct: Good - Ready with review 33-37 correct: Fair - Review weak areas Below 33: Needs comprehensive review - Data Center hierarchy and organization - Storage type limitations (local vs shared, cannot mix) - Cluster configuration (CPU types, policies, fencing) - Affinity rules (positive vs negative, enforcement) - SPM role (one per Data Center) - Storage domains (one Data Center only, required before init) - Thin vs pre-allocated disk provisioning - Log file locations and log collection tools

```

## Section 4: KVM Host Installation

```quiz

Q: 1. What is the minimum Oracle Linux version required for a KVM host?
- A. Oracle Linux 7.5
* B. Oracle Linux 8.5 or later
- C. Oracle Linux 9.0
- D. Oracle Linux 8.0
> Answer key: B - Oracle Linux 8.5 or later

Q: 2. Which installation type is recommended for the KVM host operating system?
- A. Full installation with GUI
* B. Minimal install
- C. Server with GUI
- D. Workstation installation
> Answer key: B - Minimal install

Q: 3. Which two Unbreakable Enterprise Kernel releases are supported for KVM hosts? (Choose 2)
- A. UEKR5
* B. UEKR6
* C. UEKR7
- D. UEKR8
> Answer key: B, C - UEKR6 and UEKR7

Q: 4. Which additional repository channel is required specifically for VDSM on the KVM host?
- A. UEKR6
* B. UEKR7
- C. Gluster AppStream
- D. KVM AppStream
> Answer key: B - UEKR7

Q: 5. What is the MINIMUM CPU requirement for a KVM host?
- A. Single-core 32-bit CPU
* B. 64-bit dual-core CPU
- C. 64-bit quad-core CPU
- D. 64-bit eight-core CPU
> Answer key: B - 64-bit dual-core CPU

Q: 6. What is RECOMMENDED for better performance in a KVM host?
- A. Single CPU with hyper-threading
* B. Multiple CPUs
- C. Higher clock speed only
- D. Overclocked CPUs
> Answer key: B - Multiple CPUs

Q: 7. Which two hardware virtualization technologies must the CPU support? (Choose 2)
* A. Intel VT-x
- B. Intel SGX
* C. AMD-V
- D. ARM TrustZone
> Answer key: A, C - Intel VT-x and AMD-V

Q: 8. What is the MINIMUM RAM required for a KVM host?
- A. 1 GB
* B. 2 GB
- C. 4 GB
- D. 8 GB
> Answer key: B - 2 GB

Q: 9. What is the MAXIMUM tested RAM capacity for Oracle Linux Virtualization Manager hosts?
- A. 1 terabyte
- B. 2 terabytes
- C. 4 terabytes
* D. 6 terabytes
> Answer key: D - 6 terabytes

Q: 10. What is the minimum local storage requirement for a KVM host?
- A. 30 GB
- B. 40 GB
- C. 50 GB
* D. 60 GB
> Answer key: D - 60 GB

Q: 11. What is the purpose of the 60 GB local storage on a KVM host?
- A. Storing all virtual machine disks
* B. Dedicated for host operating system and OLVM components
- C. Backup storage only
- D. ISO image storage
> Answer key: B - Dedicated for host operating system and OLVM components

Q: 12. What is the MINIMUM network interface requirement for a KVM host?
- A. One NIC with 100 Mbps bandwidth
* B. One NIC with 1 Gbps bandwidth
- C. Two NICs with 1 Gbps bandwidth
- D. Four NICs with 1 Gbps bandwidth
> Answer key: B - One NIC with 1 Gbps bandwidth

Q: 13. What is RECOMMENDED for network configuration on a KVM host?
- A. One NIC with 10 Gbps
* B. Two or more NICs with at least 1 Gbps bandwidth
- C. Four NICs with 100 Mbps
- D. Single bonded interface
> Answer key: B - Two or more NICs with at least 1 Gbps bandwidth

Q: 14. Why are multiple NICs recommended for KVM hosts?
- A. For load balancing only
- B. For redundancy only
* C. NICs can be dedicated for network-intensive activities like VM migration
- D. To connect to multiple data centers
> Answer key: C - NICs can be dedicated for network-intensive activities like VM migration

Q: 15. When are firewall configurations automatically set up on a KVM host?
- A. During OS installation
* B. When the host is registered with the engine
- C. Never, must be done manually
- D. Only for production hosts
> Answer key: B - When the host is registered with the engine

Q: 16. What port is used for SSH access between the KVM host and Manager?
- A. 21
* B. 22
- C. 23
- D. 3389
> Answer key: B - 22

Q: 17. Which two ports are used for web management and web access? (Choose 2)
* A. 8080
- B. 8000
* C. 8443
- D. 9000
> Answer key: A, C - 8080 and 8443

Q: 18. What is the port range for VM Console Access using SPICE or VNC?
- A. 3389 to 3400
- B. 5800 to 5900
* C. 5900 to 6923
- D. 8000 to 9000
> Answer key: C - 5900 to 6923

Q: 19. Which two ports are used for VDSM communication? (Choose 2)
- A. 54320
* B. 54321
* C. 54322
- D. 54323
> Answer key: B, C - 54321 and 54322

Q: 20. Which three ports are required for high availability with configured cluster management? (Choose 3)
* A. 5405 UDP
* B. 5406 UDP
- C. 5407 TCP
* D. 2224 TCP
- E. 2225 UDP
> Answer key: A, B, D - 5405 UDP, 5406 UDP, 2224 TCP

Q: 21. What is the MAXIMUM number of physical CPU cores tested for a KVM host?
- A. 128
- B. 256
* C. 384
- D. 512
> Answer key: C - 384

Q: 22. What is the MAXIMUM tested memory capacity for a KVM host?
- A. 2 terabytes
- B. 4 terabytes
* C. 6 terabytes
- D. 8 terabytes
> Answer key: C - 6 terabytes

Q: 23. What is the MAXIMUM number of concurrently running VMs tested on a single KVM host?
- A. 300
- B. 400
- C. 500
* D. Above 600
> Answer key: D - Above 600

Q: 24. Which repository contains the latest version of Oracle Linux 8 base OS packages?
- A. ol8_baseos
* B. ol8_baseos_latest
- C. ol8_latest
- D. baseos_latest
> Answer key: B - ol8_baseos_latest

Q: 25. Which repository provides additional application packages not included in the base OS?
- A. ol8_applications
* B. ol8_AppStream
- C. ol8_extras
- D. ol8_apps
> Answer key: B - ol8_AppStream

Q: 26. Which repository specifically provides KVM-related packages?
- A. ol8_virtualization
- B. ol8_kvm
* C. ol8_kvm_appstream
- D. kvm_utils
> Answer key: C - ol8_kvm_appstream

Q: 27. Which repository contains packages for oVirt 4.4 on Oracle Linux 8?
- A. ol8_ovirt
* B. ol8_ovirt44
- C. ovirt44_ol8
- D. ol8_virtualization44
> Answer key: B - ol8_ovirt44

Q: 28. Which repository provides supplementary packages for oVirt 4.4?
- A. ol8_ovirt44_additions
* B. ol8_ovirt44_extras
- C. ol8_ovirt_extras
- D. ovirt_supplemental
> Answer key: B - ol8_ovirt44_extras

Q: 29. Which repository provides Gluster file system packages?
- A. ol8_gluster
- B. ol8_gluster_appstream
* C. ol8_64_gluster_appstream
- D. gluster_ol8
> Answer key: C - ol8_64_gluster_appstream

Q: 30. Which repository contains Unbreakable Enterprise Kernel Release 7 packages?
- A. ol8_UEKR7
* B. ol8_64_UEKR7
- C. UEKR7_ol8
- D. uek_release7
> Answer key: B - ol8_64_UEKR7

Q: 31. Which virt module must be DISABLED when preparing a KVM host?
- A. virt:rhel
* B. virt:ol
- C. virt:kvm
- D. virt:enterprise
> Answer key: B - virt:ol

Q: 32. Which virt module must be ENABLED when preparing a KVM host?
- A. virt:kvm
- B. virt:kvm_utils
* C. virt:kvm_utils2
- D. virt:ol8
> Answer key: C - virt:kvm_utils2

Q: 33. What command is used to install the Oracle oVirt release package on the KVM host?
- A. dnf install ovirt-release-el8
- B. dnf install oracle-ovirt-el8
* C. dnf install oracle-ovirt-release-el8
- D. dnf install olvm-release-el8
> Answer key: C - dnf install oracle-ovirt-release-el8

Q: 34. What command cleans the dnf repository cache after configuring repositories?
- A. dnf clear
- B. dnf clean
* C. dnf clean all
- D. dnf cache-clean
> Answer key: C - dnf clean all

Q: 35. Where in the Administration Portal do you add a new KVM host?
- A. Storage -> Hosts
* B. Compute -> Hosts
- C. Network -> Hosts
- D. Configuration -> Hosts
> Answer key: B - Compute -> Hosts

Q: 36. What button do you click to begin adding a new host?
- A. Add
* B. New
- C. Create
- D. Register
> Answer key: B - New

Q: 37. Which three pieces of information are required when adding a new host? (Choose 3)
* A. Host name (unique identifier)
- B. Email address
* C. Hostname (FQDN)
* D. SSH port
- E. Physical location
> Answer key: A, C, D - Host name (unique), Hostname (FQDN), SSH port

Q: 38. Which two authentication methods can be used when adding a KVM host? (Choose 2)
* A. Password authentication
- B. Kerberos
* C. SSH key authentication
- D. Certificate authentication
> Answer key: A, C - Password authentication and SSH key authentication

Q: 39. For which user account must authentication credentials be provided when adding a host?
- A. admin user
* B. root user
- C. ovirt user
- D. vdsm user
> Answer key: B - root user

Q: 40. If you select SSH key authentication, where must the SSH public key be placed on the KVM host?
- A. /etc/ssh/authorized_keys
* B. /root/.ssh/authorized_keys
- C. /home/ovirt/.ssh/authorized_keys
- D. /var/lib/ovirt/authorized_keys
> Answer key: B - /root/.ssh/authorized_keys

Q: 41. When adding multiple SSH keys to authorized_keys file, where should the engine's SSH key be placed?
- A. At the end of the file
* B. As the first key in the file
- C. Anywhere in the file
- D. In a separate file
> Answer key: B - As the first key in the file

Q: 42. What option in the Add Host wizard automatically configures firewall rules?
- A. Enable Firewall
- B. Configure Firewall
* C. Automatically Configure Host Firewall
- D. Setup Firewall Rules
> Answer key: C - Automatically Configure Host Firewall

Q: 43. After adding a host, what indicates the host is successfully registered and running?
- A. Red arrow icon
- B. Yellow arrow icon
* C. Green arrow icon
- D. Blue arrow icon
> Answer key: C - Green arrow icon

Q: 44. Which command is used to set SELinux enforcing policies on the KVM host?
- A. selinux
- B. setenforce
* C. semanage
- D. sestatus
> Answer key: C - semanage

Q: 45. Which SELinux boolean policy allows fencing to connect to the network?
- A. fenced_network_connect
* B. fenced_can_network_connect
- C. fence_network_enabled
- D. network_fence_allow
> Answer key: B - fenced_can_network_connect

Q: 46. Which SELinux boolean policy allows virtualization to use NFS?
- A. virt_nfs_enabled
* B. virt_use_nfs
- C. nfs_virt_enabled
- D. allow_virt_nfs
> Answer key: B - virt_use_nfs

Q: 47. What is the purpose of setting the OTOPI_PYTHON environment variable?
- A. To enable Python on the host
* B. To specify the Python version for OTOPI operations and avoid package mismatches
- C. To install Python packages
- D. To configure Python security
> Answer key: B - To specify the Python version for OTOPI operations and avoid package mismatches

Q: 48. Where is the OTOPI_PYTHON environment variable typically configured?
- A. /etc/python.env
* B. /etc/otopi.env.d/my-python.env
- C. /var/lib/otopi/python.conf
- D. /root/.python_env
> Answer key: B - /etc/otopi.env.d/my-python.env

Q: 49. In a testing/lab environment, what service might be disabled (though NOT recommended for production)?
- A. sshd
- B. NetworkManager
* C. firewalld
- D. vdsmd
> Answer key: C - firewalld

Q: 50. What command is used to disable the firewall daemon?
- A. firewall-cmd --disable
* B. systemctl disable firewalld
- C. service firewalld disable
- D. firewall disable
> Answer key: B - systemctl disable firewalld 46-50 correct: Excellent - Comprehensive mastery 41-45 correct: Very Good - Strong understanding 36-40 correct: Good - Ready with review 31-35 correct: Fair - Review weak areas Below 31: Needs comprehensive review - Hardware requirements (CPU, memory, storage, network) - Scalability limits (384 CPUs, 6TB RAM, 600+ VMs) - Repository names and purposes (7 repositories to know) - Firewall port numbers (22, 8080/8443, 5900-6923, 54321/54322, 5405/5406/2224) - virt module management (disable :ol, enable :kvm_utils2) - SSH key authentication and authorized_keys placement - SELinux policy configuration (fenced_can_network_connect, virt_use_nfs) - Post-installation tasks (OTOPI_PYTHON, firewalld)

```

## Section 5: Storage Domain Management

```quiz

Q: 1. What does OLVM storage provide centralized storage for? (Choose 3)
* A. Virtual machine disk images
- B. User home directories
* C. ISO files
* D. Snapshots
- E. Email archives
> Answer key: A, C, D - VM disk images, ISO files, Snapshots

Q: 2. Which storage protocol allows OLVM hosts to access block-level storage over TCP/IP networks?
- A. NFS
* B. iSCSI
- C. SMB
- D. HTTP
> Answer key: B - iSCSI

Q: 3. Which storage protocol allows OLVM hosts to connect to fiber channel SAN storage systems?
- A. NFS
- B. iSCSI
* C. FCP (Fiber Channel Protocol)
- D. GlusterFS
> Answer key: C - FCP (Fiber Channel Protocol)

Q: 4. Which storage protocol is a distributed file system providing scalability and redundancy?
- A. NFS
- B. iSCSI
- C. FCP
* D. GlusterFS
> Answer key: D - GlusterFS

Q: 5. Can a Data Center be initialized without a storage domain attached?
- A. Yes, storage is optional
* B. No, at least one storage domain must be attached before initialization
- C. Only in test environments
- D. Only for Self-Hosted Engine
> Answer key: B - No, at least one storage domain must be attached before initialization

Q: 6. Which type of storage does OLVM support? (Choose 2)
* A. Block device storage (SAN)
- B. Tape storage
* C. File system storage (NAS)
- D. Cloud object storage
> Answer key: A, C - Block device storage (SAN), File system storage (NAS)

Q: 7. Which two protocols are examples of block device (SAN) storage? (Choose 2)
- A. NFS
* B. iSCSI
* C. FCP
- D. GlusterFS
> Answer key: B, C - iSCSI, FCP

Q: 8. Which two protocols are examples of file system (NAS) storage? (Choose 2)
- A. iSCSI
* B. NFS
- C. FCP
* D. GlusterFS
> Answer key: B, D - NFS, GlusterFS

Q: 9. For VMs to be migrated between hosts, what storage requirement must be met?
- A. Each host needs local storage
* B. VMs must share the same storage domain
- C. Storage must be SSD-based
- D. VMs must use iSCSI only
> Answer key: B - VMs must share the same storage domain

Q: 10. Can a storage domain be shared between multiple Data Centers?
- A. Yes, freely shared
* B. No, storage domains cannot be shared between different OLVM Data Centers
- C. Only ISO domains can be shared
- D. Only with special configuration
> Answer key: B - No, storage domains cannot be shared between different OLVM Data Centers

Q: 11. What is the Storage Pool Manager (SPM)?
- A. A physical storage device
* B. A management role assigned to one host in the Data Center
- C. A type of storage domain
- D. A backup application
> Answer key: B - A management role assigned to one host in the Data Center

Q: 12. How many hosts can have the SPM role in a single Data Center at one time?
- A. All hosts share the SPM role equally
* B. Only one host at a time
- C. Two hosts for redundancy
- D. Up to five hosts
> Answer key: B - Only one host at a time

Q: 13. What is the primary responsibility of the SPM?
- A. Running virtual machines
* B. Coordinating storage metadata operations like creating disks or snapshots
- C. Managing network traffic
- D. User authentication
> Answer key: B - Coordinating storage metadata operations like creating disks or snapshots

Q: 14. If the host with the SPM role fails, what happens?
- A. All storage operations stop permanently
* B. OLVM automatically assigns the SPM role to another host
- C. The Data Center becomes unavailable
- D. All VMs must be restarted
> Answer key: B - OLVM automatically assigns the SPM role to another host

Q: 15. What type of cluster is created when configuring local storage?
- A. Multi-host cluster
* B. Single-host cluster
- C. High-availability cluster
- D. Distributed cluster
> Answer key: B - Single-host cluster

Q: 16. Can VMs in a local storage domain be migrated to other hosts?
- A. Yes, freely migrated
* B. No, migration is not possible
- C. Only with special configuration
- D. Only during maintenance windows
> Answer key: B - No, migration is not possible

Q: 17. Can VMs in a local storage domain be fenced?
- A. Yes, fencing works normally
* B. No, fencing is not available
- C. Only manual fencing
- D. Only with power management
> Answer key: B - No, fencing is not available

Q: 18. What happens when you configure a KVM host for local storage if it was previously in a clustered Data Center?
- A. It remains in the cluster
* B. It is automatically removed from the previous Data Center and cluster
- C. Managing network traffic
- C. It joins both clusters
- D. It requires manual removal first
> Answer key: B - It is automatically removed from the previous Data Center and cluster

Q: 19. What is the RECOMMENDED way to configure local storage?
- A. Use the root filesystem
- B. Create a directory on existing partition
* C. Use a separate logical volume or disk
- D. Use network-attached storage
> Answer key: C - Use a separate logical volume or disk

Q: 20. After creating and mounting a local storage disk, where should it be added to ensure automatic mounting on reboot?
- A. /etc/hosts
* B. /etc/fstab
- C. /etc/mount
- D. /etc/storage
> Answer key: B - /etc/fstab

Q: 21. What must the host's state be before configuring local storage?
- A. Up
* B. Maintenance
- C. Non-operational
- D. Installing
> Answer key: B - Maintenance

Q: 22. What is the recommended operating system for OLVM manager and KVM hosts when using NFS storage?
- A. Any Linux distribution
* B. Oracle Linux
- C. Windows Server
- D. FreeBSD
> Answer key: B - Oracle Linux

Q: 23. What is the default user ID for the VDSM user?
- A. 0
* B. 36
- C. 100
- D. 1000
> Answer key: B - 36

Q: 24. What is the default group ID for the KVM group?
- A. 0
* B. 36
- C. 100
- D. 1000
> Answer key: B - 36

Q: 25. Which users are members of the KVM group? (Choose 2)
- A. root
* B. QEMU
* C. sanlock
- D. vdsm
> Answer key: B, C - QEMU, sanlock

Q: 26. What command is used to install NFS utilities on the NFS server?
- A. yum install nfs
* B. yum install nfs-utils
- C. dnf install nfs-server
- D. yum install nfs-tools
> Answer key: B - yum install nfs-utils

Q: 27. Which services must be added to the firewall for NFS? (Choose 3)
* A. nfs
- B. ssh
- C. nfs3
* D. mountd
* E. rpc-bind
> Answer key: A, D, E - nfs, mountd, rpc-bind

Q: 28. What command reloads the firewall after adding services?
- A. firewall-cmd restart
* B. firewall-cmd --reload
- C. systemctl restart firewalld
- D. service firewall reload
> Answer key: B - firewall-cmd --reload

Q: 29. What must be the ownership of NFS share directories?
- A. root:root
* B. vdsm:kvm
- C. qemu:sanlock
- D. admin:users
> Answer key: B - vdsm:kvm

Q: 30. What is the purpose of the /etc/exports file on an NFS server?
- A. To list exported users
* B. To define NFS export configurations for sharing directories
- C. To configure firewall rules
- D. To manage user permissions
> Answer key: B - To define NFS export configurations for sharing directories

Q: 31. What command applies NFS export configurations?
- A. exportfs -a
* B. exportfs -ra
- C. nfs-export apply
- D. systemctl reload nfs iSCSI STORAGE DOMAIN
> Answer key: B - exportfs -ra

Q: 32. What must be installed on KVM hosts before using iSCSI storage?
- A. NFS utilities
* B. iSCSI initiator software
- C. Fiber channel drivers
- D. GlusterFS client
> Answer key: B - iSCSI initiator software

Q: 33. What information is required to discover iSCSI targets? (Choose 2)
* A. IP address of storage server
- B. MAC address of storage server
* C. Port number
- D. DNS hostname only
> Answer key: A, C - IP address of storage server, Port number

Q: 34. What is the default port number for iSCSI?
- A. 22
* B. 3260
- C. 5432
- D. 8080
> Answer key: B - 3260

Q: 35. What authentication method can optionally be used with iSCSI?
- A. Kerberos
- B. LDAP
* C. CHAP
- D. OAuth
> Answer key: C - CHAP

Q: 36. What does LUN stand for in iSCSI terminology?
- A. Local Unit Number
* B. Logical Unit Number
- C. Linux Unified Node
- D. Link Universal Network
> Answer key: B - Logical Unit Number

Q: 37. What is the purpose of iSCSI multipathing?
- A. To increase storage capacity
* B. To prevent host downtime caused by network path failures
- C. To encrypt data transfers
- D. To compress data
> Answer key: B - To prevent host downtime caused by network path failures

Q: 38. What is an iSCSI bond?
- A. A physical cable connection
* B. A logical connection that aggregates multiple physical and logical connections to iSCSI targets
- C. A storage encryption method
- D. A user authentication mechanism
> Answer key: B - A logical connection that aggregates multiple physical and logical connections to iSCSI targets

Q: 39. How many paths to the same iSCSI target should be selected when configuring multipathing?
- A. Only one path
* B. All paths to the same target
- C. Maximum of two paths
- D. Paths from different targets
> Answer key: B - All paths to the same target

Q: 40. What is GlusterFS?
- A. A block storage protocol
* B. A distributed file system
- C. A backup application
- D. A virtual machine hypervisor
> Answer key: B - A distributed file system

Q: 41. What format is used to specify the GlusterFS volume when creating a storage domain?
- A. IP:/volume_name
* B. FQDN:/volume_name
- C. volume_name@FQDN
- D. //FQDN/volume_name
> Answer key: B - FQDN:/volume_name

Q: 42. What port is typically used for GlusterFS management?
- A. 22
- B. 3260
- C. 24007
* D. The transcript does not specify
> Answer key: D - The transcript does not specify (Actually 24007 but not in transcript)

Q: 43. What must be the state of a storage domain before it can be detached from a Data Center?
- A. Active
* B. Maintenance
- C. Locked
- D. Unattached
> Answer key: B - Maintenance

Q: 44. Can you move a storage domain to maintenance mode if a VM has a lease on it?
- A. Yes, always
* B. No, you cannot
- C. Only if the VM is powered off
- D. Only with admin privileges
> Answer key: B - No, you cannot

Q: 45. Can you detach a storage domain from one Data Center and attach it to another?
* A. Yes, but not simultaneously to both
- B. No, once attached it's permanent
- C. Yes, it can be attached to multiple Data Centers
- D. Only ISO domains can be moved
> Answer key: A - Yes, but not simultaneously to both

Q: 46. What is the MINIMUM number of storage domains a Data Center must have to remain active?
- A. Zero
* B. One (at least one storage domain)
- C. Two
- D. Three
> Answer key: B - One (at least one storage domain)

Q: 47. Where do you upload ISO images in the Administration Portal?
- A. Compute -> VMs
* B. Storage -> Disks
- C. Network -> Images
- D. Administration -> Media
> Answer key: B - Storage -> Disks

Q: 48. What button is used to begin uploading an ISO image?
- A. New
- B. Import
* C. Upload
- D. Add
> Answer key: C - Upload

Q: 49. What must be installed in the browser to successfully upload images without connection errors?
- A. Java plugin
- B. Flash player
* C. Engine CA certificate
- D. ActiveX controls
> Answer key: C - Engine CA certificate

Q: 50. What error might occur if the transfer image client inactivity timeout is too low?
- A. Connection refused
* B. Timeout due to transfer inactivity error
- C. Disk full error
- D. Authentication failure
> Answer key: B - Timeout due to transfer inactivity error

Q: 51. In the practice labs using Oracle Cloud Infrastructure, what type of attachment is used for block volumes?
- A. NFS
* B. iSCSI
- C. Paravirtualized
- D. SMB
> Answer key: B - iSCSI

Q: 52. What access mode is typically configured for block volumes attached to storage servers?
- A. Read-only
- B. Write-only
* C. Read-write
- D. No access
> Answer key: C - Read-write

Q: 53. What command is used to list newly attached disks on a Linux system?
- A. lsblk
* B. fdisk -l
- C. df -h
- D. mount
> Answer key: B - fdisk -l

Q: 54. After attaching block devices via iSCSI commands, what tool can partition the devices?
- A. parted
- B. fdisk
- C. gdisk
* D. All of the above
> Answer key: D - All of the above

Q: 55. Where are pre-created block volumes located in Oracle Cloud Infrastructure?
- A. Compute -> Instances
* B. Storage -> Block Volumes
- C. Network -> Storage
- D. Database -> Volumes
> Answer key: B - Storage -> Block Volumes 51-55 correct: Excellent - Comprehensive mastery 46-50 correct: Very Good - Strong understanding 41-45 correct: Good - Ready with review 36-40 correct: Fair - Review weak areas Below 36: Needs comprehensive review - Storage cannot be shared between Data Centers - SPM: one host per Data Center, automatically reassigned - Local storage: single-host cluster, no migration/fencing - VDSM user ID and KVM group ID: both 36 - NFS ownership: vdsm:kvm - iSCSI default port: 3260 - iSCSI multipathing: all paths to same target - GlusterFS format: FQDN:/volume_name - Storage domain maintenance required before detach - Minimum one storage domain per Data Center

```

## Section 6: Network Management

```quiz

Q: 1. What is network bonding?
- A. Connecting VMs to the network
* B. Combining multiple network interfaces into a single logical interface to increase throughput and provide redundancy
- C. Encrypting network traffic
- D. Creating virtual networks
> Answer key: B - Combining multiple network interfaces into a single logical interface for throughput and redundancy

Q: 2. Why is network bonding recommended especially on production hosts?
- A. To reduce costs
* B. To ensure high availability and fault tolerance
- C. To simplify configuration
- D. To increase security only
> Answer key: B - To ensure high availability and fault tolerance

Q: 3. What is the purpose of using VLANs in OLVM?
- A. To increase network speed
* B. To segment network traffic logically for enhanced security and performance
- C. To reduce the number of physical switches needed
- D. To enable wireless connectivity
> Answer key: B - To segment network traffic logically for enhanced security and performance

Q: 4. Which types of traffic can be separated using VLANs? (Choose 3)
* A. Management network traffic
- B. Email traffic
* C. Storage network traffic
* D. User traffic
- E. DNS queries
> Answer key: A, C, D - Management traffic, Storage traffic, User traffic

Q: 5. When adding a physical NIC with VLAN support, where should the VLAN be assigned?
- A. To the virtual machine
* B. Directly to the physical interface
- C. To the logical network only
- D. To the cluster
> Answer key: B - Directly to the physical interface

Q: 6. What does FQDN stand for?
- A. Fast Query Domain Name
* B. Fully Qualified Domain Name
- C. Forward Query DNS Name
- D. File Query Domain Network
> Answer key: B - Fully Qualified Domain Name

Q: 7. Which two types of name resolution must OLVM support? (Choose 2)
* A. Forward name resolution (domain name to IP)
- B. Lateral name resolution
* C. Reverse name resolution (IP to domain name)
- D. Broadcast name resolution
> Answer key: A, C - Forward name resolution, Reverse name resolution

Q: 8. Where should DNS servers used for OLVM name resolution be hosted?
- A. As VMs inside the OLVM environment
- B. On the engine host
* C. Outside of the OLVM environment
- D. On each KVM host
> Answer key: C - Outside of the OLVM environment

Q: 9. Why should DNS servers be hosted outside the OLVM environment?
- A. To reduce costs
* B. To prevent dependency loops and ensure DNS availability even if the virtual environment encounters issues
- C. To increase DNS performance
- D. Because VMs cannot run DNS services
> Answer key: B - To prevent dependency loops and ensure DNS availability

Q: 10. What are logical networks in OLVM?
- A. Physical network cables
* B. Representations of network resources that provide connectivity for KVM virtual machines
- C. Virtual switches only
- D. Network security policies
> Answer key: B - Representations of network resources that provide connectivity for KVM VMs

Q: 11. At what level are network roles assigned to logical networks?
- A. Data Center level
* B. Cluster level
- C. Host level
- D. VM level
> Answer key: B - Cluster level

Q: 12. What is the purpose of a Management network role?
- A. Running VMs
* B. Handling management traffic between OLVM engine and KVM hosts for administrative tasks
- C. Providing internet access
- D. Storing backups
> Answer key: B - Handling management traffic between OLVM engine and KVM hosts

Q: 13. What is the purpose of a VM network role?
- A. Managing the engine
* B. Carrying traffic generated by virtual machines
- C. Live migration only
- D. Storage access
> Answer key: B - Carrying traffic generated by virtual machines

Q: 14. What is the purpose of a Display network role?
- A. Managing VMs
* B. Handling display traffic such as graphical display output of virtual machines
- C. Storing VM images
- D. Network monitoring
> Answer key: B - Handling display traffic such as graphical display output of VMs

Q: 15. What is the purpose of a Migration network role?
- A. Moving physical servers
* B. Handling live migration traffic when VMs are moved between hosts without downtime
- C. Backup operations
- D. User authentication
> Answer key: B - Handling live migration traffic when VMs are moved between hosts

Q: 16. What is the name of the default logical network automatically created during OLVM setup?
- A. default_network
* B. ovirtmgmt
- C. management_net
- D. cluster_network
> Answer key: B - ovirtmgmt

Q: 17. What role is typically assigned to the ovirtmgmt network?
- A. Display role
* B. Management role
- C. Storage role
- D. Migration role
> Answer key: B - Management role

Q: 18. How many logical networks can be configured as the default route for a KVM host?
- A. Zero
* B. One
- C. Two for redundancy
- D. As many as needed
> Answer key: B - One

Q: 19. What happens if a KVM host loses connectivity to a network marked as "required"?
- A. Nothing, it continues normally
* B. The host will be considered non-operational
- C. Only VMs on that network stop
- D. The host reboots automatically
> Answer key: B - The host will be considered non-operational

Q: 20. For VM networks, what is created on the host for each logical network?
- A. A VLAN tag
* B. A bridge (virtual switch)
- C. A firewall rule
- D. A routing table
> Answer key: B - A bridge (virtual switch)

Q: 21. What does a network bridge act as on a KVM host?
- A. A router
* B. A virtual switch connecting VMs to the physical network
- C. A firewall
- D. A load balancer
> Answer key: B - A virtual switch connecting VMs to the physical network

Q: 22. In bridge network configuration, what do VMs use to connect to bridges?
- A. Physical NICs directly
* B. VNICs (Virtual Network Interface Cards)
- C. USB adapters
- D. Wireless connections
> Answer key: B - VNICs (Virtual Network Interface Cards)

Q: 23. Can a single VM have multiple VNICs connected to different bridges?
- A. No, only one VNIC per VM
* B. Yes, VMs can have multiple VNICs connected to different bridges
- C. Only in special configurations
- D. Only on physical hosts
> Answer key: B - Yes, VMs can have multiple VNICs connected to different bridges

Q: 24. How do VMs on different hosts in the same logical network communicate?
- A. They cannot communicate
* B. Through the physical network infrastructure that connects the hosts
- C. Only through the engine
- D. Only through storage domains
> Answer key: B - Through the physical network infrastructure that connects the hosts

Q: 25. What does VLAN stand for?
- A. Virtual LAN
* B. Virtual Local Area Network
- C. Verified LAN
- D. Variable Link Access Network
> Answer key: B - Virtual Local Area Network

Q: 26. What is a VLAN identified by?
- A. MAC address
- B. IP address
* C. VLAN ID
- D. Hostname
> Answer key: C - VLAN ID

Q: 27. What device uses the VLAN ID to segregate traffic?
- A. The router
* B. The switch
- C. The firewall
- D. The DNS server
> Answer key: B - The switch

Q: 28. If a host does not have enough physical NICs, what can be used instead?
- A. Virtual NICs only
* B. VLAN configurations to create multiple virtual LANs
- C. Bonded interfaces only
- D. Additional virtual machines
> Answer key: B - VLAN configurations to create multiple virtual LANs

Q: 29. Why must VLANs be configured on the physical network infrastructure before use in OLVM?
- A. To reduce costs
* B. To support logical networks that require VLANs for traffic segregation
- C. To increase security only
- D. To enable wireless access VIRTUAL NICS (VNICs)
> Answer key: B - To support logical networks that require VLANs for traffic segregation

Q: 30. What does a VM use to connect to a logical network?
- A. Physical NIC directly
* B. VNIC (Virtual Network Interface Controller)
- C. USB adapter
- D. Serial port
> Answer key: B - VNIC (Virtual Network Interface Controller)

Q: 31. What are VNICs always attached to on the KVM host?
- A. A VLAN
* B. A bridge
- C. A router
- D. A firewall
> Answer key: B - A bridge

Q: 32. What does Oracle Linux Virtualization Manager automatically assign to a VNIC?
- A. IP address
* B. MAC address
- C. VLAN ID
- D. Hostname
> Answer key: B - MAC address

Q: 33. What does each MAC address assigned to a VNIC correspond to?
- A. Multiple VNICs
* B. A single VNIC
- C. All VNICs on a host
- D. All VNICs in a cluster
> Answer key: B - A single VNIC

Q: 34. What is the purpose of the MAC address on a VNIC?
- A. Encryption
* B. Identifying the VNIC on the network and ensuring proper communication
- C. Routing only
- D. Storage access
> Answer key: B - Identifying the VNIC on the network and ensuring proper communication

Q: 35. In a bonded network configuration, what are multiple NICs combined into?
- A. Multiple separate networks
* B. A single logical interface (bond)
- C. Virtual switches
- D. VLAN tags
> Answer key: B - A single logical interface (bond)

Q: 36. After creating a bond, what is created over the bonded interface?
- A. A firewall
* B. A bridge
- C. A VM
- D. A storage domain
> Answer key: B - A bridge

Q: 37. What benefit does bonding provide for network transmission?
- A. Reduces costs
* B. Combines transmission capabilities of all NICs for increased bandwidth and redundancy
- C. Simplifies configuration only
- D. Reduces power consumption
> Answer key: B - Combines transmission capabilities of all NICs for increased bandwidth and redundancy

Q: 38. What does pass-through networking allow?
- A. Encrypted connections only
* B. Network traffic to bypass the software network stack, providing direct access to physical NICs
- C. Faster VM creation
- D. Automatic IP assignment
> Answer key: B - Network traffic to bypass the software network stack, providing direct access to physical NICs

Q: 39. What is a benefit of pass-through networking?
- A. Easier configuration
* B. Reduced latency and improved performance
- C. Lower cost
- D. More VMs per host
> Answer key: B - Reduced latency and improved performance

Q: 40. What is a limitation of VMs with pass-through NICs?
- A. They use more memory
* B. They are less flexible in terms of live migration because specific physical hardware must be available on the target
- C. They cannot connect to networks
- D. They require more CPU cores
> Answer key: B - They are less flexible in live migration because specific hardware must be available

Q: 41. Which CPU features must be supported for pass-through networking? (Choose 2)
- A. Intel VTX
* B. Intel VT-d
- C. AMD-V
* D. IOMMU support (Intel VT-d or AMD-Vi)
> Answer key: B, D - Intel VT-d, IOMMU support

Q: 42. Can a physical NIC in pass-through mode be shared with other VMs on the same host?
- A. Yes, freely shared
* B. No, it cannot be shared while in pass-through mode
- C. Only with the same operating system
- D. Only in high-availability mode
> Answer key: B - No, it cannot be shared while in pass-through mode

Q: 43. What does SR-IOV stand for?
- A. Standard Root IO Virtualization
* B. Single Root IO Virtualization
- C. Shared Resource IO Virtualization
- D. Secure Root IO Virtualization
> Answer key: B - Single Root IO Virtualization

Q: 44. What does SR-IOV allow?
- A. Multiple operating systems on one VM
* B. A single physical network interface to be divided into multiple virtual interfaces (virtual functions)
- C. Faster storage access
- D. Encrypted networking
> Answer key: B - A single physical NIC to be divided into multiple virtual interfaces

Q: 45. How do you display virtual functions associated with network interfaces in the Administration Portal?
- A. Click Enable VF
* B. Click Show Virtual Functions button
- C. Edit host properties
- D. Create new network
> Answer key: B - Click Show Virtual Functions button

Q: 46. How do you associate a logical network with a virtual function?
- A. Edit the VM
* B. Drag and drop the logical network from unassigned to the virtual function
- C. Run a command line tool
- D. Restart the host
> Answer key: B - Drag and drop the logical network from unassigned to the virtual function

Q: 47. Where in the Administration Portal do you configure VM network interfaces?
- A. Storage -> Networks
* B. Compute -> Virtual Machines -> Network Interfaces tab
- C. Network -> Interfaces
- D. Configuration -> NICs
> Answer key: B - Compute -> Virtual Machines -> Network Interfaces tab

Q: 48. What can you configure for a VM's network interface? (Choose 2)
* A. Custom MAC address
- B. CPU allocation
* C. MAC address from pool
- D. Memory size
> Answer key: A, C - Custom MAC address, MAC address from pool

Q: 49. After configuring a custom MAC address for a VM's VNIC, what must you do for the changes to take effect?
- A. Reboot the host
- B. Restart the engine
* C. Start or restart the virtual machine
- D. Reboot all hosts
> Answer key: C - Start or restart the virtual machine

Q: 50. In the VM details page, which tab shows network interface information?
- A. General
* B. Network Interfaces
- C. Hardware
- D. Settings
> Answer key: B - Network Interfaces 46-50 correct: Excellent - Comprehensive mastery 41-45 correct: Very Good - Strong understanding 36-40 correct: Good - Ready with review 31-35 correct: Fair - Review weak areas Below 31: Needs comprehensive review - Network bonding: high availability and fault tolerance - VLANs: traffic segmentation and security - DNS: must be hosted outside OLVM environment - Logical network roles: Management, VM, Display, Migration - Default network: ovirtmgmt (management role) - Required networks: host becomes non-operational if unavailable - Bridges: created for each VM network on the host - VNICs: automatically assigned MAC addresses - One default route per KVM host - Pass-through: cannot share NICs, reduced migration flexibility - SR-IOV: virtual functions from single physical NIC - Network configuration at cluster level

```

## Section 7: User Management

```quiz

Q: 1. Which administrative task involves creating and managing user accounts with appropriate roles?
- A. Monitoring resources
* B. Managing user setup and setting permission levels
- C. Generating reports
- D. Managing physical resources
> Answer key: B - Managing user setup and setting permission levels

Q: 2. What are the main categories of resources that administrators manage in OLVM? (Choose 2)
* A. Physical servers
- B. Email systems
* C. Virtual resources (VMs, vCPUs, memory, storage, networks)
- D. Office applications
> Answer key: A, C - Physical servers, Virtual resources

Q: 3. What should administrators set up for critical resource thresholds?
- A. Firewalls
* B. Alerts for critical thresholds and anomalies
- C. User accounts
- D. Virtual machines
> Answer key: B - Alerts for critical thresholds and anomalies

Q: 4. What can administrators use to organize and manage OLVM objects?
- A. Folders
* B. Custom tags
- C. Directories
- D. Scripts
> Answer key: B - Custom tags

Q: 5. What are the two types of user domains in OLVM? (Choose 2)
* A. Local domain
- B. Cloud domain
* C. External domain
- D. Hybrid domain
> Answer key: A, C - Local domain, External domain

Q: 6. What is the name of the default local domain created during initial OLVM setup?
- A. local
- B. default
* C. internal
- D. admin
> Answer key: C - internal

Q: 7. What is the default user created in the internal domain?
- A. root
- B. administrator
* C. admin
- D. superuser
> Answer key: C - admin

Q: 8. When is the password for the default admin user set?
- A. After first login
* B. During engine setup
- C. Never, it's automatically generated
- D. When first VM is created
> Answer key: B - During engine setup

Q: 9. What tool is used to create additional users on the internal domain?
- A. useradd
- B. ovirt-user-tool
* C. ovirt-aaa-jdbc-tool
- D. engine-user-create
> Answer key: C - ovirt-aaa-jdbc-tool

Q: 10. What are users created on local domains called?
- A. Internal users
* B. Local users
- C. Domain users
- D. System users
> Answer key: B - Local users

Q: 11. Which two external directory servers can be attached to OLVM? (Choose 2)
* A. Active Directory
- B. NIS
* C. OpenLDAP
- D. Kerberos
> Answer key: A, C - Active Directory, OpenLDAP

Q: 12. What are users created on external domains called?
- A. External users
* B. Directory users
- C. LDAP users
- D. Remote users
> Answer key: B - Directory users

Q: 13. What are the benefits of using external domains? (Choose 3)
* A. Centralized user management
- B. Lower costs
* C. Single sign-on capabilities
* D. Adherence to organization's security policies
- E. Faster VM performance
> Answer key: A, C, D - Centralized management, Single sign-on, Security policies

Q: 14. Where do directory users authenticate?
- A. Within OLVM's internal system
* B. Against their respective external directory servers
- C. At the hypervisor level
- D. Through the database
> Answer key: B - Against their respective external directory servers

Q: 15. What command is used to add a new user to the internal domain?
- A. ovirt-aaa-jdbc-tool add-user
* B. ovirt-aaa-jdbc-tool user add
- C. useradd
- D. engine-user-add
> Answer key: B - ovirt-aaa-jdbc-tool user add

Q: 16. What parameter is used to set a user's password when creating a user?
- A. --pass
- B. --pwd
* C. --password
- D. --passwd
> Answer key: C - --password

Q: 17. What command shows details of a specific user?
- A. ovirt-aaa-jdbc-tool user list
* B. ovirt-aaa-jdbc-tool user show <username>
- C. ovirt-aaa-jdbc-tool display user
- D. ovirt-aaa-jdbc-tool query user
> Answer key: B - ovirt-aaa-jdbc-tool user show <username>

Q: 18. What command is used to edit user attributes?
- A. ovirt-aaa-jdbc-tool user modify
- B. ovirt-aaa-jdbc-tool user change
* C. ovirt-aaa-jdbc-tool user edit
- D. ovirt-aaa-jdbc-tool update user
> Answer key: C - ovirt-aaa-jdbc-tool user edit

Q: 19. Which parameter is used to specify the attribute to change when editing a user?
- A. --parameter
- B. --field
* C. --attribute
- D. --property
> Answer key: C - --attribute

Q: 20. How do you disable a user account?
- A. ovirt-aaa-jdbc-tool user delete
- B. ovirt-aaa-jdbc-tool user disable
* C. ovirt-aaa-jdbc-tool user edit --flag=+disabled
- D. ovirt-aaa-jdbc-tool user lock
> Answer key: C - ovirt-aaa-jdbc-tool user edit --flag=+disabled

Q: 21. How do you re-enable a disabled user account?
- A. ovirt-aaa-jdbc-tool user enable
* B. ovirt-aaa-jdbc-tool user edit --flag=+enabled
- C. ovirt-aaa-jdbc-tool user unlock
- D. ovirt-aaa-jdbc-tool user activate
> Answer key: B - ovirt-aaa-jdbc-tool user edit --flag=+enabled

Q: 22. What command is used to add a new group?
- A. ovirt-aaa-jdbc-tool add-group
- B. groupadd
* C. ovirt-aaa-jdbc-tool group add
- D. engine-group-create
> Answer key: C - ovirt-aaa-jdbc-tool group add

Q: 23. What command is used to add users to a group?
- A. ovirt-aaa-jdbc-tool group adduser
* B. ovirt-aaa-jdbc-tool group-manage useradd
- C. ovirt-aaa-jdbc-tool user-to-group
- D. usermod -aG
> Answer key: B - ovirt-aaa-jdbc-tool group-manage useradd

Q: 24. When specifying multiple users to add to a group, how can they be separated? (Choose 2)
* A. With commas
- B. With semicolons
* C. With multiple --user flags separated by spaces
- D. With pipe symbols
> Answer key: A, C - With commas, With multiple --user flags

Q: 25. What command lists all group manage options?
- A. ovirt-aaa-jdbc-tool group --help
* B. ovirt-aaa-jdbc-tool group-manage --help
- C. ovirt-aaa-jdbc-tool help groups
- D. man ovirt-groups
> Answer key: B - ovirt-aaa-jdbc-tool group-manage --help

Q: 26. What command shows details of a specific group?
- A. ovirt-aaa-jdbc-tool group list
- B. ovirt-aaa-jdbc-tool group display
* C. ovirt-aaa-jdbc-tool group show <groupname>
- D. ovirt-aaa-jdbc-tool query group
> Answer key: C - ovirt-aaa-jdbc-tool group show <groupname>

Q: 27. What are nested groups?
- A. Groups within VMs
* B. Hierarchical structures where groups can contain other groups (sub-groups) as members
- C. Encrypted group structures
- D. Groups spanning multiple data centers
> Answer key: B - Hierarchical structures where groups contain other groups

Q: 28. How do you add a subgroup to a parent group?
- A. ovirt-aaa-jdbc-tool group add-subgroup
- B. ovirt-aaa-jdbc-tool group nest
* C. ovirt-aaa-jdbc-tool group-manage groupadd <parent-group> --group=<sub-group>
- D. ovirt-aaa-jdbc-tool subgroup create
> Answer key: C - ovirt-aaa-jdbc-tool group-manage groupadd <parent> --group=<sub>

Q: 29. What is a benefit of nested groups?
- A. Faster VM performance
* B. Reduced redundancy in permission assignments
- C. Lower storage costs
- D. Better network performance
> Answer key: B - Reduced redundancy in permission assignments

Q: 30. What command is used to query user or group information?
- A. ovirt-aaa-jdbc-tool search
- B. ovirt-aaa-jdbc-tool find
* C. ovirt-aaa-jdbc-tool query
- D. ovirt-aaa-jdbc-tool list
> Answer key: C - ovirt-aaa-jdbc-tool query

Q: 31. What parameter specifies what to query (users or groups)?
- A. --type
* B. --what
- C. --object
- D. --target
> Answer key: B - --what

Q: 32. What parameter specifies the search pattern when querying?
- A. --search
- B. --filter
* C. --pattern
- D. --match
> Answer key: C - --pattern

Q: 33. To query all users, what value is used with --what parameter?
- A. users
* B. user
- C. all-users
- D. userlist
> Answer key: B - user

Q: 34. To query all groups, what value is used with --what parameter?
- A. groups
* B. group
- C. all-groups
- D. grouplist
> Answer key: B - group

Q: 35. What command is used to view current account settings?
- A. ovirt-aaa-jdbc-tool settings list
* B. ovirt-aaa-jdbc-tool settings show
- C. ovirt-aaa-jdbc-tool show settings
- D. ovirt-aaa-jdbc-tool display-settings
> Answer key: B - ovirt-aaa-jdbc-tool settings show

Q: 36. What command is used to modify account settings?
- A. ovirt-aaa-jdbc-tool settings change
- B. ovirt-aaa-jdbc-tool settings modify
* C. ovirt-aaa-jdbc-tool settings set
- D. ovirt-aaa-jdbc-tool set settings
> Answer key: C - ovirt-aaa-jdbc-tool settings set

Q: 37. What is the default value for MAX_LOGIN_MINUTES?
- A. 60 minutes
- B. 1,440 minutes
* C. 10,080 minutes
- D. Unlimited
> Answer key: C - 10,080 minutes

Q: 38. What setting controls the maximum number of failed login attempts before account lockout?
- A. MAX_FAILURES
* B. MAX_FAILURE_SINCE_SUCCESS
- C. LOGIN_ATTEMPTS_MAX
- D. FAILED_LOGIN_LIMIT
> Answer key: B - MAX_FAILURE_SINCE_SUCCESS

Q: 39. What is the default value for MAX_FAILURE_SINCE_SUCCESS?
- A. 3
* B. 5
- C. 10
- D. Unlimited
> Answer key: B - 5

Q: 40. What commands lock and unlock user accounts? (Choose 2)
* A. ovirt-aaa-jdbc-tool user lock <username>
* B. ovirt-aaa-jdbc-tool user unlock <username>
- C. ovirt-aaa-jdbc-tool user disable
- D. ovirt-aaa-jdbc-tool user freeze
> Answer key: A, B - user lock, user unlock

Q: 41. What is a quota in OLVM?
- A. A backup limit
* B. A resource limitation tool to restrict memory, CPU, and storage usage
- C. A user count limit
- D. A network bandwidth limit
> Answer key: B - A resource limitation tool

Q: 42. Which three types of resources can quotas limit? (Choose 3)
* A. Memory
- B. Email storage
* C. CPU
* D. Storage
- E. Network ports
> Answer key: A, C, D - Memory, CPU, Storage

Q: 43. What are the three quota modes in OLVM? (Choose 3)
* A. Enforced
- B. Monitored
* C. Audit
* D. Disabled
- E. Warning
> Answer key: A, C, D - Enforced, Audit, Disabled

Q: 44. What does the "Enforced" quota mode do?
- A. Logs violations only
* B. Puts into effect the quota limits that have been set
- C. Disables quotas
- D. Sends email alerts
> Answer key: B - Puts into effect the quota limits

Q: 45. What does the "Audit" quota mode do?
- A. Blocks all users
* B. Logs quota violations without blocking users
- C. Enforces strict limits
- D. Disables all quotas
> Answer key: B - Logs quota violations without blocking users

Q: 46. What does the "Disabled" quota mode do?
- A. Enables strict quotas
- B. Logs violations
* C. Turns off runtime and storage limitations defined by quotas
- D. Sends warnings only
> Answer key: C - Turns off runtime and storage limitations

Q: 47. At what level is the quota mode configured?
- A. Cluster level
* B. Data Center level
- C. Host level
- D. VM level
> Answer key: B - Data Center level

Q: 48. What is a cluster threshold in quota settings?
- A. CPU speed limit
* B. The limit on total storage that can be allocated across all VMs within a specific cluster
- C. Network bandwidth limit
- D. Number of VMs allowed
> Answer key: B - Limit on total storage across all VMs in cluster

Q: 49. What is cluster grace in quota settings?
- A. Extra time before enforcement
* B. The amount of cluster resources available after exhausting the cluster threshold
- C. Backup storage allocation
- D. Emergency CPU allocation
> Answer key: B - Amount of cluster resources available after exhausting threshold

Q: 50. Where in the Administration Portal do you create and manage quota policies?
- A. Compute -> Quotas
- B. Storage -> Quotas
* C. Administration -> Quota
- D. Configuration -> Quotas
> Answer key: C - Administration -> Quota 46-50 correct: Excellent - Comprehensive mastery 41-45 correct: Very Good - Strong understanding 36-40 correct: Good - Ready with review 31-35 correct: Fair - Review weak areas Below 31: Needs comprehensive review - Two domain types: local (internal) and external (AD/LDAP) - Default domain: internal, default user: admin - ovirt-aaa-jdbc-tool: primary command for user/group management - User commands: user add, user edit, user show - Group commands: group add, group-manage useradd - Nested groups: hierarchical group structures - Query command: --what and --pattern parameters - Account settings: MAX_LOGIN_MINUTES (10080), MAX_FAILURE_SINCE_SUCCESS (5) - Lock/unlock: user lock, user unlock - Three quota modes: Enforced, Audit, Disabled - Quota types: memory, CPU, storage - Quota configuration: Data Center level - Cluster threshold vs cluster grace

```

## Section 8: Optimization Practices

```quiz

Q: 1. What does memory overcommitment in OLVM allow?
- A. Using only physical memory
* B. Total memory allocated to VMs can exceed physical memory capacity of the host
- C. Reducing VM density
- D. Disabling memory management
> Answer key: B - Total memory allocated to VMs can exceed physical memory capacity

Q: 2. What is a benefit of memory overcommitment?
- A. Lower costs only
* B. Higher VM densities per host, maximizing resource efficiency
- C. Simpler configuration
- D. Reduced security
> Answer key: B - Higher VM densities per host, maximizing resource efficiency

Q: 3. What can excessive memory overcommitment lead to?
- A. Faster performance
* B. Performance degradation if not managed effectively
- C. Increased security
- D. Lower VM density
> Answer key: B - Performance degradation if not managed effectively

Q: 4. What happens with higher overcommit threshold values?
- A. They increase memory usage
* B. They conserve physical memory but may increase CPU usage
- C. They disable overcommitment
- D. They reduce CPU usage
> Answer key: B - They conserve physical memory but may increase CPU usage

Q: 5. What does MoM stand for in OLVM?
- A. Mode of Management
* B. Memory Overcommitment Manager
- C. Monitor of Memory
- D. Management of Modules
> Answer key: B - Memory Overcommitment Manager

Q: 6. What is KSM in the context of memory management?
- A. Key Storage Manager
* B. Kernel Same-page Merging
- C. KVM System Memory
- D. Kernel Security Module
> Answer key: B - Kernel Same-page Merging

Q: 7. What does KSM do?
- A. Encrypts memory pages
* B. Identifies identical memory pages across VMs and merges them into a single copy
- C. Increases memory allocation
- D. Backs up memory to storage
> Answer key: B - Identifies identical memory pages and merges them

Q: 8. Can OLVM hosts allocate more virtual CPUs to VMs than physical CPU cores available?
- A. No, never allowed
* B. Yes, vCPUs can exceed physical CPU core count
- C. Only with special licensing
- D. Only in test environments
> Answer key: B - Yes, vCPUs can exceed physical CPU core count

Q: 9. What ensures fair CPU allocation among VMs on the same host?
- A. Manual intervention
* B. OLVM CPU schedulers
- C. Guest OS schedulers only
- D. Hardware only
> Answer key: B - OLVM CPU schedulers

Q: 10. How can administrators set CPU limits for VMs?
- A. Only by rebooting
* B. In terms of percentage of total host CPU resources or specific CPU cycles
- C. Through guest OS only
- D. Cannot be set
> Answer key: B - Percentage of total host CPU resources or specific CPU cycles

Q: 11. What is CPU pinning?
- A. Limiting CPU usage
* B. Assigning specific vCPUs to dedicated physical CPU cores or threads on the host
- C. Disabling CPUs
- D. Sharing CPUs among VMs
> Answer key: B - Assigning specific vCPUs to dedicated physical CPU cores

Q: 12. What is a benefit of CPU pinning?
- A. Lower costs
* B. Optimizes performance by reducing cache misses and ensuring consistent access
- C. Simpler configuration
- D. Higher VM density
> Answer key: B - Optimizes performance by reducing cache misses

Q: 13. What is memory reservation?
- A. Blocking memory access
* B. Guaranteeing a minimum amount of memory always available to a VM
- C. Encrypting memory
- D. Reducing memory allocation
> Answer key: B - Guaranteeing minimum memory always available to VM

Q: 14. What does the memory balloon device allow?
- A. Increasing physical memory
* B. VMs to reclaim unused memory and return it to the host for other VMs
- C. Encrypting memory
- D. Blocking memory access
> Answer key: B - VMs reclaim unused memory and return to host

Q: 15. Where is memory balloon optimization configured?
- A. VM level only
* B. Cluster level
- C. Data Center level
- D. Host level only
> Answer key: B - Cluster level

Q: 16. What does VirtIO disk optimization do?
- A. Encrypts disks
* B. Pins VirtIO disks to dedicated threads or CPU cores to reduce I/O contention
- C. Increases disk size
- D. Backs up disks
> Answer key: B - Pins VirtIO disks to dedicated threads to reduce I/O contention

Q: 17. What is ballooning in memory management?
- A. Expanding physical memory
* B. A technique where a balloon driver requests memory from guest OS to reduce VM memory usage
- C. Encrypting memory
- D. Backing up memory
> Answer key: B - Balloon driver requests memory from guest OS to reduce VM memory

Q: 18. Is the balloon device included in VMs by default?
- A. No, must be manually added
* B. Yes, unless explicitly removed during VM configuration
- C. Only in Linux VMs
- D. Only in Windows VMs
> Answer key: B - Yes, unless explicitly removed during VM configuration

Q: 19. What must be installed in the guest OS for the balloon device to work?
- A. Additional storage drivers
* B. Relevant balloon drivers
- C. Network drivers
- D. Video drivers
> Answer key: B - Relevant balloon drivers

Q: 20. What command is used to check current memory overcommit settings?
- A. olvm-config
* B. engine-config
- C. memory-check
- D. vm-config
> Answer key: B - engine-config

Q: 21. What parameter can you grep to view memory overcommit settings?
- A. maxmem
* B. maxvdsmem
- C. memorymax
- D. vdsmemory
> Answer key: B - maxvdsmem

Q: 22. Which two parameters define memory overcommit ratios?
- A. MaxMemory and MinMemory
* B. MaxVdsMemOverCommit and MaxVdsMemOverCommitForServers
- C. MemoryLimit and MemoryReserve
- D. VMMemory and HostMemory
> Answer key: B - MaxVdsMemOverCommit and MaxVdsMemOverCommitForServers

Q: 23. What command is used to change memory overcommit settings?
* A. engine-config -s
- B. olvm-set-memory
- C. vm-memory-config
- D. set-overcommit
> Answer key: A - engine-config -s

Q: 24. Where should a fencing proxy host be located?
- A. In a different data center
* B. In the same cluster or data center as the host requiring fencing
- C. On the engine host only
- D. Outside the OLVM environment
> Answer key: B - Same cluster or data center as host requiring fencing

Q: 25. Which three methods can perform power management operations? (Choose 3)
* A. By the manager after it reboots
* B. By the proxy host
* C. Manually in the administration portal
- D. By the guest OS
- E. By the storage system
> Answer key: A, B, C - By manager after reboot, By proxy host, Manually in portal

Q: 26. What does OLVM automatically do to non-responsive hosts with power management enabled?
- A. Deletes them
* B. Attempts to fence them
- C. Ignores them
- D. Reboots all VMs
> Answer key: B - Attempts to fence them

Q: 27. Why might a host appear non-responsive during boot?
- A. Hardware failure only
* B. Temporary delays due to initialization routines or startup tasks
- C. Network attacks
- D. Storage corruption
> Answer key: B - Temporary delays due to initialization routines

Q: 28. What command parameter prevents fencing during host bootup?
- A. disable-fencing
* B. DisableFenceAtStartupInSeconds
- C. no-fence-boot
- D. boot-fence-disable
> Answer key: B - DisableFenceAtStartupInSeconds

Q: 29. What does PMHealthCheckEnabled do when set to true?
- A. Disables health checks
* B. Initiates regular health checks for all configured host power management agents
- C. Only checks once
- D. Checks only failed hosts
> Answer key: B - Initiates regular health checks for power management agents

Q: 30. What does PMHealthCheckIntervalInSeconds specify?
- A. Boot timeout
* B. The interval at which OLVM performs health checks for power management agents
- C. VM restart time
- D. Storage check frequency
> Answer key: B - Interval at which health checks are performed

Q: 31. When does a highly available VM automatically restart or migrate? (Choose 2)
- A. During normal operation
* B. When its original host experiences failure
* C. When scheduled for maintenance
- D. When VM is idle
- E. When storage is full
> Answer key: B, C - Host failure, Scheduled maintenance

Q: 32. What happens when a host is placed into maintenance mode?
- A. VMs are deleted
* B. VMs are gracefully shut down or migrated to another host before maintenance
- C. VMs continue running on that host
- D. All VMs are paused
> Answer key: B - VMs gracefully shut down or migrated before maintenance

Q: 33. For VM migration, what must both source and destination hosts access? (Choose 2)
* A. The same data domain where VM disks reside
- B. Different data domains
* C. The same virtual networks and VLANs
- D. Different network segments
> Answer key: A, C - Same data domain, Same virtual networks/VLANs

Q: 34. What is required for highly available VMs?
- A. Single host only
* B. Host must be part of a cluster of two or more available hosts
- C. Local storage only
- D. Shared CPU only
> Answer key: B - Part of cluster of two or more available hosts

Q: 35. What is the typical size range for huge pages?
- A. 4 KB to 8 KB
* B. 2 MB to 1 GB
- C. 1 KB to 2 KB
- D. 4 GB to 8 GB
> Answer key: B - 2 MB to 1 GB

Q: 36. What does using huge pages reduce?
- A. Memory capacity
* B. The number of entries required in the system's page table
- C. CPU cores
- D. Storage space
> Answer key: B - Number of entries in system's page table

Q: 37. What is TLB in the context of huge pages?
- A. Total Logical Buffer
* B. Translation Lookaside Buffer - hardware cache for virtual to physical address translations
- C. Thread Load Balancer
- D. Time Limit Buffer
> Answer key: B - Translation Lookaside Buffer

Q: 38. How do larger page sizes affect TLB?
- A. Increase TLB misses
* B. Reduce TLB misses because fewer translations are needed
- C. Disable TLB
- D. Have no effect
> Answer key: B - Reduce TLB misses

Q: 39. When are huge pages allocated?
- A. Dynamically during runtime
* B. Pre-allocated when a VM starts
- C. Only when needed
- D. After VM is running for 1 hour
> Answer key: B - Pre-allocated when VM starts

Q: 40. What is a limitation of huge pages?
- A. Cannot use with Linux VMs
* B. Do not support hotplug or hot unplug operations for memory
- C. Reduce performance
- D. Increase memory usage HOT PLUGGING vCPUs
> Answer key: B - Do not support hotplug/hot unplug for memory

Q: 41. What is hot plugging?
- A. Restarting VMs
* B. Adding or removing vCPUs from a running VM without downtime
- C. Increasing temperature
- D. Adding physical CPUs
> Answer key: B - Adding/removing vCPUs from running VM without downtime

Q: 42. Can you hot unplug a vCPU that was part of the original VM configuration?
- A. Yes, always
* B. No, only vCPUs that were previously hot plugged can be hot unplugged
- C. Only on Linux VMs
- D. Only with engine restart
> Answer key: B - No, only previously hot plugged vCPUs can be unplugged

Q: 43. What is the minimum number of vCPUs a VM cannot go below?
- A. 1 vCPU
* B. The number it was originally created with
- C. 2 vCPUs
- D. 4 vCPUs
> Answer key: B - The number it was originally created with

Q: 44. What is the MINIMUM number of vCPUs required for hot plugging capability?
- A. 1 vCPU
- B. 2 vCPUs
* C. 4 vCPUs
- D. 8 vCPUs
> Answer key: C - 4 vCPUs

Q: 45. What must Windows VMs have installed for hot plugging to work?
- A. Additional drivers only
* B. Guest agent
- C. Service packs
- D. .NET Framework
> Answer key: B - Guest agent

Q: 46. What must the VM's operating system support for hot plugging?
- A. Any OS works
* B. Must explicitly support CPU hot plug functionality
- C. Only Windows is supported
- D. Only Linux is supported
> Answer key: B - Must explicitly support CPU hot plug functionality

Q: 47. What happens each time you hot plug memory to a VM?
- A. VM restarts
* B. It appears as a new memory device under VM devices
- C. Old memory is removed
- D. VM is paused
> Answer key: B - Appears as new memory device under VM devices

Q: 48. What happens to hot plugged memory devices when you shut down and restart a VM?
- A. They become permanent
* B. They are cleared from VM devices without reducing the virtual memory space
- C. Memory is lost
- D. VM cannot restart
> Answer key: B - Cleared from VM devices without reducing virtual memory space

Q: 49. Can memory be hot plugged while a VM is running?
- A. No, VM must be stopped
* B. Yes, memory can be adjusted on the fly
- C. Only during maintenance mode
- D. Only once per day
> Answer key: B - Yes, memory can be adjusted on the fly

Q: 50. What is incompatible with memory hot plug/unplug operations?
- A. VirtIO disks
* B. Huge pages
- C. Balloon devices
- D. Network interfaces
> Answer key: B - Huge pages 46-50 correct: Excellent - Comprehensive mastery 41-45 correct: Very Good - Strong understanding 36-40 correct: Good - Ready with review 31-35 correct: Fair - Review weak areas Below 31: Needs comprehensive review - Memory overcommitment: higher VM density, potential performance issues - MoM (Memory Overcommitment Manager) and KSM (Kernel Same-page Merging) - CPU overcommitment: vCPUs can exceed physical cores - CPU pinning: reduces cache misses, good for latency-sensitive apps - Memory reservation: guarantees minimum memory - Balloon device: included by default, requires guest drivers - engine-config command: check/set overcommit (maxvdsmem) - Fencing: same cluster/data center, DisableFenceAtStartupInSeconds - VM migration: requires same data domain and virtual networks - Huge pages: 2MB-1GB, reduce TLB misses, NO hotplug support - vCPU hotplug: minimum 4 vCPUs, cannot go below original count - Memory hotplug: appears as new device, cleared on restart - Windows VMs: require guest agent for hotplug

```

## Section 9: Virtual Machine Administration

```quiz

Q: 1. What are the two main types of virtual machine storage provisioning in OLVM? (Choose 2)
* A. Pre-allocated (thick provisioning)
- B. Compressed provisioning
* C. Sparse (thin provisioning)
- D. Encrypted provisioning
> Answer key: A, C - Pre-allocated (thick), Sparse (thin)

Q: 2. What is pre-allocated storage also known as?
- A. Thin provisioning
* B. Thick provisioning
- C. Sparse provisioning
- D. Dynamic provisioning
> Answer key: B - Thick provisioning

Q: 3. How does sparse allocation work?
- A. Allocates all storage upfront
* B. Defines maximum capacity but physical space is allocated only as data is written
- C. Compresses all data
- D. Encrypts all data
> Answer key: B - Physical space allocated only as data is written

Q: 4. Which two disk format types does OLVM use? (Choose 2)
* A. QCOW2
- B. VMDK
* C. Raw
- D. VHD
> Answer key: A, C - QCOW2, Raw

Q: 5. What does QCOW2 stand for?
- A. Quality Copy On Write 2
* B. QEMU Copy On Write version 2
- C. Quick Copy Overwrite 2
- D. Queue Copy On Write 2
> Answer key: B - QEMU Copy On Write version 2

Q: 6. Which disk format supports snapshots, encryption, compression, and sparse allocation?
- A. Raw
- B. VMDK
* C. QCOW2
- D. VHD
> Answer key: C - QCOW2

Q: 7. On NFS storage, what format is a QCOW2 disk created as?
- A. A block device
* B. A file with initial size close to 0
- C. A compressed archive
- D. An encrypted container
> Answer key: B - A file with initial size close to 0

Q: 8. On SAN storage, what format is a QCOW2 disk created as?
- A. A file
* B. A block device with initial size smaller than defined virtual disk size
- C. A compressed file
- D. An LVM partition
> Answer key: B - A block device with initial size smaller than defined size

Q: 9. On NFS storage, can raw disks be sparse?
- A. No, only pre-allocated
* B. Yes, they can be sparse files or pre-allocated
- C. Only with special configuration
- D. Raw disks are not supported on NFS
> Answer key: B - Yes, they can be sparse files or pre-allocated

Q: 10. On SAN storage, are raw disks sparse or pre-allocated?
- A. Only sparse
- B. Both sparse and pre-allocated
* C. Only pre-allocated
- D. Neither, they use compression
> Answer key: C - Only pre-allocated

Q: 11. What does the "wipe after delete" flag do?
- A. Encrypts the disk
* B. Replaces user data with zeros when a virtual disk is deleted
- C. Backs up the disk
- D. Compresses the disk
> Answer key: B - Replaces user data with zeros when deleted

Q: 12. On file storage systems like NFS, what effect does enabling "wipe after delete" have?
- A. It wipes data thoroughly
* B. It does nothing because the file system ensures no data exists
- C. It encrypts the data
- D. It backs up the data
> Answer key: B - Does nothing because file system ensures no data exists

Q: 13. When is enabling "wipe_after_delete" recommended?
- A. Always
- B. Never
* C. If the virtual disk contained sensitive data
- D. Only on SAN storage
> Answer key: C - If the virtual disk contained sensitive data

Q: 14. What is the default value for the wipe_after_delete flag?
- A. True
* B. False
- C. Enabled
- D. Auto
> Answer key: B - False

Q: 15. What command is used to set SANWipeAfterDelete to true?
- A. olvm-config --set SANWipeAfterDelete=true
* B. engine-config --set SANWipeAfterDelete=true
- C. vm-config SANWipeAfterDelete true
- D. set-san-wipe true
> Answer key: B - engine-config --set SANWipeAfterDelete=true

Q: 16. After changing the SANWipeAfterDelete setting, what must you do?
- A. Reboot all hosts
* B. Restart the ovirt-engine service
- C. Restart all VMs
- D. Nothing, it takes effect immediately
> Answer key: B - Restart the ovirt-engine service

Q: 17. What is a side effect of enabling wipe_after_delete?
- A. Faster deletion
* B. Performance degradation and prolonged delete times
- C. Increased storage capacity
- D. Better VM performance
> Answer key: B - Performance degradation and prolonged delete times

Q: 18. What are shareable disks in OLVM?
- A. Disks shared between data centers
* B. Virtual disks that can be accessed simultaneously by multiple VMs
- C. Backup disks
- D. Network disks
> Answer key: B - Virtual disks accessed simultaneously by multiple VMs

Q: 19. What type of VMs should use shareable disks?
- A. Any VMs
* B. Only cluster-aware VMs
- C. Single VMs only
- D. Test VMs only
> Answer key: B - Only cluster-aware VMs

Q: 20. What can happen if you attach a shared disk to VMs that are not cluster-aware?
- A. Performance improvement
* B. Data corruption
- C. Increased security
- D. Nothing
> Answer key: B - Data corruption

Q: 21. What is an appropriate use case for shareable disks?
- A. General file storage
* B. Clustered database servers or highly available services
- C. Single user workstations
- D. Test environments
> Answer key: B - Clustered database servers or highly available services

Q: 22. Where in the Administration Portal do you create a new VM?
- A. Storage -> VMs
* B. Compute -> Virtual Machines
- C. Network -> VMs
- D. Administration -> VMs
> Answer key: B - Compute -> Virtual Machines

Q: 23. When creating a VM from scratch (not a template), what template option do you select?
- A. Default
* B. Blank
- C. Empty
- D. None
> Answer key: B - Blank

Q: 24. What are the two disk allocation policies? (Choose 2)
* A. Pre-allocated
- B. Compressed
* C. Thin provisioning (sparse)
- D. Encrypted
> Answer key: A, C - Pre-allocated, Thin provisioning (sparse)

Q: 25. What is the recommended disk interface for VM disks?
- A. IDE
- B. SATA
* C. VirtIO-SCSI
- D. USB
> Answer key: C - VirtIO-SCSI

Q: 26. In boot options, which device is typically selected for first boot when installing an OS?
- A. Hard disk
* B. CD-ROM
- C. Network
- D. USB
> Answer key: B - CD-ROM

Q: 27. After OS installation is complete, which device should be the first boot device?
- A. CD-ROM
* B. Hard disk
- C. Network
- D. Floppy
> Answer key: B - Hard disk

Q: 28. What key do you press to access the boot menu in the VM console?
- A. F2
- B. Delete
* C. Escape
- D. F12
> Answer key: C - Escape

Q: 29. What key combination releases the cursor from the VM console?
- A. Ctrl+Alt
- B. Alt+F4
* C. Shift+F12
- D. Ctrl+Esc
> Answer key: C - Shift+F12

Q: 30. What is a VM snapshot?
- A. A backup copy stored externally
* B. A point-in-time capture of VM state including memory, disk, and device state
- C. A template
- D. A compressed VM image
> Answer key: B - Point-in-time capture of VM state

Q: 31. Should snapshots be used as a primary backup method?
- A. Yes, they are perfect for backups
* B. No, they should not be used as primary backup
- C. Only for production VMs
- D. Only for test VMs
> Answer key: B - No, should not be used as primary backup

Q: 32. Why should snapshots NOT be used as primary backup?
- A. They are too slow
* B. They are stored on the same storage as VM disks; if storage fails, snapshots become inaccessible
- C. They use too much CPU
- D. They are encrypted
> Answer key: B - Stored on same storage; if storage fails, snapshots inaccessible

Q: 33. When is it appropriate to take a snapshot?
- A. Every day as backup
* B. Before testing updates, configuration changes, or application patches
- C. Never
- D. Only once per VM
> Answer key: B - Before testing updates, configuration changes, patches

Q: 34. What should you do with snapshots after reverting or when no longer needed?
- A. Keep them forever
* B. Delete them promptly to maintain optimal VM performance
- C. Archive them
- D. Encrypt them
> Answer key: B - Delete them promptly

Q: 35. What can happen if you take several snapshots without cleanup?
- A. Performance improvement
* B. Increased storage usage and performance degradation
- C. Better security
- D. Faster backups
> Answer key: B - Increased storage usage and performance degradation

Q: 36. What should be installed before taking snapshots?
- A. Antivirus software
* B. Latest guest agent package (qemu-guest-agent)
- C. Backup software
- D. Monitoring tools
> Answer key: B - Latest guest agent package (qemu-guest-agent)

Q: 37. Where do you create a VM snapshot in the Administration Portal?
- A. Storage -> Snapshots
* B. Compute -> Virtual Machines -> select VM -> Create Snapshot button
- C. Network -> Snapshots
- D. Configuration -> Snapshots
> Answer key: B - Compute -> Virtual Machines -> select VM -> Create Snapshot

Q: 38. What can you optionally include when creating a snapshot?
- A. Network configuration
* B. Memory state of the VM
- C. User accounts
- D. Firewall rules
> Answer key: B - Memory state of the VM

Q: 39. What happens to the VM when you select "Save Memory" during snapshot creation?
- A. VM continues running normally
* B. VM will be paused while saving memory
- C. VM is shut down
- D. VM is restarted
> Answer key: B - VM will be paused while saving memory

Q: 40. What is a VM template in OLVM?
- A. A backup file
* B. A pre-configured VM that captures configuration and state for quick deployment of new VMs
- C. A snapshot
- D. A disk image
> Answer key: B - Pre-configured VM for quick deployment

Q: 41. How do you create a template from a snapshot?
- A. Copy the VM
* B. Select snapshot -> click "Make Template" button
- C. Export the VM
- D. Clone the snapshot
> Answer key: B - Select snapshot -> "Make Template" button

Q: 42. Where in the Administration Portal do you view templates?
- A. Storage -> Templates
* B. Compute -> Templates
- C. Network -> Templates
- D. VM -> Templates
> Answer key: B - Compute -> Templates

Q: 43. What option allows all users to access a template?
- A. Public template
* B. Allow all users to access this template
- C. Shared template
- D. Global template
> Answer key: B - Allow all users to access this template

Q: 44. What does "Seal template" do and for which OS is it available?
- A. Encrypts template, Windows only
* B. Removes system-specific details, Linux only
- C. Backs up template, all OS
- D. Compresses template, all OS
> Answer key: B - Removes system-specific details, Linux only

Q: 45. When creating a VM from a template, what do you NOT need to provide?
- A. VM name
* B. Instance images (disks are already from template)
- C. Network interface
- D. Memory size EXPORTING VMs
> Answer key: B - Instance images (disks already from template)

Q: 46. What file format is used when exporting a VM?
- A. VMDK
* B. OVA
- C. VHD
- D. ISO
> Answer key: B - OVA

Q: 47. Where must you create a directory for VM export?
- A. On the engine host
* B. On the KVM host where export will happen
- C. On the storage domain
- D. On the client machine
> Answer key: B - On the KVM host where export will happen

Q: 48. What permissions should be set on the export directory?
- A. 600 (owner only)
- B. 755 (owner full, others read/execute)
* C. 777 (full permissions for all)
- D. 644 (owner read/write, others read)
> Answer key: C - 777 (full permissions for all)

Q: 49. How do you initiate VM export in the Administration Portal?
- A. File -> Export
* B. Select VM -> Options menu (three dots) -> Export as OVA
- C. Right-click -> Export
- D. VM -> Tools -> Export
> Answer key: B - Select VM -> Options menu -> Export as OVA

Q: 50. Can you export a running VM?
- A. No, it must be shut down first
* B. Yes, VMs can be exported while running
- C. Only if memory is saved
- D. Only test VMs
> Answer key: B - Yes, VMs can be exported while running

Q: 51. To revert a VM to a snapshot, what must be the VM state?
- A. Running
* B. Shut down
- C. Paused
- D. Any state
> Answer key: B - Shut down

Q: 52. What two actions are required to revert to a snapshot? (Choose 2)
* A. Select snapshot -> Preview
- B. Delete the VM
* C. Click Commit button
- D. Restart engine
> Answer key: A, C - Preview, then Commit

Q: 53. After reverting to a snapshot, what happens to changes made after the snapshot was taken?
- A. They are preserved
* B. They are lost
- C. They are archived
- D. They are encrypted
> Answer key: B - They are lost

Q: 54. In the practice, what file was created post-snapshot to demonstrate reversion?
- A. test-file
* B. post-snap
- C. snapshot-test
- D. demo-file
> Answer key: B - post-snap

Q: 55. After reverting to the snapshot, was the post-snap file still present?
- A. Yes, it remained
* B. No, it was gone because VM reverted to snapshot state
- C. It was moved to backup
- D. It was encrypted
> Answer key: B - No, it was gone 51-55 correct: Excellent - Comprehensive mastery 46-50 correct: Very Good - Strong understanding 41-45 correct: Good - Ready with review 36-40 correct: Fair - Review weak areas Below 36: Needs comprehensive review - Two storage types: Pre-allocated (thick) vs Sparse (thin) - Two disk formats: QCOW2 (features) vs Raw (simple) - QCOW2 on NFS: file; on SAN: block device - Raw on NFS: sparse or pre-allocated; on SAN: pre-allocated only - Wipe after delete: replaces data with zeros, default=false - Shareable disks: only for cluster-aware VMs, risk of corruption - VM creation: Blank template for new installs - Boot devices: CD-ROM first (install), Hard disk after - Snapshots: NOT primary backup, stored on same storage - Delete snapshots promptly after use - Guest agent required before snapshots - Templates: created from snapshots - Export format: OVA file - Revert: VM must be shut down, Preview then Commit

```

## Section 10: Events & Logs Management

```quiz

Q: 1. What are the three main categories of log files in OLVM? (Choose 3)
* A. Engine installation log files
- B. Network log files
* C. oVirt engine log files
* D. Host log files
- E. User activity log files
> Answer key: A, C, D - Engine installation, oVirt engine, Host log files

Q: 2. Where are engine installation log files located?
- A. /var/log/ovirt
* B. /var/log/ovirt-engine
- C. /var/log/engine
- D. /etc/ovirt-engine/logs
> Answer key: B - /var/log/ovirt-engine

Q: 3. Which two key log files are in the engine installation logs? (Choose 2)
* A. engine-setup.log
- B. engine-install.log
* C. engine-setup-summary.log
- D. installation.log
> Answer key: A, C - engine-setup.log, engine-setup-summary.log

Q: 4. What does the engine-setup.log file contain?
- A. Summary only
* B. Detailed information about each step and action during setup or upgrade
- C. Error messages only
- D. Network configurations
> Answer key: B - Detailed information about each step during setup/upgrade

Q: 5. Where are oVirt engine log files located?
- A. /var/log/engine
* B. /var/log/ovirt-engine
- C. /var/log/olvm
- D. /etc/ovirt/logs
> Answer key: B - /var/log/ovirt-engine

Q: 6. Which four key log files are part of oVirt engine logs? (Choose 4)
* A. engine.log
* B. server.log
* C. audit.log
* D. boot.log
- E. network.log
- F. storage.log
> Answer key: A, B, C, D - engine.log, server.log, audit.log, boot.log

Q: 7. What does the engine.log file record?
- A. User authentication only
* B. General operation information about oVirt engine (starting/stopping services, API requests)
- C. Boot process only
- D. Audit trails only
> Answer key: B - General operation information (services, API requests)

Q: 8. What does the server.log file contain?
- A. Audit information
* B. Detailed information about server-side processes
- C. Boot messages only
- D. User activities
> Answer key: B - Detailed information about server-side processes

Q: 9. What does the audit.log file contain?
- A. General operations
* B. Security events and audit trails (user actions, authentication, permission changes)
- C. Boot process
- D. Network events
> Answer key: B - Security events and audit trails

Q: 10. What does the boot.log file record?
- A. User logins
* B. The booting process of oVirt engine
- C. Shutdown events
- D. API calls
> Answer key: B - The booting process of oVirt engine

Q: 11. Where is the libvirt log file located?
- A. /var/log/libvirt
* B. /var/log/messages
- C. /var/log/vdsm
- D. /var/log/hosts
> Answer key: B - /var/log/messages

Q: 12. What does the SPM lock.log file detail?
- A. User logins
* B. Host ability to obtain a lease on storage pool management
- C. Network traffic
- D. VM migrations
> Answer key: B - Host ability to obtain lease on storage pool management

Q: 13. Where is the SPM lock.log file located?
- A. /var/log/spm
* B. /var/log/vdsm/spm-lock.log
- C. /var/log/ovirt
- D. /var/log/storage
> Answer key: B - /var/log/vdsm/spm-lock.log

Q: 14. Where is the VDSM service log file located?
* A. /var/log/vdsm/vdsm.log
- B. /var/log/vdsm.log
- C. /var/log/messages
- D. /var/log/ovirt/vdsm
> Answer key: A - /var/log/vdsm/vdsm.log

Q: 15. Where is the host deployment log file located?
- A. /var/log/deploy
* B. /tmp/ovirt-host-deploy-<date>
- C. /var/log/ovirt-deploy
- D. /var/log/vdsm/deploy
> Answer key: B - /tmp/ovirt-host-deploy-<date>

Q: 16. Where are VM import log files located?
- A. /var/log/imports
* B. /var/log/vdsm/import/import-<UUID>-<date>
- C. /var/log/vm-import
- D. /tmp/imports
> Answer key: B - /var/log/vdsm/import/import-<UUID>-<date>

Q: 17. What does the supervdsm.log file record?
- A. All VDSM activities
* B. VDSM tasks executed with superuser permissions
- C. VM migrations only
- D. Network configurations
> Answer key: B - VDSM tasks with superuser permissions

Q: 18. Where is the supervdsm.log file located?
- A. /var/log/supervdsm
* B. /var/log/vdsm/supervdsm.log
- C. /var/log/root
- D. /var/log/admin
> Answer key: B - /var/log/vdsm/supervdsm.log

Q: 19. What does the mom.log file contain?
- A. Network bandwidth
* B. Memory overcommitment information and MoM (Memory Overcommitment Manager) activities
- C. User activities
- D. Storage usage
> Answer key: B - Memory overcommitment and MoM activities

Q: 20. Where is the upgrade.log file located?
- A. /var/log/upgrade
* B. /var/log/vdsm/upgrade.log
- C. /var/log/ovirt/upgrade
- D. /tmp/upgrade
> Answer key: B - /var/log/vdsm/upgrade.log

Q: 21. What notification methods does OLVM support? (Choose 2)
* A. Email notifications
- B. SMS notifications
* C. SNMP traps
- D. Slack integration
> Answer key: A, C - Email notifications, SNMP traps

Q: 22. Where is the event notifier configuration file located?
- A. /etc/ovirt-engine/notifier.conf
- B. /etc/ovirt-engine-notifier/ovirt-engine-notifier.conf
* C. /etc/ovirt-engine/services/ovirt-engine-notifier/ovirt-engine-notifier.conf
- D. /var/lib/ovirt/notifier.conf
> Answer key: C - /etc/ovirt-engine/services/ovirt-engine-notifier/ovirt-engine-notifier.conf

Q: 23. What parameter specifies the SMTP mail server address?
- A. smtp_server
* B. mail_server
- C. email_server
- D. notification_server
> Answer key: B - mail_server

Q: 24. Which three port numbers are commonly used for mail servers? (Choose 3)
* A. 25 (plain SMTP)
* B. 465 (SMTP with SSL)
* C. 587 (SMTP with TLS)
- D. 8080 (HTTP)
- E. 3306 (MySQL)
> Answer key: A, B, C - 25, 465, 587

Q: 25. What parameter specifies the username for mail server authentication?
- A. smtp_user
* B. mail_user
- C. email_user
- D. auth_user
> Answer key: B - mail_user

Q: 26. What parameter contains the password for mail authentication?
- A. smtp_password
* B. mail_password
- C. email_password
- D. auth_password
> Answer key: B - mail_password

Q: 27. What parameter specifies the engine's process ID?
- A. engine_id
* B. engine_pid
- C. process_id
- D. ovirt_pid
> Answer key: B - engine_pid

Q: 28. Which two SNMP versions does oVirt support? (Choose 2)
- A. SNMP version 1
* B. SNMP version 2
* C. SNMP version 3
- D. SNMP version 4
> Answer key: B, C - SNMP version 2, version 3

Q: 29. What are the three SNMP security levels? (Choose 3)
* A. NoAuthNoPriv
- B. BasicAuth
* C. AuthNoPriv
* D. AuthPriv
- E. FullAuth
> Answer key: A, C, D - NoAuthNoPriv, AuthNoPriv, AuthPriv

Q: 30. What does NoAuthNoPriv security level provide?
- A. Full encryption
* B. SNMP traps sent without authentication or encryption (clear text)
- C. Password authentication only
- D. Encryption only
> Answer key: B - No authentication or encryption (clear text)

Q: 31. What does AuthNoPriv security level provide?
- A. No security
* B. Password authentication but no encryption (clear text transmission)
- C. Full encryption
- D. Authentication and encryption
> Answer key: B - Password authentication but no encryption

Q: 32. What does AuthPriv security level provide?
- A. No security
- B. Password authentication only
* C. Password authentication and encryption (highest security)
- D. Encryption only
> Answer key: C - Password authentication and encryption

Q: 33. What information is required to configure SNMP managers? (Choose 3)
* A. IP address or FQDN of SNMP manager
- B. MAC address
* C. SNMP version supported
* D. Community string or SNMP v3 credentials
- E. Operating system version
> Answer key: A, C, D - IP/FQDN, SNMP version, Credentials

Q: 34. What is the oVirt log collector diagnostic tool used for?
- A. Creating new logs
* B. Collecting comprehensive logs from manager and attached hosts for troubleshooting
- C. Deleting old logs
- D. Encrypting logs
> Answer key: B - Collecting logs from manager and hosts for troubleshooting

Q: 35. What package should be installed on manager and hosts for system diagnostics?
- A. syslog
* B. sos (sosreport)
- C. logrotate
- D. rsyslog
> Answer key: B - sos (sosreport)

Q: 36. What command is used to install the ovirt-log-collector package?
- A. dnf install log-collector
* B. yum install ovirt-log-collector
- C. dnf install olvm-logs
- D. yum install log-diagnostic
> Answer key: B - yum install ovirt-log-collector

Q: 37. What does the ovirt-log-collector tool collect? (Choose 3)
* A. SOS report from engine
* B. Engine log files
- C. Browser history
* D. Engine PostgreSQL database logs
- E. User passwords
> Answer key: A, B, D - SOS report, Engine logs, PostgreSQL logs

Q: 38. What parameter skips PostgreSQL database logs when collecting?
- A. --skip-database
* B. --no-postgresql
- C. --exclude-db
- D. --no-database
> Answer key: B - --no-postgresql

Q: 39. What parameter skips host log collection?
- A. --skip-hosts
- B. --no-hosts
* C. --no-hypervisors
- D. --exclude-hosts
> Answer key: C - --no-hypervisors

Q: 40. What parameter collects logs per hypervisor per cluster?
- A. --cluster-mode
- B. --per-cluster
* C. --hypervisors-per-cluster
- D. --cluster-hypervisors
> Answer key: C - --hypervisors-per-cluster

Q: 41. What are the two PostgreSQL databases in OLVM? (Choose 2)
* A. engine
- B. admin
* C. ovirt_engine_history
- D. manager
- E. olvm_data
> Answer key: A, C - engine, ovirt_engine_history

Q: 42. What does the engine database contain?
- A. Historical data only
* B. Active operational data
- C. Backup data
- D. Archived logs
> Answer key: B - Active operational data

Q: 43. What does the ovirt_engine_history database contain?
- A. Active data only
* B. Historical configuration information and statistical metrics collected over time
- C. User accounts
- D. Network configurations
> Answer key: B - Historical configuration and statistical metrics

Q: 44. What command switches to the postgres user?
- A. su postgres
* B. su - postgres
- C. sudo postgres
- D. switch postgres
> Answer key: B - su - postgres

Q: 45. What command connects to the engine database?
- A. psql engine
* B. psql -d engine
- C. postgres -d engine
- D. connect engine
> Answer key: B - psql -d engine

Q: 46. What PostgreSQL command lists all databases?
- A. show databases
- B. list databases
* C. \l
- D. \databases
> Answer key: C - \l

Q: 47. What PostgreSQL command shows connection information?
- A. \info
- B. \connection
* C. \conninfo
- D. \conn
> Answer key: C - \conninfo

Q: 48. What PostgreSQL command lists all tables?
- A. show tables
- B. \tables
* C. \dt
- D. list tables
> Answer key: C - \dt

Q: 49. What PostgreSQL command describes a specific table structure?
- A. describe tablename
* B. \d tablename
- C. show tablename
- D. \desc tablename
> Answer key: B - \d tablename

Q: 50. What PostgreSQL command quits the database prompt?
- A. exit
- B. quit
* C. \q
- D. \quit
> Answer key: C - \q

Q: 51. What command shows the Grafana server version?
- A. grafana --version
* B. grafana-server -v
- C. grafana version
- D. grafana-server --version
> Answer key: B - grafana-server -v

Q: 52. What port does Grafana typically use?
- A. 8080
* B. 3000
- C. 5432
- D. 9090
> Answer key: B - 3000

Q: 53. What configuration must be enabled to avoid "origin not allowed" errors in Grafana?
- A. cors_enabled
* B. proxy_preserve_host
- C. allow_origin
- D. enable_proxy
> Answer key: B - proxy_preserve_host

Q: 54. What database does Grafana connect to for OLVM monitoring?
- A. engine database
* B. ovirt_engine_history (DWH database)
- C. admin database
- D. grafana database
> Answer key: B - ovirt_engine_history (DWH database)

Q: 55. What permission level should be granted to the Grafana database user?
- A. Full admin access
* B. Read-only (SELECT) permissions on DWH database
- C. Write access
- D. Root access
> Answer key: B - Read-only (SELECT) permissions 51-55 correct: Excellent - Comprehensive mastery 46-50 correct: Very Good - Strong understanding 41-45 correct: Good - Ready with review 36-40 correct: Fair - Review weak areas Below 36: Needs comprehensive review - Three log categories: Engine install, oVirt engine, Host logs - Engine logs location: /var/log/ovirt-engine - Four key engine logs: engine.log, server.log, audit.log, boot.log - Host logs: messages, vdsm.log, spm-lock.log, supervdsm.log, mom.log - Event notification: email and SNMP traps - Mail parameters: mail_server, mail_port, mail_user, mail_password - SNMP versions: 2 and 3 - SNMP security: NoAuthNoPriv, AuthNoPriv, AuthPriv - Log collector: yum install ovirt-log-collector - Log collector flags: --no-postgresql, --no-hypervisors - Two databases: engine (active), ovirt_engine_history (historical) - PostgreSQL commands: \l (list), \dt (tables), \d (describe), \q (quit) - psql -d engine (connect to database) - Grafana: DWH database, port 3000, proxy_preserve_host

```

## Section 11: Recovery Management

```quiz

Q: 1. What tool is used to backup and restore OLVM?
- A. olvm-backup
* B. engine-backup
- C. ovirt-backup
- D. manager-backup
> Answer key: B - engine-backup

Q: 2. What does the engine-backup tool backup? (Choose 2)
* A. Engine database
- B. Virtual machine disk images
* C. Configuration files
- D. User home directories
> Answer key: A, C - Engine database, Configuration files

Q: 3. Can the engine-backup tool be used while the engine is active?
- A. No, engine must be stopped
* B. Yes, backups can be performed while engine is active
- C. Only in maintenance mode
- D. Only during off-peak hours
> Answer key: B - Yes, backups can be performed while engine is active

Q: 4. What user must you be logged in as to run engine-backup?
- A. admin
- B. postgres
* C. root
- D. ovirt
> Answer key: C - root

Q: 5. What parameter specifies whether to perform backup or restore?
- A. --action
* B. --mode
- C. --operation
- D. --task
> Answer key: B - --mode

Q: 6. What is the default scope when running engine-backup without parameters?
- A. files
- B. db
* C. all
- D. config
> Answer key: C - all

Q: 7. What does scope=all backup?
- A. Database only
- B. Files only
* C. Full backup of all databases and configuration files
- D. Configuration files only
> Answer key: C - Full backup of all databases and configuration files

Q: 8. What does scope=files backup?
- A. All files and databases
* B. Only the files on the system
- C. Only database files
- D. Only log files
> Answer key: B - Only the files on the system

Q: 9. What does scope=db backup?
- A. All databases
* B. Only the engine database (active database)
- C. Only data warehouse database
- D. Configuration files
> Answer key: B - Only the engine database

Q: 10. What does scope=dwhdb backup?
- A. Engine database
* B. Only the data warehouse database (historical data)
- C. All databases
- D. Configuration files
> Answer key: B - Only the data warehouse database

Q: 11. Which other database scopes are available? (Choose 2)
* A. cinderlib_db
- B. user_db
* C. grafana_db
- D. admin_db
> Answer key: A, C - cinderlib_db, grafana_db

Q: 12. What parameter specifies the path and name of the backup file?
- A. --backup
* B. --file
- C. --output
- D. --archive
> Answer key: B - --file

Q: 13. What parameter specifies where to write backup/restore operation logs?
- A. --logfile
* B. --log
- C. --output-log
- D. --recording
> Answer key: B - --log

Q: 14. What is the default file location for engine-backup?
- A. /root
- B. /tmp
* C. /var/lib/ovirt-engine-backup
- D. /home/admin
> Answer key: C - /var/lib/ovirt-engine-backup

Q: 15. What is the default log file location?
- A. /var/log/ovirt-engine
- B. /tmp/engine-backup.log
* C. /var/log/engine-backup.log
- D. /root/backup.log
> Answer key: C - /var/log/engine-backup.log

Q: 16. To restore a backup, what mode must be specified?
- A. --mode=recover
* B. --mode=restore
- C. --mode=import
- D. --mode=recovery
> Answer key: B - --mode=restore

Q: 17. Before restoring a backup, what should be done to the engine service?
- A. Nothing required
* B. Stop the engine service
- C. Restart the engine service
- D. Enable debug mode
> Answer key: B - Stop the engine service

Q: 18. After restoring a backup, what command must be run to reconfigure the engine?
- A. engine-config
* B. engine-setup
- C. ovirt-setup
- D. engine-restore
> Answer key: B - engine-setup

Q: 19. If database credentials are not known during restore, what must you do?
- A. Reinstall the engine
* B. Change the password for the engine database owner using PostgreSQL
- C. Use default credentials
- D. Skip database restore
> Answer key: B - Change password for engine database owner using PostgreSQL

Q: 20. What PostgreSQL command changes the database owner password?
- A. UPDATE USER SET PASSWORD
* B. ALTER USER username ENCRYPTED PASSWORD 'newpassword'
- C. CHANGE PASSWORD FOR username
- D. SET PASSWORD username = 'newpassword'
> Answer key: B - ALTER USER username ENCRYPTED PASSWORD 'newpassword'

Q: 21. What are two methods to migrate the data warehouse? (Choose 2)
* A. Migrate DWH service only, keep database on engine
- B. Delete and recreate
* C. Migrate both DWH database and service to new machine
- D. Export and import
> Answer key: A, C - Migrate service only, or Migrate both database and service

Q: 22. What Oracle Linux version must the new DWH server have?
- A. Oracle Linux 7
* B. Oracle Linux 8
- C. Oracle Linux 9
- D. Any version
> Answer key: B - Oracle Linux 8

Q: 23. Which modules must be enabled on the new DWH server? (Choose 4)
* A. javapackages-tools
* B. postgresql:13 or postgresql:12
* C. mod_auth_openidc:2.3
* D. nodejs:14
- E. python:3.8
> Answer key: A, B, C, D - javapackages-tools, postgresql, mod_auth_openidc, nodejs

Q: 24. What command enables a module in RHEL/Oracle Linux 8?
- A. yum module enable
* B. dnf module enable
- C. module enable
- D. enable-module
> Answer key: B - dnf module enable

Q: 25. What command synchronizes packages to latest versions?
- A. dnf update
- B. dnf upgrade
* C. dnf distro-sync
- D. dnf sync
> Answer key: C - dnf distro-sync

Q: 26. What must be backed up before migrating DWH?
- A. Only the database
* B. DWH database and configuration files
- C. Only configuration files
- D. Virtual machine data
> Answer key: B - DWH database and configuration files

Q: 27. What package must be installed on the new DWH machine?
- A. ovirt-engine
* B. ovirt-engine-tools-backup
- C. postgresql-backup
- D. dwh-tools
> Answer key: B - ovirt-engine-tools-backup

Q: 28. Which PostgreSQL packages must be installed? (Choose 2)
* A. postgresql-server
- B. postgresql-client
* C. postgresql-contrib
- D. postgresql-backup
> Answer key: A, C - postgresql-server, postgresql-contrib

Q: 29. After installing PostgreSQL, what must be done before restoring?
- A. Nothing
* B. Enable and start PostgreSQL service
- C. Configure firewall only
- D. Create database manually
> Answer key: B - Enable and start PostgreSQL service

Q: 30. What scope is used to restore DWH to the new machine?
- A. --scope=all
- B. --scope=dwhdb
* C. --scope=files,grafana_db,dwhdb
- D. --scope=database
> Answer key: C - --scope=files,grafana_db,dwhdb

Q: 31. What tool is used to rename the OLVM engine?
- A. engine-rename
- B. ovirt-rename
* C. ovirt-engine-rename
- D. manager-rename
> Answer key: C - ovirt-engine-rename

Q: 32. Where is the ovirt-engine-rename command located?
- A. /usr/bin
- B. /usr/sbin
* C. /usr/share/ovirt-engine/setup/bin
- D. /etc/ovirt-engine
> Answer key: C - /usr/share/ovirt-engine/setup/bin

Q: 33. What must be updated before renaming the engine? (Choose 2)
* A. DNS A records
- B. MAC addresses
* C. DNS PTR records
- D. IP addresses
> Answer key: A, C - DNS A records, DNS PTR records

Q: 34. If DHCP is used, what must be updated?
- A. Nothing
* B. DHCP server configuration to reflect new hostname
- C. Static IP addresses
- D. DNS only
> Answer key: B - DHCP server configuration to reflect new hostname

Q: 35. What must be updated on the engine server itself?
- A. IP address
* B. Hostname to new FQDN
- C. Network interface
- D. Storage domain
> Answer key: B - Hostname to new FQDN

Q: 36. What configuration is used for active-active disaster recovery?
- A. Two separate environments
* B. Stretched cluster configuration
- C. Master-slave replication
- D. Backup-only site
> Answer key: B - Stretched cluster configuration

Q: 37. In active-active DR, are both sites operational?
- A. No, one is standby
* B. Yes, both sites maintain active operations simultaneously
- C. Only during failover
- D. Depends on load
> Answer key: B - Yes, both sites maintain active operations simultaneously

Q: 38. What type of network is required for stretched clusters?
- A. Layer 3 only
* B. Layer 2 network with sufficient bandwidth and low latency
- C. VPN only
- D. Satellite links
> Answer key: B - Layer 2 network with sufficient bandwidth and low latency

Q: 39. What storage type is typically required for stretched clusters?
- A. Local storage only
* B. Shared storage (SAN or NAS) accessible from both sites
- C. Cloud storage
- D. Tape storage
> Answer key: B - Shared storage (SAN or NAS) accessible from both sites

Q: 40. What happens if one site fails in active-active DR?
- A. All VMs stop
* B. Cluster migrates all VMs to the other site
- C. Manual intervention required
- D. Data is lost
> Answer key: B - Cluster migrates all VMs to the other site

Q: 41. How is active-passive DR implemented?
- A. Stretched cluster
* B. Two separate OLVM environments (primary and secondary sites)
- C. Single site with backups
- D. Cloud-only deployment
> Answer key: B - Two separate OLVM environments

Q: 42. In active-passive DR, which site runs production workloads?
- A. Both sites
* B. Primary site
- C. Secondary site
- D. Alternates between sites
> Answer key: B - Primary site

Q: 43. What is the role of the secondary site in active-passive DR?
- A. Active workload processing
* B. Standby/backup location that replicates data from primary
- C. Development only
- D. Storage only
> Answer key: B - Standby/backup location that replicates data

Q: 44. What is a benefit of active-passive DR?
- A. Higher performance
* B. Cost-effective, utilizing standby resources only when needed
- C. Faster than active-active
- D. No replication needed
> Answer key: B - Cost-effective, resources only when needed

Q: 45. What must be replicated from primary to secondary site?
- A. Only databases
* B. Critical data, OLVM configurations, and VM images
- C. Only configuration files
- D. User accounts only
> Answer key: B - Critical data, OLVM configurations, VM images

Q: 46. What command uninstalls the OLVM engine?
- A. engine-remove
* B. engine-cleanup
- C. engine-uninstall
- D. ovirt-remove
> Answer key: B - engine-cleanup

Q: 47. What happens when you run engine-cleanup?
- A. Only stops the service
* B. Stops engine service and removes all installed ovirt components
- C. Backs up data
- D. Restarts the engine
> Answer key: B - Stops engine and removes all installed ovirt components

Q: 48. After running engine-cleanup, what happens to the Administration Portal?
- A. Still accessible
* B. Site cannot be reached
- C. Shows error page
- D. Redirects to new URL
> Answer key: B - Site cannot be reached

Q: 49. What parameter restores file permissions during restore?
- A. --restore-perms
* B. --restore-permissions
- C. --fix-permissions
- D. --permissions
> Answer key: B - --restore-permissions

Q: 50. After restoring from backup, what appears in the Administration Portal?
- A. Empty environment
* B. All previous data, VMs, and configurations are restored
- C. Only database is restored
- D. Only configuration files
> Answer key: B - All previous data, VMs, configurations are restored 46-50 correct: Excellent - Comprehensive mastery 41-45 correct: Very Good - Strong understanding 36-40 correct: Good - Ready with review 31-35 correct: Fair - Review weak areas Below 31: Needs comprehensive review - engine-backup: backup and restore tool - Must run as root user - Default scope: all (full backup) - Scope options: all, files, db, dwhdb, grafana_db, cinderlib_db - Key parameters: --mode, --file, --log - Restore: --mode=restore, then run engine-setup - DWH migration: Oracle Linux 8, enable modules (postgresql, nodejs, etc.) - DWH restore scope: files,grafana_db,dwhdb - Renaming: ovirt-engine-rename, update DNS A/PTR records - Active-active DR: stretched cluster, both sites active, Layer 2 network - Active-passive DR: two separate environments, primary active - engine-cleanup: removes all components - Restore includes: --restore-permissions parameter 515 questions across 11 exam objectives Good luck on Exam 1Z0-1170!

```

## Acknowledgements

- **Source** - OLVM_Practice_Tests_Combined.pdf
- **Generated From** - Oracle Linux Virtualization Manager Exam 1Z0-1170 practice test collection
