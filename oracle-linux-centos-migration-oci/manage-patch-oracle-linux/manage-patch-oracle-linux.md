# Lab 5: Manage and Patch Oracle Linux

## Introduction

Now that the instance boots Oracle Linux, verify its package repositories and apply normal DNF updates. This lab also confirms that the Apache page continues to work after package maintenance.

Estimated Lab Time: 25 minutes

### Objectives

- Inspect the Oracle Linux release and kernel packages.
- Review enabled repositories and apply available package updates.
- Confirm services and application health.

### Prerequisites

- Completed Lab 4 and connected to the Oracle Linux VM.

## Task 1: Inspect Oracle Linux

1. Confirm the OS identity and kernel:

    ```bash
    cat /etc/os-release
    uname -r
    rpm -qf "/boot/vmlinuz-$(uname -r)" --qf '%{NAME} %{VERSION}-%{RELEASE} %{VENDOR}\n'
    rpm -qa 'kernel*' | sort
    rpm -qa 'kernel-uek*' | sort
    ```

    RHCK packages have standard names such as `kernel` and `kernel-core`; their names do not contain `rhck`. The `kernel-uek*` command can return no packages. Keep RHCK as the running kernel for this workshop.

## Task 2: Review repositories and apply DNF updates

1. List enabled repositories and check for updates:

    ```bash
    sudo dnf repolist --enabled
    sudo dnf check-update
    ```

    `dnf check-update` can return exit code 100 when updates are available. Confirm the enabled sources are Oracle Linux repositories rather than CentOS Stream repositories.

2. Apply available updates:

    ```bash
    sudo dnf update -y
    ```

    Do not assume a kernel update requires an immediate restart. Record whether one was installed. The Ksplice lab uses the kernel running now and does not require a reboot.

## Task 3: Check services and the workload

1. Confirm Apache and the guest firewall remain active:

    ```bash
    systemctl is-active httpd
    systemctl is-active firewalld
    sudo firewall-cmd --list-services
    getenforce
    ```

2. Verify the page and check failed services:

    ```bash
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    systemctl --failed --no-pager
    ```

    A healthy result includes the marker and `0 loaded units listed.` If a service is failed, inspect it before continuing.

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, September 2026
