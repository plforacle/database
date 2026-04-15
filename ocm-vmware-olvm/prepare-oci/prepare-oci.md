# Prepare OCI Prerequisites

## Introduction

This lab walks you through the OCI foundation setup required for the migration workshop. You will review the required access, create or identify the workshop compartment, and create the virtual cloud network (VCN) that supports the OCVS deployment and migration environment.

Estimated Lab Time: 20 minutes

### Objectives

In this lab, you will:
* Review the OCI permissions required for the workshop
* Create or identify the compartment used for the migration environment
* Review the VCN addressing plan
* Create the VCN and required subnets
* Review optional supporting OCI resources used by the workshop

### Prerequisites (Optional)

This lab assumes you have:
* Access to an OCI tenancy with permission to manage compartments, networking resources, and supporting services
* Access to an OCI tenancy enabled for OCVS
* A non-overlapping IP address plan for your workshop environment

## Task 1: Review Required OCI Access

In this task, you will review the OCI access needed for the workshop.

1. Sign in to the OCI Console.

2. Confirm that you have administrator access or delegated IAM policies that allow you to work with OCVS, networking, and related OCI resources.

3. Open the navigation menu and select **Hybrid & Multicloud**, then **Software-Defined Data Centers**.

4. Confirm that you can access the OCVS service in your tenancy.

5. If you cannot access the required OCVS functions, stop here and confirm that your tenancy and user account have the required permissions.

## Task 2: Create or Identify the Workshop Compartment

In this task, you will create or identify the compartment used for the workshop resources.

1. Open the navigation menu and select **Identity & Security**, then select **Compartments**.

2. Create a new sub-compartment if needed, or identify the existing compartment that will be used for the workshop.

3. Use a consistent name for the workshop compartment.

4. Confirm that the compartment is available for networking, OCVS, and related resources.

## Task 3: Review the VCN Addressing Plan

Before creating the VCN, review the network plan used for the workshop.

1. Confirm that the VCN CIDR block does not overlap with other workshop or regional environments.

2. Review the subnet layout used for the environment:
   - public subnet
   - private subnet
   - OCVS subnet

3. Confirm that your selected address ranges leave room for the OCVS deployment and related resources.

4. Record the CIDR ranges you will use in your environment.

## Task 4: Create the VCN

In this task, you will create the VCN for the workshop.

1. Open the navigation menu and select **Networking**, then select **Virtual cloud networks**.

2. Click **Create VCN**.

3. Select the workshop compartment.

4. Enter a name for the VCN.

5. Enter the CIDR block for the VCN.

6. Create the required subnets for the environment:
   - a public subnet
   - a private subnet
   - an OCVS subnet

7. Confirm that the VCN and subnets are created successfully.

## Task 5: Review Optional Supporting OCI Resources

The presentation also includes additional OCI preparation items that may be used in the full demo environment.

1. Review whether you will use an OCI Vault for passwords, keys, and secrets associated with the workshop.

2. Review whether you will use a jump host or OCI Bastion for administrative access to the environment.

3. Confirm which of these supporting resources are part of your final workshop scope.

You have now prepared the core OCI prerequisites for the migration workshop.

## Learn More

* [OCI Compartments Documentation](https://docs.oracle.com/en-us/iaas/Content/Identity/Tasks/managingcompartments.htm)
* [OCI Networking Documentation](https://docs.oracle.com/en-us/iaas/Content/Network/Concepts/overview.htm)
* [OCI Vault Documentation](https://docs.oracle.com/en-us/iaas/Content/KeyManagement/home.htm)

## Acknowledgements
* **Author** - Mark Atkinson, Master Principal Sales Consultant , Linux
* **Contributors** - ???, Group
* **Last Updated By/Date** - Perside Foster, Aril 2026