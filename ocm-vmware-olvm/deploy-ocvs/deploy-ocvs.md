# Deploy the OCVS Source Environment

## Introduction

This lab walks you through the deployment of the Oracle Cloud VMware Solution (OCVS) source environment used in this workshop. You will create a single-host Software-Defined Data Center (SDDC), configure the required networking, and review the deployed environment in OCI.

Estimated Lab Time: 30 minutes

### Objectives

In this lab, you will:
* Create a single-host OCVS SDDC
* Configure the OCVS cluster networking in OCI
* Review the deployed SDDC details
* Identify the management endpoints for vCenter and NSX Manager

### Prerequisites (Optional)

This lab assumes you have:
* Completed the OCI prerequisite setup for the workshop
* Access to an OCI tenancy enabled for OCVS
* A non-overlapping network plan for the OCVS deployment

## Task 1: Start the OCVS SDDC Deployment

In this task, you will begin the OCVS deployment in the OCI Console.

1. Open the navigation menu and select **Hybrid & Multicloud**, then select **Software-Defined Data Centers**.

2. Confirm that you are working in the correct compartment.

3. Click **Create SDDC**.

4. Enter the SDDC name for the workshop environment.

5. Accept the default VMware software version unless your workshop environment requires a different version.

6. Provide the SSH public key required for the deployment.

7. In the HCX section, select **Don't enable HCX**.

8. Add a management cluster and configure it as a **single-host SDDC**.

## Task 2: Configure the Management Cluster

In this task, you will configure the OCVS management cluster.

1. Enter the cluster name.

2. Select the hardware shape used for the workshop environment.

3. Set the host type to **Single Host SDDC**.

4. Enter the ESXi host name prefix.

5. Select the pricing interval required for your environment.

6. Review the cluster settings and continue.

## Task 3: Configure OCVS Networking

In this task, you will configure the networking used by the OCVS deployment.

1. Select the VCN created earlier for the workshop.

2. Choose the option to create a new subnet and VLANs for the SDDC cluster.

3. Enter the cluster CIDR for the OCVS subnet.

4. Accept the default provisioning subnet and VLAN sizing unless your environment requires a different configuration.

5. Enter the cluster workload network CIDR that will be used for the VMware workload segment.

6. Confirm that the selected network ranges do not overlap with other workshop networks.

7. Review the networking configuration and continue.

## Task 4: Review and Create the SDDC

In this task, you will submit the deployment.

1. Review the full OCVS configuration.

2. Confirm that the VCN, cluster CIDR, and workload CIDR are correct.

3. Verify that there are no overlapping IP ranges between OCI, OCVS, and any related workshop environments.

4. Click **Create SDDC**.

5. Monitor the deployment status in the OCI Console until the SDDC becomes active.

## Task 5: Review the Deployed SDDC

In this task, you will review the deployed OCVS environment.

1. Open the SDDC details page.

2. Confirm that the SDDC state is **Active**.

3. Review the management information provided on the page.

4. Identify the URLs, IP addresses, usernames, and passwords for:
   - **vCenter**
   - **NSX Manager**

5. Record these details for use in the next lab.

You have now deployed the OCVS source environment and reviewed the management endpoints needed for the workshop.

## Learn More

* [Oracle Cloud VMware Solution Documentation](https://docs.oracle.com/en-us/iaas/Content/VMware/home.htm)
* [Set Up an SDDC Documentation](https://docs.oracle.com/en-us/iaas/Content/VMware/Tasks/ocvssettingupsddc.htm)

## Acknowledgements
* **Author** - Mark Atkinson, Master Principal Sales Consultant , Linux
* **Contributors** - ???, Group
* **Last Updated By/Date** - Perside Foster, Aril 2026