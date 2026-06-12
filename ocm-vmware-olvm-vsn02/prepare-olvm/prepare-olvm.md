# Prepare the OLVM Target

## Introduction

This lab prepares the OLVM target side of the demo. The source deck says to deploy OLVM with authentication, authorization, and accounting (AAA), but it does not include the full OLVM build procedure. Use this lab to select the approved OLVM build method, confirm that the target is ready, and collect the exact target values required by OCM.

Estimated Lab Time: 45 minutes after OLVM is deployed

### Objectives

In this lab, you will:

* Select the approved OLVM build method for the demo
* Confirm OLVM engine access
* Verify hosts, storage, networking, and templates
* Confirm AAA readiness for demo users
* Prepare the OLVM API credential and certificate for OCM
* Record OLVM target values for OCM

### Prerequisites (Optional)

This lab assumes you have access to an OLVM deployment method approved by your team. This can be a completed manual OLVM install, an internal automation stack, or a prebuilt OLVM demo environment.

## Task 1: Select the OLVM Build Method

Choose the OLVM target build method before configuring OCM.

1. Confirm whether your team will use an existing OLVM environment or build a new one.

2. If you use the manual build path, complete the OLVM manual install workshop or your team's equivalent procedure.

3. If you use an automation stack, confirm that it creates the required OLVM components.

    The local source materials include an OpenTofu-based OLVM stack that creates one OLVM engine, two KVM hosts, an NFS storage domain, a guest VLAN, OL8 and OL9 templates, and a DHCP server VM.

4. Confirm that the OLVM network plan does not overlap with the OCVS VCN, the OCVS cluster CIDR, or the NSX workload CIDR.

5. Store all generated OLVM credentials and keys in OCI Vault.

## Task 2: Confirm OLVM Engine Access

Verify that you can administer OLVM from the selected access path.

1. Connect to the jump host or access method approved for your environment.

2. Open the OLVM engine URL.

3. If the browser reports a certificate warning, install the OLVM engine CA certificate according to your team's browser procedure.

4. Sign in to the OLVM Administration Portal.

5. Confirm that the dashboard loads without critical errors.

6. Record the OLVM engine URL and credential vault secret name.

## Task 3: Confirm Hosts and Storage

OCM must be able to place the migrated VM on an OLVM cluster with usable hosts and storage.

1. In the OLVM Administration Portal, open **Compute**, then **Hosts**.

2. Confirm that the KVM hosts are up.

3. Open **Storage**, then **Domains**.

4. Confirm that the target data domain is active.

5. Confirm that the storage domain has enough free capacity for the migrated VM disks.

6. Record the cluster name and storage domain name.

## Task 4: Confirm Target Networking

Confirm the target network that the migrated VM will use.

1. In OLVM, open the network configuration for the target cluster.

2. Confirm that the logical network or VLAN for migrated VMs exists.

3. Confirm that the target VnicProfile exists.

4. Confirm that DHCP or a static IP plan is available for the migrated VM after cutover.

5. Record the target network and VnicProfile names.

## Task 5: Confirm Templates and AAA

The OCM OLVM workflow requires OLVM API access that can discover target resources and perform replication-related actions.

1. Open the templates view in OLVM.

2. Confirm that the required OLVM template is available.

3. Confirm that the OLVM administrator account can create and manage VMs in the target cluster.

4. If your demo uses AAA integration, confirm that the demo user or group can sign in and has the required role assignments.

5. Record the template name and the account or group used for OCM access.

6. Confirm that the OCM user can list the OLVM resources needed for discovery:

    Clusters, data centers, storage domains, attached storage domains, networks, VnicProfiles, and templates.

7. Confirm that the OCM user has the roles needed for replication actions.

    The planning material identifies `VmCreator` and `DiskOperator` as the minimum roles for disk, image transfer, and disk attachment operations.

8. If you do not want to use `admin@internal`, create a dedicated migration user and grant only the roles required by your team's security standard.

## Task 6: Prepare the OLVM Credential Secret

OCM needs OLVM credentials and the OLVM engine CA certificate. Store these values in OCI Vault.

1. Open the OLVM engine login page.

2. Download the **Engine CA Certificate** from the OLVM login page.

3. Open the downloaded certificate file and copy the full certificate text, including the begin and end certificate lines.

4. Create or update the OCI Vault secret used by OCM for OLVM access.

5. Use this JSON structure for the secret value, replacing the example values with your environment values:

    ```json
    {
      "username": "admin@internal",
      "password": "olvm-password-value",
      "certificateString": "-----BEGIN CERTIFICATE-----\ncertificate-body\n-----END CERTIFICATE-----"
    }
    ```

6. Confirm that the secret is stored in the migration secrets compartment or in the approved demo vault location.

7. Confirm that OCM IAM policies allow the discovery and replication components to read this secret.

8. Record the vault name, key name, secret name, and secret compartment in your worksheet.

## Task 7: Prepare the OCM Target Worksheet

Before configuring OCM, capture the target values in one place.

1. Record the OLVM engine URL.

2. Record the OLVM credential vault secret name.

3. Record the OLVM cluster.

4. Record the target VnicProfile.

5. Record the target template.

6. Record the target storage domain.

7. Record the compartment where OCM should create or track migration resources.

## Learn More

* [Oracle Linux Virtualization Manager Documentation](https://docs.oracle.com/en/virtualization/oracle-linux-virtualization-manager/)
* [Oracle Linux KVM Documentation](https://docs.oracle.com/en/operating-systems/oracle-linux/kvm/)
* [Oracle Cloud Migrations Documentation](https://docs.oracle.com/en-us/iaas/Content/cloud-migration/home.htm)

## Acknowledgements

* **Author** - Mark Atkinson, Master Principal Sales Consultant, Open Cloud Technologies
* **Workshop Draft** - Perside Foster, May 2026
* **Source Material** - OCM-DEMO-BUILD presentation, local OLVM automation notes, Confluence VMware-to-OLVM planning PDF, and Oracle Cloud Migrations LA PDF
