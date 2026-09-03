# Oracle Linux Value Navigator

This repository contains the complete workshop and application source for building Oracle Linux Value Navigator. The workshop guides a learner from an Oracle Cloud Infrastructure LAMP environment to a tested subscription-cost comparison application.

The initial workshop uses only synthetic sample data. It is a subscription-cost comparison, not a quote, licensing determination, or full TCO analysis.

The executable application is packaged as `catalog-database/files/ol-value-navigator-application.zip`. It includes the MySQL schema, private PHP libraries, public controllers and views, a staged deployment script, and automated checks. Lab 1 is the validated infrastructure baseline. Labs 2 through 6 create, deploy, enable, and test the application.

## Production publication reminder

- [ ] Complete successful end-to-end testing of Labs 1 through 6.
- [ ] Upload the final application ZIP to the LiveLabs production Object Storage location.
- [ ] Replace the testing Object Storage PAR URL in `catalog-database/catalog-database.md` with the LiveLabs production URL.
- [ ] Verify the production object against the SHA-256 checksum documented in Lab 2 before publishing the workshop.
