# Review OLVM Engine Architecture and Complete the Quiz

## Introduction

This page provides architecture context for Oracle Linux Virtualization Manager (OLVM) and includes exam-style knowledge checks aligned to common Engine concepts (architecture, sizing, repositories, ports, and portals).

Estimated Lab Time: 15–30 minutes

### Objectives

In this lab, you will:
- Understand the Oracle Virtualization platform layers
- Identify the virtualization stack components (Engine, VDSM, libvirt, QEMU, KVM)
- Review deployment sizing guidance for small/medium/large environments
- Validate your understanding through exam-style practice questions

### Prerequisites

This lab assumes you have:
- Basic familiarity with virtualization concepts (hosts, VMs, management plane)
- (Recommended) Completed the hands-on Engine deployment: [olvm-engine.md](olvm-engine.md)

---

## Task 1: Oracle Virtualization — The Big Picture

![](images/olvm-big-picture.png)

#### WHAT DOES ORACLE VIRTUALIZATION LOOK LIKE? — The complete platform, from control plane to infrastructure

### Platform layers

1. **Physical Servers** — The foundation. Provide CPU, memory, storage, and networking.
2. **Oracle Linux KVM Hosts** — The middle layer. Run multiple virtual machines with different guest operating systems, including Linux and Windows.
3. **Oracle Linux Virtualization Manager (OLVM)** — The centralized control plane. Logically separated from the KVM hosts and the VMs they run.

> **Note:** This separation is intentional — it improves reliability and scalability, and simplifies management across many hosts.

### Key concept — User Space vs Kernel Space

- **Kernel space** is where the OS core and drivers run — direct hardware access.
- **User space** is where applications run — hardware access is requested through the kernel.

In OLVM, **KVM runs in kernel space** for near-native performance, while each VM runs as a **QEMU** process in **user space**.

---

## Task 2: Architecture Overview (The Virtualization Stack)

![](images/olvm-stack.png)

**Walk through from top to bottom:**
1. **oVirt ENGINE** — The brain. Java app on WildFly server (formerly JBoss) + PostgreSQL. Sends commands down.
2. **VDSM** — Virtual Desktop and Server Manager. Daemon on EVERY KVM host. The agent.
3. **libvirt** — The API layer. VDSM talks to libvirt, not directly to KVM.
4. **QEMU** — Quick Emulator. Emulates hardware (CPU, memory, disk, NIC) for each VM. Each VM is a QEMU process in USER SPACE.
5. **KVM** — The actual hypervisor. Runs in KERNEL SPACE. Provides near-native performance.

> **Note:** If the engine goes offline, VMs keep running! The engine is management only. KVM handles execution independently.

![](images/olvm-architecture.png)

---

## Task 3: Deployment Sizing Reference

|  | Small | Medium | Large |
|---|---|---|---|
| **Hosts** | 1–5 | 5–50 | 50–200 |
| **VMs** | Up to 50 | 50–500 | 500–2,000 |
| **CPU (recommended)** | 4 cores | 8 cores | 16 cores |
| **RAM (recommended)** | 16 GB | 32 GB | 64 GB |
| **Disk (recommended)** | 50 GB | 100 GB | 200 GB |
| **CPU (minimum)** | 2 cores | — | — |
| **RAM (minimum - Standalone)** | 4 GB | — | — |
| **RAM (minimum - Self-Hosted Engine)** | 16 GB | — | — |
| **Disk (minimum)** | 25 GB | — | — |
| **Deployment Types** | Standalone & Self-Hosted Engine | Standalone & Self-Hosted Engine | Standalone & Self-Hosted Engine |

> **Note:** Minimum values are provided only for Small deployments; Medium and Large deployments should follow recommended values.

---

## Task 4: Exam Practice #1 — Architecture Quiz

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

## Task 5: Exam Practice #1 — Infrastructure Quiz

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

## Task 6: Exam Practice #2 — Repositories, Setup, and Ports

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

## Task 7: Exam Practice #3 — Portals and Databases

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

## Next Steps

- Return to the hands-on Engine deployment page: [olvm-engine.md](olvm-engine.md)

## Acknowledgements

- **Author** - <Name, Title, Group>
- **Contributors** - <Name, Group> (optional)
- **Last Updated By/Date** - <Name, Month Year>
```
