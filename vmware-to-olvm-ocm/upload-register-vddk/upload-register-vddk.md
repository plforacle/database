# Upload and Register the VDDK

## Introduction

The VMware Virtual Disk Development Kit (VDDK) is required by the OCM remote agent to read VMware snapshot data during replication. In this lab, you upload the VDDK package to Object Storage and register it as an agent dependency.

Estimated Time: 20 minutes

### Objectives

In this lab, you will:

* Upload the VDDK package to Object Storage.
* Create an OCM agent dependency for the VDDK package.
* Confirm that the dependency is available before remote agent setup.

## Task 1: Upload the VDDK to Object Storage

1. In the OCI Console, open **Storage**, then open **Buckets**.

2. Select the migration compartment.

3. Open the bucket created by the prerequisites stack.

4. Click **Upload**.

5. Select the VDDK package file that you downloaded from the VMware portal.

6. Click **Upload**.

7. Wait for the upload to complete.

8. Confirm that the VDDK file appears in the bucket object list.

9. Record the Object Storage values.

    ```text
    Object Storage namespace:
    VDDK bucket:
    VDDK object:
    ```

## Task 2: Register the VDDK as an Agent Dependency

1. Open **Migration & Disaster Recovery**, then open **Cloud Migrations**.

2. Open **Remote Connections**.

3. Open **Agent Dependencies**.

4. Click **Create Agent Dependency**.

5. For **Name**, enter `vddk-package` or your approved dependency name.

6. Select the migration compartment.

7. Select the Object Storage bucket that contains the VDDK package.

8. Select the VDDK object.

9. Click **Create**.

10. Confirm that the agent dependency appears with a status of **Available**.

11. Record the dependency name.

    ```text
    Agent dependency:
    Dependency status:
    ```

## Learn More

* [Oracle Cloud Migrations documentation](https://docs.oracle.com/en-us/iaas/Content/cloud-migration/home.htm)
* [OCI Object Storage documentation](https://docs.oracle.com/en-us/iaas/Content/Object/home.htm)

## Acknowledgements

* **Author** - Mark Atkinson, Evgeny Golenkov, Perside Foster
* **Last Updated By/Date** - Perside Foster, June 2026
