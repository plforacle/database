# Oracle Linux Value Navigator Version 2

This directory contains the Version 2 workshop and application source for building Oracle Linux Value Navigator in a new, isolated OCI environment. Version 1 remains under `../ol-value-navigator` and is not modified by Version 2 development.

The Version 2 baseline was copied from Version 1 at Git commit `a2b5fc162d72dc9cddd87357fd2101e80f1ce979`. Its OCI resources, database, deployment paths, session, calculation rule, and application package use separate Version 2 identifiers.

The Version 2 application uses application-managed usernames and passwords, secure PHP sessions, and user-owned comparisons. It uses only synthetic sample data. It is a subscription-cost comparison, not a quote, licensing determination, or full TCO analysis.

The readable application source of truth is stored under `catalog-database/files/application`. It includes the MySQL schema, private PHP libraries, public controllers and views, a staged deployment script, and automated checks. The generated learner package is `catalog-database/files/ol-value-navigator-2-application.zip`. Regenerate the ZIP from the readable source after every application change, update the Lab 2 checksum, and test the packaged files before uploading them to Object Storage. Lab 1 is the validated infrastructure baseline. Labs 2 through 6 create, deploy, enable, and test the application.

The standalone team tutorial is stored in `team-user-guide.md`. It documents account registration, login, user-owned comparisons, logout, and the complete application workflow. The application Help page provides the matching in-product guidance.

The five-minute management walkthrough is stored in `management-quick-start.md`.

The target published workshop URL is `https://plforacle.github.io/database/ol-value-navigator-v2/workshops/tenancy/index.html`.

## Version 2 development checklist

- [x] Copy the validated Version 1 workshop and application baseline.
- [x] Assign separate Version 2 OCI, database, deployment, session, rule, and package identifiers.
- [x] Select and document application-managed authentication.
- [x] Add users, login, logout, secure session lifecycle, and comparison ownership.
- [x] Add automated authentication, route-guard, schema, and owner-filter checks.
- [ ] Prove two-user isolation for every comparison query and action.
- [ ] Complete successful end-to-end testing of the Version 2 labs.
- [ ] Deploy the final ZIP to the test compute instance and run the Stage 5 installation verification.
- [ ] Upload the corrected Version 2 application ZIP to Object Storage.
- [x] Replace `VERSION_2_OBJECT_STORAGE_PAR_URL` in Lab 2 with the final PAR URL.
- [ ] Verify the production object against the SHA-256 checksum documented in Lab 2 before publishing the workshop.
