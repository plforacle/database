# Oracle Linux Value Navigator Management Quick Start

**Time:** About 5 minutes  
**Use synthetic demonstration data only.**

> **Version 2 update:** User login will be available by Monday, September 7, 2026. Version 1 does not have login or individual comparison ownership, so all current users share the saved-comparisons list.

## 1. Open the application

Open:

```text
http://129.80.238.67/ol-value-navigator/
```

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

1. Optionally select **Edit customer details** to add a demonstration customer name, objective, scope, and recommended next step. Save, then select **View results**. This does not change the calculated amounts.
2. Select **Download PowerPoint** for four editable summary slides.
3. Select **Comparison**, then **Export CSV workbook** for the complete supporting data.

**Done.** You created, formatted, reviewed, calculated, and exported a complete demonstration comparison.
