# Create the OLVM Migration Project

## Introduction

In this lab, you create an OCM migration project with an initial OLVM migration plan, add the discovered VMware VM, configure the replication location, and select OLVM target values.

Estimated Time: 30 minutes

### Objectives

In this lab, you will:

* Create a migration project with an initial migration plan.
* Enable OLVM migration project mode.
* Add discovered VMware assets.
* Set the replication location.
* Configure OLVM cluster, VnicProfile, and template values.

## Task 1: Start the Migration Project Wizard

1. In the OCI Console Menu, open **Migration & Recovery**,**Cloud Migrations**, **Migrations**.
    ![Migrations Menu](images/migrations-menu.png "Select Migrations Menu")

2. From **Migration projects** page click **Create migration project** button.
    ![Create migrations project button](images/create-migrations-project-button.png "Click Create migrations project  button")

3. Select **Create a migration project with initial migration plan**. Then click **Create Migration Project**.
    ![Create migrations project initial plan](images/migration-project-initial-plan.png "Select  Create migrations project initial plan")

## Task 2: Enter Basic Information

1. For **Display Name**, enter `olvm-migration-wave-01` or your migration wave name.

2. Select the migration compartment.

3. Enable the **OLVM Migration Project** option.

    This option is required. Without it, the project targets OCI Compute instead of OLVM and the OLVM-specific target fields will not appear.

4. Select a replication schedule or leave the schedule as manual for a first test.

5. Click **Next**.

## Task 3: Add Assets

1. Click **Add Assets** info ... select  **OCM Invetory** button.

2. Select the VMware VMs from inventory that you want to include in the migration wave.

3. Click **Add Selected Assets**.

4. Confirm that the selected VMs appear in the assets list.

5. Click **Next**.

## Task 4: Set the Replication Location

1. Under **Default Replication Location**, select the target availability domain.

2. Select the replication compartment.

3. Select the replication bucket created by the prerequisites stack.

4. Click **Select**.

5. Confirm that all assets show the selected replication location.

6. Click **Next**.

## Task 5: Configure the Initial Migration Plan

1. Enter a display name for the migration plan, such as `olvm-plan-wave-01`.

2. Select the target compartment.

3. Click **Next**.

## Task 6: Configure the OLVM Target Environment

1. Select the OLVM cluster where migrated VMs will be provisioned.

2. Select the VnicProfile to assign to migrated VM network adapters.

3. Select the template to use as the base for provisioned VMs in OLVM.

4. Click **Submit**.

5. Confirm that the migration project and initial migration plan appear with an active status.

6. Record the project and plan names.

    ```text
    Migration project:
    Migration plan:
    Replication bucket:
    Target cluster:
    Target VnicProfile:
    Target template:
    ```

## Learn More

* [Create a migration project for OLVM](https://docs.oracle.com/en-us/iaas/Content/cloud-migration/cloud-migration-create-migration-project-olvm.htm)

## Acknowledgements

* **Author** - Mark Atkinson, Evgeny Golenkov, Andrey Sokolov, Perside Foster
* **Contributor** - Keya Balutkar
* **Last Updated By/Date** - Perside Foster, July 2026
