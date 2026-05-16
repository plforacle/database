# Deploy OLVM Engine

## Introduction

In this lab, you will connect to the manager host created in Lab 1 using SSH, install the required OLVM packages, run `engine-setup`, and validate access to the Administration Portal directly from your local browser.

**Estimated Lab Time:** 40-60 minutes, including package download and engine setup time.

### Video Walkthrough

[](video:https://objectstorage.us-ashburn-1.oraclecloud.com/n/idhwewbjlvpy/b/olvm-train-oci/o/videos/olvm-on-oci-lab2-no-presenter.mp4)

### Objectives

In this lab, you will:

- Connect to the OLVM manager using SSH from your local terminal
- Install the required OLVM repositories and engine packages
- Run `engine-setup` and record the `admin@ovirt` password
- Log in to the Administration Portal from your local browser and verify the deployment

### Prerequisites

This lab assumes you have:

- Completed Lab 1 and the Lab 1 checkpoint
- Recorded the public IP address for `olvm`
- Copied `olvm-cluster-id_rsa` to your local machine
- A local PowerShell terminal available
- A local browser (Chrome, Firefox, or Edge)

> **Important:** The Administration Portal is accessed directly from your local browser over HTTPS. No VNC client or SSH tunnel is required.

### Connection Reference

Use these connection paths throughout this and later labs:

- **Local machine -> OLVM manager shell:** `ssh -i C:\Users\<you>\.ssh\olvm-cluster-id_rsa oracle@<olvm-public-ip>`
- **Manager shell -> KVM hosts:** `ssh olkvm01` or `ssh olkvm02`
- **Manager shell -> application VMs in later labs:** `ssh opc@10.0.10.100` or `ssh opc@10.0.10.101`
- **Administration Portal (local browser):** `https://<olvm-fqdn>/ovirt-engine`

## Task 1: Connect to the Manager via SSH

1. From your local Windows machine, open a **PowerShell** window.

2. Connect to the OLVM manager:

    ```powershell
    <copy>ssh -i C:\Users\<you>\.ssh\olvm-cluster-id_rsa oracle@<olvm-public-ip></copy>
    ```

3. Verify you are on the correct host:

    ```bash
    <copy>hostname -f</copy>
    ```

    Record the FQDN output — you will need it in Task 3 to access the Administration Portal.

## Task 2: Install the Engine

1. Install the Oracle Linux Virtualization Manager release package:

    ```bash
    <copy>sudo dnf install -y oracle-ovirt-release-45-el8</copy>
    ```

2. Install the UEK extra kernel modules package:

    ```bash
    <copy>sudo dnf install -y kernel-uek-modules-extra</copy>
    ```

3. Reboot the system:

    ```bash
    <copy>sudo reboot</copy>
    ```

    **Important:** Your SSH session will disconnect during the reboot. Wait 2-3 minutes for the system to come back online, then reconnect before continuing.

4. Reconnect to the manager after reboot:

    ```powershell
    <copy>ssh -i C:\Users\<you>\.ssh\olvm-cluster-id_rsa oracle@<olvm-public-ip></copy>
    ```

5. Clear the DNF cache:

    ```bash
    <copy>sudo dnf clean all</copy>
    ```

6. List the configured repositories and verify that the required repositories are enabled:

    ```bash
    <copy>sudo dnf repolist</copy>
    ```

    Required repositories:

    - `ol8_baseos_latest`
    - `ol8_addons`
    - `ol8_appstream`
    - `ol8_kvm_appstream`
    - `ovirt-4.5`
    - `ovirt-4.5-extra`
    - `ol8_gluster_appstream`
    - `ol8_UEKR7`

7. If any required repository is disabled, enable it:

    ```bash
    <copy>sudo dnf config-manager --enable <repository_name></copy>
    ```

8. Install the OLVM engine package:

    ```bash
    <copy>sudo dnf install -y ovirt-engine</copy>
    ```

    This package install can take 10-15 minutes depending on repository speed.

9. Run the OLVM engine setup:

    ```bash
    <copy>sudo engine-setup --accept-defaults</copy>
    ```

    **Expected runtime:** 5-10 minutes.

    `engine-setup` still prompts you to set the `admin@ovirt` password. Use a strong password that includes uppercase, lowercase, a number, and a special character.

    > **Critical:** Write down the `admin@ovirt` password before you continue.

    If `engine-setup` runs longer than 15 minutes but continues printing progress, let it finish. If it stops with an error, capture the message and contact the instructor or workshop owner before changing the configuration manually.

10. Open port 443 on the OS firewall to allow browser access to the Administration Portal:

    ```bash
    <copy>sudo firewall-cmd --zone=public --permanent --add-service=https
    sudo firewall-cmd --reload</copy>
    ```

11. Verify the rule is active:

    ```bash
    <copy>sudo firewall-cmd --list-services</copy>
    ```

    The output should include `https`.

## Task 3: Update Your Local Hosts File

The OLVM Administration Portal must be accessed using the engine's fully qualified domain name (FQDN). Because that FQDN is an internal OCI DNS name, your local browser cannot resolve it. Adding a single entry to your local hosts file maps the FQDN to the public IP of the `olvm` instance, which lets your browser reach the portal directly.

1. From your SSH session on the `olvm` instance, get the engine FQDN:

    ```bash
    <copy>hostname -f</copy>
    ```

    Record the FQDN — for example, `olvm.pub.olv.oraclevcn.com`.

2. On your local machine, edit the hosts file using the instructions for your operating system:

    **Windows:**
    - Type `cmd` in the Start menu, right-click **Command Prompt**, and select **Run as administrator**
    - Run: `notepad C:\Windows\System32\drivers\etc\hosts`

    **macOS:**    
    ```bash
    <copy>sudo nano /etc/hosts</copy>
    ```

    **Linux:**
    ```bash
    <copy>sudo nano /etc/hosts</copy>
    ```

3. Add a line at the bottom of the file that maps the public IP of the `olvm` instance to the engine FQDN:

    ```
    <olvm-public-ip>   <olvm-fqdn>
    ```

    Example:

    ```
    141.148.13.243   olvm.pub.olv.oraclevcn.com
    ```

4. Save the file and close the editor.

## Task 4: Log in to the Administration Portal

1. Open your local browser. **Firefox:** is a lot easier to use(Chrome, Firefox, or Edge).

2. Navigate to the Administration Portal using the engine FQDN:

    ```
    <copy>https://<olvm-fqdn>/ovirt-engine</copy>
    ```

    For example: `https://olvm.pub.olv.oraclevcn.com/ovirt-engine`

    ![The OLVM welcome page showing the Engine CA Certificate and Administration Portal links.](images/olvm-welcome.png)

3. Your browser displays a certificate warning because the lab uses a self-signed certificate. Click **Advanced -> Accept the Risk and Continue** (Firefox) or **Advanced -> Proceed** (Chrome/Edge).

4. On the landing page, click **Engine CA Certificate** to download it.

5. Import the certificate into your browser:

    **Firefox:**
    - Open browser menu -> **Settings**
    - Search for **cert**
    - Click **View Certificates... -> Import**
    - Select the downloaded certificate file
    - Check **Trust this CA to identify websites**
    - Click **OK**

    **Chrome / Edge:**
    - Open **Settings -> Privacy and Security -> Security -> Manage certificates**
    - Click **Import** and follow the wizard
    - Select the downloaded certificate file
    - Place it in the **Trusted Root Certification Authorities** store

6. Return to `https://<olvm-fqdn>/ovirt-engine` and click **Administration Portal**.

7. Sign in with:

    - Username: `admin@ovirt`
    - Password: the password you created during `engine-setup`

    The Administration Portal should open successfully. If the page is still starting, wait 1-2 minutes and refresh once.

    ![The OLVM Administration Portal dashboard after successful login.](images/olvm-admin-portal.png)

### Deploy OLVM Engine Checkpoint

At this point, you should have:

- SSH access to the OLVM manager from your local machine
- OLVM engine installed and configured
- The Administration Portal accessible from your local browser
- The `admin@ovirt` password recorded

Keep your SSH session and browser open for Labs 3-5.

## Learn More

- Oracle Linux Virtualization Manager install lab (official): https://docs.oracle.com/en/learn/olvm-install/index.html

## Acknowledgements

- **Author** - Shawn Kelley, John Priest
- **Contributors** - Perside Foster
- **Last Updated By/Date** - Perside Foster, May 6, 2026
