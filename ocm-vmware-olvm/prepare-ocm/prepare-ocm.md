
## Introduction

# Prepare Oracle Cloud Migrations


This lab walks you through preparing Oracle Cloud Migrations (OCM) for the migration workflow. You will access the migration service, review the migration project, and prepare the source and target connection details required to migrate a VMware virtual machine from OCVS to OLVM in OCI.

Estimated Lab Time: 20 minutes

### Objectives

In this lab, you will:
* Access Oracle Cloud Migrations in OCI
* Review the migration project used for the workshop
* Prepare the VMware source connection details
* Prepare the OLVM target connection details
* Confirm that Oracle Cloud Migrations is ready for the migration workflow

### Prerequisites (Optional)

This lab assumes you have:
* Completed the OCVS source environment setup
* Completed the OLVM target environment review
* Access to Oracle Cloud Migrations in the OCI tenancy used for the workshop
* The required source and target credentials and connection details

## Task 1: Access Oracle Cloud Migrations

In this task, you will access Oracle Cloud Migrations in the OCI Console.

1. Sign in to the OCI Console.

2. Open the navigation menu and select **Oracle Cloud Migrations**.

3. Select the compartment used for the workshop.

4. Confirm that you can access the migration service and view the migration resources available in the environment.

## Task 2: Review the Migration Project

In this task, you will review the migration project used for the workshop.

1. Open the migration project associated with this lab, or create a project if your environment requires one.

2. Review the project details and confirm that it will be used to migrate a VMware workload from OCVS to OLVM.

3. Review any migration assets, discovered systems, or related resources already associated with the project.

## Task 3: Prepare the VMware Source Connection

In this task, you will prepare the source connection details for the VMware environment.

1. Gather the connection details for the VMware source environment in OCVS.

2. Confirm the **vCenter** endpoint, credentials, and any required access information.

3. Verify that the source virtual machine prepared earlier is the workload that will be migrated in this workshop.

4. Confirm that Oracle Cloud Migrations can use the source environment details required for the migration workflow.

## Task 4: Prepare the OLVM Target Connection

In this task, you will prepare the target connection details for the OLVM environment.

1. Gather the connection details for the OLVM target environment.

2. Confirm the OLVM management endpoint and credentials.

3. Review the target network and storage details that will be used for the migrated virtual machine.

4. Confirm that Oracle Cloud Migrations has the information needed to use the OLVM environment as a migration target.

## Task 5: Confirm Migration Readiness

In this task, you will confirm that Oracle Cloud Migrations is ready for the migration workflow.

1. Review the source and target configuration prepared in this lab.

2. Confirm that the required source and target details are available and correct.

3. Verify that you are ready to begin the migration in the next lab.

You have now prepared Oracle Cloud Migrations for the migration workflow.

## Learn More

* [Oracle Cloud Migrations Documentation](https://docs.oracle.com/en-us/iaas/Content/cloud-migrations/home.htm)

## Acknowledgements
* **Author** - Mark Atkinson, Master Principal Sales Consultant , Linux
* **Contributors** - ???, Group
* **Last Updated By/Date** - Perside Foster, Aril 2026