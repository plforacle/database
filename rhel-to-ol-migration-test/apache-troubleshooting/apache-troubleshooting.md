# Lab 11: Configure and Troubleshoot Apache

## Introduction

Add a separate practice page and diagnose a controlled service outage. The migration page at `/var/www/html/index.html` remains unchanged. A brief Apache stop affects both pages; restore the service before finishing.

### Objectives

In this lab, you will:

- Configure an Apache alias for a separate practice page.
- Validate configuration before reloading the service.
- Inspect access logs, error logs, and systemd events.
- Recover from a stopped service and verify both pages.

Estimated Lab Time: 25 minutes

### Prerequisites

Complete Labs 7 and 10. Use the Oracle Linux destination VM as `cloud-user`. Keep the original migration evidence. The paths `workshop-practice.conf` and `/var/www/html/workshop-practice` are reserved for this exercise.

Use the guided steps if you are new to Linux. Experienced learners can attempt the independent challenge within the same lab time, then verify every completion check.

## Task 1: Verify the starting point

1. Confirm the original page and configuration are healthy:

    ```bash
    <copy>
    systemctl is-active httpd
    sudo apachectl configtest
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    sha256sum --check "$HOME/ol-migration-evidence/before/application-sha256.txt"
    </copy>
    ```

    Expect `active`, `Syntax OK`, the marker, and checksum OK. A warning about a missing fully qualified ServerName can accompany `Syntax OK`; a configuration error must be corrected before proceeding.

## Task 2: Add a separate practice page and alias

1. Create the practice content:

    ```bash
    <copy>
    sudo mkdir -p /var/www/html/workshop-practice
    printf '<html><body><h1>ORACLE_LINUX_PRACTICE_OK</h1></body></html>\n' \
      | sudo tee /var/www/html/workshop-practice/index.html >/dev/null
    sudo chmod 0755 /var/www/html/workshop-practice
    sudo chmod 0644 /var/www/html/workshop-practice/index.html
    sudo restorecon -Rv /var/www/html/workshop-practice
    </copy>
    ```

2. Create a dedicated Apache configuration file:

    ```bash
    <copy>
    sudo tee /etc/httpd/conf.d/workshop-practice.conf >/dev/null <<'EOF'
    Alias /practice/ /var/www/html/workshop-practice/
    <Directory "/var/www/html/workshop-practice">
        Require all granted
        Options -Indexes
    </Directory>
    EOF
    sudo apachectl configtest
    </copy>
    ```

    `Alias` maps a URL to a filesystem directory. `Require all granted` permits requests to this directory; `Options -Indexes` prevents automatic directory listings. The original document root remains intact.

3. Reload only after the configuration test succeeds:

    ```bash
    <copy>
    sudo apachectl configtest && sudo systemctl reload httpd
    curl --fail --silent http://127.0.0.1/practice/ | grep ORACLE_LINUX_PRACTICE_OK
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

    Both markers must appear. Open `http://<public-ip>/practice/` in your browser. If configtest fails, inspect `workshop-practice.conf`, compare it with Step 2, and correct it before reloading. To abandon just this alias, rename that file to `workshop-practice.conf.disabled`, run configtest, and reload; restore it to `.conf` and retest before the next lab.

## Task 3: Generate requests and inspect logs

1. Request the working page and a deliberately missing URL:

    ```bash
    <copy>
    curl --silent --output /dev/null --write-out "practice status=%{http_code}\n" \
      http://127.0.0.1/practice/
    curl --silent --output /dev/null --write-out "missing status=%{http_code}\n" \
      http://127.0.0.1/practice/not-a-real-file
    </copy>
    ```

    Expect status 200 for the practice page and 404 for the missing path.

2. Inspect the logs:

    ```bash
    <copy>
    sudo tail -20 /var/log/httpd/access_log
    sudo tail -20 /var/log/httpd/error_log
    sudo grep '/practice/' /var/log/httpd/access_log | tail -10
    sudo journalctl -u httpd -n 20 --no-pager
    </copy>
    ```

    Find the 200 and 404 requests in the access log. Not every 404 creates an error-log entry at the configured logging level. Use the journal for service lifecycle events, not as a substitute for the HTTP access log.

## Task 4: Diagnose and recover a controlled outage

1. Stop Apache on this practice instance:

    ```bash
    <copy>
    sudo systemctl stop httpd
    systemctl is-active httpd
    curl --fail --silent --show-error --max-time 5 http://127.0.0.1/practice/
    </copy>
    ```

    `inactive` and a connection failure are expected. Stop commands here interrupt the workshop web application; keep the SSH session open.

2. Diagnose without changing firewall or SELinux settings:

    ```bash
    <copy>
    systemctl status httpd --no-pager
    sudo ss -lntp
    sudo journalctl -u httpd -n 20 --no-pager
    sudo apachectl configtest
    </copy>
    ```

    A stopped service explains the missing listener. `systemctl status` returns a nonzero status for an inactive service; this is part of the exercise.

3. Recover and verify both pages:

    ```bash
    <copy>
    sudo apachectl configtest && sudo systemctl start httpd
    systemctl is-enabled httpd
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/practice/ | grep ORACLE_LINUX_PRACTICE_OK
    curl --fail --silent http://127.0.0.1/ | grep MIGRATION_WORKLOAD_OK
    </copy>
    ```

    If you interrupt the exercise after stopping Apache, run this recovery step before doing anything else.

## Task 5: Complete the independent challenge and checkpoint

1. Challenge: explain the differences between a connection failure, HTTP 403, HTTP 404, and a configuration syntax error. Identify the first command or log you would use for each. Beginners can use the examples above for connection failure and 404; Lab 12 demonstrates 403.

2. Record the cause and recovery of the service outage in `$HOME/ol-practice-reports/apache-incident.txt`. Check the original checksum again:

    ```bash
    <copy>
    sha256sum --check "$HOME/ol-migration-evidence/before/application-sha256.txt"
    systemctl is-active httpd
    </copy>
    ```

3. Complete when both pages work, Apache is enabled and active, and the original checksum is OK. Keep the practice page and configuration for Labs 12 and 13. Continue to Lab 12.

## Learn More

- [Apache configuration control](https://httpd.apache.org/docs/2.4/programs/apachectl.html)
- [Apache log files](https://httpd.apache.org/docs/2.4/logs.html)
- [Apache Alias directive](https://httpd.apache.org/docs/2.4/mod/mod_alias.html#alias)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
