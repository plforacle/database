# Lab 1: Connect to Your CentOS Stream Source

## Introduction

Your instructor has prepared a separate CentOS Stream 9 VM for you in OCI. You do not need to download an image, create a bucket, import an image, configure a network, or launch a VM. This lab checks that you can reach your assigned source and that it is ready for the migration exercise.

Estimated Lab Time: 15 minutes

### Objectives

- Identify your assigned VM, public IP, and SSH key.
- Connect to the source VM and verify its operating system.
- Check that CentOS repositories and the network are available.

### Prerequisites

- The instructor has given you a VM name, public IP address, OCI compartment, and region. You know the location of your own SSH private key.
- Your VM is **Running** and its public subnet permits SSH from your client network.

## Task 1: Identify your assigned VM

1. Record the VM name, public IP address, region, and compartment from your instructor. If you have OCI Console access, open **Compute** > **Instances** and confirm that your assigned VM is **Running**. Do not create another instance.

2. Confirm that your SSH private key is on your own computer. Do not upload or share the private key. The corresponding public key was installed on your VM when the instructor launched it.

## Task 2: Connect and verify CentOS Stream 9

1. From a local terminal, connect to your assigned VM. Replace the placeholders with your own values:

    ```bash
    ssh -i <private-key-path> cloud-user@<public-ip>
    ```

    If your SSH agent already has the key, `ssh cloud-user@<public-ip>` may work. The instructor will confirm the login name during VM preparation.

2. On the VM, confirm the operating system, architecture, and running kernel:

    ```bash
    cat /etc/os-release
    uname -m
    uname -r
    ```

    Continue only if the VM reports **CentOS Stream 9** and `x86_64`. If it reports another operating system, contact the instructor before proceeding.

## Task 3: Check source access

1. Confirm that cloud-init has finished and that the VM has a default route:

    ```bash
    sudo cloud-init status --wait
    ip route
    ```

2. Check package metadata and outbound access:

    ```bash
    sudo dnf repolist --enabled
    sudo dnf makecache
    curl --fail --head https://raw.githubusercontent.com/
    ```

    BaseOS and AppStream must be reachable. If a command fails, give the instructor the full error before starting Lab 2. Do not edit repository files or run the migration to work around a failed check.

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, September 2026
