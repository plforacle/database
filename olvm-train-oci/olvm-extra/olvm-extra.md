# OLVM Exam Practice
--

## Exam Practice #1 

### Architecture  

```quiz
Q: 1. What is a Standalone Engine in Oracle Linux Virtualization Manager?
* A. A management engine installed on a dedicated server separate from the virtualization hosts.
- B. An engine that runs as a virtual machine within the same cluster it manages.
- C. A host that only provides storage and networking services.
- D. A management console that only functions when connected to the public cloud.

Q: 2. What is a Self-Hosted Engine in Oracle Linux Virtualization Manager?
- A. An engine that requires a dedicated physical server
* B. A management engine that runs as a VM directly on the virtualization hosts
- C. An engine that runs in Oracle Cloud Infrastructure
- D. A backup engine for disaster recovery

Q: 3. What is the primary benefit of using a Self-Hosted Engine deployment?
- A. It requires more hardware resources
* B. It eliminates the need for a separate management server
- C. It increases the complexity of deployment
- D. It requires Oracle Linux Enterprise Kernel

Q: 4. What component monitors and manages the Self-Hosted Engine VM on each host?
- A. oVirt engine service
- B. VDSM daemon
* C. HA agent (ovirt-ha-agent)
- D. PostgreSQL database

Q: 5. What is the core component that serves as the backbone of Oracle Linux Virtualization Manager?
- A. KVM hypervisor
- B. VDSM agent
* C. oVirt engine
- D. PostgreSQL database

Q: 6. Which three tasks does the oVirt engine perform? (Choose 3)
* A. Discovering KVM hosts
- B. Running virtual machine processes
* C. Configuring storage for virtualized data centers
* D. Configuring networking for virtualized data centers
- E. Providing hardware emulation for VMs

Q: 7. What is the role of VDSM in Oracle Linux Virtualization Manager?
- A. It provides hardware emulation for virtual machines
* B. It acts as a host agent running continuously as a daemon on the KVM host
- C. It manages the PostgreSQL database
- D. It provides the web-based user interface

Q: 8. What does QEMU (Quick Emulator) provide in the virtualization stack?
- A. Network connectivity between VMs
* B. Hardware component emulation such as CPU, memory, network, and disk devices
- C. User authentication services
- D. Database management

Q: 9. Where does KVM operate within the system?
- A. In user space as an application
* B. In the kernel space
- C. On a separate management server
- D. In the cloud

Q: 10. What is the relationship between VDSM and libvirt?
- A. VDSM replaces libvirt completely
* B. VDSM relies on libvirt to manage the lifecycle of VMs and collect statistics
- C. libvirt runs inside VDSM
- D. They operate independently without interaction
```

---

## Exam Practice #2 

### Infrastructure Quiz

```quiz
Q: 1. What is the minimum Oracle Linux version required for installing the OLVM Manager (Engine)?
- A. Oracle Linux 7.5
* B. Oracle Linux 8.5 and higher
- C. Oracle Linux 9.0
- D. Oracle Linux 6.8

Q: 2. Which two CPU technologies must be supported for the Engine host processor? (Choose 2)
* A. Intel VT-x
- B. Intel SGX
* C. AMD-V
- D. ARM TrustZone

Q: 3. What are the RECOMMENDED hardware requirements for an OLVM (Engine) SMALL deployment? (Choose 3)
- A. 2 cores
* B. 4 cores
* C. 16 GB or greater RAM
- D. 32 GB RAM
* E. 50 GB or greater local writable disk
```

---

## Exam Practice #3

### Repositories, Setup, and Ports

```quiz
Q: 1. Which six repositories must be enabled on the Oracle Linux system for OLVM Engine? (Choose 6)
* A. BaseOS Latest
* B. AppStream
* C. KVM AppStream
* D. oVirt
* E. oVirt Extras
* F. UEKR7
- G. Docker CE
- H. Kubernetes

Q: 2. What command is used to install the Oracle oVirt release package for Enterprise Linux 8?
- A. dnf install ovirt-release-el8
- B. dnf install oracle-ovirt-el8
* C. dnf install oracle-ovirt-release-45-el8
- D. dnf install ovirt-engine-release

Q: 3. What command is used to install the OLVM Engine package?
- A. yum install olvm-engine
* B. dnf install ovirt-engine
- C. dnf install olvm-manager
- D. yum install oracle-engine

Q: 4. What command is used to configure the OLVM Engine after package installation?
- A. ovirt-setup
* B. engine-setup
- C. olvm-configure
- D. manager-setup

Q: 5. Which five configuration groupings are part of the engine-setup process? (Choose 5)
* A. Database configuration
- B. Hardware configuration
* C. Network configuration
* D. Administration user setup
* E. Certificates and security
* F. Service configurations
- G. Storage domain setup

Q: 6. What does the engine-setup command display after all questions are answered?
- A. Error log
* B. Summary of entered values
- C. Installation progress bar
- D. Database schema

Q: 7. Which two ports are used for web interface and REST API access? (Choose 2)
* A. 80 (TCP)
- B. 8080 (TCP)
* C. 443 (TCP)
- D. 8443 (TCP)

Q: 8. What is the default port number for PostgreSQL database communication?
- A. 3306
* B. 5432
- C. 5433
- D. 27017
```

---

## Exam Practice #4 

### Portals and Databases

```quiz
Q: 1. Oracle Linux Virtualization Manager is built from which open-source project?
- A. OpenStack
* B. oVirt
- C. Proxmox
- D. XenServer

Q: 2. Which three portals are available in the OLVM Engine web interface? (Choose 3)
* A. Administration Portal
- B. Developer Portal
* C. VM Portal
* D. Monitoring Portal (Grafana)
- E. Storage Portal

Q: 3. How many PostgreSQL databases are used by Oracle Linux Virtualization Manager?
- A. One
* B. Two
- C. Three
- D. Four
```

---


## Exam Practice #5 

### KVM Host Prerequisites

```quiz
Q: 1. What is the minimum Oracle Linux version required for a KVM host?
- A. Oracle Linux 7.5
* B. Oracle Linux 8.5 or later
- C. Oracle Linux 9.0
- D. Oracle Linux 8.0

Q: 2. What is the MINIMUM CPU requirement for a KVM host?
- A. Single-core 32-bit CPU
* B. 64-bit dual-core CPU
- C. 64-bit quad-core CPU
- D. 64-bit eight-core CPU

Q: 3. What is the MINIMUM RAM required for a KVM host?
- A. 1 GB
* B. 2 GB
- C. 4 GB
- D. 8 GB

Q: 4. What is the MINIMUM network interface requirement for a KVM host?
- A. One NIC with 100 Mbps bandwidth
* B. One NIC with 1 Gbps bandwidth
- C. Two NICs with 1 Gbps bandwidth
- D. Four NICs with 1 Gbps bandwidth
```

### Adding Host to Engine

```quiz
Q: 5. Where in the Administration Portal do you add a new KVM host?
- A. Storage -> Hosts
* B. Compute -> Hosts
- C. Network -> Hosts
- D. Configuration -> Hosts

Q: 6. Which two authentication methods can be used when adding a KVM host? (Choose 2)
* A. Password authentication
- B. Kerberos
* C. SSH key authentication
- D. Certificate authentication

Q: 7. For which user account must authentication credentials be provided when adding a host?
- A. admin user
* B. root user
- C. ovirt user
- D. vdsm user
```

### VDSM & Host Architecture

```quiz
Q: 8. What is the role of the VDSM service on a KVM host?
- A. It manages the PostgreSQL database
* B. It acts as a host agent running continuously as a daemon on the KVM host
- C. It provides the web-based administration interface
- D. It handles SSL certificate generation

Q: 9. How does the oVirt engine communicate with VDSM on the KVM hosts?
- A. Through shared storage
* B. Through the VDSM service (host agent)
- C. Through the PostgreSQL database
- D. Through SNMP traps

Q: 10. What happens to a virtual machine if the oVirt engine goes offline?
- A. The VM automatically suspends
* B. The VM continues to run on the KVM host
- C. The VM is migrated to another host
- D. The VM shuts down gracefully
```

---
## Exam Practice #6 

### LOGICAL NETWORKS

```quiz
Q: 1. What are logical networks in OLVM?
- A. Physical network cables
* B. Representations of network resources that provide connectivity for KVM virtual machines  
- C. Virtual switches only
- D. Network security policies

Q: 2. What is the name of the default logical network automatically created during OLVM setup?
- A. default_network
* B. ovirtmgmt 
- C. management_net
- D. cluster_network

Q: 3. What happens if a KVM host loses connectivity to a network marked as "required"?
- A. Nothing, it continues normally
* B. The host will be considered non-operational 
- C. Only VMs on that network stop
- D. The host reboots automatically

Q: 4. For VM networks, what is created on the host for each logical network?
- A. A VLAN tag
* B. A bridge (virtual switch) 
- C. A firewall rule
- D. A routing table

Q: 5. What does a network bridge act as on a KVM host?
- A. A router
* B. A virtual switch connecting VMs to the physical network 
- C. A firewall
- D. A load balancer
```

### STORAGE DOMAINS

```quiz
Q: 6. Can a Data Center be initialized without a storage domain attached?
- A. Yes, storage is optional
* B. No, at least one storage domain must be attached before initialization 
- C. Only in test environments
- D. Only for Self-Hosted Engine

Q: 7. For VMs to be migrated between hosts, what storage requirement must be met?
- A. Each host needs local storage
* B. HOSTS must share the same storage domain 
- C. Storage must be SSD-based
- D. VMs must use iSCSI only

Q: 8. What does LUN stand for in storage terminology?
- A. Local Unit Number
* B. Logical Unit Number
- C. Linux Unified Node
- D. Link Universal Network
```

### VIRTUAL MACHINES & TEMPLATES

```quiz
Q: 9. Where in the Administration Portal do you create a new VM?
- A. Storage -> VMs
* B. Compute -> Virtual Machines 
- C. Network -> VMs
- D. Administration -> VMs

Q: Q: 10. What are the two disk allocation policies? **(Choose 2)**
* A. Pre-allocated 
- B. Compressed
- **C. Thin provisioning (sparse)
- D. Encrypted

Q: 11. What does a VM use to connect to a logical network?
- A. Physical NIC directly
* B. VNIC (Virtual Network Interface Controller)
- C. USB adapter
- D. Serial port
```

