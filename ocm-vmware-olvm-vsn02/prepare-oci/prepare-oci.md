# Prepare OCI Prerequisites

## Introduction

This lab prepares the OCI foundation for the OCM demo. You will confirm tenancy access, create or identify the workshop compartment, create a vault for secrets, and define the VCN addressing plan used by the OCVS source environment and the OLVM target.

Estimated Lab Time: 1 hour

### Objectives

In this lab, you will:

* Confirm OCVS and OCM access
* Create or identify the workshop compartment
* Create an OCI vault and encryption key for demo secrets
* Create or verify OCM migration prerequisites
* Define a non-overlapping network plan
* Create the VCN and supporting networking resources
* Choose the management access pattern for the build

### Prerequisites (Optional)

This lab assumes you have access to an OCI tenancy where you can manage compartments, IAM policies, networking, vault resources, compute instances, OCVS, and OCM.

## Task 1: Confirm Tenancy and IAM Readiness

Before you build anything, confirm that the tenancy and user account can deploy the services used by the demo.

1. Sign in to the OCI Console.

2. Confirm that your user has administrator permissions or delegated IAM policies for OCVS, OCM, networking, compute, vault, and OLVM-related resources.

3. Open the navigation menu and select **Hybrid & Multicloud**, then **Software-Defined Data Centers**.

4. Click **Create SDDC** far enough to confirm the workflow opens.

5. If the workflow fails because the tenancy is not enabled for OCVS, stop and request OCVS enablement before continuing.

6. Open the navigation menu and confirm that **Migration & Disaster Recovery** includes **Cloud Migrations**.

7. If the OCM service is not visible or access is denied, stop and request OCM access before continuing.

## Task 2: Create or Identify the Workshop Compartment

Create a dedicated compartment so the demo resources are easy to find and clean up. The OCM planning material recommends thinking in three logical compartments: a migration compartment for temporary migration resources, a migration-secrets compartment for vault secrets, and a destination compartment for target resources. For this demo, you can use one `olvm-migrations` compartment if your team standard allows it, but keep the logical separation in your worksheet.

1. Open the navigation menu and select **Identity & Security**, then **Compartments**.

2. Create or identify a sub-compartment one level below the tenancy root.

3. Use this recommended name unless your team has a different naming standard:

    `olvm-migrations`

4. Confirm that you can select the compartment in the OCVS, Compute, Networking, Vault, and OCM pages.

## Task 3: Create the Demo Vault

The build produces several credentials and keys. Store them in OCI Vault so the demo team can retrieve them later without sharing credentials in notes or chat.

1. Open the navigation menu and select **Identity & Security**, then **Vault**.

2. Confirm that the root tenancy compartment is selected, or select the compartment required by your team's vault standard.

3. Create a vault. Use one of the following names, or use your team's standard:

    `Root-SC-Vault`

    `Master-SC-Vault`

4. Open **Master encryption keys** and create a key named:

    `SCMasterKey`

5. Open **Secret Management**.

6. Plan to store these items as secrets during the build:

    OCVS SSH public and private keys, vCenter credentials, NSX credentials, jump host password, Oracle Linux VM password, OLVM administrator password, OCM credentials, and any certificates required by the demo.

## Task 4: Create OCM Migration Prerequisites

OCM can create migration prerequisites through a Resource Manager stack. Use this workflow to create or verify the service prerequisites before source discovery and replication.

1. Open the navigation menu and select **Migration & Disaster Recovery**, then **Cloud Migrations**.

2. Open **Overview**.

3. Select the migration compartment.

4. Click **Create Prerequisites** if prerequisites have not already been created.

5. Review the generated Resource Manager stack.

6. Confirm the stack creates or references the Cloud Migrations tag namespace and tag definitions required by the migration service.

7. Confirm the stack creates or references the Object Storage bucket used for replication snapshots.

8. Confirm the stack creates or updates the IAM policies required by OCM service components.

9. Apply the stack and wait for it to complete.

10. Return to the Cloud Migrations Overview page and review the created prerequisite resources.

11. Record the replication bucket name and prerequisite stack name in your build worksheet.

12. If the prerequisites already exist, confirm that they were created with the current stack version and update them if the console prompts you to do so.

## Task 5: Define the Network Plan

The source deck emphasizes non-overlapping CIDR ranges. This matters because OCVS, NSX, OLVM, and any future DRG peering or routing must be able to coexist.

1. Choose the VCN name:

    `ocm-demo`

2. Choose a regional CIDR plan. The source deck provides these examples:

    | Team | VCN CIDR | Public subnet | Private subnet | OCVS cluster CIDR |
    | --- | --- | --- | --- | --- |
    | NA SC | `10.10.0.0/16` | `10.10.0.0/24` | `10.10.1.0/24` | `10.10.3.0/24` |
    | JAPAC SC | `10.11.0.0/16` | `10.11.0.0/24` | `10.11.1.0/24` | `10.11.3.0/24` |
    | EMEA SC | `10.12.0.0/16` | `10.12.0.0/24` | `10.12.1.0/24` | `10.12.3.0/24` |
    | ODSC | `10.13.0.0/16` | `10.13.0.0/24` | `10.13.1.0/24` | `10.13.3.0/24` |

3. Reserve the NSX workload segment CIDR for OCVS:

    `192.168.200.0/24`

4. Confirm that the OLVM target network does not overlap with the VCN, OCVS cluster CIDR, or NSX workload CIDR.

## Task 6: Create the VCN

Create the VCN before starting the OCVS deployment.

1. Open the navigation menu and select **Networking**, then **Virtual cloud networks**.

2. Click **Create VCN**.

3. Select the `olvm-migrations` compartment.

4. Enter the VCN name:

    `ocm-demo`

5. Enter the selected VCN CIDR block.

6. Create a public subnet for the jump host.

7. Create a private subnet if your team uses one for OLVM, supporting services, or private management.

8. Confirm that a NAT gateway exists or can be created by the OCVS workflow when required.

9. Confirm that the VCN is available in the compartment before continuing.

## Task 7: Choose the Management Access Pattern

You need an administrative path into vCenter, NSX Manager, and OLVM.

1. Use a Windows jump host for the initial build if you are following the source deck.

2. Use OCI Bastion after the build if your team wants time-bound SSH tunnels or managed sessions.

3. Do not expose management endpoints broadly to the internet.

4. Plan to restrict jump host ingress to the public IP address or CIDR range used by the demo builder.

## Learn More

* [OCI Compartments Documentation](https://docs.oracle.com/en-us/iaas/Content/Identity/Tasks/managingcompartments.htm)
* [OCI Networking Documentation](https://docs.oracle.com/en-us/iaas/Content/Network/Concepts/overview.htm)
* [OCI Vault Documentation](https://docs.oracle.com/en-us/iaas/Content/KeyManagement/home.htm)
* [Oracle Cloud Migrations IAM Setup](https://docs.oracle.com/en-us/iaas/Content/cloud-migration/cloud-migration-get-started.htm)

## Acknowledgements

* **Author** - Mark Atkinson, Master Principal Sales Consultant, Open Cloud Technologies
* **Workshop Draft** - Perside Foster, May 2026
* **Source Material** - OCM-DEMO-BUILD presentation, Confluence VMware-to-OLVM planning PDF, and Oracle Cloud Migrations LA PDF
