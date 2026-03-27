# Oracle Linux Virtualization Manager (OLVM) — Hands-on Lab

## About this Workshop

Oracle Virtualization provides a high-performance, cost-effective, open-source server virtualization solution that includes KVM virtualization and management capabilities. In this workshop, you will deploy Oracle Linux Virtualization Manager (OLVM) and complete core administration tasks including host/cluster setup, networking, storage, and VM lifecycle management. An optional application tier is included to demonstrate a realistic workload and live migration.

**Estimated Time:** 4–5 hours (hands-on) 


This lab is available as a hands-on lab in **Oracle Luna Labs** (luna.oracle.com). It uses example values for Oracle Cloud Infrastructure (OCI) credentials, tenancy, and compartments—substitute values with those specific to your environment.



## Lab Structure

This workshop is organized into 9 progressive parts :

1. **Setup OCI Infrastructure** — Create the OCI OLVM objects  
2. **Deploy OLVM Engine** — Install Oracle Linux Virtualization Manager on a dedicated host with Administration Portal access
3. **Configure KVM Cluster** — Add two KVM hosts (olkvm01, olkvm02) with VDSM agents to form a high-availability cluster
4. **Set Up Networking** — Create the `l2-vm-network` logical network and assign it to both hosts
5. **Configure Storage** — Add a Fibre Channel storage domain for VM disks and templates
6. **Import Templates** — Download and import an Oracle Linux 9 OVA template
7. **Create a Test VM** — Deploy a virtual machine (ol9-vm1 at 10.0.10.105) from the template to verify the infrastructure
8. **Import Application VMs** — Import pre-built OVA files to deploy ol9-mysql (10.0.10.100) and ol9-webapp (10.0.10.101) with no manual installation required
9. **Live Migration** — Migrate a running VM between hosts with zero downtime

**End Result:** A fully functional OLVM cluster running distributed VMs with an Employee Directory web application accessible at `http://10.0.10.101:8080/employee-app/employees`


---

## Objectives

In this workshop, you will:

- Deploy Oracle Linux Virtualization Manager (OLVM) Engine and verify portal access
- Add and configure KVM hosts into a cluster
- Configure logical networking for virtual machines
- Configure shared storage domains and import VM templates
- Deploy and validate virtual machines
- Deploy a multi-tier application and perform live migration

---

## Prerequisites

This workshop assumes you have:

- Access to an Oracle Cloud Infrastructure Tenancy and account
- Tiger VNC Client
- 4–5 hours available for the hands-on portion 
- A note-taking tool (you will record IP addresses) 
- Basic familiarity with Linux command line usage, SSH, and terminal workflows 
- No Java or application development experience is required (deployment is scripted) 

---

## Learn More

- Oracle Linux Virtualization Manager install lab (official): https://docs.oracle.com/en/learn/olvm-install/index.html 
- Oracle Luna Labs: https://luna.oracle.com/ 

---

## Acknowledgements

- **Author** - Perside Foster
- **Contributors** - Shawn Kelly
- **Last Updated By/Date** - Perside Foster , April 1, 2026