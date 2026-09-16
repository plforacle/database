# Lab 4: Enter, Import, and Review Subscription Information

## Introduction

> **Version 3 authoring draft.** This lab is not ready for deployment or a learner run. Read the prerequisites and pending checkpoints before executing anything. Version 1 remains unchanged.

Keep source information separate from reviewed decisions. AI may suggest fields, but the representative decides what to adopt.

Estimated Time: Pending the integrated workflow rehearsal.

### Objectives

* Review the current import and extraction paths.
* Distinguish source quantities, prices, and assumptions.
* Verify a manual fallback without calling it live AI.

## Task 1: Choose the input path

1. Use invented RHEL subscription information. Do not enter real customer, contract, or personal information.

2. Identify the intended scenario workflow under `/demo/coverage`. The reviewed code supports CSV, TSV, XLSX, and image review with manual transcription.

3. For spreadsheets, map the columns and confirm the quantity and price basis. A quantity in a report does not automatically establish purchased entitlements or physical host counts.

4. Keep unknown values unresolved. Do not invent a price, silently remove a repeated purchase, or treat an unreviewed formula as an adopted value.

    **Current boundary:** PDF import and automatic OCR are not implemented. The parser applies an 8 MB input limit and a 2,000-row limit; target server limits must also be tested.

## Task 2: Verify the AI connection separately

1. Inspect the upstream analysis extraction path, `/demo/analyses/{analysisId}/extract`, and its HeatWave client. It calls `sys.ML_GENERATE`.

2. Record that this path is separate from the newer scenario import workflow. Do not describe the existing parser as AI extraction.

3. After integration is implemented, submit a synthetic text example and review every suggestion before adoption.

4. Test malformed responses and an unavailable AI service. Use manual entry when needed, and record the check as fallback rather than successful live AI.

    **Pending checkpoint:** The unified input journey needs implementation and live verification on V3.

## Task 3: Review before calculating

1. Confirm product identity, quantity meaning, the selected price basis, comparison scope, and any excluded rows.

2. Record explicit reasons for corrections and assumptions. Preserve the original source evidence.

3. Save unfinished work without treating it as a confirmed result.

4. Confirm that unresolved required values prevent a misleading total. The application must explain what remains to be reviewed.

    **Pending checkpoint:** Exact buttons, screenshots, and the shortest learner example will come from the accepted candidate, not Version 1's interface.

## Acknowledgements

* **Authors** - Perside Foster, Mark Atkinson, and Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, September 2026
