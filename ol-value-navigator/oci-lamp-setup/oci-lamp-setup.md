# Lab 1: Create the OCI LAMP Environment

## Introduction

In this lab, you create the OCI foundation for the Oracle Linux Value Navigator. You will organize the project resources in a compartment, create a Virtual Cloud Network (VCN), configure network access, launch an Oracle Linux compute instance, and install the LAMP software.

This lab uses one compute instance for the prototype. Apache, PHP, and MySQL run together on that instance. Later labs add the database structure and application code.

Estimated Time: 90 minutes

### About the LAMP Environment

LAMP combines Linux, Apache, MySQL, and PHP. This workshop uses Oracle Linux 9, Apache HTTP Server, PHP, and Oracle MySQL Server from the Oracle Linux Application Stream repository.

The OCI resources in this lab have clear names so that you can find them later. The public subnet provides internet access to the web server. Network rules and the Oracle Linux firewall permit only the traffic needed for administration and the prototype web page.

### Objectives

In this lab, you will:

* Create a compartment for the workshop resources.
* Create a VCN and public subnet for the LAMP server.
* Configure SSH and web traffic rules.
* Launch an Oracle Linux 9 compute instance.
* Connect to the instance with SSH.
* Install and start Apache, PHP, and MySQL.
* Verify the PHP web page from your browser.

### Prerequisites

This lab assumes you have:

* Access to an OCI tenancy.
* Permission to create compartments, networking resources, and compute instances.
* An SSH client on your local computer.

> **Note:** If your organization manages compartments, networks, or security rules for you, ask your OCI administrator to create or approve the values in this lab. Do not create duplicate resources.

*This is the fold. The remaining sections are collapsed by default.*

## Task 1: Create the project compartment

A compartment keeps the Oracle Linux Value Navigator resources together. It also gives your OCI administrator a clear location for access policies, budgets, and cleanup.

1. Sign in to the **Oracle Cloud Console**.

2. Open the navigation menu, select **Identity & Security**, and then select **Compartments** under **Identity**.

3. Navigate to the parent compartment approved by your OCI administrator.

    If you are using a personal training tenancy and no parent compartment was provided, remain in the root compartment.

4. Select **Create compartment**.

5. Enter the following values.

    | Field | Value |
    | --- | --- |
    | Name | `ol-value-navigator` |
    | Description | `Resources for the Oracle Linux Value Navigator workshop` |
    | Parent compartment | Your approved parent compartment or the root compartment |

6. Leave the tagging fields empty unless your organization requires specific tags, and then select **Create compartment**.

7. Confirm that `ol-value-navigator` appears in the compartment list with an **Active** status.

    > **Checkpoint:** You now have one compartment dedicated to the workshop. Select this compartment whenever a later step asks where to create a resource.

## Task 2: Create the virtual cloud network

The VCN is the private network for the application. The OCI wizard creates the public subnet, gateway, and route required for the LAMP server to reach the internet.

1. Open the navigation menu, select **Networking**, and then select **Virtual cloud networks**.

2. In the **Compartment** list, select `ol-value-navigator`.

3. Select **Start VCN Wizard**.

    If the button is not visible, select **Actions**, and then select **Start VCN Wizard**.

4. Select **Create VCN with Internet Connectivity**, and then select **Start VCN Wizard**.

5. Enter the following values.

    | Field | Value |
    | --- | --- |
    | VCN name | `ol-value-navigator-vcn` |
    | Compartment | `ol-value-navigator` |
    | VCN IPv4 CIDR block | `10.0.0.0/16` |
    | Public subnet IPv4 CIDR block | `10.0.0.0/24` |
    | Private subnet IPv4 CIDR block | `10.0.1.0/24` |
    | Use DNS hostnames in this VCN | Selected |

6. Leave IPv6 disabled for this prototype unless your organization requires it.

7. Select **Next** and review the resources that the wizard will create.

    The list should include the VCN, a public subnet, a private subnet, an internet gateway, a NAT gateway, a service gateway, route tables, and security lists.

8. Select **Create** and wait for every resource to show **Succeeded**.

9. Select **View VCN**.

    > **Checkpoint:** The VCN `ol-value-navigator-vcn` was created successfully. You will select its public subnet when you create the LAMP server.

## Task 3: Configure the network security rules

OCI security lists act as a virtual firewall for the subnet. SSH and HTTP access are available to workshop users so they can connect to the server and open the prototype in a browser.

1. On the `ol-value-navigator-vcn` page, select the **Security** tab at the top of the page.

2. In the **Security Lists** section, select **Default Security List** for ol-value-navigator-vcn, and then select **Security Rules**.

3. Under **Ingress Rules**, confirm that the existing stateful TCP rule for destination port `22` uses the source CIDR `0.0.0.0/0`.

4. Select **Add Ingress Rules** and add the following HTTP rule.

    | Field | Value |
    | --- | --- |
    | Stateless | Cleared |
    | Source type | CIDR |
    | Source CIDR | `0.0.0.0/0` |
    | IP protocol | TCP |
    | Source port range | All |
    | Destination port range | `80` |
    | Description | `Allow HTTP access to the workshop application` |

5. Confirm that the security list includes these workshop rules.

    | Purpose | Protocol | Source | Destination port |
    | --- | --- | --- | --- |
    | SSH administration | TCP | `0.0.0.0/0` | `22` |
    | Prototype web page | TCP | `0.0.0.0/0` | `80` |

    > **Checkpoint:** The public subnet allows SSH traffic on TCP port 22 and HTTP traffic on TCP port 80.

## Task 4: Create the Oracle Linux compute instance

The compute instance is the server that runs the complete prototype. This workshop uses a paid, general-purpose AMD x86 flexible shape with enough memory for Apache, PHP, and MySQL.

1. Open the navigation menu, select **Compute**, and then select **Instances**.

2. In the **Compartment** list, select `ol-value-navigator`.

3. Select **Create instance**.

4. Enter the following basic values.

    | Field | Value |
    | --- | --- |
    | Name | `ol-value-navigator-app` |
    | Compartment | `ol-value-navigator` |
    | Placement | Accept the default availability domain |

5. In the **Image and shape** section, select **Edit**, and then configure these values.

    | Field | Value |
    | --- | --- |
    | Image | Oracle Linux 9, latest available platform image |
    | Shape | `VM.Standard.E5.Flex` |
    | OCPUs | `1` |
    | Memory | `8 GB` |

    If `VM.Standard.E5.Flex` is unavailable in your region, select another approved paid x86 flexible VM shape with at least 1 OCPU and 8 GB of memory.

6. In the **Primary VNIC information** or **Networking** section, select **Edit**, and then configure these values.

    | Field | Value |
    | --- | --- |
    | Virtual cloud network | `ol-value-navigator-vcn` |
    | Subnet | The public subnet with CIDR `10.0.0.0/24` |
    | Use network security groups | Cleared |
    | Assign a public IPv4 address | Selected |
    | Private IPv4 address | Automatically assigned |

7. In the **Add SSH keys** section, select one option.

    * Select **Upload public key files** if you already have an OpenSSH public key.
    * Select **Paste public keys** if you want to paste the public key text.
    * Select **Generate a key pair for me** if you need OCI to create the keys.

    If OCI generates the key pair, select **Save private key** and store the file in a secure location. OCI does not retain a copy that you can download later.

8. Keep the default boot volume size and select **Use in-transit encryption** if that option is available.

9. Select **Create**.

10. Wait until the instance lifecycle state changes to **Running**.

11. On the instance details page, locate **Instance access** and record the **Public access IP address** in your private notes.

    Do not save the private SSH key, OCI OCIDs, or other protected tenancy information in the workshop repository.

    > **Checkpoint:** The instance `ol-value-navigator-app` is running Oracle Linux 9 in the public subnet and has a public IPv4 address.

## Task 5: Connect to and update the server

1. Open PowerShell, Windows Terminal, or another terminal on your local computer.

2. Connect to the instance. Replace the two placeholders with the private-key path and the instance public IP address.

    ```bash
    <copy>ssh -i /path/to/private-key opc@PUBLIC_IP_ADDRESS</copy>
    ```

    The default SSH user for an Oracle Linux platform image is `opc`.

3. If this is your first connection, review the host fingerprint, type `yes`, and press Enter.

4. Confirm that the prompt changes to the remote `opc` account.

5. Update the installed packages.

    ```bash
    <copy>sudo dnf update -y</copy>
    ```

6. Confirm the operating system and enabled software repositories.

    ```bash
    <copy>cat /etc/os-release
    sudo dnf repolist</copy>
    ```

    > **Checkpoint:** The output identifies Oracle Linux 9 and the package update completes without errors.

## Task 6: Install and configure the LAMP software

1. Review the MySQL module streams available from the Oracle Linux 9 Application Stream repository.

    ```bash
    <copy>sudo dnf module list mysql</copy>
    ```

2. Install MySQL 8.4 from the Oracle Linux Application Stream repository.

    ```bash
    <copy>sudo dnf module install -y mysql:8.4</copy>
    ```

3. Install Apache, PHP, and the PHP MySQL driver.

    ```bash
    <copy>sudo dnf install -y httpd php php-mysqlnd</copy>
    ```

4. Enable Apache and MySQL so that they start automatically when the instance starts. Start both services now.

    ```bash
    <copy>sudo systemctl enable --now httpd mysqld</copy>
    ```

5. Allow HTTP traffic through the Oracle Linux firewall.

    ```bash
    <copy>sudo firewall-cmd --permanent --add-service=http
    sudo firewall-cmd --reload</copy>
    ```

6. Allow Apache to connect to the database. This SELinux setting supports both local and future network database connections.

    ```bash
    <copy>sudo setsebool -P httpd_can_network_connect_db 1</copy>
    ```

7. Confirm that Apache and MySQL are running.

    ```bash
    <copy>systemctl is-active httpd
    systemctl is-active mysqld</copy>
    ```

    Both commands should return `active`.

8. Confirm the installed MySQL and PHP versions.

    ```bash
    <copy>mysql --version
    php --version</copy>
    ```

    The MySQL command must report version 8.4 or later. Do not continue to Lab 2 if it reports MySQL 8.0.

9. Run the MySQL security configuration program.

    ```bash
    <copy>sudo mysql_secure_installation</copy>
    ```

    Answer the prompts as follows.

    | Prompt | Answer |
    | --- | --- |
    | Set up the VALIDATE PASSWORD component? | `N` |
    | New password | Enter a strong MySQL root password |
    | Re-enter new password | Enter the same root password again |
    | Remove anonymous users? | `Y` |
    | Disallow root login remotely? | `Y` |
    | Remove test database and access to it? | `Y` |
    | Reload privilege tables now? | `Y` |

    Store the MySQL root password securely. Do not add it to the workshop repository.

    Confirm that the program finishes with:

    ```text
    All done!
    ```

10. Confirm that the operating-system firewall permits HTTP.

    ```bash
    <copy>sudo firewall-cmd --list-services</copy>
    ```

    The output should include `http`.

    > **Checkpoint:** Apache, PHP, and Oracle MySQL Server are installed. Apache and MySQL are active, and both OCI and Oracle Linux permit TCP port 80.

## Task 7: Verify PHP through Apache

1. Create the first Oracle Linux Value Navigator PHP page.

    ```bash
    <copy>echo '&lt;?php echo "Oracle Linux Value Navigator is running"; ?&gt;' | sudo tee /var/www/html/index.php</copy>
    ```

2. Test the page from the compute instance.

    ```bash
    <copy>curl http://localhost/</copy>
    ```

    The command should return:

    ```text
    Oracle Linux Value Navigator is running
    ```

3. On your local computer, open a browser and enter the following address. Replace the placeholder with the instance public IP address.

    ```text
    http://PUBLIC_IP_ADDRESS/
    ```

4. Confirm that the browser displays `Oracle Linux Value Navigator is running`.

5. If the browser cannot reach the page, verify all of the following items before continuing.

    * The compute instance lifecycle state is **Running**.
    * The instance has a public IPv4 address.
    * The instance is in the public subnet with CIDR `10.0.0.0/24`.
    * The OCI security list permits inbound TCP port `80` from `0.0.0.0/0`.
    * `systemctl is-active httpd` returns `active`.
    * `sudo firewall-cmd --list-services` includes `http`.

You have created the OCI infrastructure and installed the LAMP environment for the Oracle Linux Value Navigator. In the next lab, you will create the governed catalog database.

## Learn More

* [Creating a Compartment](https://docs.oracle.com/en-us/iaas/Content/Identity/compartments/To_create_a_compartment.htm)
* [Virtual Networking Wizards](https://docs.oracle.com/en-us/iaas/Content/Network/Tasks/quickstartnetworking.htm)
* [Creating a Security List](https://docs.oracle.com/en-us/iaas/Content/Network/Concepts/creating-securitylist.htm)
* [Launching Your First Linux Instance](https://docs.oracle.com/en-us/iaas/Content/Compute/tutorials/first-linux-instance/overview.htm)
* [Oracle Linux 9 Image](https://docs.oracle.com/en-us/iaas/oracle-linux/oci/oracle-linux-9.htm)
* [Install Apache and PHP on Oracle Linux](https://docs.oracle.com/en-us/iaas/Content/developer/apache-on-oracle-linux/01-summary.htm)
* [MySQL 8.4 Reference Manual](https://dev.mysql.com/doc/refman/8.4/en/)

## Acknowledgements

* **Author** - Perside Foster, Mark Atkinson, Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, August 2026
