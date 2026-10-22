# Configure Access to the Planned MySQL Subnet

## Introduction

Add a MySQL ingress rule to the private subnet's security list. The rule prepares access from a future client in the public subnet.

Estimated Time: 10 minutes

### Objectives

- Identify the security list associated with the private subnet.
- Add a stateful rule for MySQL Classic traffic.
- Verify the saved source CIDR and destination port.

### Prerequisites

Complete Lab 1. Keep the VCN details page open.

## Task 1: Select the destination subnet security list

1. In **ForMySQL**, open **Subnets**, then select the **private** subnet with CIDR **10.0.1.0/24**.

2. Open the subnet's **Security** tab, or its **Security Lists** resource section. Select the security list associated with this private subnet. Its generated name usually contains **Security List for Private Subnet**.

    Check the subnet association instead of relying only on the security list name. Do not select the public subnet's default security list for this managed MySQL path.

3. Open **Ingress Rules** and select **Add Ingress Rules**.

## Task 2: Add the MySQL rule

1. Enter these values in the rule form.

    | Field | Value |
    | --- | --- |
    | Stateless | Off, leaving the rule stateful |
    | Source Type | CIDR |
    | Source CIDR | 10.0.0.0/24 |
    | IP Protocol | TCP |
    | Source Port Range | Leave blank, meaning all source ports |
    | Destination Port Range | 3306 |
    | Description | MySQL Classic from workshop client subnet |

    Use the public subnet CIDR you selected in Lab 1 if you changed the example address plan. A future client with a fixed private address can use a narrower /32 source rule.

2. Check the source and destination fields, then select **Add Ingress Rules** to save the rule. The destination port is **3306**; do not put it in the source port field.

3. Return to the ingress rule list. Confirm a saved stateful TCP rule with source **10.0.0.0/24** and destination port **3306**. Capture your own screenshot of this saved rule for your lab record.

    The supplied recording ends without a clear saved-rule confirmation, so the learner's rule list is the verification checkpoint.

## Task 3: Review the source differences

1. Compare your saved configuration with the recording's form at 01:30: the recording enters **0.0.0.0/0** in the public subnet's default security list. Your configuration intentionally uses the private subnet and an authorized client source range.

    The recording's ingress-form screenshot is retained in the authoring evidence rather than shown here because it displays different rule values.

2. Keep destination port **3306** for this exercise. If a later lab requires MySQL X Protocol, add a separate scoped rule for **33060** at that time.

3. Continue to Lab 3. There is no DB endpoint to connect to yet; this checkpoint verifies the saved rule only.

## Learn More

- [Oracle managed MySQL ingress rule procedure](https://docs.oracle.com/en-us/iaas/mysql-database/doc/adding-ingress-rules-compute-instance-bastion-session-or-vpn-connection.html)

## Acknowledgements

- **Author** - Shay Hameed, Cloud Engineer, Oracle
- **Last Updated By/Date:** Workshop adaptation, October 2026.