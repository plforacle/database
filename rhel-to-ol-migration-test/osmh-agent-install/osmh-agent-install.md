# Install Management Agent for the On-Premises OSMH Labs

## Introduction

Use this reference on the management station during instructor preparation or on the assigned on-premises client during Student Lab 9. Complete the calling guide's profile step first. Return to that guide after activation.

Estimated Time: 10 minutes per host, included in the calling guide's estimate

### Objectives

- Install the Management Agent RPM and required privilege rules.
- Activate the OSMH plugin using the correct connection settings.
- Verify the agent without exposing its install key.

### Prerequisites

Use a clean, assigned Oracle Linux 9 x86_64 host with sudo access, a matching `/etc/osmh-profile`, synchronized time, and a current Management Agent RPM from OCI. Use version `260828.3213` or later for this procedure; it includes a private Java runtime. Older versions require a different Java prerequisite and are outside this lab's chosen path.

This procedure is for non-OCI hosts. OCI Compute instances use Oracle Cloud Agent, not this registration procedure. If the assigned host already has an active Management Agent registration, stop and ask the instructor to confirm the host assignment.

## Task 1: Confirm the host and install the RPM

1. Confirm that you are on the host assigned by the calling guide:

    ```bash
    <copy>
    hostname
    grep -E '^(ID|VERSION_ID)=' /etc/os-release
    uname -m
    </copy>
    ```

2. Set the full path to the RPM downloaded by the instructor and inspect its name and version:

    ```bash
    <copy>
    AGENT_RPM='/home/YOUR_USER/oracle.mgmt_agent.YOUR_VERSION.Linux-x86_64.rpm'
    rpm -qp "$AGENT_RPM"
    </copy>
    ```

    Replace the path and verify the required version before continuing. The host needs access to any package dependencies during installation.

3. Install the selected local RPM:

    ```bash
    <copy>
    sudo dnf install -y "$AGENT_RPM"
    </copy>
    ```

## Task 2: Configure and validate the plugin privileges

1. Run `sudo visudo` and confirm that `/etc/sudoers.d` is included. The include directive can use `#includedir` or `@includedir`. Do not remove existing sudo configuration.

2. On this clean lab host, create the Management Agent rules. If `/etc/sudoers.d/mgmt_agent` already contains organization-specific rules, have the instructor review them before replacing anything.

    ```bash
    <copy>
    sudo tee /etc/sudoers.d/mgmt_agent >/dev/null <<'EOF'
    mgmt_agent ALL=(ALL) NOPASSWD:/opt/oracle/mgmt_agent/agent_inst/bin/chown_recursive_ep.sh
    mgmt_agent ALL=(root) NOPASSWD:SETENV: /opt/oracle/mgmt_agent/plugins/osmh/*/osmh
    Defaults:mgmt_agent !requiretty
    EOF
    sudo chmod 440 /etc/sudoers.d/mgmt_agent
    sudo visudo -cf /etc/sudoers.d/mgmt_agent
    </copy>
    ```

3. Continue only when sudo validation reports the file parsed correctly. Confirm that the station or client profile from the calling guide is already saved as `/etc/osmh-profile`.

## Task 3: Create the private activation response file

1. Obtain the compartment's Management Agent install key through the instructor's private handoff or **Management Agent > Downloads and Keys**. Do not paste it into commands, screenshots, or shared notes.

2. Create a private file and enter the key at the hidden prompt:

    ```bash
    <copy>
    umask 077
    RESPONSE_FILE=$(mktemp /tmp/ol-migrate-osmh.XXXXXX)
    read -rsp 'Management Agent install key: ' MACS_KEY
    printf '\n'
    printf 'ManagementAgentInstallKey = %s\nService.plugin.osmh.download = true\n' \
      "$MACS_KEY" > "$RESPONSE_FILE"
    unset MACS_KEY
    </copy>
    ```

3. For an on-premises **client**, append the shared station's actual proxy hostname and port:

    ```bash
    <copy>
    printf 'GatewayServerHost = %s\nGatewayServerPort = %s\n' \
      'YOUR_STATION_HOSTNAME' '16080' >> "$RESPONSE_FILE"
    </copy>
    ```

    Replace both values with the instructor's configuration. This is the proxy port, not the package mirror port.

4. For the **station**, skip step 3 when it reaches OCI directly. If the station requires a corporate forwarding proxy, append that proxy's hostname and port using the same parameter names. Do not point the station at its own proxy during bootstrap.

## Task 4: Activate and verify the agent

1. Activate the agent with the OSMH plugin:

    ```bash
    <copy>
    sudo /opt/oracle/mgmt_agent/agent_inst/bin/setup.sh opts="$RESPONSE_FILE"
    </copy>
    ```

2. Expect the setup to report that the agent is running. Allow additional time for OSMH plugin deployment. If setup fails, preserve the error and use the calling guide's troubleshooting instructions before retrying.

3. After successful activation, delete only the temporary response file and check the service:

    ```bash
    <copy>
    rm -f -- "$RESPONSE_FILE"
    unset RESPONSE_FILE
    systemctl is-active mgmt_agent
    </copy>
    ```

    Expect `active`. If activation failed, keep the private response file only while diagnosing that failure, then remove it. Do not display its contents.

4. Return to the calling guide to check the station or client in OSMH. A running Management Agent alone does not prove successful OSMH registration.

## Learn More

- [Installing Management Agents](https://docs.oracle.com/en-us/iaas/management-agents/doc/install-management-agent-chapter.html)
- [Registering an OSMH management station](https://docs.oracle.com/en-us/iaas/osmh/doc/register-management-station.htm)
- [Registering an on-premises client](https://docs.oracle.com/en-us/iaas/osmh/doc/register-non-oci-instance.htm)

## Acknowledgements

- **Author** - Perside Foster, Principal Solution Engineer, Oracle
- **Contributors** - Oracle Linux SMEs; adapted from the supplied osmh-onprem-lab shared agent procedure
- **Last Updated By/Date** - Oracle LiveLabs Workshop Team, October 2026
