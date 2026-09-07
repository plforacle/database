# Oracle Linux Value Navigator Version 2 Management Quick Start

**Time:** About 5 minutes  
**Use synthetic demonstration data only.**

Each account sees and manages only its own comparisons. Use a unique workshop username and a demonstration-only passphrase.

## 1. Open the application

Open:

```text
http://VERSION_2_PUBLIC_IP_ADDRESS/ol-value-navigator-2/
```

If this is your first visit, select **Register**, create a username and a passphrase of 12 to 128 characters, and select **Register**. If you already have an account, enter your username and password and select **Login**.

## 2. Create a comparison

1. In **Comparison name**, enter:

   ```text
   Management demonstration - YOUR NAME
   ```

2. In **RHEL SKU information**, paste:

   ```text
   DEMO-RHEL-STD | Demonstration RHEL standard support | Quantity 10 | Annual unit price USD 1200.00
   DEMO-RHEL-PREM | Demonstration RHEL premium support | Quantity 2 | Annual unit price USD 2400.00
   ```

3. In **Oracle Linux SKU information**, paste:

   ```text
   DEMO-OL-BASIC | Demonstration Oracle Linux basic support | Quantity 10 | Annual unit price USD 800.00
   DEMO-OL-PREM | Demonstration Oracle Linux premier support | Quantity 2 | Annual unit price USD 1600.00
   ```

4. Select **Save original inputs**.

## 3. Format with GenAI

1. Select **Format with GenAI** once.
2. Wait for the **Review and align** page. Do not refresh while GenAI is working.

## 4. Confirm the four lines

Set the **Group** and **Decision** for each SKU:

| SKU | Group | Decision |
| --- | ---: | --- |
| `DEMO-RHEL-STD` | `1` | **Confirmed** |
| `DEMO-OL-BASIC` | `1` | **Confirmed** |
| `DEMO-RHEL-PREM` | `2` | **Confirmed** |
| `DEMO-OL-PREM` | `2` | **Confirmed** |

Select **Save representative review**.

## 5. Calculate and review

1. Select **Calculate confirmed results**.
2. Confirm these results:

   | Period | RHEL | Oracle Linux | Difference |
   | --- | ---: | ---: | ---: |
   | Annual | `$16,800.00` | `$11,200.00` | `$5,600.00` |
   | Three years | `$50,400.00` | `$33,600.00` | `$16,800.00` |
   | Five years | `$84,000.00` | `$56,000.00` | `$28,000.00` |

## 6. Export

1. Select **Comparison**.
2. Under **Workbook actions**, select **Export CSV workbook**.
3. Open the downloaded CSV file.

4. Return to the application and select **Logout** when the demonstration is complete.

**Done.** You signed in, created, formatted, reviewed, calculated, and exported a user-owned demonstration comparison, then ended the session.
