# Set Up the VMware Source Environment

## Introduction

In this lab, you store vCenter credentials, create the OCM source environment, download and deploy the remote agent appliance, verify the appliance registration endpoint, and register the agent.

Estimated Time: 45 minutes

### Objectives

In this lab, you will:

* Store the vCenter password in OCI Vault.
* Create the VMware source environment in OCM.
* Download the remote agent appliance OVA.
* Deploy and power on the appliance in vCenter.
* Verify the appliance registration endpoint on port 3000.
* Register the remote agent and confirm health.

## Task 1: Store vCenter Credentials in Vault

1. In the OCI Console, open **Identity & Security**, then open **Vault**.

2. Open the Vault created by the prerequisites stack.

3. Open **Secrets**.

4. Click **Create Secret**.

5. For **Name**, enter `vcenter-password` or your approved secret name.

6. Enter the vCenter service account password as the secret content.

7. Click **Create Secret**.

8. Confirm that the secret status is **Active**.

## Task 2: Create the Source Environment

1. Open **Migration & Disaster Recovery**, then open **Cloud Migrations**.

2. Open **Remote Connections**.

3. Open **Source Environments**.

4. Click **Create Source Environment**.

5. For **Name**, enter `vmware-source-01` or a name that identifies the source vCenter.

6. Select the migration compartment.

7. Select the VDDK agent dependency if the workflow prompts for it.

8. Click **Create**.

9. Confirm that the source environment appears with an active status.

## Task 3: Download the Remote Agent Appliance OVA

1. Open the source environment details page.

2. Click **Actions**.

3. Click **Download Agent VM**.

4. Select the most recent agent version.

5. Download the OVA file.

6. Confirm that the OVA download completes successfully.

## Task 4: Add the OVA to the vCenter Content Library

1. Sign in to vCenter.

2. Open your vCenter content library.

3. Import or upload the downloaded OVA as a new content library item.

4. Wait for the upload to complete.

5. Confirm that the OVA item status is ready.

## Task 5: Deploy the Agent Appliance

1. In vCenter, select the target cluster.

2. Start **Deploy OVF Template** from the content library item.

3. For **Name**, enter `OCM-Agent-01`.

4. Select the target cluster and datastore.

5. On the network mapping page, select the correct network layout.

    Use **Advanced** for a dual-interface deployment. Map the external adapter to the network with OCI egress over TCP 443 and map the internal adapter to the vCenter management network.

6. If your network is not isolated, use the standard single-interface deployment and map the appliance to the network that can reach both OCI and vCenter.

7. Enter static IP settings if your environment requires them.

8. Finish the deployment.

9. Power on the appliance VM.

10. Open the VM console.

11. Press **Enter** to refresh diagnostics.

12. Confirm that the appliance reports healthy and not registered.

## Task 6: Verify Agent Console Accessibility

1. Record the external IP address shown in the appliance console or in vCenter network details.

    ```text
    Agent external IP:
    ```

2. From your workstation, open a browser to the registration endpoint.

    ```text
    https://<agent-external-ip>:3000
    ```

3. Accept the certificate warning if your browser displays one.

4. If the browser reports that the client sent an HTTP request to an HTTPS server, change `http://` to `https://` in the address bar.

5. Confirm that the registration status page loads without a connection error.

## Task 7: Register the Agent

1. Return to the source environment in the OCI Console.

2. Click **Register Agent**.

3. Enter the external IP address of the agent appliance.

4. Keep the new browser tab open while the key exchange completes.

5. Enter the agent name, such as `OCM-Agent-01`.

6. Click **Register**.

7. Click **Confirm** when prompted.

8. Click **Close**.

9. Open the appliance VM console in vCenter.

10. Press **Enter** to refresh diagnostics.

11. Confirm that the status changes to healthy and registered.

12. In the OCI Console, confirm that the agent shows a recent heartbeat.

13. Confirm plugin status.

    ```text
    Agent Monitoring:
    Discovery:
    Replication:
    ```

    Replication may show a needs-attention state until asset sources are configured.

## Learn More

* [Oracle Cloud Migrations documentation](https://docs.oracle.com/en-us/iaas/Content/cloud-migration/home.htm)

## Acknowledgements

* **Author** - Mark Atkinson, Evgeny Golenkov, Andrey Sokolov, Perside Foster
* **Contributor** - Keya Balutkar
* **Last Updated By/Date** - Perside Foster, June 2026
