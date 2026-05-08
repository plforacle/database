# Deploy OLVM Engine

## Introduction

In this lab, you will use the manager host created in Lab 1, access its desktop through an SSH tunnel, install the required OLVM packages, run `engine-setup`, and validate access to the Administration Portal.

**Estimated Lab Time:** 60-90 minutes, including package download and engine setup time.

### Objectives

In this lab, you will:

- Open a local TigerVNC session to the OLVM manager through an SSH tunnel
- Install the required OLVM repositories and engine packages
- Run `engine-setup` and record the `admin@ovirt` password
- Log in to the Administration Portal and verify the deployment

### Prerequisites

This lab assumes you have:

- Completed Lab 1 and the Lab 1 checkpoint
- Recorded the public IP address for `olvm`
- Copied `olvm-cluster-id_rsa` to your local machine
- TigerVNC Viewer installed on your local machine
- A local PowerShell terminal available

> **Important:** In this workshop, the "manager desktop" means your local TigerVNC client connected to `localhost:5914` through SSH tunneling.
>
> **Important:** If your endpoint policy blocks TigerVNC, stop and contact the instructor or workshop owner. Do **not** expose port `5901` publicly in OCI.

### Connection Reference

Use these connection paths throughout this and later labs:

- **Local machine -> manager desktop:** `ssh -N -L 5914:localhost:5901 -i C:\Users\<you>\.ssh\olvm-cluster-id_rsa oracle@<olvm-public-ip>`
- **Manager shell -> KVM hosts:** `ssh olkvm01` or `ssh olkvm02`
- **Manager shell -> application VMs in later labs:** `ssh opc@10.0.10.100` or `ssh opc@10.0.10.101`

## Task 1: Open a VNC Session to the Manager

1. From your local Windows machine, open a new PowerShell window.

2. If you did not keep the Lab 1 tunnel open, start it now:

    ```powershell
    <copy>ssh -N -L 5914:localhost:5901 -i C:\Users\<you>\.ssh\olvm-cluster-id_rsa oracle@<olvm-public-ip></copy>
    ```

3. Leave this PowerShell window open. It forwards local port `5914` to the manager's local VNC port `5901`.

4. Start **TigerVNC Viewer** on your local machine.

5. In the **VNC Server** field, enter `localhost:5914`, then click **Connect**.

    ```
    <copy>localhost:5914</copy>
    ```

6. When prompted, enter the `oracle` user's VNC password:

    ```
    <copy>oracle</copy>
    ```

7. The manager desktop appears. If first-login screens are shown, click through them and then close or minimize the welcome window.

## Task 2: Install the Engine

1. Open the **Terminal** application inside the manager desktop.

2. Enable clipboard copy and paste inside the VNC session:

    ```bash
    <copy>vncconfig -nowin &</copy>
    ```

3. Install the Oracle Linux Virtualization Manager release package:

    ```bash
    <copy>sudo dnf install -y oracle-ovirt-release-45-el8</copy>
    ```
4. Install the UEK extra kernel modules package.

    ```bash
    <copy>sudo dnf install -y kernel-uek-modules-extra</copy>
    ```
5. Reboot the system.

    ```bash
    <copy>sudo reboot</copy>
    ```
    **Important:** Your SSH or VNC session will disconnect during the reboot. Wait for the system to come back online, then reconnect and continue with the next step.

6. After the engine reboots, close the old SSH tunnel terminal or open a new terminal.

7. Recreate the SSH tunnel to the OLVM engine from your local Windows machine.

    ```powershell
    <copy>ssh -N -L 5914:localhost:5901 -i C:\Users\<you>\.ssh\olvm-cluster-id_rsa oracle@<olvm-public-ip></copy>
    ```

8. Reopen TigerVNC Viewer and connect to host and enter password:

    ```text
    <copy>localhost:5914</copy>
    ```

    **Important:** The previous SSH tunnel disconnects during the reboot. If you receive an error that the local port is already in use, close the old terminal window and try the SSH tunnel command again.


9. Clear the DNF cache:

    ```bash
    <copy>sudo dnf clean all</copy>
    ```

10. List the configured repositories and verify that the required repositories are enabled:

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

11. If any required repository is disabled, enable it:

    ```bash
    <copy>sudo dnf config-manager --enable <repository_name></copy>
    ```



12. Install the OLVM engine package:

    ```bash
    <copy>sudo dnf install -y ovirt-engine</copy>
    ```

    This package install can take 10-15 minutes depending on repository speed.

13. Run the OLVM engine setup:

    ```bash
    <copy>sudo engine-setup --accept-defaults</copy>
    ```

    **Expected runtime:** 5-10 minutes.

    `engine-setup` still prompts you to set the `admin@ovirt` password. Use a strong password that includes uppercase, lowercase, a number, and a special character.

    > **Critical:** Write down the `admin@ovirt` password before you continue.

    If `engine-setup` runs longer than 15 minutes but continues printing progress, let it finish. If it stops with an error, capture the message and contact the instructor or workshop owner before changing the configuration manually.

## Task 3: Log in to the Administration Portal

1. Get the fully qualified domain name of the manager host:

    ```bash
    <copy>hostname -f</copy>
    ```

2. Open **Firefox** inside the manager desktop.

3. Browse to the manager URL:

    ```
    <copy>https://<output-from-hostname-f></copy>
    ```

    If `engine-setup` prints a more specific portal URL at the end of the run, use that exact URL instead.

4. Firefox displays a certificate warning because the lab uses a self-signed certificate. Click **Advanced -> Accept the Risk and Continue**.

5. On the landing page, click **Engine CA Certificate**.

    ![](images/olvm-welcome.png)

6. Import the certificate into Firefox:

    - Open the browser menu -> **Settings**
    - Search for **cert**
    - Click **View Certificates...**
    - Click **Import...**
    - Select the downloaded certificate file
    - Check **Trust this CA to identify websites**
    - Click **OK**

7. Return to the engine landing page and click **Administration Portal**.

8. Sign in with:

    - Username: `admin@ovirt`
    - Password: the password you created during `engine-setup`

    The Administration Portal should open successfully. If the page is still starting, wait 1-2 minutes and refresh once.

### Deploy OLVM Engine Checkpoint

At this point, you should have:

- A working SSH tunnel from your local machine to the manager desktop
- OLVM engine installed and configured
- The Administration Portal open and accessible
- The `admin@ovirt` password recorded

Keep the SSH tunnel and manager desktop available for Labs 3-5.

## Learn More

- Oracle Linux Virtualization Manager install lab (official): https://docs.oracle.com/en/learn/olvm-install/index.html

## Acknowledgements

- **Author** - Shawn Kelley, John Priest
- **Contributors** - Perside Foster
- **Last Updated By/Date** - Perside Foster, May 6, 2026
