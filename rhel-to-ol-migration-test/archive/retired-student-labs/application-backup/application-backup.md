# Lab 11: Back Up and Restore the Application

## Introduction

Back up application content and Apache configuration using a local tar archive. Change only a practice page and its dedicated alias configuration, then restore and verify them. This is an application recovery exercise, separate from the optional pre-conversion VM backup.

### Objectives

In this lab, you will:

- Archive website files, Apache configuration, and secure practice content.
- Create a checksum manifest and inspect archive contents.
- Restore selected files after a controlled change.
- Verify configuration, ownership, SELinux labels, and application responses.

Estimated Lab Time: 20 minutes

### Prerequisites

Complete Lab 10 with `/practice/`, `/secure-practice/`, and the original page working. This lab keeps its archives in `$HOME/ol-practice-backups`; migration evidence remains unchanged.

Follow the guided path if you are new to the tools. Experienced learners can attempt the independent challenge within the same lab time and must still pass the completion checks.

## Task 1: Verify the application and select a new archive

1. Confirm all pages and the configuration are healthy:

    ```bash
    <copy>
    sudo apachectl configtest
    curl --fail --silent http://127.0.0.1/practice/ | grep ORACLE_LINUX_PRACTICE_OK
    curl --fail --silent http://127.0.0.1/secure-practice/ | grep SECURE_PRACTICE_OK
    sha256sum --check "$HOME/ol-migration-evidence/before/application-sha256.txt"
    df -h /
    </copy>
    ```

2. Create a timestamped backup path:

    ```bash
    <copy>
    mkdir -p "$HOME/ol-practice-backups"
    BACKUP="$HOME/ol-practice-backups/application-$(date +%Y%m%dT%H%M%S).tar.gz"
    printf '%s\n' "$BACKUP" | tee "$HOME/ol-practice-backups/latest-backup-path.txt"
    </copy>
    ```

    Keep this archive until restoration and verification finish. A timestamp avoids replacing a previous known-good archive on a later run.

## Task 2: Archive and inspect the application

1. Capture checksums of the two files that will be changed:

    ```bash
    <copy>
    sudo sha256sum /var/www/html/workshop-practice/index.html \
      /etc/httpd/conf.d/workshop-practice.conf \
      > "$HOME/ol-practice-backups/practice-before.sha256"
    </copy>
    ```

2. Create the archive with file metadata:

    ```bash
    <copy>
    sudo tar --selinux --acls --xattrs -czf "$BACKUP" -C / \
      var/www/html etc/httpd srv/workshop-secure
    sudo chown "$(id -u):$(id -g)" "$BACKUP"
    sudo tar -tzf "$BACKUP" | sed -n '1,25p'
    sudo ls -lh "$BACKUP"
    </copy>
    ```

    The archive contains paths relative to `/`, including both practice files. `--selinux`, `--acls`, and `--xattrs` retain supported metadata; permissions and ownership are recorded by tar. The following ownership change makes your self-created archive readable by your login account for download. This static site has no database transactions to quiesce. A production application may need additional consistency steps.

3. Confirm the files needed for this recovery are present:

    ```bash
    <copy>
    sudo tar -tzf "$BACKUP" | grep -E "^(var/www/html/workshop-practice/index.html|etc/httpd/conf.d/workshop-practice.conf)$"
    sudo gzip -t "$BACKUP"
    </copy>
    ```

    Both file paths must appear and gzip integrity checking must return no error. Do not introduce the controlled change until the archive checks pass. A local archive on this VM does not protect against losing the VM or disk.

## Task 3: Introduce a controlled change

1. Replace only the practice page content and add a harmless configuration comment:

    ```bash
    <copy>
    printf '<html><body><h1>CHANGED_FOR_RESTORE_EXERCISE</h1></body></html>\n' \
      | sudo tee /var/www/html/workshop-practice/index.html >/dev/null
    printf '\n# Changed for restore exercise\n' \
      | sudo tee -a /etc/httpd/conf.d/workshop-practice.conf >/dev/null
    sudo apachectl configtest
    curl --fail --silent http://127.0.0.1/practice/ | grep CHANGED_FOR_RESTORE_EXERCISE
    sudo sha256sum --check "$HOME/ol-practice-backups/practice-before.sha256"
    </copy>
    ```

    The checksum check must report both files as FAILED. That failure is expected and establishes that a restore is needed. The original migration page is not modified.

## Task 4: Restore selected files and verify recovery

1. Reload the archive path and restore the two exercise files:

    ```bash
    <copy>
    BACKUP="$(cat "$HOME/ol-practice-backups/latest-backup-path.txt")"
    sudo tar --selinux --acls --xattrs -xzf "$BACKUP" -C / \
      var/www/html/workshop-practice/index.html \
      etc/httpd/conf.d/workshop-practice.conf
    sudo restorecon -Rv /var/www/html/workshop-practice \
      /etc/httpd/conf.d/workshop-practice.conf
    sudo apachectl configtest && sudo systemctl reload httpd
    </copy>
    ```

    Restore only from the archive you created in this lab. The absolute destination is intentional: the stored paths are relative to the filesystem root. `restorecon` reconciles restored files with the current SELinux policy. If interrupted during Task 3, return to this recovery step.

2. Verify the restored files and every application endpoint:

    ```bash
    <copy>
    sudo sha256sum --check "$HOME/ol-practice-backups/practice-before.sha256"
    ls -lZ /var/www/html/workshop-practice/index.html
    systemctl is-active httpd
    curl --fail --silent http://127.0.0.1/practice/ | grep ORACLE_LINUX_PRACTICE_OK
    curl --fail --silent http://127.0.0.1/secure-practice/ | grep SECURE_PRACTICE_OK
    sha256sum --check "$HOME/ol-migration-evidence/before/application-sha256.txt"
    </copy>
    ```

    Both restored-file checks must be OK, Apache active, both practice markers present, and the original migration-page checksum OK. If not, inspect archive membership, file permissions, labels, and the dedicated Apache configuration before changing other settings.

## Task 5: Complete the independent challenge and checkpoint

1. Challenge: explain why an archive on the same disk is insufficient protection against disk loss. Describe when you would restore selected files instead of an entire system. Identify which metadata the archive retains.

2. Record the archive path, restored files, and verification outcome in `$HOME/ol-practice-reports/restore-result.txt`. Keep the archive and checksum manifest.

3. Complete when the content and configuration checksums match and all three pages work. Continue to Lab 12.

## Learn More

- [GNU tar manual](https://www.gnu.org/software/tar/manual/)
- [Apache configuration checking](https://httpd.apache.org/docs/2.4/programs/apachectl.html)
- [SELinux file contexts](https://docs.oracle.com/en/operating-systems/oracle-linux/selinux/selinux-ChangingtheDefaultFileType.html)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
