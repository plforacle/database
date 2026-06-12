# Introduction

## Introduction

This workshop builds an Oracle Cloud Migrations demo environment for a VMware-to-Oracle Linux Virtualization Manager migration scenario in Oracle Cloud Infrastructure (OCI).

You will build the source side of the demo with Oracle Cloud VMware Solution (OCVS), create a test Oracle Linux virtual machine in the VMware Software-Defined Data Center (SDDC), prepare or verify the Oracle Linux Virtualization Manager (OLVM) target, configure Oracle Cloud Migrations (OCM), and run the migration workflow.

The OCM demo build deck provides detailed steps for the OCI foundation, OCVS SDDC, jump host, NSX/vCenter access, and the Oracle Linux source VM. The VMware-to-OLVM planning export and Oracle Cloud Migrations LA PDF supplement the later labs with OCM prerequisites, remote agent appliance setup, VDDK dependency, OLVM credentials, discovery, migration project, replication, migration plan, Resource Manager stack execution, and post-migration validation details.

The OLVM deployment itself is still environment-specific. Use your team's approved OLVM build method, then use this workshop to verify that OLVM is ready for OCM.

The following diagram shows the demo build sequence.

![OCM demo build sequence](images/migration-build-steps.png)

Estimated Workshop Time: 7 hours for the OCVS source build, plus additional time to prepare OLVM and complete OCM migration validation

### Objectives

In this workshop, you will:

* Review the OCM demo architecture
* Prepare OCI compartments, IAM access, vault secrets, and networking
* Deploy a single-host OCVS SDDC as the VMware source environment
* Create a secure administrative access path with a jump host or OCI Bastion
* Configure OCVS networking access to OCI resources
* Access vCenter and NSX Manager from the jump host
* Build an Oracle Linux 9 test VM in OCVS
* Prepare or verify the OLVM target environment
* Configure Oracle Cloud Migrations for VMware-to-OLVM migration
* Migrate the source VM and validate the target VM in OLVM

### Prerequisites (Optional)

This workshop assumes you have:

* An OCI tenancy enabled for OCVS and OCM
* Administrator access or delegated IAM policies for compartments, networking, compute, OCVS, OLVM, vault, and OCM
* A non-overlapping IP address plan for the VCN, OCVS cluster, NSX workload segment, and OLVM target
* Basic familiarity with OCI, VMware vSphere, NSX-T, Oracle Linux, and OLVM
* Access to your team's approved OLVM deployment method
* Approval for any OCVS VMware licensing model required by your tenancy

## Learn More

* [Oracle Cloud VMware Solution Documentation](https://docs.oracle.com/en-us/iaas/Content/VMware/home.htm)
* [Oracle Cloud Migrations Documentation](https://docs.oracle.com/en-us/iaas/Content/cloud-migration/home.htm)
* [Oracle Linux Virtualization Manager Documentation](https://docs.oracle.com/en/virtualization/oracle-linux-virtualization-manager/)

## Acknowledgements

* **Author** - Mark Atkinson, Master Principal Sales Consultant, Open Cloud Technologies
* **Workshop Draft** - Perside Foster, May 2026
* **Source Material** - OCM-DEMO-BUILD presentation, Confluence VMware-to-OLVM planning PDF, and Oracle Cloud Migrations LA PDF
