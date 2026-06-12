# Validate the Migrated VM

## Introduction

This lab validates the migrated VM in OLVM. You will confirm that the VM exists in the target environment, review its placement, start it if needed, and verify basic guest functionality.

Estimated Lab Time: 30 minutes

### Objectives

In this lab, you will:

* Locate the migrated VM in OLVM
* Review compute, storage, and network placement
* Start and access the migrated VM
* Validate guest operating system and network functionality
* Complete the migration project after validation
* Capture demo completion notes

### Prerequisites (Optional)

This lab assumes the OCM migration workflow has completed or has created a target VM in OLVM that is ready for validation.

## Task 1: Locate the Migrated VM

Confirm that OCM created or registered the migrated VM in OLVM.

1. Open the OLVM Administration Portal.

2. Navigate to **Compute**, then **Virtual Machines**.

3. Locate the migrated VM.

4. If the VM name changed during migration, use the migration plan details in OCM to identify the target VM.

5. Confirm that the VM is in the expected cluster.

## Task 2: Review VM Placement

Review the migrated VM configuration before powering it on.

1. Open the VM details page.

2. Review the assigned memory and CPU.

3. Review the attached disks.

4. Confirm that the disks are on the expected storage domain.

5. Review the network interface.

6. Confirm that the network interface uses the expected VnicProfile.

7. Confirm that any required boot options or console settings are present.

## Task 3: Start and Access the VM

Power on the VM and open the console.

1. Start the migrated VM if it is not already running.

2. Open the VM console.

3. Wait for Oracle Linux to boot.

4. Sign in with the expected guest user.

5. If login fails, confirm whether OCM preserved the guest credentials or whether your team changed them during migration.

## Task 4: Validate Guest Functionality

Run basic checks inside the migrated guest.

1. Confirm the hostname.

2. Confirm the operating system version.

3. Confirm that the primary network interface is up.

4. Confirm that the VM has the expected IP address plan for the OLVM target network.

5. Confirm DNS resolution.

6. Confirm that the application or demo payload required by your scenario is available.

7. Record any differences between the source VM and the migrated VM.

## Task 5: Complete Post-Migration Fixes

Complete the common post-migration checks before declaring the demo successful.

1. Rebuild or update the virtual NIC configuration inside the guest if the migrated VM does not attach to the expected OLVM network.

2. Confirm that old MAC-address-specific configuration is removed.

3. Confirm that the VM receives the expected IP address or static configuration on the OLVM network.

4. Confirm that attached storage is accessible.

5. Confirm that the filesystem mounts expected by `/etc/fstab` are mounted.

6. Confirm that applications and services required by the demo start successfully.

7. Review guest logs for boot, driver, storage, or network errors.

8. Record any manual post-migration fixes in the runbook.

## Task 6: Mark the Migration Project Complete

After the VM is validated, mark the migration complete in OCM.

1. Return to the OCI Console.

2. Open **Migration & Disaster Recovery**, then **Cloud Migrations**.

3. Open **Migrations**.

4. Select the migration project used by this workshop.

5. Confirm that the migration asset and migration plan no longer require changes.

6. Click **Mark migration complete**.

7. If additional testing is required, leave the project active until the team completes validation.

## Task 7: Capture Completion Notes

Capture the operational details needed to repeat the demo.

1. Record the OCM migration project name.

2. Record the migration plan name.

3. Record the source VM name and target VM name.

4. Record the OLVM cluster, storage domain, and VnicProfile used.

5. Record any manual fixes required after migration.

6. Store final credentials, certificates, and access notes in OCI Vault or your team's approved secret store.

7. Record the remote agent appliance name and status.

8. Record the VDDK dependency name.

9. Record the replication bucket name.

10. Record any work request IDs associated with migration errors or retries.

## Learn More

* [Oracle Linux Virtualization Manager Documentation](https://docs.oracle.com/en/virtualization/oracle-linux-virtualization-manager/)
* [Oracle Cloud Migrations Documentation](https://docs.oracle.com/en-us/iaas/Content/cloud-migration/home.htm)

## Acknowledgements

* **Author** - Mark Atkinson, Master Principal Sales Consultant, Open Cloud Technologies
* **Workshop Draft** - Perside Foster, May 2026
* **Source Material** - OCM-DEMO-BUILD presentation, Confluence VMware-to-OLVM planning PDF, and Oracle Cloud Migrations LA PDF
