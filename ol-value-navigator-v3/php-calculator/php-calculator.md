# Lab 3: Configure the PHP Application and User Access

## Introduction

> **Version 3 authoring draft.** This lab is not ready for deployment or a learner run. Read the prerequisites and pending checkpoints before executing anything. Version 1 remains unchanged.

Configure one PHP application using the approved V3 installation. Authentication must identify each real user before ownership checks can separate their comparisons.

Estimated Time: Pending application and identity rehearsal.

### Objectives

* Identify runtime dependencies and private configuration.
* Review routing, HTTPS, and private-file boundaries.
* Define the two-user acceptance check.

## Task 1: Prepare the PHP runtime

1. Use a supported PHP 8.3 test/runtime environment and the reviewed Composer lock file. Inspect project scripts before installing dependencies.

2. Verify required extensions and dependencies against the actual target. The author's currently observed Windows PHP 8.2.30 is not proof of PHP 8.3 compatibility.

3. Configure Apache to serve only the candidate's `public` directory. Keep configuration, source libraries, vendor files, and uploaded evidence outside direct web access.

4. Verify Slim routes and cookie paths. The reviewed source uses `/demo` routes; do not invent a new base path without adapting and testing routing.

    **Pending checkpoint:** The candidate installation and Apache configuration are not yet verified. Do not use Version 1's staged deployment script.

## Task 2: Review private settings

1. Inspect `config/app.php` for the required setting names.

    | Setting | Purpose |
    | --- | --- |
    | `OLVN_DATABASE_DSN` | Selects the database connection |
    | `OLVN_DATABASE_USER_SECRET_PATH` | Points to the runtime credential file |
    | `HEATWAVE_MODEL_ID` | Selects the configured extraction model |
    | `OLVN_DEMO_REPRESENTATIVE_SUBJECT` | Supplies the current demo identity, not production login |
    | `OLVN_ENVIRONMENT` | Defaults to production; influences secure cookie behavior |

2. Install V3 credentials through the approved private procedure. Do not copy credentials from Version 1 or put secret values in screenshots or source control.

3. Verify how the web process receives settings. The current entry point does not automatically load a `.env` file.

4. Verify database encryption and server identity, HTTPS cookies, and private-file denial. Setting a value is not proof that the connection uses it correctly.

## Task 3: Establish real user identity

1. Choose the approved identity-provider integration and allowed users. Do not assume installed OAuth/JWT libraries constitute working sign-in.

2. Replace the shared configured identity throughout the request and service flow. The current bootstrap also passes that actor into extraction services.

3. Test User A, User B, and an unauthenticated browser. Check listing, read, edit, confirm, export, source preview, and image access.

4. Test logout and session expiry. Record failures without customer data.

    **Stop here:** Real login is not implemented in the reviewed handoff. Multiuser isolation cannot pass while all visitors receive the same demo identity.

## Acknowledgements

* **Authors** - Perside Foster, Mark Atkinson, and Shawn Kelley
* **Contributors** - Nick Mader
* **Last Updated By/Date** - Perside Foster, September 2026
