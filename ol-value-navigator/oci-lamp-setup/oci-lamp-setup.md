# Lab 1: Create the OCI Oracle Linux, Apache, PHP, and MySQL HeatWave Environment

## Introduction

In this lab, you create the OCI foundation for the Oracle Linux Value Navigator. You will organize the project resources in a compartment, create a Virtual Cloud Network (VCN), configure network access, launch an Oracle Linux compute instance, and install the LAMP software.

Apache and PHP run on the Oracle Linux compute instance. A private MySQL HeatWave DB System stores the application data, and its MySQL HeatWave Cluster provides MySQL HeatWave GenAI.

Estimated Time: 90 minutes

### About the LAMP Environment

For this workshop, LAMP combines Linux, Apache, MySQL HeatWave, and PHP. The database runs in a managed MySQL HeatWave DB System.

The OCI resources in this lab have clear names so that you can find them later. The public subnet provides internet access to the web server. Network rules and the Oracle Linux firewall permit only the traffic needed for administration and the prototype web page.

### Objectives

In this lab, you will:

* Create a compartment for the workshop resources.
* Create a VCN and public subnet for the LAMP server.
* Configure SSH and web traffic rules.
* Launch an Oracle Linux 9 compute instance.
* Connect to the instance with SSH.
* Install Apache, PHP, and the MySQL client.
* Create a MySQL HeatWave DB System and configure it for MySQL HeatWave GenAI.
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

6. Open the security list associated with the private subnet and add this ingress rule for MySQL traffic.

    | Field | Value |
    | --- | --- |
    | Stateless | Cleared |
    | Source type | CIDR |
    | Source CIDR | `10.0.0.0/24` |
    | IP protocol | TCP |
    | Source port range | All |
    | Destination port range | `3306` |
    | Description | `Allow MySQL traffic from the application subnet` |

    > **Checkpoint:** The public subnet allows SSH traffic on TCP port 22 and HTTP traffic on TCP port 80. The private subnet allows MySQL traffic on TCP port 3306 only from the application subnet CIDR `10.0.0.0/24`.

## Task 4: Create the Oracle Linux compute instance

The compute instance runs the web application. This workshop uses a paid, general-purpose AMD x86 flexible shape with enough memory for Apache and PHP while MySQL HeatWave runs as a managed service.

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

5. Confirm the operating system and enabled software repositories.

    ```bash
    <copy>cat /etc/os-release
    sudo dnf repolist</copy>
    ```

    > **Checkpoint:** The output identifies Oracle Linux 9 and lists the enabled BaseOS and AppStream repositories.

## Task 6: Install and configure Apache, PHP, and the MySQL client

1. Enable the recommended Oracle Linux 9 application streams for PHP and the MySQL client.

    ```bash
    <copy>sudo dnf module enable -y php:8.3 mysql:8.4</copy>
    ```

    PHP 8.3 and MySQL 8.4 are Oracle-supported Oracle Linux 9 AppStream versions with published support through 2029. Selecting the streams explicitly prevents DNF from installing the original PHP 8.0 and MySQL 8.0 streams.

2. Install Apache, PHP 8.3, the matching PHP driver for MySQL, and the MySQL 8.4 command-line client.

    ```bash
    <copy>sudo dnf install -y httpd php php-mysqlnd mysql</copy>
    ```

    The `mysql` package installs the command-line client. It does not install a local MySQL Server on the compute instance.

    If you previously completed an older draft of this task and installed PHP 8.0 and MySQL 8.0, switch the installed packages to the selected streams before continuing.

    ```bash
    <copy>sudo dnf module reset -y php mysql
    sudo dnf module enable -y php:8.3 mysql:8.4
    sudo dnf distro-sync -y
    sudo dnf install -y httpd php php-mysqlnd mysql</copy>
    ```

3. Enable and start Apache.

    ```bash
    <copy>sudo systemctl enable --now httpd</copy>
    ```

4. Allow HTTP traffic through the Oracle Linux firewall.

    ```bash
    <copy>sudo firewall-cmd --permanent --add-service=http
    sudo firewall-cmd --reload</copy>
    ```

5. Allow Apache to connect to the private MySQL HeatWave DB System.

    ```bash
    <copy>sudo setsebool -P httpd_can_network_connect_db 1</copy>
    ```

6. Confirm that Apache is running.

    ```bash
    <copy>systemctl is-active httpd</copy>
    ```

    The command should return `active`.

7. Confirm the installed MySQL client and PHP versions, and verify that the client supports the required TLS option.

    ```bash
    <copy>mysql --version
    mysql --help | grep ssl-mode
    php --version</copy>
    ```

    Confirm that the output reports MySQL client 8.4, PHP 8.3, and the `ssl-mode` option.

8. Confirm that the operating-system firewall permits HTTP.

    ```bash
    <copy>sudo firewall-cmd --list-services</copy>
    ```

    The output should include `http`.

    > **Checkpoint:** Apache, PHP 8.3, and the Oracle Linux-provided MySQL 8.4 client are installed. The MySQL client supports TLS connections, Apache is active, and TCP port 80 is available.

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

You have created the Oracle Linux web tier. Next, create the MySQL HeatWave DB System and verify MySQL HeatWave GenAI.

## Task 8: Create the MySQL HeatWave DB System and configure MySQL HeatWave GenAI

1. In the OCI Console, open the navigation menu, select **Databases**, and then select **DB systems** under **MySQL HeatWave**.

2. Select **Create DB system**, and then select **Development or testing**.

3. Configure the DB System.

    | OCI form section | Field | Value |
    | --- | --- | --- |
    | Template | Template | Development or testing |
    | DB System information | Create in compartment | `ol-value-navigator` |
    | DB System information | Name | `ol-value-navigator-db` |
    | DB System information | Description | `MySQL HeatWave DB System for Oracle Linux Value Navigator comparison storage and GenAI workloads` |
    | Administrator credentials | Username | `olvnadmin` |
    | Setup | Topology | Standalone |
    | Networking | Virtual cloud network compartment | `ol-value-navigator` |
    | Networking | Virtual cloud network | `ol-value-navigator-vcn` |
    | Networking | Subnet compartment | `ol-value-navigator` |
    | Networking | Subnet | Private regional subnet with CIDR `10.0.1.0/24` |
    | Networking | Network security groups | None; use the subnet security list created earlier |
    | Placement | Availability domain | Any available availability domain |
    | Placement | Fault domain | Let Oracle choose |
    | Hardware | Enable HeatWave cluster | Enabled |
    | Hardware | DB System shape | `MySQL.2` or an approved paid HeatWave-capable shape |
    | HeatWave Cluster | Shape | `HeatWave.512GB` |
    | HeatWave Cluster | Nodes | `1` |
    | HeatWave Cluster | MySQL HeatWave Lakehouse | Enabled |
    | Storage | Initial data storage size | `50 GiB` |
    | Storage | Automatic storage expansion | Disabled |
    | Backup plan | Automatic backups | Disabled |
    | Tools | MySQL Studio | Disabled |
    | Operational notifications | Contact email | Optional |
    | Deletion plan | Delete protected | Disabled |
    | Deletion plan | Retain automatic backups | Disabled |
    | Deletion plan | Require final backup | Disabled |
    | Advanced options: Configuration | Configuration | Default configuration for `MySQL.2` |
    | Advanced options: Configuration | Database version | Latest available supported MySQL version with MySQL HeatWave GenAI, version 9.0 or later |
    | Advanced options: Encryption | Encryption key | Oracle-managed key |
    | Other advanced options | Connections, crash recovery, maintenance, management, data import, security attributes, telemetry, and tags | Keep the defaults |

    > **Note:** These settings are for a temporary development and testing environment. Production deployments require a separate review of high availability, backups, deletion protection, storage expansion, encryption, monitoring, and operational contacts.

4. Create and securely store the administrator password. Do not add it to the repository.

5. Create the DB System and wait for both the DB System and MySQL HeatWave Cluster states to become **Active**.

6. Record the DB System private IP address in your private notes.

7. From the Oracle Linux instance, connect to the private DB System. Replace the placeholders.

    ```bash
    <copy>mysql --host=HEATWAVE_PRIVATE_IP --user=olvnadmin --password --ssl-mode=REQUIRED</copy>
    ```

8. Confirm the MySQL Server version and the availability of the MySQL HeatWave GenAI routine.

    ```sql
    <copy>SELECT VERSION();
    SELECT sys.ML_GENERATE(
      'Return the word READY.',
      JSON_OBJECT('task','generation','model_id','mistral-7b-instruct-v3')
    );</copy>
    ```

    > **Checkpoint:** The MySQL HeatWave DB System is reachable from the Oracle Linux instance, reports MySQL Server 9.0 or later, and returns a response from `ML_GENERATE`.

You have created the OCI LAMP environment and configured a MySQL HeatWave DB System for MySQL HeatWave GenAI. In the next lab, you will create the saved-comparison database schema.

## Learn More

* [Creating a Compartment](https://docs.oracle.com/en-us/iaas/Content/Identity/compartments/To_create_a_compartment.htm)
* [Virtual Networking Wizards](https://docs.oracle.com/en-us/iaas/Content/Network/Tasks/quickstartnetworking.htm)
* [Creating a Security List](https://docs.oracle.com/en-us/iaas/Content/Network/Concepts/creating-securitylist.htm)
* [Launching Your First Linux Instance](https://docs.oracle.com/en-us/iaas/Content/Compute/tutorials/first-linux-instance/overview.htm)
* [Oracle Linux 9 Image](https://docs.oracle.com/en-us/iaas/oracle-linux/oci/oracle-linux-9.htm)
* [Install Apache and PHP on Oracle Linux](https://docs.oracle.com/en-us/iaas/Content/developer/apache-on-oracle-linux/01-summary.htm)
* [Oracle Linux 9 PHP 8.3 and MySQL 8.4 application streams](https://docs.oracle.com/en/operating-systems/oracle-linux/9/relnotes9.6/ol9-features-DynamicProgramming.html)
* [Oracle Linux application-stream life cycles](https://docs.oracle.com/en/operating-systems/oracle-linux/product-lifecycle/)
* [MySQL HeatWave GenAI requirements](https://dev.mysql.com/doc/heatwave/en/mys-hw-genai-requirements.html)
* [ML_GENERATE](https://dev.mysql.com/doc/heatwave/en/mys-hwgenai-ml-generate.html)

## Acknowledgements

* **Author** - Perside Foster, Mark Atkinson, Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, August 2026
