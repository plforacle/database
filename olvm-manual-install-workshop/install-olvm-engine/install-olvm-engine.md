# Lab 3: Install Oracle Linux Virtualization Manager

## Introduction

In this lab, you will install Oracle Linux Virtualization Manager on `olvm-engine`, run `engine-setup`, capture the manager summary, update your local host name resolution, and sign in to the administration portal.

Estimated Time: 25 minutes

### Objectives

In this lab, you will:
* Install the OLVM release package, kernel modules, and engine package.
* Run `engine-setup --accept-defaults` on the engine host.
* Capture the manager host FQDN and add it to your local `hosts` file.
* Import the engine CA certificate and sign in to the administration portal.

### Prerequisites (Optional)

This lab assumes `olvm-engine` is reachable through SSH and the public security rule for port `443` is already in place.

## Task 1: Install the Engine Packages

1. Connect to `olvm-engine` over SSH.

    ```bash
    ssh opc@<olvm-engine-public-ip>
    ```

2. Install the release package and the required kernel modules, clean the `dnf` cache, and reboot the host.

    ```bash
    sudo dnf install -y oracle-ovirt-release-45-el8
    sudo dnf install -y kernel-uek-modules-extra
    sudo dnf clean all
    sudo reboot
    ```

3. Reconnect to the host after the reboot and install the engine package.

    ```bash
    ssh opc@<olvm-engine-public-ip>
    sudo dnf install -y ovirt-engine
    ```

## Task 2: Run Engine Setup and Capture the Summary

1. Start the OLVM engine setup and accept the default configuration values.

    ```bash
    sudo engine-setup --accept-defaults
    ```

2. When the installer prompts for the administration password, create the password you will use to sign in to the OLVM console. The source notes that the password is not echoed while you type it.

3. When the setup finishes, capture the summary output. The source notes that the same information is also written to `/var/log/ovirt-engine/setup/ovirt-engine-setup-xxx.log`.

4. Record the fully qualified host name for the manager.

    ```bash
    hostname -f
    ```

## Task 3: Update Local Name Resolution and Sign In to the Portal

1. On your workstation, edit the local `hosts` file and add the OLVM engine public IP address and the fully qualified host name reported by `hostname -f`.

    For example:

    ```text
    143.47.107.118   olvm-engine.sub04151620080.olvvcn.oraclevcn.com
    ```

2. Open a browser and go to the manager URL from the source.

    ```text
    https://<fqdn-of-the-manager-host>/ovirt-engine
    ```

3. Under **Downloads**, download the **Engine CA Certificate** and import it into your browser trust store.

4. If you use Firefox, open **Settings**, search for `cert`, open **View Certificates**, click **Import**, select the downloaded certificate, and enable the option to trust the CA to identify websites.

5. If you use macOS, drag the certificate into **Keychain Access**, open the certificate details, expand **Trust**, and choose **Always Trust**, as directed by the source.

6. In the engine Web UI, click **Administration Portal**, sign in as `admin@ovirt`, and use the password you created during `engine-setup`.

7. If your browser displays a security warning during sign-in, approve it and continue.

## Learn More

* [Oracle Linux Virtualization Manager 4.5 Getting Started](https://docs.oracle.com/en/virtualization/oracle-linux-virtualization-manager/getstart/getstarted-manager-install.html)

## Acknowledgements
* **Author** - Codex
* **Contributors** - `seighman` (source tutorial author shown in the PDF)
* **Last Updated By/Date** - Codex, May 2026
