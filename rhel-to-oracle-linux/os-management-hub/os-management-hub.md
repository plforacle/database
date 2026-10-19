# Lab 5: Manage Packages with OS Management Hub

## Introduction

Use OS Management Hub to inspect package inventory and submit a package installation job from your SSH session. A local DNF command runs a local package transaction; this exercise submits work to the centralized service, then verifies the result on the server.

Estimated Time: 25 minutes.

### Objectives

- Inspect the registered server and its package inventory.
- Submit one managed package installation and inspect its completion.
- Verify the installed package and application locally.

### Prerequisites

- Completed the preceding labs, or recorded the Ksplice exercise's limitations.
- Instructor confirmation that this converted server is registered and healthy.
- The prepared `workshop-management` helper and an assigned package that is absent locally.

Your instructor handles service access, registration, software sources, and recovery. The helper calls the real service using prepared authentication; it does not simulate job completion. If registration or permissions are unavailable, stop and record the exercise as incomplete or instructor-demonstrated.

## Task 1: Verify registration and inventory

1. Wait for your instructor to complete registration after migration and Ksplice. Registration changes repository management. Do not start local DNF transactions during managed jobs.

2. Verify the helper and managed-server state:

    ```bash
    <copy>
    workshop-management --help
    workshop-management status
    </copy>
    ```

    Require managed status `NORMAL` and an instructor-confirmed recent check-in. A missing helper, permission error, or other status requires instructor help. Do not attempt registration or change service configuration yourself.

3. Compare service inventory with the installed Apache package:

    ```bash
    <copy>
    workshop-management inventory httpd
    rpm -q httpd
    </copy>
    ```

    Allow the initial inventory to arrive. Record any difference and ask the instructor to investigate stale or missing results.

## Task 2: Select the prepared package

1. Read the assigned package name and check whether it is installed:

    ```bash
    <copy>
    . /etc/rhel-to-oracle-linux/workshop.env
    printf 'Assigned package: %s\n' "$DEMO_PACKAGE"
    rpm -q "$DEMO_PACKAGE"
    </copy>
    ```

    A not-installed result and exit status `1` are expected. If it is already installed, ask the instructor for a different prepared exercise. Do not remove an existing package to manufacture work.

2. Verify the package is available through the service:

    ```bash
    <copy>
    workshop-management available-packages "$DEMO_PACKAGE"
    </copy>
    ```

    Require at least one matching available package before submission.

## Task 3: Submit and inspect the job

1. Submit one installation request:

    ```bash
    <copy>
    workshop-management install "$DEMO_PACKAGE"
    </copy>
    ```

    The helper records the request identifier and submission response under `$HOME/ol-migration-evidence/osmh`. Submission is not completion. If submission returns an error or no tracking identifier, stop and ask the instructor to inspect service history before retrying; a request could have reached the service.

2. Check the request:

    ```bash
    <copy>
    workshop-management job-status
    </copy>
    ```

    Repeat this read-only command about every 15 to 30 seconds. Continue only at `SUCCEEDED`. `ACCEPTED`, `IN_PROGRESS`, and `WAITING` are not completion. For `FAILED`, `CANCELED`, `CANCELING`, or `SKIPPED`, preserve the evidence and contact the instructor. Do not submit overlapping jobs.

3. Verify the package locally after success:

    ```bash
    <copy>
    rpm -q "$DEMO_PACKAGE"
    workshop-management inventory "$DEMO_PACKAGE"
    </copy>
    ```

    Local RPM output confirms installation. Service inventory may need time to refresh. Both checks must agree before closing the exercise.

## Task 4: Validate the application and record the result

1. Check the application and security state:

    ```bash
    <copy>
    bash /opt/rhel-to-oracle-linux/validate-system.sh
    </copy>
    ```

2. Record the selected package, request identifier, final request state, local RPM result, and application result. Keep the evidence files for Lab 6.

3. Explain the distinction: the helper submitted a service-managed job; the server's agent performed the package operation. This exercise did not configure centralized Ksplice jobs or maintenance schedules.

## Learn More

- [Managed package operations](https://docs.oracle.com/en-us/iaas/osmh/doc/packages-instances.htm).

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle.
- **Last Updated By/Date** - Workshop adaptation draft, October 19, 2026.
