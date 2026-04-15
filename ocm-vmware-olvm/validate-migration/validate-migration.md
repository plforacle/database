# Validate the Migrated VM

## Introduction

This lab walks you through validating the migrated virtual machine in Oracle Linux Virtualization Manager (OLVM). You will confirm that the virtual machine is present in the target environment, review its configuration, and verify basic functionality after migration.

Estimated Lab Time: 15 minutes

### Objectives

In this lab, you will:
* Confirm that the migrated virtual machine appears in OLVM
* Review the migrated virtual machine configuration
* Start or access the migrated virtual machine if needed
* Verify basic post-migration functionality

### Prerequisites (Optional)

This lab assumes you have:
* Completed the migration workflow in Oracle Cloud Migrations
* Access to the OLVM environment used in the workshop
* A successfully migrated virtual machine available in the target environment

## Task 1: Locate the Migrated Virtual Machine

In this task, you will confirm that the migrated virtual machine is present in OLVM.

1. Open the OLVM administration interface.

2. Navigate to the list of virtual machines.

3. Locate the migrated virtual machine.

4. Confirm that the virtual machine appears in the expected target environment.

## Task 2: Review the Virtual Machine Configuration

In this task, you will review the migrated virtual machine settings.

1. Open the details for the migrated virtual machine.

2. Review the assigned compute, memory, storage, and network configuration.

3. Confirm that the migrated virtual machine is connected to the expected OLVM network and storage resources.

## Task 3: Verify Basic Functionality

In this task, you will verify that the migrated virtual machine is operational.

1. Power on the migrated virtual machine if it is not already running.

2. Open the virtual machine console or connect using the access method provided in your environment.

3. Sign in to the virtual machine.

4. Verify basic functionality, such as:
   - the operating system boots successfully
   - the expected hostname or configuration is present
   - the network is available
   - the migrated system is usable

You have now validated that the VMware workload was successfully migrated from OCVS to OLVM in OCI.

## Learn More

* [Oracle Linux Virtualization Manager Documentation](https://docs.oracle.com/en/virtualization/oracle-linux-virtualization-manager/)

## Acknowledgements
* **Author** - Mark Atkinson, Master Principal Sales Consultant , Linux
* **Contributors** - ???, Group
* **Last Updated By/Date** - Perside Foster, Aril 2026