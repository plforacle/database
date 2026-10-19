# Need Help?

## Introduction

Use your instructor's support channel for the assigned server. Keep the SSH error, failing command, exit status, and relevant log path available. Do not post private keys, credentials, or complete confidential logs in shared chat.

Estimated Time: as needed.

### Objectives

- Identify the failing checkpoint.
- Preserve evidence and request the correct recovery help.

## Task 1: Check the connection

1. Confirm that your SSH command uses the instructor-supplied hostname, username, and key path. For a permission-denied error, ask the instructor to verify the account and key assignment.

2. After a reboot, retry SSH within the instructor's waiting window. Report a timeout that exceeds that window. Verify unexpected host-key changes with the instructor before proceeding.

3. If SSH disconnected during a migration command, reconnect and try `tmux attach -t migration`. Inspect the original session before starting any new command that changes the OS. Rebooting ends the tmux session.

## Task 2: Report the failed checkpoint

1. For a failed dry run or conversion, record the exit status and script log path. Do not bypass readiness checks, delete repositories, or rerun a failed conversion blindly.

2. For Ksplice errors, record the kernel RPM vendor, base kernel release, and error text. No available patches and a connection or entitlement error are different outcomes.

3. For OS Management Hub errors, preserve `$HOME/ol-migration-evidence/osmh` and ask the instructor to check registration and service history. A timed-out submission might still have created a job. Do not submit another job until it is reconciled.

4. For failed capture or validation, preserve the output and snapshot directory. The instructor handles repairs, recovery, and any reset needed before retrying.

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle.
- **Last Updated By/Date** - Workshop adaptation draft, October 19, 2026.
