-- Apply as the V3 administrator after creating olvn_v3_app in Lab 2.
-- Runtime grants adapted from upstream 003, 006, 007 and package-publication.md.
GRANT SELECT ON olvn_v3.catalog_version TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
GRANT SELECT ON olvn_v3.catalog_source TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
GRANT SELECT ON olvn_v3.catalog_sku TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
GRANT SELECT ON olvn_v3.catalog_alias TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
GRANT SELECT ON olvn_v3.catalog_mapping TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
GRANT SELECT ON olvn_v3.catalog_activation TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
GRANT SELECT ON olvn_v3.catalog_reconciliation TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
GRANT SELECT ON olvn_v3.package_member TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
GRANT SELECT ON olvn_v3.package_release TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
GRANT SELECT ON olvn_v3.package_admission TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
GRANT SELECT ON olvn_v3.package_channel TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
GRANT SELECT ON olvn_v3.package_activation TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
GRANT SELECT, INSERT, UPDATE ON olvn_v3.analysis TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
GRANT SELECT, INSERT, UPDATE ON olvn_v3.analysis_revision TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
GRANT SELECT, INSERT, UPDATE ON olvn_v3.analysis_line TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
GRANT SELECT, INSERT, UPDATE ON olvn_v3.option_selection TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
GRANT SELECT, INSERT ON olvn_v3.calculation_result TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
GRANT INSERT ON olvn_v3.audit_event TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
GRANT SELECT, INSERT, UPDATE (current_revision_id) ON olvn_v3.coverage_comparison TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
GRANT SELECT, INSERT, UPDATE (state, confirmed_by, confirmed_at) ON olvn_v3.coverage_revision TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
GRANT INSERT ON olvn_v3.coverage_audit TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
GRANT SELECT, INSERT ON olvn_v3.coverage_revision_package TO 'olvn_v3_app'@'10.0.0.0/255.255.255.0';
SELECT 'V3_RUNTIME_GRANTS_INSTALLED' AS checkpoint;
