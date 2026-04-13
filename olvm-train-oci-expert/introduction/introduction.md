# Build and Configure OLVM on OCI

## About this Workshop

This workshop guides you through building a functional Oracle Linux Virtualization Manager (OLVM) demo environment on Oracle Cloud Infrastructure (OCI). You will create the required OCI networking foundation, deploy an OLVM engine and two KVM hosts, configure storage and networking, and provision virtual machines for demonstration and testing.

By the end of the workshop, you will have a working OLVM environment that you can use to demonstrate host management, logical networking, shared storage domains, template import, and virtual machine deployment on OCI.

Estimated Workshop Time: 3 hours 30 minutes

### Objectives

In this workshop you will:
- Create the OCI networking environment required for OLVM.
- Deploy the OLVM engine and KVM host instances.
- Configure secondary VNICs, VLAN networking, and shared storage.
- Add KVM hosts and logical networks in OLVM.
- Import Oracle Linux templates and create virtual machines.
- Validate VM console and external network access.
- Review optional DHCP and troubleshooting procedures for demo environments.

### Prerequisites

This workshop assumes you have:
* Access to an OCI tenancy
* Permissions to create compartments, VCNs, subnets, VLANs, NSGs, route tables, security rules, compute instances, and block volumes
* Basic familiarity with OCI Console, SSH, and Linux command-line administration
* An SSH key pair available for instance access

> **Note:** This workshop is intended for testing, evaluation, and demo purposes. Oracle Linux Virtualization Manager support for OCI is under development and is not supported to manage OCI systems.

## Learn More

* [Oracle Linux Virtualization Manager Documentation](https://docs.oracle.com/en/virtualization/oracle-linux-virtualization-manager/)
* [Oracle Cloud Infrastructure Networking Documentation](https://docs.oracle.com/en-us/iaas/Content/Network/Concepts/overview.htm)
* [Oracle Linux Templates](https://yum.oracle.com/oracle-linux-templates.html)

## Acknowledgements
* **Author** - Shawn Kelley
* **Contributors** - Optional
* **Last Updated By/Date** - Perside Foster, April 2026