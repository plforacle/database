# Review the Migration Architecture
## Introduction



This lab introduces the migration scenario used in this workshop. You will review the main components of the solution and examine how Oracle Cloud VMware Solution (OCVS), Oracle Linux Virtualization Manager (OLVM), and Oracle Cloud Migrations (OCM) work together in the migration flow.

Estimated Lab Time: 15 minutes

### Objectives

In this lab, you will:
* Identify the source, target, and migration components used in this workshop
* Understand the role of OCVS, OLVM, and Oracle Cloud Migrations
* Review the OCVS networking model, including NSX overlay networking and OCI VLANs
* Review the high-level workflow used to migrate a VMware workload to OLVM in OCI

### Prerequisites (Optional)

This lab assumes you have:
* Access to the workshop materials
* A basic understanding of OCI and VMware concepts

## Task 1: Review the Workshop Scenario

In this workshop, the source virtual machine runs in Oracle Cloud VMware Solution (OCVS), and the target platform is Oracle Linux Virtualization Manager (OLVM) in Oracle Cloud Infrastructure (OCI). Oracle Cloud Migrations (OCM) is used to coordinate the migration workflow between the source and target environments.

1. Review the overall migration goal for the workshop.

2. Confirm that the source environment is VMware running in OCVS.

3. Confirm that the target environment is OLVM running in OCI.

4. Confirm that Oracle Cloud Migrations is the service used to manage the migration workflow.

## Task 2: Identify the Main Components

The workshop uses three main components:

1. **Oracle Cloud VMware Solution (OCVS)**  
   OCVS provides the VMware source environment. In this workshop, the source workload is hosted in a single-host Software-Defined Data Center (SDDC).

2. **Oracle Linux Virtualization Manager (OLVM)**  
   OLVM is the target virtualization platform in OCI where the migrated workload will run after the migration is complete.

3. **Oracle Cloud Migrations (OCM)**  
   OCM is used to connect to the source and target environments and perform the migration workflow.

## Task 3: Review OCVS Networking

Oracle Cloud VMware Solution uses NSX-T for virtual networking inside the VMware environment. OCI provides the underlying cloud network, including the VCN, subnets, and VLANs.

1. Review the OCI underlay networking components used by OCVS:
   - VCN
   - subnets
   - VLANs
   - routing

2. Review the NSX overlay networking components used inside the SDDC:
   - Tier-0 Gateway
   - Tier-1 Gateway
   - Segments

3. Note that the source virtual machine used in this workshop is connected to an NSX workload segment in the OCVS environment.

4. Review the architecture diagrams and screenshots provided in the workshop materials to understand the relationship between the OCI network and the NSX overlay network.

## Task 4: Review the Migration Flow

At a high level, the workshop follows this sequence:

1. Review the migration architecture.

2. Prepare OCI prerequisites.

3. Deploy the OCVS source environment.

4. Access and prepare the VMware source workload.

5. Prepare the OLVM target environment.

6. Prepare Oracle Cloud Migrations.

7. Migrate the workload.

8. Validate the migrated virtual machine in OLVM.

You are now ready to continue with the OCI prerequisite setup.

## Learn More

* [Oracle Cloud VMware Solution Documentation](https://docs.oracle.com/en-us/iaas/Content/VMware/home.htm)
* [VMware NSX Documentation](https://docs.vmware.com/en/VMware-NSX/index.html)

## Acknowledgements
* **Author** - Mark Atkinson, Master Principal Sales Consultant , Linux
* **Contributors** - ???, Group
* **Last Updated By/Date** - Perside Foster, Aril 2026