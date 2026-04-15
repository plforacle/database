# Migrate the Workload with Oracle Cloud Migrations

## Introduction

This lab walks you through using Oracle Cloud Migrations (OCM) to migrate a VMware virtual machine from Oracle Cloud VMware Solution (OCVS) to Oracle Linux Virtualization Manager (OLVM) in OCI. You will review the migration source and target, start the migration workflow, and monitor its progress.

Estimated Lab Time: 30 minutes

### Objectives

In this lab, you will:
* Review the migration source and target in Oracle Cloud Migrations
* Select the VMware virtual machine to migrate
* Configure and start the migration workflow
* Monitor the migration job until it completes

### Prerequisites (Optional)

This lab assumes you have:
* A VMware source virtual machine in OCVS ready for migration
* An OLVM target environment ready for migration
* Oracle Cloud Migrations configured with the required source and target details

## Task 1: Review the Migration Configuration

In this task, you will review the migration configuration in Oracle Cloud Migrations.

1. In the OCI Console, open **Oracle Cloud Migrations**.

2. Open the migration project used for this workshop.

3. Review the configured source environment and confirm it points to the VMware environment in OCVS.

4. Review the configured target environment and confirm it points to the OLVM environment in OCI.

5. Confirm that the source virtual machine you prepared earlier is visible and ready for migration.

## Task 2: Select the VMware Source Workload

In this task, you will identify the VMware workload to migrate.

1. In Oracle Cloud Migrations, navigate to the list of discovered or available source virtual machines.

2. Select the VMware virtual machine used in this workshop.

3. Review the workload details, including the guest operating system, storage, and network configuration.

4. Confirm that this is the correct workload for the migration.

## Task 3: Configure the Migration

In this task, you will configure the migration settings for the selected virtual machine.

1. Start a new migration for the selected VMware virtual machine.

2. Select the OLVM target environment.

3. Review and configure any required target options, such as compute, storage, and networking selections.

4. Confirm the migration settings and proceed.

## Task 4: Start and Monitor the Migration

In this task, you will run the migration and monitor its status.

1. Start the migration job.

2. Monitor the progress of the migration in Oracle Cloud Migrations.

3. Review status messages, progress indicators, or task details as the migration runs.

4. Wait until the migration completes successfully.

You have now completed the workload migration from VMware in OCVS to OLVM in OCI.

## Learn More

* [Oracle Cloud Migrations Documentation](https://docs.oracle.com/en-us/iaas/Content/cloud-migrations/home.htm)

## Acknowledgements
* **Author** - Mark Atkinson, Master Principal Sales Consultant , Linux
* **Contributors** - ???, Group
* **Last Updated By/Date** - Perside Foster, Aril 2026