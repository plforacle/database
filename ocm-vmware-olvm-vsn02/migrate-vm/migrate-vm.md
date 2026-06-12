# Migrate the Workload with Oracle Cloud Migrations

## Introduction

This lab runs the OCM migration workflow for the VMware source VM. You will create an OLVM migration project with an initial migration plan, add the discovered VMware asset, configure replication, validate the target asset configuration, replicate the VM, generate and deploy the Resource Manager stack, and monitor the migration.

Estimated Lab Time: 1 hour

### Objectives

In this lab, you will:

* Create an OCM migration project with an initial OLVM migration plan
* Add the `OL9u7-test` VMware asset to the project
* Configure the replication location and schedule
* Create an OLVM migration plan
* Configure and review the target asset
* Replicate the migration asset
* Generate and deploy the Resource Manager stack
* Monitor work requests and migration status

### Prerequisites (Optional)

This lab assumes OCM has discovered the `OL9u7-test` source VM, the OLVM target resources are visible in inventory, the remote agent appliance is active, and the VDDK dependency is verified.

## Task 1: Create the OLVM Migration Project and Initial Plan

Create a migration project for the demo and use the OLVM migration project option.

1. In the OCI Console, open **Migration & Disaster Recovery**, then **Cloud Migrations**.

2. Select the `olvm-migrations` compartment.

3. Open **Migrations**.

4. Click **Create migration project**.

5. Choose **Create a migration project with initial migration plan**.

6. Enter a project name.

    Example: `ocm-demo-olvm`

7. Select the migration compartment.

8. Enable the **OLVM Migration Project** option.

9. Choose the replication schedule option required for the demo.

    For the first demo build, use no schedule or a manual schedule unless your team wants repeated incremental transfers.

10. Add tags if required by your team.

11. Add the `OL9u7-test` asset from the VMware inventory.

12. Select the availability domain, replication compartment, and replication bucket created by the OCM prerequisites stack.

13. Enter a migration plan name.

    Example: `ol9u7-to-olvm`

14. Select the migration plan compartment.

15. Select the OLVM target compartment.

16. Select the OLVM target environment values:

    * Cluster
    * VnicProfile
    * Template

17. Review the summary and create the project.

18. Open the migration project details page.

## Task 2: Review the Replication Schedule

Confirm where OCM stores replicated data and when replication runs.

1. On the migration project details page, open the replication schedule details.

2. Confirm the replication schedule option selected during project creation.

3. Confirm the availability domain required by your target environment.

4. Confirm the replication compartment.

5. Confirm the replication bucket created by the OCM prerequisites stack.

6. Record the replication bucket and schedule in your worksheet.

7. If the schedule or replication location is wrong, update it before running the first replication.

## Task 3: Review the VMware Migration Asset

Confirm that the discovered VMware VM was added to the project.

1. On the migration project details page, open **Migration assets**.

2. Confirm that the asset list includes:

    `OL9u7-test`

3. Open the migration asset details page.

4. Confirm the source asset metadata and replication location.

5. Confirm that the migration asset is active or ready for replication.

6. If the asset was not added during project creation, add it from the VMware inventory before continuing.

## Task 4: Review or Create the OLVM Migration Plan

Review the initial migration plan created with the project. Create another plan only if your team needs an alternate target mapping.

1. On the migration project details page, open **Migration plans**.

2. Open the initial migration plan.

    Example: `ol9u7-to-olvm`

3. Confirm that the plan includes the `OL9u7-test` migration asset.

4. Confirm the OLVM target compartment.

5. Confirm the OLVM target environment values:

    * Cluster
    * VnicProfile
    * Template

6. If your console requires storage mapping, confirm the target storage domain or storage placement.

7. If you need a second plan, click **Create migration plan** and repeat the OLVM target selections.

## Task 5: Review and Configure Target Assets

Review the target asset before replication and execution.

1. On the migration plan details page, open **Target assets**.

2. Select the target asset created for `OL9u7-test`.

3. Open **Summary** and review compatibility messages.

4. Resolve any compatibility errors before continuing.

5. Review warnings such as CPU, memory, VNIC, bandwidth, GPU, operating system, or network compatibility.

6. Open **Configuration**.

7. Confirm the OLVM target cluster.

8. Confirm the target VnicProfile.

9. Confirm the target template.

10. Confirm the target storage placement.

11. Make any required target asset edits before replication and stack deployment.

12. If the source environment or target inventory changed, refresh the migration asset or migration plan before continuing.

## Task 6: Replicate the Migration Asset

Start replication and confirm that OCM creates the replicated volume data.

1. On the migration plan details page, open **Target assets**.

2. Select the target asset for `OL9u7-test`.

3. Click **Replicate migration asset**.

4. Confirm the replication action.

5. Monitor the replication work request.

6. On the migration asset details page, open **Replicated volumes**.

7. Confirm that replicated volumes appear for the source VM disks.

8. If the demo requires a later cutover point, run an incremental replication after the first replication completes.

9. If replication fails, check the remote agent appliance, VDDK dependency, vCenter permissions, ESXi connectivity, and Object Storage bucket access.

## Task 7: Generate and Deploy the Resource Manager Stack

Generate and deploy the stack used by the migration plan.

1. On the migration project details page, open **Migration plans**.

2. Open the `ol9u7-to-olvm` migration plan.

3. Click **Generate RMS stack**.

4. Wait for the Resource Manager stack link to appear on the migration plan details page.

5. Click **Deploy RMS stack**.

6. Review the Resource Manager plan.

7. Apply the stack.

8. Return to the migration plan details page.

9. Open **Created resources**.

10. Confirm that the expected migration resources were created.

## Task 8: Monitor the Migration

Monitor the migration status until the target VM is ready for validation.

1. On the migration project details page, review migration project status.

2. Review the migration plan status.

3. Review the target asset status.

4. Review work requests for any failed or blocked operation.

5. Capture the work request ID for any error that must be escalated.

6. Do not mark the migration project complete until you validate the migrated VM in OLVM.

## Learn More

* [Oracle Cloud Migrations Documentation](https://docs.oracle.com/en-us/iaas/Content/cloud-migration/home.htm)
* [Create a Migration Project for OLVM](https://docs.oracle.com/en-us/iaas/Content/cloud-migration/cloud-migration-create-migration-project-olvm.htm)

## Acknowledgements

* **Author** - Mark Atkinson, Master Principal Sales Consultant, Open Cloud Technologies
* **Workshop Draft** - Perside Foster, May 2026
* **Source Material** - OCM-DEMO-BUILD presentation, Confluence VMware-to-OLVM planning PDF, and Oracle Cloud Migrations LA PDF
