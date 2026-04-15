# Access and Prepare the VMware Source Workload

## Introduction

This lab walks you through accessing the Oracle Cloud VMware Solution (OCVS) environment and preparing the VMware source virtual machine used in the migration scenario. You will connect to the management interfaces, review the workload networking, upload the Oracle Linux installation media, and create the source virtual machine in vCenter.

Estimated Lab Time: 45 minutes

### Objectives

In this lab, you will:
* Access the OCVS management environment
* Connect to vCenter and NSX Manager
* Review the workload network used by the source virtual machine
* Upload the Oracle Linux ISO image to the vSAN datastore
* Create and validate the VMware source virtual machine

### Prerequisites (Optional)

This lab assumes you have:
* Completed the OCVS deployment
* Access to the OCVS management endpoints
* Access to the jump host or bastion method used for the workshop
* The Oracle Linux ISO image available for upload, or access to download it during the lab

## Task 1: Access the OCVS Management Environment

In this task, you will connect to the management access path for the OCVS environment.

1. Open the OCI Console and navigate to the compute instance used as the jump host for the workshop.

2. Review the instance access information and identify the public IP address and login details.

3. Connect to the jump host using the access method defined for your environment.

4. Confirm that you can open a browser session from the jump host.

5. Return to the OCVS SDDC details page and locate the URLs and credentials for **vCenter** and **NSX Manager**.

## Task 2: Trust the vCenter Certificate

In this task, you will prepare the jump host browser to trust the vCenter certificate.

1. From the jump host browser, open the **vCenter** URL.

2. Accept the initial browser security warning and open the landing page.

3. Download the trusted root certificate package provided on the page.

4. Extract the certificate package.

5. Import the Windows trusted root certificate on the jump host.

6. Restart the browser and reconnect to the vCenter URL.

7. Confirm that the certificate warning is no longer displayed.

## Task 3: Review the NSX Workload Network

In this task, you will review the NSX networking used by the source VM.

1. Open the **NSX Manager** URL from the jump host and sign in.

2. Review the NSX topology and gateway information for the OCVS environment.

3. Navigate to the **Segments** view.

4. Locate the **workload-1** segment used for virtual machines.

5. Confirm that the segment is present and review the DHCP configuration associated with it.

6. Verify that the workload network is ready to host the VMware source virtual machine.

## Task 4: Create a Datastore Folder for ISO Images

In this task, you will prepare the vSAN datastore for installation media.

1. Sign in to **vCenter**.

2. Navigate to **Storage**.

3. Open the vSAN datastore associated with the OCVS cluster.

4. Create a new folder named **ISO_Images**.

5. Confirm that the folder appears in the datastore browser.

## Task 5: Upload the Oracle Linux ISO Image

In this task, you will download and upload the Oracle Linux ISO image used to build the source VM.

1. From the jump host browser, open the Oracle Linux ISO download page.

2. Download the Oracle Linux ISO image used for the workshop.

3. Return to **vCenter** and open the **ISO_Images** folder in the datastore.

4. Upload the ISO image to the folder.

5. Wait for the upload to complete.

## Task 6: Create the VMware Source Virtual Machine

In this task, you will create the VMware virtual machine that will later be migrated.

1. In **vCenter**, navigate to the **VMs** view.

2. Start the **New Virtual Machine** workflow.

3. Enter the virtual machine name used for the workshop.

4. Select the OCVS cluster and ESXi host.

5. Select the vSAN datastore.

6. Accept the default compatibility setting.

7. Select **Oracle Linux 9** as the guest operating system.

8. Configure the virtual machine hardware based on the workshop design.

9. Connect the virtual machine to the **workload-1** network segment.

10. Attach the Oracle Linux ISO image from the datastore.

11. Confirm that the CD/DVD device is connected at power on.

12. Finish the virtual machine creation workflow.

## Task 7: Install and Validate the Source Virtual Machine

In this task, you will install Oracle Linux on the source VM and verify that it is ready for migration.

1. Power on the virtual machine.

2. Launch the web console from **vCenter**.

3. Start the Oracle Linux installation.

4. Complete the basic operating system setup using the workshop configuration.

5. Reboot the virtual machine when the installation completes.

6. Sign in to the virtual machine and verify that it is operational.

7. Confirm that the source VM is connected to the expected network and is ready for use in the migration workflow.

You have now prepared the VMware source workload in OCVS and are ready to continue with the target environment in OLVM.

## Learn More

* [Oracle Cloud VMware Solution Documentation](https://docs.oracle.com/en-us/iaas/Content/VMware/home.htm)
* [Oracle Linux ISO Download Page](https://yum.oracle.com/oracle-linux-isos.html)

## Acknowledgements
* **Author** - Mark Atkinson, Master Principal Sales Consultant , Linux
* **Contributors** - ???, Group
* **Last Updated By/Date** - Perside Foster, Aril 2026