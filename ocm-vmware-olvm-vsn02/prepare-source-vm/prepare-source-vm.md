# Access and Prepare the VMware Source Workload

## Introduction

This lab creates the access path into the OCVS environment and builds the VMware source VM used by the OCM demo. You will deploy a Windows jump host, restrict access to it, connect to vCenter and NSX Manager, upload the Oracle Linux ISO, create the source VM, and validate outbound connectivity.

Estimated Lab Time: 2 hours

### Objectives

In this lab, you will:

* Create and secure a Windows jump host
* Connect to the jump host with Remote Desktop
* Import the vCenter trusted root certificate
* Review NSX Manager and the `workload-1` segment
* Create an ISO folder in the vSAN datastore
* Upload the Oracle Linux 9 ISO
* Create and install the `OL9u7-test` source VM
* Prepare the source VM for OCM replication
* Validate source VM network access

### Prerequisites (Optional)

This lab assumes the OCVS SDDC deployment is running or complete, the `ocm-demo` VCN exists, and you have permission to create compute instances and update security rules.

## Task 1: Create the Windows Jump Host

The source deck recommends a jump host for the initial build. Build it while OCVS is provisioning.

1. Open the OCI Console and navigate to **Compute**, then **Instances**.

2. Select the `olvm-migrations` compartment.

3. Click **Create instance**.

4. Enter the instance name:

    `windowsjump`

5. Select a Windows Server image.

    The source deck used Windows Server 2025 and notes that a Windows license fee may apply.

6. Select a shape with approximately 3 OCPUs and 48 GB RAM, or use the closest approved shape in your tenancy.

7. Configure the primary VNIC:

    VNIC name: `public`

    Subnet: public subnet in `ocm-demo`

8. Manually assign an unused private IP address in the public subnet.

    For example, the NA example VCN can use `10.10.0.3` if that address is available.

9. Set the boot volume size to 75 GB.

10. Review the instance and click **Create**.

## Task 2: Restrict Jump Host Ingress

Restrict access to the jump host before connecting to it.

1. Identify your current public IP address.

2. Open **Networking**, then **Virtual cloud networks**.

3. Open the `ocm-demo` VCN.

4. Open the security list or network security group that controls the jump host public subnet.

5. Add an ingress rule for your trusted source CIDR.

    If your public IP is static, use `<your-public-ip>/32`.

    If your ISP changes your address frequently, use the narrowest CIDR approved by your security policy.

6. Allow TCP port `3389` for Remote Desktop.

7. Enter a description that identifies the owner and purpose of the rule.

8. Save the rule.

9. If you temporarily allow broader access during setup, narrow the rule before using the demo.

## Task 3: Connect to the Jump Host

Use Remote Desktop to connect and change the initial password.

1. Open the `windowsjump` instance details page.

2. Under **Instance access**, record the public IP address, username, and initial password.

3. Open Remote Desktop Connection on your workstation.

4. Connect to the public IP address.

5. Sign in as:

    `opc`

6. Use the initial password from the OCI Console.

7. Change the password when prompted.

8. Store the new jump host password in OCI Vault.

9. If the RDP connection fails, confirm your current public IP address and the ingress rule.

## Task 4: Import the vCenter Certificate

Import the vCenter trusted root certificate into the jump host so the browser trusts the vSphere Client.

1. From the OCI Console, open the OCVS SDDC details page.

2. Copy the vSphere Client URL.

3. From the jump host, open Microsoft Edge and browse to the vSphere Client URL.

4. Accept the initial browser warning.

5. On the vCenter landing page, download the trusted root CA certificates from the administrator download area.

6. Extract the downloaded file.

7. Open the Windows Start menu, type `mmc`, and launch Microsoft Management Console.

8. Select **File**, then **Add/Remove Snap-in**.

9. Select **Certificates**, click **Add**, choose **My user account**, and click **Finish**.

10. Expand **Certificates - Current User**, then **Trusted Root Certification Authorities**, then **Certificates**.

11. Select **Action**, then **All Tasks**, then **Import**.

12. Browse to the extracted `certs/win` folder and select the certificate file.

13. Place the certificate in **Trusted Root Certification Authorities**.

14. Complete the import wizard and accept the confirmation prompt.

15. Close MMC and restart Edge.

16. Reopen the vSphere Client URL and confirm that the certificate warning is gone.

## Task 5: Review NSX Manager

Review the NSX overlay before creating the source VM.

1. From the SDDC details page, copy the NSX Manager URL and credentials.

2. From the jump host, open NSX Manager and sign in.

3. Dismiss or acknowledge any initial alerts and pop-ups.

4. Navigate to **Network Topology** and review the overlay diagram.

5. Navigate to **Tier-0 Gateway**.

6. Expand the gateway details and open the HA VIP configuration for NSX Edge Uplink 1 and Uplink 2.

7. Note that the NSX Edge uplink addresses map to the OCI VCN VLANs and provide north-south access for VM traffic.

8. Close the HA VIP window.

## Task 6: Confirm the Workload Segment DHCP Settings

Confirm that the `workload-1` segment can assign addresses to the source VM.

1. In NSX Manager, open **Connectivity**, then **Networking**, then **Segments**.

2. Locate the `workload-1` segment.

3. Open the segment edit menu.

4. Open **DHCP Config**.

5. Confirm the IPv4 gateway:

    `192.168.200.1/24`

6. Confirm the DHCP range:

    `192.168.200.2-192.168.200.254`

7. Click **Cancel**.

8. Click **Close Editing**.

9. Close NSX Manager.

## Task 7: Create the ISO Folder in vCenter

Create a datastore folder for installation media.

1. From the jump host, sign in to the vSphere Client.

2. Navigate to **Storage**.

3. Open the `ocvs-demo-vsanDatastore` datastore.

4. Open the datastore browser.

5. Click **New Folder**.

6. Enter the folder name:

    `ISO_Images`

7. Confirm that the folder appears in the datastore.

## Task 8: Upload the Oracle Linux ISO

Download and upload the Oracle Linux installer to vCenter.

1. From the jump host, open a new Edge browser tab.

2. Browse to the Oracle Linux ISO download page.

3. Download the Oracle Linux 9.7 x86_64 DVD ISO.

    The source deck used `OracleLinux-R9U7-x86_64_dvd.iso`.

4. Return to the vSphere Client.

5. Open the `ISO_Images` datastore folder.

6. Click **Upload Files**.

7. If vCenter displays a VMDK upload prompt, continue with the file upload workflow.

8. Select the Oracle Linux ISO from the jump host downloads folder.

9. Wait for the upload to complete.

## Task 9: Create the Source VM

Create the Oracle Linux VM that OCM will later migrate.

1. In the vSphere Client, navigate to **VMs**.

2. Select the SDDC cluster.

3. Click **Actions**, then **New Virtual Machine**.

4. Select **Create a new virtual machine** and click **Next**.

5. Enter the VM name:

    `OL9u7-test`

6. Select the datacenter shown by your OCVS environment.

7. Select the compute resource:

    Cluster: `ocvs-demo`

    Host: `esxi01`

8. Select the datastore:

    `ocvs-demo-vsanDatastore`

9. Accept the default ESXi compatibility.

10. Select the guest OS family:

    `Linux`

11. Select the guest OS version:

    `Oracle Linux 9`

12. Customize the hardware:

    CPU: `1`

    Memory: `16 GB`

    Hard disk: `60 GB`

13. For the network, browse and select:

    `WORKLOAD-1`

14. For **New CD/DVD Drive**, select **Datastore ISO File**.

15. Browse to `ISO_Images` and select the Oracle Linux 9.7 ISO.

16. Select **Connect at Power On** for the CD/DVD drive.

17. Review the settings and click **Finish**.

## Task 10: Install Oracle Linux

Install Oracle Linux on the source VM.

1. Select the `OL9u7-test` VM.

2. Click the green power button, or select **Actions**, then **Power**, then **Power On**.

3. Click **Launch Web Console**.

4. Click in the console and select the Oracle Linux installation media.

5. Install Oracle Linux 9.7 with a minimal installation.

6. Use the default disk partitioning unless your team requires a different layout.

7. Create an `opc` user account.

8. Store the `opc` password in OCI Vault.

9. Confirm that **Network & Hostname** shows a connected network interface and receives DHCP configuration.

10. Set the date and time for your region.

11. Begin the installation.

12. Reboot the VM when installation completes.

13. Sign in as `opc`.

## Task 11: Prepare the Source VM for OCM Migration

Prepare the VM so OCM can replicate and boot it cleanly after migration.

1. Shut down the `OL9u7-test` VM from the guest operating system.

2. In the vSphere Client, right-click the VM and open **Edit Settings**.

3. Open **VM Options**, then **Advanced**, then **Configuration Parameters**.

4. Confirm that the following parameter exists and is set to `TRUE`:

    `disk.EnableUUID`

5. If the parameter does not exist, add it and set it to `TRUE`.

6. Enable Changed Block Tracking for the VM using your team's approved vSphere procedure.

7. Power on the VM.

8. Sign in to the guest as `opc`.

9. Confirm that Linux virtio drivers are available in the initramfs.

    ```bash
    lsinitrd | grep virtio
    ```

10. If required by your kernel image, rebuild initramfs with the qemu driver set.

    ```bash
    sudo dracut -f --add qemu
    ```

11. Review `/etc/fstab`.

12. Prefer UUID or LVM names for mounted devices.

13. If a mount point references a device file such as `/dev/sdb1`, add the `nofail` option so the VM can boot if device naming changes after migration.

14. Confirm SSH is enabled and starts on boot.

    ```bash
    sudo systemctl enable --now sshd
    ```

15. Confirm the guest firewall does not block the access method you plan to use after migration.

16. Confirm the network configuration uses DHCP or a target-safe network configuration.

17. Remove or update any MAC-address-specific network rules.

18. Remove or disable cloud agents that are not required after migration.

19. Install Oracle Cloud Agent or OS Management only if your post-migration target design requires them.

## Task 12: Validate Source VM Network Access

Validate the source VM before using it in OCM.

1. Sign in to the guest as `opc`.

2. Confirm DNS resolution by testing a public hostname.

3. If outbound internet access is not working, open OCI Networking and add a route rule on the NSX Edge Uplink route table:

    Target type: `NAT Gateway`

    Destination CIDR block: `0.0.0.0/0`

    Target NAT gateway: NAT gateway for `ocm-demo`

4. Repeat the route rule review for NSX Edge Uplink 2 if required.

5. Install a small package to confirm repository access:

    ```bash
    sudo dnf install -y zsh
    ```

## Learn More

* [Oracle Cloud VMware Solution Documentation](https://docs.oracle.com/en-us/iaas/Content/VMware/home.htm)
* [Oracle Linux ISO Download Page](https://yum.oracle.com/oracle-linux-isos.html)
* [Oracle Cloud Migrations Documentation](https://docs.oracle.com/en-us/iaas/Content/cloud-migration/home.htm)

## Acknowledgements

* **Author** - Mark Atkinson, Master Principal Sales Consultant, Open Cloud Technologies
* **Workshop Draft** - Perside Foster, May 2026
* **Source Material** - OCM-DEMO-BUILD presentation, Confluence VMware-to-OLVM planning PDF, and Oracle Cloud Migrations LA PDF
