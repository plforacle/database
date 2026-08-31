# Lab 4: Use MySQL HeatWave GenAI to Format Complete Inputs and Build the Representative Review Page

## Introduction

This lab walks you through sending the complete RHEL and Oracle Linux freeform SKU text to MySQL HeatWave GenAI, validating structured line suggestions, and requiring representative review before calculation.

Estimated Time: 75 minutes

### About AI-Assisted Input

AI accelerates the conversion of complete freeform SKU text into candidate fields. The source text can include SKUs, descriptions, quantities, prices, and notes. AI does not approve SKUs, prices, mappings, exclusions, or results. The PHP application validates every response and provides manual entry when AI is unavailable.

### Objectives

In this lab, you will:

* Process the complete text from both bounded freeform SKU inputs.
* Define a structured AI response contract.
* Call `sys.ML_GENERATE` through PDO.
* Validate structured AI suggestions.
* Build representative confirmation, correction, and exclusion actions.
* Provide a manual-entry fallback.

### Prerequisites

This lab assumes you have:

* The working input and saved-comparison pages from Lab 3.
* An active MySQL HeatWave DB System configured for MySQL HeatWave GenAI from Lab 1.
* Demonstration input that contains no real customer information.

*This is the fold. The remaining sections are collapsed by default.*

## Task 1: Prepare both complete freeform inputs

1. Use the separate RHEL and Oracle Linux freeform text areas created in Lab 3. Each text area accepts complete SKU text, including any SKUs, descriptions, quantities, prices, and notes.

2. Display a reminder that the prototype does not accept real customer information.

3. Limit the accepted input size before sending it to the AI service.

4. Save the inputs only inside the representative's saved comparison. Do not write them to application or web-server logs.

## Task 2: Define the AI response contract

1. Define the same structured response contract for either complete freeform input.

    ```json
    {
      "lines": [
        {
          "sku": "DEMO-RHEL-STD",
          "quantity": "10",
          "description": "Demonstration RHEL standard support",
          "supplied_annual_price": "1200.00",
          "confidence": "high",
          "warnings": []
        }
      ]
    }
    ```

2. Reject a response that is malformed, incomplete, oversized, or contains unsupported fields.

3. Instruct the model to extract only supplied values, use `null` for missing values, avoid equivalence claims, and return JSON only.

## Task 3: Call MySQL HeatWave GenAI

1. Build a PHP prompt containing the response contract, extraction rules, input-side label, and the complete untrusted freeform text from that side.

2. Execute a prepared SQL statement through PDO.

    ```sql
    <copy>SELECT sys.ML_GENERATE(
      :prompt,
      JSON_OBJECT(
        'task', 'generation',
        'model_id', 'mistral-7b-instruct-v3',
        'temperature', 0
      )
    ) AS ai_response;</copy>
    ```

3. Decode the outer `ML_GENERATE` response, extract its `text` value, and decode the structured JSON returned by the model.

4. Reject invalid JSON, unsupported fields, oversized responses, nonnumeric quantities or prices, and values not supported by the source text.

5. Insert accepted candidates into `comparison_line` with status `AI_SUGGESTED` and record an `AI_FORMATTING_COMPLETED` event.

## Task 4: Build the representative review page

1. Show every RHEL and Oracle Linux candidate beside the complete original source text from its input.

2. Allow the representative to correct SKU, description, quantity, and annual unit price.

3. Allow the representative to align related lines, confirm a line, or exclude it with a reason.

4. Save the representative decision separately from the original AI suggestion.

5. Continue to withhold totals while an included line remains unresolved.

## Task 5: Add and test manual fallback

1. Provide a manual-entry action when the AI service times out or returns an invalid response.

2. Apply the same field validation and representative-review rules to manually entered lines.

3. Test a valid demonstration example, an unknown SKU, an unavailable AI service, and pasted prompt-injection text.

4. Confirm that pasted text is always treated as untrusted data and never as an application instruction.

## Learn More

* [MySQL HeatWave GenAI](https://dev.mysql.com/doc/heatwave/en/mys-hw-genai-overview.html)
* [ML_GENERATE](https://dev.mysql.com/doc/heatwave/en/mys-hwgenai-ml-generate.html)
* [PHP JSON functions](https://www.php.net/manual/en/book.json.php)

## Acknowledgements

* **Author** - Perside Foster, Mark Atkinson, Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, August 2026
