# Lab 1: Create the OCI Oracle Linux, Apache, PHP, and MySQL HeatWave GenAI Environment

## Introduction

In this lab, you create the OCI foundation for the Oracle Linux Value Navigator. You will organize the project resources in a compartment, create a Virtual Cloud Network (VCN), configure network access, launch an Oracle Linux compute instance, and install the LAMP software.

Apache and PHP run on the Oracle Linux compute instance. MySQL HeatWave runs in a private DB System with HeatWave GenAI enabled.

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
* Install Apache, PHP, and the MySQL HeatWave client.
* Create a MySQL HeatWave DB System with HeatWave GenAI.
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

6. Open the security list associated with the private subnet and add this MySQL HeatWave rule.

    | Field | Value |
    | --- | --- |
    | Stateless | Cleared |
    | Source type | CIDR |
    | Source CIDR | `10.0.0.0/24` |
    | IP protocol | TCP |
    | Source port range | All |
    | Destination port range | `3306` |
    | Description | `Allow MySQL HeatWave from the application subnet` |

    > **Checkpoint:** The public subnet allows SSH traffic on TCP port 22 and HTTP traffic on TCP port 80.

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

## Task 6: Install and configure Apache, PHP, and the MySQL HeatWave client

1. Install Apache, PHP, the PHP driver for MySQL HeatWave, and the MySQL HeatWave client.

    ```bash
    <copy>sudo dnf install -y httpd php php-mysqlnd mysql</copy>
    ```

2. Enable and start Apache.

    ```bash
    <copy>sudo systemctl enable --now httpd</copy>
    ```

3. Allow HTTP traffic through the Oracle Linux firewall.

    ```bash
    <copy>sudo firewall-cmd --permanent --add-service=http
    sudo firewall-cmd --reload</copy>
    ```

4. Allow Apache to connect to the private HeatWave DB System.

    ```bash
    <copy>sudo setsebool -P httpd_can_network_connect_db 1</copy>
    ```

5. Confirm that Apache is running.

    ```bash
    <copy>systemctl is-active httpd</copy>
    ```

    The command should return `active`.

6. Confirm the installed MySQL HeatWave client and PHP versions.

    ```bash
    <copy>mysql --version
    php --version</copy>
    ```

7. Confirm that the operating-system firewall permits HTTP.

    ```bash
    <copy>sudo firewall-cmd --list-services</copy>
    ```

    The output should include `http`.

    > **Checkpoint:** Apache, PHP, and the MySQL HeatWave client are installed. Apache is active, and TCP port 80 is available.

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

You have created the Oracle Linux web tier. Next, create the managed HeatWave database and verify GenAI.

## Task 8: Create the MySQL HeatWave GenAI DB System

1. In the OCI Console, open the navigation menu, select **Databases**, and then select **DB systems** under **MySQL HeatWave**.

2. Select **Create DB system**, and then select **Development or testing**.

3. Configure the DB System.

    | Field | Value |
    | --- | --- |
    | Compartment | `ol-value-navigator` |
    | Name | `ol-value-navigator-db` |
    | MySQL HeatWave version | 9.0 Innovation or later |
    | Configuration | Standalone |
    | Shape | `MySQL.2` or an approved paid HeatWave-capable shape |
    | Subnet | Private subnet with CIDR `10.0.1.0/24` |
    | Administrator username | `olvnadmin` |

4. Create and securely store the administrator password. Do not add it to the repository.

5. Create the DB System and wait for its state to become **Active**.

6. On the DB System details page, select **Add HeatWave cluster**.

7. Select the `HeatWave.512GB` shape, use one node, enable **MySQL HeatWave Lakehouse**, and select **Add HeatWave cluster**.

8. Wait for the HeatWave cluster state to become **Active**.

9. Record the DB System private IP address in your private notes.

10. From the Oracle Linux instance, connect to the private DB System. Replace the placeholders.

    ```bash
    <copy>mysql --host=HEATWAVE_PRIVATE_IP --user=olvnadmin --password --ssl-mode=REQUIRED</copy>
    ```

11. Confirm the MySQL HeatWave version and GenAI routine.

    ```sql
    <copy>SELECT VERSION();
    SELECT sys.ML_GENERATE(
      'Return the word READY.',
      JSON_OBJECT('task','generation','model_id','mistral-7b-instruct-v3')
    );</copy>
    ```

    > **Checkpoint:** The managed DB System is reachable from PHP, reports MySQL HeatWave 9.0 or later, and returns a response from `ML_GENERATE`.

You have created the OCI LAMP and MySQL HeatWave GenAI environment. In the next lab, you will create the saved-comparison database.

## Learn More

* [Creating a Compartment](https://docs.oracle.com/en-us/iaas/Content/Identity/compartments/To_create_a_compartment.htm)
* [Virtual Networking Wizards](https://docs.oracle.com/en-us/iaas/Content/Network/Tasks/quickstartnetworking.htm)
* [Creating a Security List](https://docs.oracle.com/en-us/iaas/Content/Network/Concepts/creating-securitylist.htm)
* [Launching Your First Linux Instance](https://docs.oracle.com/en-us/iaas/Content/Compute/tutorials/first-linux-instance/overview.htm)
* [Oracle Linux 9 Image](https://docs.oracle.com/en-us/iaas/oracle-linux/oci/oracle-linux-9.htm)
* [Install Apache and PHP on Oracle Linux](https://docs.oracle.com/en-us/iaas/Content/developer/apache-on-oracle-linux/01-summary.htm)
* [MySQL HeatWave GenAI requirements](https://dev.mysql.com/doc/heatwave/en/mys-hw-genai-requirements.html)
* [ML_GENERATE](https://dev.mysql.com/doc/heatwave/en/mys-hwgenai-ml-generate.html)

## Acknowledgements

* **Author** - Perside Foster, Mark Atkinson, Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, August 2026
