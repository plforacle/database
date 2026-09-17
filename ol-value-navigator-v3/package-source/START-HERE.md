# Oracle Linux Value Navigator V3: Package R3

Use **Lab 3: Install and Check the PHP Application** as the main installation guide. This ZIP replaces the older R1/R2 candidates. It includes Shawn's unchanged application and all production PHP dependencies; no Composer commands are needed on OCI.

## Existing V3 environment

Keep the compute instance and HeatWave database created in Labs 1 and 2. This package does not create infrastructure, migrate the database, or change Version 1.

HeatWave still stores comparisons, revisions, and catalog snapshots and runs GenAI. The app uses its bundled catalog. Catalog updates require a new reviewed package; database catalog activation is not required.

## Install and test

After downloading and verifying the ZIP on the V3 compute instance:

```bash
unzip ol-value-navigator-v3-r3.zip
cd ol-value-navigator-v3-r3
sudo bash install.sh
sudo bash /opt/olvn-v3/package-r3/test.sh
```

Lab 3 lists the required PHP extensions and the administrator-only grants in genai-grant.sql. Perside already applied all three grants and verified READY through PHP on September 17. Do not repeat them for that environment.

The installer checks prerequisites and credentials before creating application directories. It reuses existing Lab 3 credentials without displaying them. Otherwise it prompts for the private DB IP and application password.

A retry accepts only matching package files and configuration. Existing credentials and comparison records are not overwritten. Changed files, unsafe permissions, or a conflicting Apache configuration cause a stop. Do not delete directories to bypass those checks. R1/R2 upgrades require separate review.

## Open privately

On Windows, use the V3 SSH key:

```powershell
$V3Key = 'C:\REPLACE\WITH\YOUR\V3-PRIVATE-KEY.key'
ssh -i $V3Key -o ExitOnForwardFailure=yes -N -L 127.0.0.1:8080:127.0.0.1:8009 opc@158.101.119.245
```

Leave that window open and browse to http://127.0.0.1:8080/demo/coverage. Lab 3 contains the browser acceptance checklist. Stop the tunnel with Ctrl+C afterward.

The listener is loopback-only on port 8009. Do not open that port in OCI or the firewall. The previous installation and port-80 greeting remain unchanged.

## Scope and limits

This remains a private rehearsal candidate. Linux installation and browser save, reopen, confirm, import, and export tests remain to be completed on this exact package. The test script does not claim full workflow coverage.

The app has a shared demo identity, not real login. Use only invented data. Database traffic is encrypted, but certificate identity is not verified. Browser traffic uses local HTTP through an encrypted SSH tunnel, not public HTTPS.

The newer scenario workflow supports 12 and 36 months; legacy synthetic checks cover annual, three-year, and five-year calculations. These are separate upstream workflows. No commercial pricing approval is implied.

## Maintainer record

Source commit: d419d24d035cabc68b219b9e98be23cd05770742. Dependencies follow the unchanged Composer lock file. Third-party license files are retained in app/vendor. No credentials or build tools are included.

See BUILD-REPORT.md for prior test failures, local checks, and remaining verification. The examples directory contains an upstream synthetic TSV for import testing.
