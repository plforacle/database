# Build and Configure an OLVM on OCI Demo Environment

## Introduction

This sandbox workshop converts the source tutorial into a guided, task-based flow for platform engineers. You will build the OCI network foundation, provision the Oracle Linux Virtualization Manager engine and KVM hosts, install and access the manager, attach hosts and storage, and then import and run virtual machines on the VLAN-backed network.

The source tutorial states that this environment is intended for testing and evaluation. It also notes that Oracle Linux Virtualization Manager support for OCI is under development and is not supported to manage OCI systems.

Estimated Workshop Time: 2 hours 30 minutes

### Objectives

In this workshop, you will:
* Create the OCI compartment, VCN, route rules, security rules, and VLAN required by the demo environment.
* Provision `olvm-engine`, `olvm-kvm1`, and `olvm-kvm2` with the network layout and storage attachments from the source.
* Install Oracle Linux Virtualization Manager and sign in to the administration portal.
* Add both KVM hosts, create the logical network, and attach Fibre Channel storage domains.
* Import Oracle Linux templates, create a virtual machine, and provide VLAN external access.

### Prerequisites (Optional)

This workshop assumes you have:
* Access to an OCI sandbox tenancy where you can create networking, compute, and block volume resources.
* SSH access to Oracle Linux 8 instances and a browser that can reach the OLVM engine over HTTPS.
* A workstation where you can edit the local `hosts` file and import the OLVM engine CA certificate.

## Workshop Flow

The labs follow the same progression as the source tutorial:
* Lab 1 creates the OCI compartment, VCN, security rules, and VLAN.
* Lab 2 provisions the OLVM engine and KVM hosts, adds the private and VLAN VNICs, and attaches block volumes.
* Lab 3 installs Oracle Linux Virtualization Manager on the engine and signs in to the administration portal.
* Lab 4 prepares the KVM hosts, adds them to the manager, creates the logical network, and attaches storage domains.
* Lab 5 imports templates, creates a virtual machine, enables external access, and captures the optional DHCP and troubleshooting steps from the source.

## Learn More

* [Oracle Linux Virtualization Manager 4.5 Getting Started](https://docs.oracle.com/en/virtualization/oracle-linux-virtualization-manager/getstart/getstarted-manager-install.html)
* [Oracle Linux Templates](https://yum.oracle.com/oracle-linux-templates.html)

## Acknowledgements
* **Author** - Codex
* **Contributors** - `seighman` (source tutorial author shown in the PDF)
* **Last Updated By/Date** - Codex, May 2026
