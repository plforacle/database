# RHEL to Oracle Linux

## Introduction

You are responsible for moving an existing RHEL server to Oracle Linux while preserving its application. Your instructor provides the server and recovery arrangements. You connect through SSH, assess the source, perform an in-place migration, reboot, and compare the results. You then apply available Ksplice live updates and submit a package job through OS Management Hub.

The migration requires a reboot. The Ksplice exercise demonstrates live patching without another reboot. This workshop uses an Oracle Linux Red Hat Compatible Kernel (RHCK) on x86_64.

Estimated Workshop Time: 2 hours 30 minutes, including a five-minute introduction. Confirm the estimate during rehearsal.

### Objectives

- Capture the source operating system, packages, security state, and application baseline.
- Assess migration readiness and recognize conditions that require instructor help.
- Convert the assigned RHEL server to Oracle Linux within the same major release.
- Confirm the Oracle-provided running kernel and application after reboot.
- Apply available live kernel patches and record evidence that the boot did not change.
- Submit and inspect an OS Management Hub package job through SSH.
- Compare results and explain remaining exceptions.

### Prerequisites

- Basic experience with Linux terminals, files, SSH, RPM, and sudo.
- The hostname, SSH username, private-key location, and host-key fingerprint supplied by your instructor.
- Sudo access and authorization to reboot your assigned training server.
- An instructor-prepared RHEL 9 x86_64 server with a working Apache application and repository access.
- Instructor confirmation that a recoverable backup exists before conversion.

Your instructor supplies the exact tested source and target versions. Do not use a personal production server. The migration script used in this workshop is community maintained; an Oracle-owned repository does not imply official support for the script. See the [migration repository](https://github.com/oracle/migrate-to-ol).

## Workshop Flow

| Lab | Outcome | Estimated time |
| --- | --- | --- |
| 1. Connect and Capture the Baseline | Source and application evidence recorded | 20 minutes |
| 2. Assess Migration Readiness | Verified script and reviewed dry run | 20 minutes |
| 3. Migrate to Oracle Linux | Converted server, reboot, and application checks | 40 minutes |
| 4. Apply Ksplice Live Updates | Patch results and unchanged boot identity | 25 minutes |
| 5. Manage Packages with OS Management Hub | Managed package job submitted and verified | 25 minutes |
| 6. Validate the Results | Evidence compared and exceptions recorded | 15 minutes |

## Working Agreements

- Run Linux commands in your SSH session unless a step says to use your local terminal.
- Replace angle-bracket placeholders with the connection details from your instructor.
- Use the same assigned server throughout. A fallback demonstration must be recorded as a separate server.
- Verify the host-key fingerprint before accepting a new SSH host key. Ask the instructor about any unexpected change.
- Check the output and exit status of each required command before continuing.
- If the server does not return after reboot or a migration step fails, preserve the evidence and contact your instructor. The instructor handles recovery.
- Keep private keys and credentials out of evidence archives and shared terminals.

OS Management Hub is a centralized management service. This workshop supplies a terminal helper and prepared access so that you can use it without administering its hosting environment. Your instructor will confirm when registration is ready. If the service exercise is unavailable, record it as incomplete or instructor-demonstrated.

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle.
- **Last Updated By/Date** - Workshop adaptation draft, October 19, 2026.
