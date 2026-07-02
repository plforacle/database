# VMware to OLVM Migration with Oracle Cloud Migrations

## Introduction

This workshop walks through a VMware to Oracle Linux Virtualization Manager (OLVM) migration by using Oracle Cloud Migrations (OCM). You will prepare the OCI tenancy and source VMs, configure OCM prerequisites, upload the VMware Virtual Disk Development Kit (VDDK), deploy and register the remote agent appliance, discover VMware and OLVM assets, create an OLVM migration project, replicate a selected VM, validate the migrated VM, and clean up temporary migration resources.

Before migration planning, prepare the selected source VMs for the OLVM target environment. For Windows VMs, download and install the required VirtIO network and storage drivers before migration. Missing VirtIO drivers can prevent a migrated Windows VM from booting in OLVM.

Estimated Workshop Time: 3 hours, plus replication and migration execution time

### Objectives

In this workshop, you will:

* Create a dedicated OCI compartment for migration activity.
* Deploy the OCM prerequisites Resource Manager stack.
* Prepare selected source VMs, including VirtIO drivers for Windows VMs.
* Upload and register the VDDK package as an agent dependency.
* Configure the VMware source environment and remote agent appliance.
* Discover VMware source VMs and OLVM target assets.
* Configure the OLVM target asset source.
* Create an OLVM migration project and migration plan.
* Replicate, migrate, validate, cut over, and clean up a migrated VM.
* Review advanced scheduling, incremental replication, Windows driver readiness, and common troubleshooting steps.

### Prerequisites

The OLVM target environment must be fully configured and ready **before migration planning begins**. Confirm the following with the OLVM administrator:

* An OLVM cluster is available for the migrated VMs.
* Required VNIC profiles are configured for the target VM networks.
* Required VM templates are available for the migration operator or customer administrator to select during migration planning.

This workshop assumes you have:

* An OCI administrator or workshop operator with permission to manage compartments, IAM, Vault, Object Storage, Resource Manager, and Cloud Migrations.
* A VMware administrator who can deploy the remote agent appliance in vCenter. OCM uses a dedicated vCenter service account for source discovery and replication.
* An OLVM administrator who can confirm target readiness and provide the OLVM Manager URL, service-account credentials, and TLS certificate used by OCM.
* The VDDK package downloaded from the VMware portal.
* Windows source VMs prepared with the required VirtIO network and storage drivers before migration.
* A workstation that can reach OCI over HTTPS and the remote agent appliance registration endpoint.
* Network connectivity and firewall rules that permit communication between the workstation, remote agent appliance, VMware environment, OCI, and OLVM, as listed in the following section.
* Working knowledge of VMware vCenter and OLVM administration for the respective VMware and OLVM administrators.



## Required Network Connectivity

Open the following ports before deploying and registering the remote agent appliance. Confirm the network routes, DNS resolution, and firewall rules with the VMware, OCI, and OLVM administrators.

| Source | Destination | Port and protocol | Purpose |
| --- | --- | --- | --- |
| User workstation | Remote agent appliance external interface | TCP 3000 | Agent registration and reset. |
| Remote agent appliance | DNS server | TCP/UDP 53 | Resolves OCI, vCenter, and ESXi host names. |
| Remote agent appliance | DHCP server, if used | TCP/UDP 67 and 68 | Obtains appliance network configuration. |
| Remote agent appliance | NTP server | TCP/UDP 123 | Synchronizes appliance time. |
| Remote agent appliance external interface | OCI endpoints in the `oraclecloud.com` domain | TCP 443 | Connects securely to OCM and other OCI services. |
| Remote agent appliance internal interface | vCenter | TCP 443 | Connects to the vCenter Server Management API. |
| Remote agent appliance internal interface | vCenter and ESXi hosts | TCP/UDP 902 | Provides VDDK connectivity for discovery and replication. |
| OCM service and remote agent appliance | OLVM Manager | TCP 443 | Discovers target assets and manages replication. |
| Remote agent appliance external interface | OLVM Manager | TCP 54323 | Replicates migration data to OLVM. |

## Workshop Architecture

The workshop uses Oracle Cloud Migrations in OCI to coordinate a migration from a VMware vSphere source environment to an OLVM target environment. OCI provides the Cloud Migrations service, Object Storage for migration dependencies and replication artifacts, and the service endpoints required by the remote agent appliance.

The remote agent appliance runs in the VMware environment and needs network access to vCenter, ESXi hosts, DNS, NTP, OCI service endpoints, and OLVM. The user workstation is used only for registration and console-driven setup tasks.

![VMware to OLVM migration architecture](images/vmware-to-olvm-architecture.png "Show VMware to OLVM migration architecture")

## Workshop Flow

* Before Lab 1, prepare the selected source VMs, including VirtIO drivers for Windows VMs.
* Lab 1 creates the migration compartment.
* Lab 2 deploys the OCM prerequisites stack.
* Lab 3 uploads and registers the VDDK.
* Lab 4 sets up the VMware source environment and remote agent.
* Lab 5 discovers VMware source VMs.
* Lab 6 sets up the OLVM target environment.
* Lab 7 creates the OLVM migration project.
* Lab 8 replicates, migrates, validates, and cuts over the VM.
* Lab 9 marks the project complete and cleans up temporary resources.
* Lab 10 reviews advanced scheduling and common troubleshooting scenarios for field teams.

## Learn More

* [Oracle Cloud Migrations documentation](https://docs.oracle.com/en-us/iaas/Content/cloud-migration/home.htm)
* [Oracle Linux Virtualization Manager documentation](https://docs.oracle.com/en/virtualization/oracle-linux-virtualization-manager/)
* [OCI Resource Manager documentation](https://docs.oracle.com/en-us/iaas/Content/ResourceManager/home.htm)
* [Oracle Cloud Migrations remote agent appliance network requirements](https://docs.oracle.com/en-us/iaas/Content/cloud-migration/cloud-migration-remote-agent-appliance.htm)

## Acknowledgements

* **Author** - Mark Atkinson, Evgeny Golenkov, Andrey Sokolov, Perside Foster
* **Contributor** - Keya Balutkar
* **Last Updated By/Date** - Perside Foster, July 2026
