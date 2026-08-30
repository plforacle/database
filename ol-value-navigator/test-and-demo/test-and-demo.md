# Lab 6: Test, Deploy, and Demonstrate the Application

## Introduction

This lab walks you through testing the complete Oracle Linux Value Navigator workflow and rehearsing a clear demonstration of the prototype.

Estimated Time: 60 minutes

### About Prototype Verification

A successful installation is not enough. The final checks verify the browser workflow, database behavior, calculations, unresolved-line handling, manual fallback, and safe error behavior.

### Objectives

In this lab, you will:

* Test confirmed, excluded, incomplete, and unresolved scenarios.
* Reconcile the displayed totals manually.
* Verify the deployed browser path.
* Rehearse the complete demonstration.
* Record the remaining work required for broader use.

### Prerequisites

This lab assumes you have:

* A deployed Oracle Linux Value Navigator prototype.
* Demonstration catalog data and test scenarios.
* Browser and SSH access to the workshop environment.

*This is the fold. The remaining sections are collapsed by default.*

## Task 1: Test the core scenarios

1. Test a confirmed comparison containing only demonstration catalog lines.

2. Verify the annual, three-year, and five-year totals manually.

3. Test an unknown SKU and confirm that the application withholds comparative totals.

4. Test an excluded line and confirm that its reason remains visible while its amount is excluded from comparative totals.

5. Test an AI failure and confirm that manual input remains available.

## Task 2: Verify the deployed browser path

1. Open the application through the OCI instance web address.

2. Confirm that Apache serves the PHP pages and the database-backed catalog choices load.

3. Confirm that invalid input produces a helpful message without exposing database credentials, connection details, or pasted source text.

4. Restart Apache and MySQL and confirm that the application returns to service.

    ```bash
    <copy>sudo systemctl restart httpd mysqld
    sudo systemctl status httpd mysqld --no-pager</copy>
    ```

## Task 3: Rehearse the demonstration

1. Explain the business problem and the subscription-cost comparison boundary.

2. Enter or paste a small demonstration scenario.

3. Review and confirm the suggested lines.

4. Show the annual, three-year, and five-year comparison results.

5. Demonstrate one unresolved SKU and explain why the application withholds totals.

6. State that the prototype is not a quote, licensing determination, full TCO model, or customer-data system.

## Task 4: Record the next improvements

1. Record the authorized catalog owner, source, effective date, and price version required before broader use.

2. Record the authentication, audit, privacy, network, and operational work required after the prototype demonstration.

3. Preserve the demonstration data and keep customer information out of the repository.

## Learn More

* [Oracle Linux documentation](https://docs.oracle.com/en/operating-systems/oracle-linux/)
* [OCI Compute documentation](https://docs.oracle.com/en-us/iaas/Content/Compute/home.htm)

## Acknowledgements

* **Author** - Perside Foster, Mark Atkinson, Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, August 2026
