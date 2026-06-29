# Set Up the OLVM Target Environment

## Introduction

In this lab, you store OLVM credentials and certificate material in Vault, create the OLVM asset source in OCM, and discover OLVM target assets.

Estimated Time: 25 minutes

### Objectives

In this lab, you will:

* Store OLVM password and certificate secrets in Vault.
* Create the OLVM asset source.
* Discover OLVM target assets.
* Confirm that target clusters, storage domains, templates, and network profiles are visible in inventory.

## Task 1: Download Engine CA Certificate 

1. from OLVM  https://157....nip.io/ovirt-engine/

2. Download certificate

3. Open certificate with notepad  and copy  


## Task 2: Create the OLVM Asset Source

1. Open **Migration & Recovery**, then open **Cloud Migrations**.

2. Open **Asset Sources**.

3. Click **Create Asset Source**.

4. Select **OLVM** as the source type.

5. Enter the OLVM asset source details.

    | Field | Value |
    | --- | --- |
    | Name | **olvm-target-01** |
    | Compartment | Migration compartment |
    | OLVM Manager URL | OLVM Manager FQDN or IP |
    | Remote connections source environment | vmware-source-01 |
    | Username | OLVM admin or migration user |
    | Password secret | olvm-password |
    | Certificate secret | olvm-certificate |

6. Click **Create Asset Source**.

7. Confirm that the asset source status is **Active**.

## Task 3: Discover OLVM Target Assets

1. Open the OLVM asset source details page.

2. Click **Discover Assets**.

3. Wait for the discovery job to complete.

4. Open **Inventory**.

5. Filter for OLVM assets.

6. Confirm that the inventory contains the target resources needed by the migration plan.

    ```text
    OLVM cluster:
    Storage domains:
    Templates:
    Network profiles:
    ```

7. If the OLVM Manager is not reachable from OCI, use standalone discovery through the OCM API with the **CreateDiscoverySchedule** operation.

## Learn More

* [Oracle Linux Virtualization Manager documentation](https://docs.oracle.com/en/virtualization/oracle-linux-virtualization-manager/)
* [Oracle Cloud Migrations documentation](https://docs.oracle.com/en-us/iaas/Content/cloud-migration/home.htm)

## Acknowledgements

* **Author** - Mark Atkinson, Evgeny Golenkov, Andrey Sokolov, Perside Foster
* **Contributor** - Keya Balutkar
* **Last Updated By/Date** - Perside Foster, June 2026
