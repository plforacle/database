# Lab 6: Test, Deploy, and Demonstrate the Complete Application Workflow

## Introduction

This lab walks you through testing the complete Oracle Linux Value Navigator workflow and rehearsing a clear demonstration of the prototype.

Estimated Time: 60 minutes

### About Prototype Verification

A successful installation is not enough. The final checks verify HeatWave GenAI formatting, saved-comparison behavior, calculations, unresolved-line handling, manual fallback, and safe error behavior.

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
* Demonstration RHEL and Oracle Linux input scenarios.
* Browser and SSH access to the workshop environment.

*This is the fold. The remaining sections are collapsed by default.*

## Task 1: Test the core scenarios

1. Test a comparison containing demonstration RHEL and Oracle Linux freeform input.

2. Verify the annual, three-year, and five-year totals manually.

3. Test missing and ambiguous values and confirm that the application withholds comparative totals.

4. Test an excluded line and confirm that its reason remains visible while its amount is excluded from comparative totals.

5. Test an invalid `ML_GENERATE` response and confirm that manual input remains available.

6. Reopen the saved comparison and confirm that its input, reviewed lines, decisions, and result snapshot remain available.

## Task 2: Verify the deployed browser path

1. Open the application through the OCI instance web address.

2. Confirm that Apache serves the PHP pages and connects to the private HeatWave DB System.

3. Confirm that invalid input produces a helpful message without exposing database credentials, connection details, or pasted source text.

4. Restart Apache and confirm that the application reconnects to HeatWave.

    ```bash
    <copy>sudo systemctl restart httpd
    sudo systemctl status httpd --no-pager</copy>
    ```

## Task 3: Rehearse the demonstration

1. Explain the business problem and the subscription-cost comparison boundary.

2. Paste small demonstration RHEL and Oracle Linux scenarios.

3. Review and confirm the suggested lines.

4. Show the annual, three-year, and five-year comparison results.

5. Demonstrate one unresolved value and explain why the application withholds totals.

6. Reopen the saved comparison and explain how MySQL HeatWave provides workbook-like persistence.

7. State that the prototype is not a quote, licensing determination, full TCO model, or production customer-data system.

## Task 4: Record the next improvements

1. Record the approved calculation-rule owner, source, and version required before broader use.

2. Record the authentication, audit, privacy, network, and operational work required after the prototype demonstration.

3. Preserve the demonstration data and keep customer information out of the repository.

## Learn More

* [Oracle Linux documentation](https://docs.oracle.com/en/operating-systems/oracle-linux/)
* [OCI Compute documentation](https://docs.oracle.com/en-us/iaas/Content/Compute/home.htm)
* [MySQL HeatWave GenAI](https://dev.mysql.com/doc/heatwave/en/mys-hw-genai-overview.html)

## Acknowledgements

* **Author** - Perside Foster, Mark Atkinson, Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, August 2026
