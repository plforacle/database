# Deploy the OCVS Source Environment

## Introduction

This lab deploys the OCVS source environment for the OCM demo. You will create a single-host SDDC, configure the OCVS networking, monitor provisioning, and connect the SDDC workload network to OCI VCN resources.

Estimated Lab Time: 4 hours

### Objectives

In this lab, you will:

* Create a single-host OCVS SDDC
* Configure the management cluster
* Configure the OCVS cluster CIDR and workload CIDR
* Monitor the SDDC until it becomes active
* Record vCenter and NSX Manager access information
* Configure connectivity from the SDDC NSX workload network to the VCN

### Prerequisites (Optional)

This lab assumes you have completed the OCI prerequisite setup, selected a non-overlapping network plan, and have access to a tenancy enabled for OCVS.

## Task 1: Start the OCVS SDDC Workflow

Create the OCVS SDDC in the workshop compartment.

1. Open the navigation menu and select **Hybrid & Multicloud**, then **Software-Defined Data Centers**.

2. Confirm that you are working in the `olvm-migrations` compartment.

3. Click **Create SDDC**.

4. Enter the SDDC name:

    `ocvs`

5. Select the SDDC compartment:

    `olvm-migrations`

6. Accept the default VMware software version unless your team has a specific version requirement.

    The source deck used VMware 8.0 Update 3.

7. Create or select the SSH public key for the SDDC deployment.

8. Store the SSH public key and private key in OCI Vault.

9. In the HCX section, select **Don't enable HCX**.

10. Continue to the cluster section.

## Task 2: Configure the Management Cluster

Create the single-host management cluster used for the demo.

1. Click **Add Management Cluster**.

2. Enter the cluster name:

    `ocvs-demo`

3. Change the cluster hardware shape to:

    `BM.DenseIO2.52`

4. Select the host type:

    `Single Host SDDC`

5. Enter the ESXi host prefix:

    `esxi01`

6. Select **On-Demand Capacity**.

7. Select **Hourly** for the pricing interval commitment if that is approved for your tenancy.

8. If the console displays VMware license portability or BYOL options, follow your team's current licensing guidance before continuing.

9. Confirm the required pricing acknowledgement checkbox.

10. Click **Next**.

## Task 3: Configure OCVS Networking

Configure the SDDC cluster networking. These values must not overlap with the VCN, NSX workload segment, or OLVM target networks.

1. Select the VCN:

    `ocm-demo`

2. Select **Create New Subnet and VLANs**.

3. Enter the SDDC cluster network CIDR for your region.

    Use the reserved `10.x.3.0/24` range from your network plan.

4. Accept the default provisioning subnet and VLAN sizing unless your network plan requires a different layout.

5. Enter the cluster workload network CIDR:

    `192.168.200.0/24`

6. Confirm that `192.168.200.0/24` does not overlap with the OCI VCN, the OCVS cluster CIDR, or OLVM.

7. Leave datastore cluster settings unchanged unless your team has a different storage design.

8. Configure notifications if required.

9. Click **Next** to review the deployment.

## Task 4: Review and Create the SDDC

Review carefully before submitting the deployment.

1. Confirm that the compartment, VCN, cluster CIDR, workload CIDR, host shape, and host type are correct.

2. Correct any overlapping CIDR ranges before proceeding.

3. Click **Submit**.

4. Click **Create SDDC**.

5. Monitor the deployment status.

    The source deck estimates 2.5 to 3 hours for OCVS provisioning.

6. While the SDDC provisions, continue to the next lab and build the jump host.

## Task 5: Record the Active SDDC Details

When provisioning completes, record the access details needed later.

1. Return to **Hybrid & Multicloud**, then **Software-Defined Data Centers**.

2. Confirm that the SDDC state is **Active**.

3. Open the SDDC details page.

4. Record the vSphere Client URL.

5. Record the NSX Manager URL.

6. Record the initial usernames and passwords.

7. Store the credentials and URLs in OCI Vault.

## Task 6: Connect the SDDC Workload Network to the VCN

Use the OCVS network workflow to connect NSX-T workload traffic to OCI VCN resources. This is required so the OCM appliance and migrated workload path can communicate with OCI services and supporting resources.

1. Open the SDDC details page.

2. Open **vSphere Clusters**.

3. Select the `ocvs-demo` cluster.

4. Open **Cluster Networks**.

5. Under **Quick Actions**, click **Configure Connectivity to VCN Resources**.

6. Start the workflow.

7. Enter the cluster workload CIDR:

    `192.168.200.0/24`

8. Add the subnet selected by your OCVS workflow.

9. Review the ingress rules, egress rules, and selected subnets.

10. Click **Apply Configuration**.

11. Click **Close** when the workflow completes.

## Learn More

* [Oracle Cloud VMware Solution Documentation](https://docs.oracle.com/en-us/iaas/Content/VMware/home.htm)
* [Set Up an SDDC](https://docs.oracle.com/en-us/iaas/Content/VMware/Tasks/ocvssettingupsddc.htm)

## Acknowledgements

* **Author** - Mark Atkinson, Master Principal Sales Consultant, Open Cloud Technologies
* **Workshop Draft** - Perside Foster, May 2026
* **Source Material** - OCM-DEMO-BUILD presentation, January 2026
