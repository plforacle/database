# Deploy Multi Tier Application

## Introduction

In this optional lab, you will import two prebuilt OVAs: one for a MySQL database VM and one for a Java web application VM. The lab demonstrates how OLVM can package complete workloads and redeploy them across the cluster with minimal manual configuration.

Estimated Time: 15-30 minutes, including OVA download, import, and application startup time.

### Video Walkthrough

This walkthrough video is silent and does not include audio narration.

[](video:https://objectstorage.us-ashburn-1.oraclecloud.com/n/idhwewbjlvpy/b/olvm-on-oci/o/videos%2Fvideos_olvm-on-oci-lab6-no-presenter.mp4)

### Objectives

In this lab, you will:

- Import and start the `ol9-mysql` database VM
- Import and start the `ol9-webapp` application VM
- Verify connectivity across `l2-vm-network`
- Validate the Employee Directory application in the browser

### Prerequisites

This lab assumes you have:

- Completed the Lab 4 checkpoint
- A working `l2-vm-network`
- An active shared storage domain
- Access to the Administration Portal from your local browser

> **Important:** Complete the database VM import and validation before you start the web application VM import. Do not run both imports in parallel during the workshop.

## Task 1: Download the `ol9-mysql` OVA

1. From the manager terminal, download `ol9-mysql.ova` to `olkvm01`:

    ```bash
    <copy>ssh olkvm01 "curl -L https://objectstorage.us-ashburn-1.oraclecloud.com/n/idhwewbjlvpy/b/olvm-on-oci/o/ova-e4-shape%2Fol9-mysql.ova -o /tmp/ol9-mysql.ova"</copy>
    ```

    **Expected time:** 10-20 minutes. Wait for the `curl` command to complete before continuing to Task 2.

## Task 2: Import the `ol9-mysql` OVA

1. In the **Administration Portal**, navigate to **Compute -> Virtual Machines -> Import**.

    ![The Import VM dialog showing Source set to Virtual Appliance OVA and olkvm01 selected as Host.](images/import-vm.png)

2. Set **Data Center** to **Default**.

3. Set **Source** to **Virtual Appliance (OVA)**.

4. Set **Host** to **olkvm01**.

5. Set **File Path** to `/tmp`.

6. Click **Load**.

7. Under **Virtual Machines on Source**, select `ol9-mysql.ova`.

8. Move it to **Virtual Machines to Import** with the arrow button.

9. Click **Next**.

10. Select `ol9-mysql`, open **Network Interfaces**, and set both **Network Name** and **Profile Name** to `l2-vm-network`.

11. Click **OK**.

12. Wait for the `ol9-mysql` VM status to show **Down** before you continue.

    **Expected time:** 10-15 minutes.

## Task 3: Start and Test the `ol9-mysql` VM

1. Select `ol9-mysql` and click **Run**.

2. Wait for the VM status to change to **Up**.

3. From the manager terminal, connect to the database VM(username: **opc** / password: **oracle**):

    ```bash
    <copy>ssh opc@10.0.10.100</copy>
    ```

4. Verify MySQL and the sample data:

    ```bash
    <copy>mysql -u empapp -pWelcome#123 employee_db -e "SELECT COUNT(*) as employee_count FROM employees;"</copy>
    ```

    You should see a count of **8** employee records.

5. Exit the database VM:

    ```bash
    <copy>exit</copy>
    ```

## Task 4: Download the `ol9-webapp` OVA

1. From the manager terminal, download `ol9-webapp.ova` to `olkvm02`:

    ```bash
    <copy>ssh olkvm02 "curl -L https://objectstorage.us-ashburn-1.oraclecloud.com/n/idhwewbjlvpy/b/olvm-on-oci/o/ova-e4-shape%2Fol9-webapp.ova -o /tmp/ol9-webapp.ova"</copy>
    ```

    **Expected time:** 10-20 minutes. Wait for the `curl` command to complete before continuing to Task 5.

## Task 5: Import the `ol9-webapp` OVA

1. In the **Administration Portal**, navigate to **Compute -> Virtual Machines -> Import**.

2. Set **Data Center** to **Default**.

3. Set **Source** to **Virtual Appliance (OVA)**.

4. Set **Host** to **olkvm02**.

5. Set **File Path** to `/tmp`.

6. Click **Load**.

7. Under **Virtual Machines on Source**, select `ol9-webapp.ova`.

8. Move it to **Virtual Machines to Import** with the arrow button.

9. Click **Next**.

10. Select `ol9-webapp`, open **Network Interfaces**, and set both **Network Name** and **Profile Name** to `l2-vm-network`.

11. Click **OK**.

12. Wait for the `ol9-webapp` VM status to show **Down** before you continue.

    **Expected time:** 10-15 minutes.

## Task 6: Start and Test the `ol9-webapp` VM

1. Select `ol9-webapp` and click **Run**.

2. Wait for the VM status to change to **Up**.

3. From the manager terminal, connect to the application VM (username: **opc** / password: **oracle**):

    ```bash
    <copy>ssh opc@10.0.10.101</copy>
    ```

4. Verify that the application responds:

    ```bash
    <copy>curl -s http://localhost:8080/employee-app/ | grep -q "Welcome to Employee Directory" && echo "Application is responding" || echo "Application check failed"</copy>
    ```

    If this check fails immediately after boot, wait 2-3 minutes for Tomcat to finish starting and try again.

5. Verify connectivity to the MySQL VM:

    ```bash
    <copy>ping -c 3 10.0.10.100</copy>
    ```

6. Exit the application VM:

    ```bash
    <copy>exit</copy>
    ```


## Task 7: Access the Application from Your Local Browser

1. From a **new** local PowerShell window, create an SSH tunnel to the webapp VM:

    ```powershell
        <copy>ssh -i C:\Users\<you>\.ssh\olvm-cluster-id_rsa -L 8080:10.0.10.101:8080 -N oracle@<olvm-public-ip></copy>
    ```

    Leave this window open. The tunnel is active as long as this session remains connected.

2. From your local browser, navigate to:

    ```
    <copy>http://localhost:8080/employee-app/</copy>
    ```

3. Wait 2-5 minutes for the application to finish loading if needed.

4. Verify the application:

    - The home page displays **Welcome to Employee Directory**
    - Clicking **View Employees** opens the employee list
    - The employee list shows eight records

    ![The Employee Directory application running in the browser showing 8 employee records.](images/employee-directory.png)


### Deploy Multi Tier Application Checkpoint

At this point, you should have:

- `ol9-mysql` running and returning 8 employee records
- `ol9-webapp` running and reachable from the manager
- Network connectivity between `10.0.10.101` and `10.0.10.100`
- The Employee Directory application visible in the browser

## Learn More

- Oracle Linux Virtualization Manager install lab (official): https://docs.oracle.com/en/learn/olvm-install/index.html

## Acknowledgements

- **Author** - Shawn Kelley, John Priest
- **Contributors** - Perside Foster
- **Last Updated By/Date** - Perside Foster, May 20, 2026
