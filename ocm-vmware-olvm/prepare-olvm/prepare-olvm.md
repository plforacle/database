# Prepare the OLVM Target in OCI

## Introduction

This lab walks you through reviewing and preparing the Oracle Linux Virtualization Manager (OLVM) target environment in Oracle Cloud Infrastructure (OCI). You will confirm that the OLVM management environment, hosts, networking, and storage are ready to receive the migrated VMware workload.

Estimated Lab Time: 20 minutes

### Objectives

In this lab, you will:
* Review the OLVM target environment in OCI
* Access the OLVM administration interface
* Confirm that the required hosts, networks, and storage resources are available
* Verify that the target environment is ready for migration

### Prerequisites (Optional)

This lab assumes you have:
* Completed the OCI prerequisite setup for the workshop
* Access to the OLVM environment used for the migration demo
* Access to the OLVM administration interface and credentials
* A migration target design that identifies the required network and storage resources

## Task 1: Review the OLVM Environment

In this task, you will review the OLVM deployment used as the migration target.

1. Sign in to the OCI environment used for the workshop.

2. Locate the OLVM resources associated with the migration target environment.

3. Review the OLVM architecture used for the workshop, including the manager and virtualization hosts.

4. Confirm that the target environment is deployed and available for use.

## Task 2: Access the OLVM Administration Interface

In this task, you will confirm access to the OLVM administration interface.

1. Open the OLVM administration URL for the workshop environment.

2. Sign in with the credentials provided for the lab.

3. Review the OLVM administration dashboard.

4. Confirm that the target environment is healthy and manageable.

## Task 3: Review Target Hosts, Networks, and Storage

In this task, you will verify that the target resources are ready for migration.

1. Review the virtualization hosts available in the OLVM environment.

2. Confirm that the target logical network for the migrated VM is present.

3. Review the storage domains that will be used by the migrated VM.

4. Confirm that the required compute, network, and storage resources are available.

## Task 4: Confirm Target Readiness

In this task, you will verify that the target environment is ready for Oracle Cloud Migrations.

1. Confirm that the OLVM environment is reachable from the migration workflow.

2. Record the target details that will be needed later, including:
   - OLVM management endpoint
   - credentials
   - target network
   - target storage resources

3. Confirm that the environment is ready to receive the migrated VMware workload.

You have now reviewed and prepared the OLVM target environment in OCI.

## Learn More

* [Oracle Linux Virtualization Manager Documentation](https://docs.oracle.com/en/virtualization/oracle-linux-virtualization-manager/)
* [Oracle Linux KVM Documentation](https://docs.oracle.com/en/operating-systems/oracle-linux/kvm/)

## Acknowledgements
* **Author** - Mark Atkinson, Master Principal Sales Consultant , Linux
* **Contributors** - ???, Group
* **Last Updated By/Date** - Perside Foster, Aril 2026