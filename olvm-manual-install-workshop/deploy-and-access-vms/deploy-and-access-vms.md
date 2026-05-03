# Lab 5: Deploy and Access Virtual Machines

## Introduction

In this lab, you will import Oracle Linux OVA templates, create a virtual machine on the `l2-vm-network`, enable noVNC console access, add VLAN external access in OCI, and then capture the optional DHCP and troubleshooting actions from the source tutorial.

Estimated Time: 35 minutes

### Objectives

In this lab, you will:
* Download OVA templates to `olvm-kvm1` and import them into OLVM.
* Create a virtual machine with a static address on `10.0.10.0/24`.
* Run the virtual machine and verify console and network behavior.
* Add public external access for the VLAN-backed virtual machine.
* Record the source tutorial's DHCP and troubleshooting steps for later use.

### Prerequisites (Optional)

This lab assumes both KVM hosts are `Up`, the `l2-vm-network` logical network is assigned, and the storage domains are active.

## Task 1: Import the Oracle Linux Templates

1. In the OLVM administration portal, go to **Compute** and then **Templates**, and click **Import**.

2. Keep the default values for the data center, source, and host, and enter `/tmp` as the file path.

3. Switch to the SSH session for `olvm-kvm1`, change to `/tmp`, and download the Oracle Linux templates from the source tutorial.

    ```bash
    cd /tmp
    curl -L https://yum.oracle.com/templates/OracleLinux/OL9/u5/x86_64/OL9U5_x86_64-olvm-b253.ova -o /tmp/ol9u5.ova
    curl -L https://yum.oracle.com/templates/OracleLinux/OL8/u10/x86_64/OL8U10_x86_64-olvm-b258.ova -o ol8u10.ova
    curl -L https://yum.oracle.com/templates/OracleLinux/OL7/u9/x86_64/OL7U9_x86_64-olvm-b257.ova -o ol7u9.ova
    ```

4. Return to the portal, click **Load**, move the templates to **Virtual Machines to Import**, click **Next**, review the template details, and click **OK**.

5. Wait until the import status shows `OK`.

## Task 2: Create a Virtual Machine on the VLAN Network

1. In the administration portal, go to **Compute** and then **Virtual Machines**, and click **New**.

2. Create a virtual machine from the imported Oracle Linux 9 template and use the static addressing pattern from the source.

    Use these values:
    * Template: `OL9U5_x86_64-olvm-b253`
    * Operating System: `Oracle Linux 9.x x64`
    * Name: `ol9-vm1-101`
    * `nic1`: `l2-vm-network`

3. Click **Show Advanced Options**, enable **Initial Run**, open **Authentication**, and create the `opc` user password.

4. Open **Networks** and enter the static guest configuration from the source.

    Use these values:
    * DNS Servers: `1.1.1.1`
    * In-guest Network Interface Name: `eth0`
    * IPv4 Boot Protocol: `Static`
    * IPv4 Address: `10.0.10.101`
    * IPv4 Netmask: `255.255.255.0`
    * IPv4 Gateway: `10.0.10.1`

5. Click **OK** and wait for the virtual machine status to change from `Importing` to `Down`.

## Task 3: Run the Virtual Machine and Verify the Isolated Network

1. Select the virtual machine and click **Run**.

2. Open **Console Options**, choose `noVNC`, and then open the console.

3. Sign in with the `opc` credentials you created and verify the guest network settings.

    ```bash
    ip addr
    ping 10.0.10.1
    ```

4. Ping an internet address and confirm the request fails before external access is added. The source uses `oracle.com` and `google.com` to show the difference between DNS resolution and ICMP reachability.

## Task 4: Add External Access to the VLAN and Retest

1. Open the OCI Console, go to **Networking**, open **Virtual cloud networks**, and select `OLVM-VCN`.

2. Open **VLANs**, select `VLAN-VMs`, and click **Add External Access**.

3. Use the public access flow from the source.

    Use these values:
    * External Access Type: `Public Access`
    * Name: the virtual machine name
    * Private IP Address: `10.0.10.101`
    * Reserved Public IP Address: `Create New`

4. Return to the guest console and confirm the virtual machine can now reach the internet.

5. The source notes that after external access is added, you can update the guest with `sudo dnf upgrade -y`.

## Task 5: Record the Optional DHCP and Troubleshooting Steps

1. If you want DHCP on `l2-vm-network`, create a second virtual machine similar to the first one, name it `ol9-dhcpserver`, assign `10.0.10.2`, and use **Initial Run** with **Custom Script**.

2. Paste the DHCP server script from the source.

    ```text
    runcmd:
      - |
        dnf install -y dhcp-server
        echo 'option domain-name-servers 10.0.10.1;
        default-lease-time 43200;
        max-lease-time 86400;
        authoritative;
        log-facility local7;
        subnet 10.0.10.0 netmask 255.255.255.0 {
          range 10.0.10.200 10.0.10.250;
          option domain-name-servers 10.0.10.1;
          option routers 10.0.10.1;
          option broadcast-address 10.0.10.255;
        }' >> /etc/dhcp/dhcpd.conf
        systemctl enable --now dhcpd.service
    ```

3. If `noVNC` fails with the source error message about the connection being closed, verify that the engine CA certificate is imported and that your public ingress rule still matches your current workstation IP address.

4. If the VLAN `VNIC3` mapping issue appears on a KVM host, use `nmcli` and the generated `ifcfg` files to confirm `ens5` maps to `ens5` and `ens6` maps to `ens6`, and then restart `NetworkManager`.

    ```bash
    sudo nmcli dev show
    sudo nmcli con show
    cat /etc/sysconfig/network-script/ifcfg-ens5
    cat /etc/sysconfig/network-script/ifcfg-ens6
    sudo systemctl restart NetworkManager
    ip addr show
    sudo nmcli dev show
    sudo nmcli con show
    ```

## Learn More

* [Oracle Linux Templates](https://yum.oracle.com/oracle-linux-templates.html)

## Acknowledgements
* **Author** - Codex
* **Contributors** - `seighman` (source tutorial author shown in the PDF)
* **Last Updated By/Date** - Codex, May 2026
