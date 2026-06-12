# Run VMware Asset Discovery

## Introduction

In this lab, you create the VMware asset source and run discovery so OCM can inventory the source VMs that you plan to migrate.

Estimated Time: 25 minutes

### Objectives

In this lab, you will:

* Create a VMware asset source.
* Associate the source environment and VDDK dependency.
* Discover VMware source VMs.
* Confirm source VM inventory metadata.

## Task 1: Create a VMware Asset Source

1. In the OCI Console, open **Migration & Disaster Recovery**, then open **Cloud Migrations**.

2. Open **Asset Sources**.

3. Click **Create Asset Source**.

4. Select **VMware** as the source type.

5. Enter the VMware asset source details.

    | Field | Value |
    | --- | --- |
    | Name | `vsphere-source-01` |
    | Compartment | Migration compartment |
    | vCenter URL | vCenter FQDN or IP |
    | Username | vCenter service account |
    | Password secret | `vcenter-password` |
    | Source environment | `vmware-source-01` |
    | Agent dependency | `vddk-package` |

6. Click **Create Asset Source**.

7. Confirm that the VMware asset source status is **Active**.

## Task 2: Discover VMware Source VMs

1. Open the VMware asset source details page.

2. Click **Discover Assets**.

3. Wait for the discovery job to complete.

4. Open **Inventory**.

5. Confirm that VMware VMs appear in inventory.

6. Open the VM you plan to migrate.

7. Confirm that CPU, memory, disk, network adapter, and guest operating system metadata are visible.

8. Record the selected source VM.

    ```text
    VMware asset source:
    Source VM:
    Discovery job:
    ```

## Learn More

* [Oracle Cloud Migrations documentation](https://docs.oracle.com/en-us/iaas/Content/cloud-migration/home.htm)

## Acknowledgements

* **Author** - Mark Atkinson, Evgeny Golenkov, Perside Foster
* **Last Updated By/Date** - Perside Foster, June 2026
