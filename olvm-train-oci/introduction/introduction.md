# Oracle Linux Virtualization Manager (OLVM) on OCI

## Introduction

Oracle Linux Virtualization Manager (OLVM) provides KVM-based virtualization and centralized management for Oracle Linux environments. In this workshop, you will deploy OLVM on Oracle Cloud Infrastructure (OCI) and complete core administration tasks including host onboarding, networking, storage, virtual machine lifecycle management, user management, and live migration.

**Estimated Workshop Time:** 4-5 hours of hands-on work, including wait time for OCI provisioning, package installation, OVA downloads, and host registration.

## Audience and Delivery Model

This workshop is designed for instructor-led enablement sessions for Oracle solution engineers and partners. It has been validated in guided delivery.

If you complete the workshop outside a guided session, use each checkpoint before moving on. Do not assume a later lab can be started safely while an earlier lab is still provisioning resources.

## Workshop Flow

This workshop is organized into the following labs:

1. **Lab 1: Setup OCI Infrastructure** - Provision the bootstrap instance and use Ansible to create the OLVM manager and KVM hosts.
2. **Lab 2: Deploy OLVM Engine** - Connect securely to the manager desktop, install the OLVM engine, and validate portal access.
3. **Lab 3: Configure KVM Cluster** - Add `olkvm01` and `olkvm02` to the default cluster and wait for both hosts to reach `Up`.
4. **Lab 4: Set Up Networking, Storage, and VM** - Create the VM network, add shared storage, import the Oracle Linux template, and validate the first VM.
5. **Lab 5: Deploy Multi Tier Application** - Import the application OVAs, power on the database and web application VMs, and validate connectivity.
6. **Lab 6: Perform Live Migration** - Migrate a running VM between hosts with no planned downtime.
7. **Lab 7: Manage Users and Roles** - Create users and validate role-based access within OLVM.
8. **Lab 8: Configure OCI NAT Gateway for VM Internet Access** - Create a NAT Gateway, add a route table, and associate it with the VLAN for outbound VM connectivity.

**End Result:** A working OLVM deployment on OCI with a two-host KVM cluster, shared storage, multiple virtual machines, and a running Employee Directory application.

## Workshop Rules

Follow these rules throughout the workshop:

- Complete the labs in order.
- Do not start Lab 4 until both KVM hosts in Lab 3 show status `Up`.
- Do not start Lab 5 until Lab 4 confirms the logical network, storage domain, template import, and test VM are all working.
- Treat the documented wait times as part of the workshop. Long-running tasks are expected.
- If a step runs materially longer than the documented range and you cannot verify progress, stop and contact the instructor or workshop owner before changing the environment manually.

## Objectives

In this workshop, you will:

- Deploy Oracle Linux Virtualization Manager (OLVM) Engine and verify portal access
- Add and configure KVM hosts in a cluster
- Configure logical networking for virtual machines
- Configure OCI outbound internet access for VLAN-based virtual machines
- Configure shared storage domains and import VM templates
- Deploy and validate virtual machines
- Deploy a multi-tier application and perform live migration
- Review OLVM user and role administration

## Prerequisites

This workshop assumes you have:

- Access to an Oracle Cloud Infrastructure tenancy and the target compartment for the lab
- Permission to create OCI networking, compute, and storage resources required by the workshop
- A local SSH client and local PowerShell terminal
- TigerVNC Viewer installed on your local machine for the OLVM manager desktop session
- 4-5 hours available for the hands-on portion
- A note-taking tool for recording hostnames, IP addresses, and credentials
- Basic familiarity with Linux command line usage, SSH, and terminal workflows

## Learn More

- Oracle Linux Virtualization Manager install lab (official): https://docs.oracle.com/en/learn/olvm-install/index.html

## Acknowledgements

- **Author** - Shawn Kelley, John Priest
- **Contributors** - Perside Foster
- **Last Updated By/Date** - Perside Foster, May 6, 2026
