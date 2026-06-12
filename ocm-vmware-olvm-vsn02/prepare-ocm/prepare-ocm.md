# Prepare Oracle Cloud Migrations

## Introduction

This lab prepares OCM for the VMware-to-OLVM migration demo. You will confirm service prerequisites, create the source environment, deploy and register the remote agent appliance, add the VDDK dependency, create VMware and OLVM asset sources, run discovery, and confirm that the source and target inventory is ready for the OLVM migration project.

Estimated Lab Time: 1 hour

### Objectives

In this lab, you will:

* Confirm OCM access and prerequisite resources
* Create the OCM source environment for OCVS
* Deploy and register the remote agent appliance in vCenter
* Add the VDDK agent dependency required for VMware replication
* Create and discover the VMware asset source
* Create and discover the OLVM asset source
* Confirm migration readiness before creating the project

### Prerequisites (Optional)

This lab assumes the OCVS source VM is running, the OLVM target worksheet is complete, OCM prerequisites were created, and the OCVS workload network can reach OCI services, vCenter, ESXi hosts, DNS, NTP, and OLVM.

## Task 1: Confirm OCM Access and Prerequisites

Confirm that the OCM service and prerequisite resources are available in the tenancy.

1. Sign in to the OCI Console.

2. Select the `olvm-migrations` compartment.

3. Open the navigation menu and select **Migration & Disaster Recovery**, then **Cloud Migrations**.

4. Confirm that you can view or create migration resources.

5. Open **Overview**.

6. Confirm that the OCM prerequisites stack has completed.

7. Confirm that the replication bucket exists.

8. Confirm that the Cloud Migrations tag namespace and required tag definitions exist.

9. Confirm that OCM service policies and dynamic-group policies were created or reviewed.

10. If access is denied or prerequisite resources are missing, stop and resolve IAM or OCM prerequisite setup before continuing.

## Task 2: Verify Remote Agent Network Connectivity

The remote agent appliance sits in the VMware environment and must reach both VMware and OCI services.

1. Confirm DNS resolution from the appliance network.

2. Confirm NTP access from the appliance network.

3. Confirm DHCP is available if the appliance will use DHCP.

4. Confirm the appliance external interface can reach Oracle Cloud endpoints over HTTPS.

5. Confirm the appliance can reach vCenter over HTTPS.

6. Confirm the appliance can reach vCenter and ESXi hosts over the VDDK replication ports used by your environment.

7. Confirm the migration workflow can reach OLVM over HTTPS and the OLVM replication management endpoint required by the migration workflow.

8. If your environment uses separate internal and external appliance interfaces, confirm the internal interface reaches vCenter and ESXi hosts and the external interface reaches OCI.

9. Use this connectivity checklist before you deploy the appliance:

    * DNS: TCP or UDP `53`
    * DHCP: UDP `67` and `68`, if the appliance uses DHCP
    * NTP: UDP `123`
    * OCI service endpoints and Object Storage: TCP `443`
    * vCenter: TCP `443`
    * ESXi and VDDK data path: TCP `902`
    * Remote agent appliance registration: TCP `3000`
    * OLVM engine API: TCP `443`
    * OLVM replication management, if required by your deployment: TCP `54323`

## Task 3: Create the OCM Source Environment

Create the remote connections source environment that represents the OCVS VMware environment.

1. Open **Migration & Disaster Recovery**, then **Cloud Migrations**.

2. Open **Remote Connections**.

3. Create a source environment if one does not already exist.

4. Enter a name that identifies the OCVS source.

    Example: `ocvs-source`

5. Select the `olvm-migrations` compartment.

6. Save the source environment.

7. Open the environment details page.

## Task 4: Deploy and Register the Remote Agent Appliance

Deploy the remote agent appliance OVA into the OCVS vCenter environment.

1. On the source environment details page, click **Download agent VM**.

2. Download the remote agent appliance OVA file.

3. From the jump host, sign in to the OCVS vSphere Client.

4. Deploy the OVA template.

5. Enter a unique VM name for the appliance.

    Example: `ocm-remote-agent`

6. Select the OCVS cluster and datastore.

7. Select the appliance network.

    If you use one interface, select a network that can route to both OCI and VMware management endpoints.

    If you use isolated interfaces, map the external interface to the network that reaches OCI and the internal interface to the network that reaches vCenter and ESXi hosts.

8. Configure the appliance hostname, IP address, gateway, DNS, and proxy settings as required by your environment.

9. Power on the appliance.

10. Open the appliance console and confirm it boots successfully.

11. Return to the OCI Console source environment page.

12. Register the agent by entering the appliance host name or IP address and port.

    The current OCM remote agent guidance and the planning material use port `3000` for registration.

13. Complete the registration workflow from the appliance page.

14. Confirm the agent state is **Active** and the heartbeat is healthy.

## Task 5: Add the VDDK Agent Dependency

VMware replication requires VMware Virtual Disk Development Kit libraries. OCM does not include VDDK in the appliance package, so you must add it as an agent dependency.

1. Open **Remote Connections**.

2. Open **Agent dependencies**.

3. Create an agent dependency.

4. Select the migration compartment.

5. Enter a display name.

    Example: `vddk-for-ocvs`

6. Select dependency type:

    `VDDK`

7. Upload the VDDK package to the selected Object Storage bucket, or select an existing dependency if your team already created one.

8. Wait for the dependency to finish verification.

9. Associate the dependency with the OCVS source environment or remote agent appliance as required by the console workflow.

10. Confirm the dependency is available before running discovery.

## Task 6: Create and Discover the VMware Asset Source

Create the asset source that discovers the VMware source VM.

1. Open **Migration & Disaster Recovery**, then **Cloud Migrations**.

2. Open **Discovery**.

3. Click **Create asset source**.

4. Select the VMware asset source type.

5. Enter a name.

    Example: `ocvs-vcenter-assets`

6. Select the asset source compartment.

7. Select the target compartment or inventory used to store discovered assets.

8. Select the OCVS source environment.

9. Enter the vCenter SDK endpoint for the OCVS vCenter.

    Use this format, replacing the host name with your environment value:

    ```text
    https://vcenter-fqdn:443/sdk
    ```

10. Select the vault secret that stores the vCenter credentials.

11. Select the discovery schedule.

    For the first demo build, choose no schedule and run discovery manually.

12. Create the asset source.

13. Open the asset source details page and confirm its connectors become active.

14. Run a discovery work request.

15. Wait for discovery to complete.

16. Open the discovered inventory and locate:

    `OL9u7-test`

17. Confirm that the source VM metadata is present.

## Task 7: Create and Discover the OLVM Asset Source

Create the OLVM asset source so OCM can discover the target OLVM resources.

1. Open **Discovery**.

2. Click **Create asset source**.

3. Select the OLVM asset source type.

4. Enter a name.

    Example: `olvm-target-assets`

5. Select the asset source compartment.

6. Select the target compartment or inventory used for OLVM assets.

7. Select the source environment or remote connection that has network access to OLVM.

8. Enter the OLVM endpoint.

    Use the OLVM engine HTTPS URL and port used by your environment.

9. Select the OCI Vault secret that contains the OLVM username, password, and `certificateString`.

10. If the workflow exposes source or destination environment type, select the destination environment type for the OLVM target.

11. Select no discovery schedule for the first demo build unless your team wants scheduled inventory refreshes.

12. Create the asset source.

13. Run discovery.

14. Confirm that OCM discovers the OLVM cluster, data center, storage domains, networks, VnicProfiles, and templates.

15. If discovery fails, confirm OLVM permissions, the engine CA certificate value, IAM secret access, and network connectivity from the selected remote connection to OLVM.

## Task 8: Confirm Migration Readiness

Confirm that OCM has the information required to create the migration project.

1. Confirm the VMware inventory contains `OL9u7-test`.

2. Confirm the OLVM inventory contains the target cluster.

3. Confirm the OLVM inventory contains the target VnicProfile.

4. Confirm the OLVM inventory contains the target template.

5. Confirm the OLVM inventory contains the target storage domain.

6. Confirm the replication bucket is available.

7. Confirm the remote agent appliance is active.

8. Confirm the VDDK dependency is verified.

9. Confirm the source VM has CBT enabled and has been prepared for migration.

10. Record any warnings or missing fields before continuing.

## Learn More

* [Oracle Cloud Migrations Documentation](https://docs.oracle.com/en-us/iaas/Content/cloud-migration/home.htm)
* [Getting Started with Oracle Cloud Migrations](https://docs.oracle.com/en-us/iaas/Content/cloud-migration/cloud-migration-get-started.htm)
* [Create a Migration Project for OLVM](https://docs.oracle.com/en-us/iaas/Content/cloud-migration/cloud-migration-create-migration-project-olvm.htm)

## Acknowledgements

* **Author** - Mark Atkinson, Master Principal Sales Consultant, Open Cloud Technologies
* **Workshop Draft** - Perside Foster, May 2026
* **Source Material** - OCM-DEMO-BUILD presentation, Confluence VMware-to-OLVM planning PDF, and Oracle Cloud Migrations LA PDF
