# Lab 5: Add AI-Assisted Input and Representative Review

## Introduction

This lab walks you through accepting pasted demonstration text, requesting structured line suggestions from an approved AI service, and requiring representative review before calculation.

Estimated Time: 75 minutes

### About AI-Assisted Input

AI accelerates the conversion of freeform text into candidate fields. It does not approve SKUs, prices, mappings, exclusions, or results. The PHP application validates every response and provides manual entry when AI is unavailable.

### Objectives

In this lab, you will:

* Add a bounded freeform input page.
* Define a structured AI response contract.
* Validate AI suggestions against the workshop catalog.
* Build representative confirmation, correction, and exclusion actions.
* Provide a manual-entry fallback.

### Prerequisites

This lab assumes you have:

* A working comparison and results workflow.
* Access to an approved AI service for the workshop, or permission to use the manual fallback only.
* Demonstration input that contains no real customer information.

*This is the fold. The remaining sections are collapsed by default.*

## Task 1: Add the paste input page

1. Add a text area where the representative can paste demonstration RHEL subscription information.

2. Display a reminder that the prototype does not accept real customer information.

3. Limit the accepted input size before sending it to the AI service.

4. Do not write pasted input, session values, or AI responses to application logs.

## Task 2: Define the AI response contract

1. Define a structured response with these candidate fields.

    ```json
    {
      "lines": [
        {
          "sku": "DEMO-RHEL-STD",
          "quantity": "10",
          "description": "Demonstration RHEL standard support",
          "supplied_annual_price": "1200.00",
          "source_excerpt": "DEMO-RHEL-STD quantity 10"
        }
      ]
    }
    ```

2. Reject a response that is malformed, incomplete, oversized, or contains unsupported fields.

3. Keep database credentials, catalog-write access, calculation authority, and approval authority outside the AI integration.

## Task 3: Build the representative review page

1. Show every AI candidate beside its exact, possible, or unresolved catalog match.

2. Allow the representative to correct the candidate SKU and quantity.

3. Allow the representative to confirm a governed match or exclude the line with a reason.

4. Save the representative decision separately from the original AI suggestion.

5. Continue to withhold totals while an included line remains unresolved.

## Task 4: Add and test manual fallback

1. Provide a manual-entry action when the AI service times out or returns an invalid response.

2. Apply the same catalog validation and review rules to manually entered lines.

3. Test a valid demonstration example, an unknown SKU, an unavailable AI service, and pasted prompt-injection text.

4. Confirm that pasted text is always treated as untrusted data and never as an application instruction.

## Learn More

* [OCI Generative AI documentation](https://docs.oracle.com/en-us/iaas/Content/generative-ai/home.htm)
* [PHP JSON functions](https://www.php.net/manual/en/book.json.php)

## Acknowledgements

* **Author** - Perside Foster, Mark Atkinson, Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, August 2026
