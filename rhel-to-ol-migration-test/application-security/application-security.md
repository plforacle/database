# Lab 12: Manage Application Security

## Introduction

Diagnose access problems caused by ordinary file permissions and SELinux labels, then compare runtime and permanent firewall rules. Exercises affect dedicated practice content. Keep SELinux enforcing, the original page available, and SSH unchanged.

### Objectives

In this lab, you will:

- Distinguish Linux permissions from SELinux authorization.
- Use a persistent SELinux file-context rule for separate web content.
- Inspect the active firewall zone and practice runtime/permanent rules.
- Recover from controlled permission and labeling faults.

Estimated Lab Time: 30 minutes

### Prerequisites

Complete Lab 11 on the Oracle Linux destination VM. The `/practice/` page and original migration page must work. This lab reserves `/srv/workshop-secure`, `workshop-security.conf`, and TCP port 8088 for practice.

Follow the guided path if you are new to the tools. Experienced learners can attempt the independent challenge within the same lab time and must still pass the completion checks.

## Task 1: Inspect the security baseline

1. Inspect permissions and SELinux state:

    ```bash
    <copy>
    getenforce
    ls -ldZ /var/www/html /var/www/html/workshop-practice
    ls -lZ /var/www/html/workshop-practice/index.html
    sudo firewall-cmd --state
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

    Expect `Enforcing`, a running firewall, and the marker. Stop if SELinux is permissive or disabled; this exercise requires enforcement to demonstrate a denial.

2. Install the SELinux management command from Oracle repositories:

    ```bash
    <copy>
    sudo dnf install policycoreutils-python-utils
    command -v semanage
    </copy>
    ```

    Review the transaction before confirming. This installs management tools; it does not disable enforcement.

## Task 2: Diagnose ordinary file permissions

1. Remove read permissions only from the practice page and request it:

    ```bash
    <copy>
    sudo chmod 000 /var/www/html/workshop-practice/index.html
    ls -l /var/www/html/workshop-practice/index.html
    sudo -u apache test -r /var/www/html/workshop-practice/index.html
    printf "Apache read test exit status: %s\n" "$?"
    curl --silent --output /dev/null --write-out "practice status=%{http_code}\n" \
      http://127.0.0.1/practice/
    </copy>
    ```

    The read test should return 1 and the HTTP request 403. The service can be active even when one file is unreadable.

2. Recover the file permission:

    ```bash
    <copy>
    sudo chmod 0644 /var/www/html/workshop-practice/index.html
    curl --fail --silent http://127.0.0.1/practice/ | grep ORACLE_LINUX_PRACTICE_OK
    </copy>
    ```

    Recover immediately with this step if interrupted. Do not use `chmod 777`; the web server only needs to read this static file.

## Task 3: Diagnose and persist an SELinux label

1. Create separate content and an alias, with normal directory/file permissions:

    ```bash
    <copy>
    sudo mkdir -p /srv/workshop-secure
    printf '<html><body><h1>SECURE_PRACTICE_OK</h1></body></html>\n' \
      | sudo tee /srv/workshop-secure/index.html >/dev/null
    sudo chmod 0755 /srv/workshop-secure
    sudo chmod 0644 /srv/workshop-secure/index.html
    sudo tee /etc/httpd/conf.d/workshop-security.conf >/dev/null <<'EOF'
    Alias /secure-practice/ /srv/workshop-secure/
    <Directory "/srv/workshop-secure">
        Require all granted
        Options -Indexes
    </Directory>
    EOF
    sudo apachectl configtest && sudo systemctl reload httpd
    </copy>
    ```

2. Apply an intentionally unsuitable label only to this practice file, then request it:

    ```bash
    <copy>
    sudo chcon -t user_home_t /srv/workshop-secure/index.html
    ls -lZ /srv/workshop-secure/index.html
    curl --silent --output /dev/null --write-out "secure status=%{http_code}\n" \
      http://127.0.0.1/secure-practice/
    sudo tail -15 /var/log/httpd/error_log
    sudo journalctl -k --since "5 minutes ago" --no-pager | tail -20
    </copy>
    ```

    With the workshop SELinux policy, expect 403 even though mode 0644 permits reading. Inspect the `user_home_t` label. Kernel/audit logging varies, so the absence of an AVC in this journal view does not prove that SELinux allowed access. Do not disable SELinux to make the request succeed.

3. Define the persistent web-content label and apply it:

    ```bash
    <copy>
    sudo semanage fcontext -a -t httpd_sys_content_t '/srv/workshop-secure(/.*)?'
    sudo restorecon -Rv /srv/workshop-secure
    ls -lZ /srv/workshop-secure/index.html
    curl --fail --silent http://127.0.0.1/secure-practice/ | grep SECURE_PRACTICE_OK
    </copy>
    ```

    If rerunning and the rule is already defined, use `semanage fcontext -m` with the same type and path instead of `-a`. Both the directory and file need suitable labels. `semanage` records the mapping; `restorecon` applies it to existing files.

4. Verify persistence and the original workload:

    ```bash
    <copy>
    sudo semanage fcontext -l -C | grep '/srv/workshop-secure'
    sudo restorecon -Rv /srv/workshop-secure
    getenforce
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

    The secure marker must respond and SELinux must remain Enforcing. For recovery, use Step 3. If a syntax error prevents Apache starting, correct `workshop-security.conf` from Step 1, run configtest, and start the service.

## Task 4: Compare firewall rule scopes without changing SSH

1. Identify the network interface and zone:

    ```bash
    <copy>
    IFACE="$(ip -4 route show default | awk 'NR == 1 {print $5}')"
    ZONE="$(sudo firewall-cmd --get-zone-of-interface="$IFACE" 2>/dev/null)" \
      || ZONE="$(sudo firewall-cmd --get-default-zone)"
    printf 'Interface=%s Zone=%s\n' "$IFACE" "$ZONE"
    printf '%s\n' "$ZONE" > "$HOME/ol-practice-reports/firewall-zone.txt"
    sudo firewall-cmd --get-active-zones
    sudo firewall-cmd --zone="$ZONE" --list-all
    </copy>
    ```

    Match the interface to the listed zone before continuing. Do not change zone assignments or the existing SSH/HTTP service rules.

2. Check that port 8088 has no existing runtime or permanent rule:

    ```bash
    <copy>
    sudo firewall-cmd --zone="$ZONE" --query-port=8088/tcp
    sudo firewall-cmd --permanent --zone="$ZONE" --query-port=8088/tcp
    </copy>
    ```

    Both must say `no`, with nonzero status expected. If either says `yes`, skip the port exercise and ask the instructor for an unused practice port; do not remove someone else's rule.

3. Add a runtime rule and compare the two views:

    ```bash
    <copy>
    sudo firewall-cmd --zone="$ZONE" --add-port=8088/tcp
    sudo firewall-cmd --zone="$ZONE" --query-port=8088/tcp
    sudo firewall-cmd --permanent --zone="$ZONE" --query-port=8088/tcp
    </copy>
    ```

    Runtime should say `yes`; permanent should still say `no`. No server is started on this practice port. A firewall allowance does not create a listening service or modify an OCI network rule.

4. Add the same permanent rule, then inspect it:

    ```bash
    <copy>
    sudo firewall-cmd --permanent --zone="$ZONE" --add-port=8088/tcp
    sudo firewall-cmd --permanent --zone="$ZONE" --query-port=8088/tcp
    </copy>
    ```

    Permanent should now say `yes`. A future firewall reload or restart loads permanent configuration into runtime. This exercise does not require a reload.

5. Remove only the two practice rules you just created:

    ```bash
    <copy>
    ZONE="$(cat "$HOME/ol-practice-reports/firewall-zone.txt")"
    sudo firewall-cmd --zone="$ZONE" --remove-port=8088/tcp
    sudo firewall-cmd --permanent --zone="$ZONE" --remove-port=8088/tcp
    sudo firewall-cmd --zone="$ZONE" --query-port=8088/tcp
    sudo firewall-cmd --permanent --zone="$ZONE" --query-port=8088/tcp
    </copy>
    ```

    Both queries should again say `no`. If interrupted after adding a practice rule, return to this step using the recorded zone. Do not flush rules or disable the firewall.

## Task 5: Complete the independent challenge and checkpoint

1. Challenge: explain why an active Apache process can return 403, distinguish a file-mode problem from an SELinux label problem, and predict which firewall configuration survives a restart. Use the fault and recovery observations as evidence.

2. Verify the completed state:

    ```bash
    <copy>
    getenforce
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/practice/ | grep ORACLE_LINUX_PRACTICE_OK
    curl --fail --silent http://127.0.0.1/secure-practice/ | grep SECURE_PRACTICE_OK
    sha256sum --check "$HOME/ol-migration-evidence/before/application-sha256.txt"
    </copy>
    ```

3. Complete when all pages work, the original checksum is OK, SELinux is Enforcing, and the temporary 8088 rules have been removed. Keep the secure alias and file-context rule for the next lab. Record your findings in `$HOME/ol-practice-reports/security-findings.txt` and continue to Lab 13.

## Learn More

- [Persistent SELinux file labels](https://docs.oracle.com/en/operating-systems/oracle-linux/selinux/selinux-ChangingtheDefaultFileType.html)
- [firewalld runtime and permanent configuration](https://firewalld.org/documentation/configuration/runtime-versus-permanent.html)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
