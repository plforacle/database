# Introduction
## Quick Start Summary

>**Important** The total time for this training is 8 hours! The extra time is for discussion, breaks, Q&A, and the practice questions. 

**What you'll build in 4-5 hours :**

1. **Deploy OLVM Engine** — Install Oracle Linux Virtualization Manager on a dedicated host with Administration Portal access
2. **Configure KVM Cluster** — Add two KVM hosts (olkvm01, olkvm02) with VDSM agents to form a high-availability cluster
3. **Set Up Networking** — Create the `l2-vm-network` logical network and assign it to both hosts
4. **Configure Storage** — Add a Fibre Channel storage domain for VM disks and templates
5. **Import Templates** — Download and import an Oracle Linux 9 OVA template
6. **Create VMs** — Deploy two virtual machines: ol9-mysql (10.0.10.100) and ol9-webapp (10.0.10.101)
7. **Enable Internet Access** — Configure OCI NAT Gateway for VM outbound connectivity
8. **(Optional) Deploy Application** — Install a multi-tier Java web app with MySQL database and Tomcat
9. **(Optional) Live Migration** — Migrate a running VM between hosts with zero downtime
10. **Validate Skills** — Gain hands-on experience covering 30-40% of Exam 1Z0-1170 objectives

**End Result:** A fully functional OLVM cluster running distributed VMs with an optional Employee Directory web application accessible at `http://10.0.10.101:8080/employee-app/employees`

---
## Lab Environment: Sandbox Simulation for On-Premises OLVM

This hands-on lab runs in an Oracle Cloud Infrasture(OCI) Tenancy. It simulates an on-premises OLVM deployment with dedicated KVM hosts, shared storage, and VM networking. It mirrors a typical datacenter virtualization setup. While provisioned on OCI infrastructure, the focus is on core OLVM administration tasks like engine installation, host clustering, VM management, networking, and storage.

The content directly aligns with the [**Oracle Linux Virtualization Manager Certified Associate Exam: 1Z0-1170**](https://education.oracle.com/oracle-linux-virtualization-manager-associate/pexam_1Z0-1170) exam objectives (Installation & Configuration, Host/Cluster Management, VM Lifecycle, Networking/Storage).

**Important:** This content is for learning, testing and evaluation purposes, and can be used to help prepare for [**Exam: 1Z0-1170**](https://education.oracle.com/oracle-linux-virtualization-manager-associate/pexam_1Z0-1170).

### Why This Lab for Exam: 1Z0-1170 Prep?

- **Estimated Time:** 4–5 hours, covers 30–40% of practical exam scenarios
- **Key Benefits:** Safe, repeatable practice in a simulated data center environment; no need for your own hardware
- **Prerequisites:** Basic Linux familiarity; access to Luna Labs
- **Post-Lab:** Tackle scenario-based questions on OLVM engine setup, VDSM hosts, logical networks, and VM troubleshooting

### Lab Approach

This experience intentionally goes beyond a minimal "hello-world" tutorial. The design demonstrates how Oracle Virtualization is actually used—starting with environment setup, moving through core administration tasks, and finally validating the platform by running workloads. The emphasis is on practical understanding and real-world context over isolated feature walkthroughs. Advanced tuning and production optimization are intentionally out of scope.

---

### Intended Audience

This lab is intended for:
- Oracle Solution Engineers
- Technical partners
- System administrators and Application developers evaluating Oracle Virtualization
- Learners seeking foundational, hands-on exposure to the platform

---

### Based on the Official Luna Lab

This lab extends the [**official "Install Oracle Linux Virtualization Manager in Oracle Cloud Infrastructure"**](https://docs.oracle.com/en/learn/olvm-install/index.html)

---

### Prerequisites

Before starting this lab, ensure you have:

- ✓ Access to Oracle Luna Labs ([**luna.oracle.com**](https://luna.oracle.com/) + Oracle account)
- ✓ A Chrome web browser with functioning copy/paste support
- ✓ 4-5 hours of uninterrupted time available
- ✓ Note-taking tool ready (you'll need to record IP addresses)
- ✓ Basic familiarity with Linux command line
- ✓ Understanding of SSH and terminal usage
- ✓ **No Java or application development experience required** (deployment is fully scripted)

---
### SSH Connection Reference

Throughout this lab, you'll need to connect to different systems. Use this reference to understand the connection paths.

### Network Architecture

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         Connection Paths                                │
│                                                                         │
│  ┌──────────────┐      ┌──────────────┐      ┌──────────────────────┐   │
│  │ Luna Desktop │ SSH  │    olvm      │ SSH  │   Virtual Machines   │   │
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
| olvm | ol9-mysql | ssh opc@10.0.10.100 |
| olvm | ol9-webapp | ssh opc@10.0.10.101 |
| olvm | KVM hosts | ssh olkvm01 or ssh olkvm02 |

**Note:** KVM hosts use pre-configured SSH keys and hostnames. VMs require username, IP, and password for SSH login.

### Estimated Time

**Total Time: 4-5 hours**

---

| Part | Focus | Estimated Time | Cumulative | Key Checkpoint |
|------|-------|----------------|------------|----------------|
| 1. Infrastructure Setup | Ansible + Engine Install | 30-45 min | 45 min | Admin Portal accessible |
| 2. Host Configuration | Add KVM hosts | 30-45 min | 1.5 hrs | Both hosts "Up" in cluster |
| 3. Networking, Storage, and VMs | Networks, storage, VMs | 60-75 min | 2.5 hrs | Both VMs running with external access |
| 4. Application Tier (Optional) | Deploy Java app, VM Migration | 30-40 min | 3 hrs | Full stack application accessible |

**Note:** Total lab duration is 4–5 hours when accounting for troubleshooting, learning pauses, verification steps, and varying experience levels.

---

## Lab Structure

This lab is organized into four progressive parts:

### Part 1: Infrastructure Setup
Deploy the OLVM environment and install the virtualization manager engine.
- **Estimated Time:** 30-45 minutes
- **Exam Relevance:** ⭐⭐⭐⭐⭐ High

### Part 2: Host Configuration
Add and configure KVM hosts to form a virtualization cluster.
- **Estimated Time:** 30-45 minutes
- **Exam Relevance:** ⭐⭐⭐⭐⭐ High

### Part 3: Networking, Storage, and VM
Configure virtual networks, storage domains, and create virtual machines.
- **Estimated Time:** 60-75 minutes
- **Exam Relevance:** ⭐⭐⭐⭐⭐ High

### Part 4: Application Tier (Optional)
Deploy a multi-tier Java web application and VM migration to demonstrate real-world usage.
- **Estimated Time:** 30-40 minutes
- **Exam Relevance:** ⭐⭐☆☆☆ Low - Demonstrates capabilities

---

### Introduction

Oracle Virtualization provides a high-performance, cost-effective, open-source server virtualization solution that includes KVM virtualization and management capabilities. This lab provides a comprehensive guide for deploying Oracle Virtualization, from initial setup through advanced multi-tier application deployment.

In this lab, you'll start by installing Oracle Linux Virtualization Manager (the management component of Oracle Virtualization), then progress to deploying a multi-tier Java web application using pre-configured VM templates and automated installation scripts. **No programming knowledge is required** - the application serves as a realistic workload to demonstrate OLVM's capabilities in a production-like scenario.

**Note:** This lab is available as a hands-on lab in [Oracle Luna Labs (luna.oracle.com)](https://luna.oracle.com). It uses example values for Oracle Cloud Infrastructure credentials, tenancy, and compartments. When completing your lab, substitute these values with ones specific to your environment.

---


## About this Workshop


_Estimated Time:_ 5 hours

## About Product/Technology



## Objectives


## Prerequisites


## Learn More


## Acknowledgements


