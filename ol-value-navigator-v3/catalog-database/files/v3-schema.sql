-- Oracle Linux Value Navigator V3: fresh database only.
-- Source: olvalnav d419d24d035cabc68b219b9e98be23cd05770742, migrations 001-008.
-- Only fixture account/grant tails from 003, 006, and 007 are omitted.
-- Run with mysql batch input, without --force. Do not rerun after an error.
-- CREATE DATABASE intentionally fails if this schema already exists.
CREATE DATABASE olvn_v3 CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE olvn_v3;

-- BEGIN upstream 001_catalog.sql
CREATE TABLE catalog_version (
    id CHAR(36) NOT NULL,
    version VARCHAR(64) NOT NULL,
    status VARCHAR(16) NOT NULL,
    source_filename VARCHAR(255) NOT NULL,
    source_sha256 CHAR(64) NOT NULL,
    effective_on DATE NOT NULL,
    approved_by VARCHAR(255) NULL,
    approved_at DATETIME(6) NULL,
    retired_by VARCHAR(255) NULL,
    retired_at DATETIME(6) NULL,
    caveats TEXT NULL,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    UNIQUE KEY uq_catalog_version_version (version),
    CONSTRAINT chk_catalog_version_status CHECK (status IN ('DRAFT', 'APPROVED', 'RETIRED')),
    CONSTRAINT chk_catalog_version_sha256 CHECK (source_sha256 REGEXP '^[0-9a-f]{64}$'),
    CONSTRAINT chk_catalog_version_approval CHECK (
        status <> 'APPROVED' OR (approved_by IS NOT NULL AND approved_at IS NOT NULL)
    ),
    CONSTRAINT chk_catalog_version_retirement CHECK (
        (status = 'RETIRED' AND retired_by IS NOT NULL AND retired_at IS NOT NULL)
        OR (status <> 'RETIRED' AND retired_by IS NULL AND retired_at IS NULL)
    )
) ENGINE=InnoDB;

CREATE TABLE catalog_import_baseline (
    workbook_sha256 CHAR(64) NOT NULL,
    rh_catalog_digest CHAR(64) NOT NULL,
    ol_catalog_digest CHAR(64) NOT NULL,
    mapping_digest CHAR(64) NOT NULL,
    sources_digest CHAR(64) NOT NULL,
    checks_digest CHAR(64) NOT NULL,
    source_digest CHAR(64) NOT NULL,
    sku_digest CHAR(64) NOT NULL,
    alias_digest CHAR(64) NOT NULL,
    staged_mapping_digest CHAR(64) NOT NULL,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    PRIMARY KEY (workbook_sha256),
    CONSTRAINT chk_catalog_import_baseline_digests CHECK (
        workbook_sha256 REGEXP '^[0-9a-f]{64}$'
        AND rh_catalog_digest REGEXP '^[0-9a-f]{64}$'
        AND ol_catalog_digest REGEXP '^[0-9a-f]{64}$'
        AND mapping_digest REGEXP '^[0-9a-f]{64}$'
        AND sources_digest REGEXP '^[0-9a-f]{64}$'
        AND checks_digest REGEXP '^[0-9a-f]{64}$'
        AND source_digest REGEXP '^[0-9a-f]{64}$'
        AND sku_digest REGEXP '^[0-9a-f]{64}$'
        AND alias_digest REGEXP '^[0-9a-f]{64}$'
        AND staged_mapping_digest REGEXP '^[0-9a-f]{64}$'
    )
) ENGINE=InnoDB;

INSERT INTO catalog_import_baseline (
    workbook_sha256,
    rh_catalog_digest,
    ol_catalog_digest,
    mapping_digest,
    sources_digest,
    checks_digest,
    source_digest,
    sku_digest,
    alias_digest,
    staged_mapping_digest
) VALUES (
    '68c60674cb0ee2a65d6c2010d71e32e89c3cbb994d7f27675e67b76dc31c9e62',
    'cef82c28338bf33dc5331de88490ffe8df764dec70ef2c3cd2d165db7bbb0b1e',
    '1019e0685b7e1e5c119da4498343566c9e52252c5b33466ef2671bc3761b6ab4',
    'f67ffa4fdfe1247504215da032c5d48ed737dc9024ebdb340a2bcd826b325a92',
    '8b8045f778f710bd8a6002e4de45687a7f32631314a70f55c9c16e39d0048afc',
    '5b69feebca5737359eeed31200a25be9c4ed39e18c1e6ef42c35a4c2ef58d60b',
    '31dbb6da34fea39adc67d36a289d346475f1406128e5ebcfa5b659793175664b',
    '4aaffde65be4d5ec393df6b6c9a6f41cb5a6e794ff1f86cf4f73718deee3e45a',
    '4f53cda18c2baa0c0354bb5f9a3ecbe5ed12ab4d8e11ba873c2f11161202b945',
    '98ef00d6173c534fb359c49997be6586d266deb3a122b64cfb90ca355d4cd5cf'
);

CREATE TABLE catalog_source (
    id CHAR(36) NOT NULL,
    catalog_version_id CHAR(36) NOT NULL,
    citation TEXT NOT NULL,
    worksheet_reference VARCHAR(255) NOT NULL,
    approved_by VARCHAR(255) NULL,
    approved_at DATETIME(6) NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_catalog_source_id_version (id, catalog_version_id),
    CONSTRAINT chk_catalog_source_approval CHECK (
        (approved_by IS NULL AND approved_at IS NULL)
        OR (approved_by IS NOT NULL AND approved_at IS NOT NULL)
    ),
    CONSTRAINT fk_catalog_source_version FOREIGN KEY (catalog_version_id) REFERENCES catalog_version (id)
) ENGINE=InnoDB;

CREATE TABLE catalog_sku (
    id CHAR(36) NOT NULL,
    catalog_version_id CHAR(36) NOT NULL,
    governed_sku VARCHAR(128) NOT NULL,
    description TEXT NOT NULL,
    unit VARCHAR(64) NOT NULL,
    annual_usd_price DECIMAL(19,4) NULL,
    effective_from DATE NOT NULL,
    effective_to DATE NULL,
    catalog_source_id CHAR(36) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_catalog_sku_version_sku (catalog_version_id, governed_sku),
    UNIQUE KEY uq_catalog_sku_id_version (id, catalog_version_id),
    CONSTRAINT chk_catalog_sku_price CHECK (annual_usd_price IS NULL OR annual_usd_price >= 0),
    CONSTRAINT chk_catalog_sku_dates CHECK (effective_to IS NULL OR effective_to >= effective_from),
    CONSTRAINT fk_catalog_sku_version FOREIGN KEY (catalog_version_id) REFERENCES catalog_version (id),
    CONSTRAINT fk_catalog_sku_source FOREIGN KEY (catalog_source_id, catalog_version_id)
        REFERENCES catalog_source (id, catalog_version_id)
) ENGINE=InnoDB;

CREATE TABLE catalog_alias (
    id CHAR(36) NOT NULL,
    catalog_version_id CHAR(36) NOT NULL,
    catalog_sku_id CHAR(36) NOT NULL,
    normalized_alias VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_catalog_alias_version_alias (catalog_version_id, normalized_alias),
    CONSTRAINT fk_catalog_alias_sku FOREIGN KEY (catalog_sku_id, catalog_version_id)
        REFERENCES catalog_sku (id, catalog_version_id)
) ENGINE=InnoDB;

CREATE TABLE catalog_mapping (
    id CHAR(36) NOT NULL,
    catalog_version_id CHAR(36) NOT NULL,
    source_catalog_sku_id CHAR(36) NOT NULL,
    oracle_catalog_sku_id CHAR(36) NOT NULL,
    rationale TEXT NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_catalog_mapping_source_option (catalog_version_id, source_catalog_sku_id, oracle_catalog_sku_id),
    CONSTRAINT chk_catalog_mapping_distinct_skus CHECK (source_catalog_sku_id <> oracle_catalog_sku_id),
    CONSTRAINT fk_catalog_mapping_source FOREIGN KEY (source_catalog_sku_id, catalog_version_id)
        REFERENCES catalog_sku (id, catalog_version_id),
    CONSTRAINT fk_catalog_mapping_option FOREIGN KEY (oracle_catalog_sku_id, catalog_version_id)
        REFERENCES catalog_sku (id, catalog_version_id)
) ENGINE=InnoDB;

CREATE TABLE catalog_reconciliation (
    id CHAR(36) NOT NULL,
    catalog_version_id CHAR(36) NOT NULL,
    status VARCHAR(16) NOT NULL,
    workbook_sha256 CHAR(64) NOT NULL,
    rh_catalog_digest CHAR(64) NOT NULL,
    ol_catalog_digest CHAR(64) NOT NULL,
    mapping_table_digest CHAR(64) NOT NULL,
    sources_table_digest CHAR(64) NOT NULL,
    checks_digest CHAR(64) NOT NULL,
    source_digest CHAR(64) NOT NULL,
    sku_digest CHAR(64) NOT NULL,
    alias_digest CHAR(64) NOT NULL,
    mapping_digest CHAR(64) NOT NULL,
    report JSON NOT NULL,
    reconciled_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    UNIQUE KEY uq_catalog_reconciliation_version (catalog_version_id),
    CONSTRAINT chk_catalog_reconciliation_status CHECK (status = 'PASS'),
    CONSTRAINT chk_catalog_reconciliation_digests CHECK (
        workbook_sha256 REGEXP '^[0-9a-f]{64}$'
        AND rh_catalog_digest REGEXP '^[0-9a-f]{64}$'
        AND ol_catalog_digest REGEXP '^[0-9a-f]{64}$'
        AND mapping_table_digest REGEXP '^[0-9a-f]{64}$'
        AND sources_table_digest REGEXP '^[0-9a-f]{64}$'
        AND checks_digest REGEXP '^[0-9a-f]{64}$'
        AND source_digest REGEXP '^[0-9a-f]{64}$'
        AND sku_digest REGEXP '^[0-9a-f]{64}$'
        AND alias_digest REGEXP '^[0-9a-f]{64}$'
        AND mapping_digest REGEXP '^[0-9a-f]{64}$'
    ),
    CONSTRAINT fk_catalog_reconciliation_version FOREIGN KEY (catalog_version_id) REFERENCES catalog_version (id)
) ENGINE=InnoDB;

CREATE TABLE catalog_activation (
    id CHAR(36) NOT NULL,
    catalog_version_id CHAR(36) NOT NULL,
    activated_by VARCHAR(255) NOT NULL,
    approval_evidence_reference VARCHAR(255) NOT NULL,
    activated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    deactivated_at DATETIME(6) NULL,
    active_slot VARCHAR(16) GENERATED ALWAYS AS (
        CASE WHEN deactivated_at IS NULL THEN 'ACTIVE' ELSE NULL END
    ) STORED,
    PRIMARY KEY (id),
    UNIQUE KEY uq_catalog_activation_version (catalog_version_id),
    UNIQUE KEY uq_catalog_activation_active (active_slot),
    CONSTRAINT chk_catalog_activation_evidence CHECK (TRIM(approval_evidence_reference) <> ''),
    CONSTRAINT chk_catalog_activation_dates CHECK (deactivated_at IS NULL OR deactivated_at >= activated_at),
    CONSTRAINT fk_catalog_activation_version FOREIGN KEY (catalog_version_id) REFERENCES catalog_version (id)
) ENGINE=InnoDB;
-- END upstream 001_catalog.sql

-- BEGIN upstream 002_analysis.sql
CREATE TABLE analysis (
    id CHAR(36) NOT NULL,
    creator_subject VARCHAR(255) NOT NULL,
    access_scope VARCHAR(255) NOT NULL,
    current_revision_id CHAR(36) NULL,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    KEY idx_analysis_creator_subject (creator_subject)
) ENGINE=InnoDB;

CREATE TABLE analysis_revision (
    id CHAR(36) NOT NULL,
    analysis_id CHAR(36) NOT NULL,
    source_text MEDIUMTEXT NOT NULL,
    extraction_mode VARCHAR(16) NOT NULL,
    prompt_version VARCHAR(255) NULL,
    model_identifier VARCHAR(255) NULL,
    catalog_version_id CHAR(36) NOT NULL,
    state VARCHAR(32) NOT NULL,
    predecessor_revision_id CHAR(36) NULL,
    creator_subject VARCHAR(255) NOT NULL,
    confirmed_by_subject VARCHAR(255) NULL,
    confirmed_at DATETIME(6) NULL,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    KEY idx_analysis_revision_analysis (analysis_id),
    UNIQUE KEY uq_analysis_revision_id_analysis (id, analysis_id),
    UNIQUE KEY uq_analysis_revision_id_catalog (id, catalog_version_id),
    CONSTRAINT chk_analysis_revision_mode CHECK (extraction_mode IN ('AI', 'MANUAL')),
    CONSTRAINT chk_analysis_revision_state CHECK (state IN ('DRAFT', 'NEEDS_REVIEW', 'INCOMPLETE', 'READY_TO_CALCULATE', 'CONFIRMED', 'SUPERSEDED')),
    CONSTRAINT chk_analysis_revision_confirmation CHECK (
        state <> 'CONFIRMED' OR (confirmed_by_subject IS NOT NULL AND confirmed_at IS NOT NULL)
    ),
    CONSTRAINT fk_analysis_revision_analysis FOREIGN KEY (analysis_id) REFERENCES analysis (id),
    CONSTRAINT fk_analysis_revision_catalog_version FOREIGN KEY (catalog_version_id) REFERENCES catalog_version (id),
    CONSTRAINT fk_analysis_revision_predecessor FOREIGN KEY (predecessor_revision_id, analysis_id)
        REFERENCES analysis_revision (id, analysis_id)
) ENGINE=InnoDB;

ALTER TABLE analysis
    ADD CONSTRAINT fk_analysis_current_revision FOREIGN KEY (current_revision_id, id)
        REFERENCES analysis_revision (id, analysis_id);

CREATE TABLE analysis_line (
    id CHAR(36) NOT NULL,
    analysis_revision_id CHAR(36) NOT NULL,
    source_start INT UNSIGNED NOT NULL,
    source_end INT UNSIGNED NOT NULL,
    resolved_sku VARCHAR(128) NULL,
    resolved_description TEXT NULL,
    extracted_identifier VARCHAR(255) NULL,
    normalized_identifier VARCHAR(255) NULL,
    extracted_description TEXT NULL,
    normalized_description TEXT NULL,
    match_state VARCHAR(16) NOT NULL,
    match_rationale TEXT NULL,
    decision VARCHAR(32) NOT NULL,
    corrected_identifier VARCHAR(255) NULL,
    quantity DECIMAL(19,4) NULL,
    annual_usd_price DECIMAL(19,4) NULL,
    currency CHAR(3) NULL,
    annual_term VARCHAR(16) NULL,
    unit_basis VARCHAR(64) NULL,
    exclusion_reason TEXT NULL,
    confirmed_by_subject VARCHAR(255) NULL,
    confirmed_at DATETIME(6) NULL,
    PRIMARY KEY (id),
    KEY idx_analysis_line_revision (analysis_revision_id),
    UNIQUE KEY uq_analysis_line_id_revision (id, analysis_revision_id),
    CONSTRAINT chk_analysis_line_span CHECK (source_end >= source_start),
    CONSTRAINT chk_analysis_line_match_state CHECK (match_state IN ('EXACT', 'NORMALIZED', 'POSSIBLE', 'UNRESOLVED')),
    CONSTRAINT chk_analysis_line_decision CHECK (decision IN ('PENDING', 'CONFIRMED', 'CORRECTED_CONFIRMED', 'EXCLUDED')),
    CONSTRAINT chk_analysis_line_quantity CHECK (quantity IS NULL OR quantity > 0),
    CONSTRAINT chk_analysis_line_price CHECK (annual_usd_price IS NULL OR annual_usd_price >= 0),
    CONSTRAINT chk_analysis_line_currency CHECK (currency IS NULL OR currency = 'USD'),
    CONSTRAINT chk_analysis_line_annual_term CHECK (annual_term IS NULL OR annual_term = 'ANNUAL'),
    CONSTRAINT fk_analysis_line_revision FOREIGN KEY (analysis_revision_id) REFERENCES analysis_revision (id)
) ENGINE=InnoDB;

CREATE TABLE option_selection (
    id CHAR(36) NOT NULL,
    analysis_line_id CHAR(36) NOT NULL,
    analysis_revision_id CHAR(36) NOT NULL,
    catalog_sku_id CHAR(36) NOT NULL,
    catalog_version_id CHAR(36) NOT NULL,
    selected_by_subject VARCHAR(255) NOT NULL,
    selected_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    UNIQUE KEY uq_option_selection_line (analysis_line_id),
    CONSTRAINT fk_option_selection_line FOREIGN KEY (analysis_line_id, analysis_revision_id)
        REFERENCES analysis_line (id, analysis_revision_id),
    CONSTRAINT fk_option_selection_revision_catalog FOREIGN KEY (analysis_revision_id, catalog_version_id)
        REFERENCES analysis_revision (id, catalog_version_id),
    CONSTRAINT fk_option_selection_sku FOREIGN KEY (catalog_sku_id, catalog_version_id)
        REFERENCES catalog_sku (id, catalog_version_id)
) ENGINE=InnoDB;

CREATE TABLE calculation_result (
    id CHAR(36) NOT NULL,
    analysis_revision_id CHAR(36) NOT NULL,
    currency CHAR(3) NOT NULL,
    annual_term VARCHAR(16) NOT NULL,
    annual_total DECIMAL(19,4) NOT NULL,
    three_year_total DECIMAL(19,4) NOT NULL,
    five_year_total DECIMAL(19,4) NOT NULL,
    assumptions JSON NOT NULL,
    exclusions JSON NOT NULL,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    UNIQUE KEY uq_calculation_result_revision (analysis_revision_id),
    CONSTRAINT chk_calculation_currency CHECK (currency = 'USD'),
    CONSTRAINT chk_calculation_annual_term CHECK (annual_term = 'ANNUAL'),
    CONSTRAINT chk_calculation_totals CHECK (annual_total >= 0 AND three_year_total >= 0 AND five_year_total >= 0),
    CONSTRAINT fk_calculation_result_revision FOREIGN KEY (analysis_revision_id) REFERENCES analysis_revision (id)
) ENGINE=InnoDB;

CREATE TABLE audit_event (
    id CHAR(36) NOT NULL,
    analysis_revision_id CHAR(36) NOT NULL,
    actor_subject VARCHAR(255) NOT NULL,
    action VARCHAR(255) NOT NULL,
    previous_state VARCHAR(32) NULL,
    resulting_state VARCHAR(32) NULL,
    request_id CHAR(36) NULL,
    payload JSON NULL,
    occurred_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    KEY idx_audit_event_revision_time (analysis_revision_id, occurred_at),
    CONSTRAINT chk_audit_event_previous_state CHECK (previous_state IS NULL OR previous_state IN ('DRAFT', 'NEEDS_REVIEW', 'INCOMPLETE', 'READY_TO_CALCULATE', 'CONFIRMED', 'SUPERSEDED')),
    CONSTRAINT chk_audit_event_resulting_state CHECK (resulting_state IS NULL OR resulting_state IN ('DRAFT', 'NEEDS_REVIEW', 'INCOMPLETE', 'READY_TO_CALCULATE', 'CONFIRMED', 'SUPERSEDED')),
    CONSTRAINT fk_audit_event_revision FOREIGN KEY (analysis_revision_id) REFERENCES analysis_revision (id)
) ENGINE=InnoDB;
-- END upstream 002_analysis.sql

-- BEGIN upstream 003_audit_guards.sql
DELIMITER $$
CREATE TRIGGER trg_catalog_version_identity_immutable
BEFORE UPDATE ON catalog_version
FOR EACH ROW
BEGIN
    IF NEW.id <> OLD.id THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Catalog version identity is immutable';
    END IF;
END$$

CREATE TRIGGER trg_catalog_import_cannot_create_approved
BEFORE INSERT ON catalog_version
FOR EACH ROW
BEGIN
    IF SUBSTRING_INDEX(USER(), '@', 1) = 'olvn_catalog_import' AND NEW.status <> 'DRAFT' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Catalog import may only create draft catalog versions';
    END IF;
END$$

CREATE TRIGGER trg_approved_catalog_version_immutable
BEFORE UPDATE ON catalog_version
FOR EACH ROW
BEGIN
    IF (NEW.status = 'RETIRED' AND OLD.status <> 'APPROVED')
        OR OLD.status = 'RETIRED' OR (
        OLD.status = 'APPROVED' AND NOT (
            NEW.status = 'RETIRED'
            AND SUBSTRING_INDEX(USER(), '@', 1) = 'olvn_catalog_activate'
            AND NEW.retired_by IS NOT NULL
            AND TRIM(NEW.retired_by) <> ''
            AND NEW.retired_at IS NOT NULL
            AND NEW.version <=> OLD.version
            AND NEW.source_filename <=> OLD.source_filename
            AND NEW.source_sha256 <=> OLD.source_sha256
            AND NEW.effective_on <=> OLD.effective_on
            AND NEW.approved_by <=> OLD.approved_by
            AND NEW.approved_at <=> OLD.approved_at
            AND NEW.caveats <=> OLD.caveats
            AND NEW.created_at <=> OLD.created_at
        )
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Approved catalog versions are immutable';
    END IF;
END$$

CREATE TRIGGER trg_approved_catalog_version_immutable_delete
BEFORE DELETE ON catalog_version
FOR EACH ROW
BEGIN
    IF OLD.status IN ('APPROVED', 'RETIRED') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Approved catalog versions are immutable';
    END IF;
END$$

CREATE TRIGGER trg_catalog_activation_requires_approved_version
BEFORE INSERT ON catalog_activation
FOR EACH ROW
BEGIN
    IF (SELECT status FROM catalog_version WHERE id = NEW.catalog_version_id) <> 'APPROVED'
        OR NOT EXISTS (
            SELECT 1
            FROM catalog_reconciliation AS reconciliation
            INNER JOIN catalog_version AS version ON version.id = reconciliation.catalog_version_id
            WHERE reconciliation.catalog_version_id = NEW.catalog_version_id
                AND reconciliation.status = 'PASS'
                AND reconciliation.workbook_sha256 = version.source_sha256
        ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Only approved catalog versions may be activated';
    END IF;
END$$

CREATE TRIGGER trg_catalog_activation_immutable_update
BEFORE UPDATE ON catalog_activation
FOR EACH ROW
BEGIN
    IF NEW.id <> OLD.id
        OR NEW.catalog_version_id <> OLD.catalog_version_id
        OR NEW.activated_by <> OLD.activated_by
        OR NEW.approval_evidence_reference <> OLD.approval_evidence_reference
        OR NEW.activated_at <> OLD.activated_at
        OR OLD.deactivated_at IS NOT NULL
        OR NEW.deactivated_at IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Catalog activation identity is immutable';
    END IF;
END$$

CREATE TRIGGER trg_catalog_activation_immutable_delete
BEFORE DELETE ON catalog_activation
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Catalog activations are append-only';
END$$

CREATE TRIGGER trg_catalog_import_cannot_approve
BEFORE UPDATE ON catalog_version
FOR EACH ROW
BEGIN
    IF SUBSTRING_INDEX(USER(), '@', 1) = 'olvn_catalog_import' AND (
        NEW.status <> OLD.status
        OR NOT (NEW.approved_by <=> OLD.approved_by)
        OR NOT (NEW.approved_at <=> OLD.approved_at)
        OR NOT (NEW.retired_by <=> OLD.retired_by)
        OR NOT (NEW.retired_at <=> OLD.retired_at)
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Catalog import cannot approve a catalog version';
    END IF;
END$$

CREATE TRIGGER trg_catalog_reconciled_version_content_immutable
BEFORE UPDATE ON catalog_version
FOR EACH ROW
BEGIN
    IF EXISTS (SELECT 1 FROM catalog_reconciliation WHERE catalog_version_id = OLD.id) AND (
        NOT (NEW.version <=> OLD.version)
        OR NOT (NEW.source_filename <=> OLD.source_filename)
        OR NOT (NEW.source_sha256 <=> OLD.source_sha256)
        OR NOT (NEW.effective_on <=> OLD.effective_on)
        OR NOT (NEW.caveats <=> OLD.caveats)
        OR NOT (NEW.created_at <=> OLD.created_at)
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Reconciled catalog metadata is immutable';
    END IF;

    IF OLD.status = 'DRAFT' AND NEW.status = 'APPROVED' AND NOT EXISTS (
        SELECT 1
        FROM catalog_reconciliation
        WHERE catalog_version_id = OLD.id
            AND status = 'PASS'
            AND workbook_sha256 = OLD.source_sha256
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Catalog approval requires bound reconciliation';
    END IF;
END$$

CREATE TRIGGER trg_catalog_reconciliation_validate_insert
BEFORE INSERT ON catalog_reconciliation
FOR EACH ROW
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM catalog_version
        WHERE id = NEW.catalog_version_id
            AND status = 'DRAFT'
            AND source_sha256 = NEW.workbook_sha256
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Reconciliation must bind a matching draft catalog';
    END IF;
    IF JSON_UNQUOTE(JSON_EXTRACT(NEW.report, '$.status')) <> 'PASS' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Reconciliation report must pass';
    END IF;
END$$

CREATE TRIGGER trg_catalog_reconciliation_immutable_update
BEFORE UPDATE ON catalog_reconciliation
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Catalog reconciliation is immutable';
END$$

CREATE TRIGGER trg_catalog_reconciliation_immutable_delete
BEFORE DELETE ON catalog_reconciliation
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Catalog reconciliation is immutable';
END$$

CREATE TRIGGER trg_catalog_source_immutable_insert
BEFORE INSERT ON catalog_source
FOR EACH ROW
BEGIN
    IF (SELECT status FROM catalog_version WHERE id = NEW.catalog_version_id) IN ('APPROVED', 'RETIRED')
        OR EXISTS (SELECT 1 FROM catalog_reconciliation WHERE catalog_version_id = NEW.catalog_version_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Approved catalog content is immutable';
    END IF;
END$$

CREATE TRIGGER trg_catalog_source_immutable_update
BEFORE UPDATE ON catalog_source
FOR EACH ROW
BEGIN
    IF (SELECT status FROM catalog_version WHERE id = OLD.catalog_version_id) IN ('APPROVED', 'RETIRED')
        OR (EXISTS (SELECT 1 FROM catalog_reconciliation WHERE catalog_version_id = OLD.catalog_version_id) AND NOT (
            SUBSTRING_INDEX(USER(), '@', 1) = 'olvn_catalog_activate'
            AND NEW.id <=> OLD.id
            AND NEW.catalog_version_id <=> OLD.catalog_version_id
            AND NEW.citation <=> OLD.citation
            AND NEW.worksheet_reference <=> OLD.worksheet_reference
            AND OLD.approved_by IS NULL
            AND OLD.approved_at IS NULL
            AND NEW.approved_by IS NOT NULL
            AND TRIM(NEW.approved_by) <> ''
            AND NEW.approved_at IS NOT NULL
        )) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Approved catalog content is immutable';
    END IF;
END$$

CREATE TRIGGER trg_catalog_source_immutable_delete
BEFORE DELETE ON catalog_source
FOR EACH ROW
BEGIN
    IF (SELECT status FROM catalog_version WHERE id = OLD.catalog_version_id) IN ('APPROVED', 'RETIRED')
        OR EXISTS (SELECT 1 FROM catalog_reconciliation WHERE catalog_version_id = OLD.catalog_version_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Approved catalog content is immutable';
    END IF;
END$$

CREATE TRIGGER trg_catalog_sku_immutable_insert
BEFORE INSERT ON catalog_sku
FOR EACH ROW
BEGIN
    IF (SELECT status FROM catalog_version WHERE id = NEW.catalog_version_id) IN ('APPROVED', 'RETIRED')
        OR EXISTS (SELECT 1 FROM catalog_reconciliation WHERE catalog_version_id = NEW.catalog_version_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Approved catalog content is immutable';
    END IF;
END$$

CREATE TRIGGER trg_catalog_sku_immutable_update
BEFORE UPDATE ON catalog_sku
FOR EACH ROW
BEGIN
    IF (SELECT status FROM catalog_version WHERE id = OLD.catalog_version_id) IN ('APPROVED', 'RETIRED')
        OR EXISTS (SELECT 1 FROM catalog_reconciliation WHERE catalog_version_id = OLD.catalog_version_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Approved catalog content is immutable';
    END IF;
END$$

CREATE TRIGGER trg_catalog_sku_immutable_delete
BEFORE DELETE ON catalog_sku
FOR EACH ROW
BEGIN
    IF (SELECT status FROM catalog_version WHERE id = OLD.catalog_version_id) IN ('APPROVED', 'RETIRED')
        OR EXISTS (SELECT 1 FROM catalog_reconciliation WHERE catalog_version_id = OLD.catalog_version_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Approved catalog content is immutable';
    END IF;
END$$

CREATE TRIGGER trg_catalog_alias_immutable_insert
BEFORE INSERT ON catalog_alias
FOR EACH ROW
BEGIN
    IF (SELECT status FROM catalog_version WHERE id = NEW.catalog_version_id) IN ('APPROVED', 'RETIRED')
        OR EXISTS (SELECT 1 FROM catalog_reconciliation WHERE catalog_version_id = NEW.catalog_version_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Approved catalog content is immutable';
    END IF;
END$$

CREATE TRIGGER trg_catalog_alias_immutable_update
BEFORE UPDATE ON catalog_alias
FOR EACH ROW
BEGIN
    IF (SELECT status FROM catalog_version WHERE id = OLD.catalog_version_id) IN ('APPROVED', 'RETIRED')
        OR EXISTS (SELECT 1 FROM catalog_reconciliation WHERE catalog_version_id = OLD.catalog_version_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Approved catalog content is immutable';
    END IF;
END$$

CREATE TRIGGER trg_catalog_alias_immutable_delete
BEFORE DELETE ON catalog_alias
FOR EACH ROW
BEGIN
    IF (SELECT status FROM catalog_version WHERE id = OLD.catalog_version_id) IN ('APPROVED', 'RETIRED')
        OR EXISTS (SELECT 1 FROM catalog_reconciliation WHERE catalog_version_id = OLD.catalog_version_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Approved catalog content is immutable';
    END IF;
END$$

CREATE TRIGGER trg_catalog_mapping_immutable_insert
BEFORE INSERT ON catalog_mapping
FOR EACH ROW
BEGIN
    IF (SELECT status FROM catalog_version WHERE id = NEW.catalog_version_id) IN ('APPROVED', 'RETIRED')
        OR EXISTS (SELECT 1 FROM catalog_reconciliation WHERE catalog_version_id = NEW.catalog_version_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Approved catalog content is immutable';
    END IF;
END$$

CREATE TRIGGER trg_catalog_mapping_immutable_update
BEFORE UPDATE ON catalog_mapping
FOR EACH ROW
BEGIN
    IF (SELECT status FROM catalog_version WHERE id = OLD.catalog_version_id) IN ('APPROVED', 'RETIRED')
        OR EXISTS (SELECT 1 FROM catalog_reconciliation WHERE catalog_version_id = OLD.catalog_version_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Approved catalog content is immutable';
    END IF;
END$$

CREATE TRIGGER trg_catalog_mapping_immutable_delete
BEFORE DELETE ON catalog_mapping
FOR EACH ROW
BEGIN
    IF (SELECT status FROM catalog_version WHERE id = OLD.catalog_version_id) IN ('APPROVED', 'RETIRED')
        OR EXISTS (SELECT 1 FROM catalog_reconciliation WHERE catalog_version_id = OLD.catalog_version_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Approved catalog content is immutable';
    END IF;
END$$

CREATE TRIGGER trg_confirmed_revision_immutable_update
BEFORE UPDATE ON analysis_revision
FOR EACH ROW
BEGIN
    IF OLD.state = 'CONFIRMED' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Confirmed revisions are immutable';
    END IF;
END$$

CREATE TRIGGER trg_correction_revision_requires_confirmed_predecessor
BEFORE INSERT ON analysis_revision
FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1 FROM analysis_revision
        WHERE analysis_id = NEW.analysis_id AND state = 'CONFIRMED'
    ) AND NOT EXISTS (
        SELECT 1 FROM analysis_revision
        WHERE id = NEW.predecessor_revision_id
            AND analysis_id = NEW.analysis_id
            AND state = 'CONFIRMED'
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Corrections must link the confirmed predecessor revision';
    END IF;
END$$

CREATE TRIGGER trg_confirmed_revision_immutable_delete
BEFORE DELETE ON analysis_revision
FOR EACH ROW
BEGIN
    IF OLD.state = 'CONFIRMED' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Confirmed revisions are immutable';
    END IF;
END$$

CREATE TRIGGER trg_confirmed_analysis_line_immutable_insert
BEFORE INSERT ON analysis_line
FOR EACH ROW
BEGIN
    IF (SELECT state FROM analysis_revision WHERE id = NEW.analysis_revision_id) = 'CONFIRMED' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Confirmed analysis child records are immutable';
    END IF;
END$$

CREATE TRIGGER trg_confirmed_analysis_line_immutable_update
BEFORE UPDATE ON analysis_line
FOR EACH ROW
BEGIN
    IF (SELECT state FROM analysis_revision WHERE id = OLD.analysis_revision_id) = 'CONFIRMED' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Confirmed analysis child records are immutable';
    END IF;
END$$

CREATE TRIGGER trg_confirmed_analysis_line_immutable_delete
BEFORE DELETE ON analysis_line
FOR EACH ROW
BEGIN
    IF (SELECT state FROM analysis_revision WHERE id = OLD.analysis_revision_id) = 'CONFIRMED' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Confirmed analysis child records are immutable';
    END IF;
END$$

CREATE TRIGGER trg_confirmed_option_selection_immutable_insert
BEFORE INSERT ON option_selection
FOR EACH ROW
BEGIN
    IF (SELECT state FROM analysis_revision WHERE id = NEW.analysis_revision_id) = 'CONFIRMED' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Confirmed analysis child records are immutable';
    END IF;
END$$

CREATE TRIGGER trg_confirmed_option_selection_immutable_update
BEFORE UPDATE ON option_selection
FOR EACH ROW
BEGIN
    IF (SELECT state FROM analysis_revision WHERE id = OLD.analysis_revision_id) = 'CONFIRMED' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Confirmed analysis child records are immutable';
    END IF;
END$$

CREATE TRIGGER trg_confirmed_option_selection_immutable_delete
BEFORE DELETE ON option_selection
FOR EACH ROW
BEGIN
    IF (SELECT state FROM analysis_revision WHERE id = OLD.analysis_revision_id) = 'CONFIRMED' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Confirmed analysis child records are immutable';
    END IF;
END$$

CREATE TRIGGER trg_calculation_result_requires_complete_ready_revision
BEFORE INSERT ON calculation_result
FOR EACH ROW
BEGIN
    IF (SELECT state FROM analysis_revision WHERE id = NEW.analysis_revision_id) <> 'READY_TO_CALCULATE' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Calculation results require a ready analysis revision';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM analysis_line
        WHERE analysis_revision_id = NEW.analysis_revision_id
            AND decision IN ('CONFIRMED', 'CORRECTED_CONFIRMED')
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Calculation results require an included confirmed line';
    END IF;

    IF EXISTS (
        SELECT 1
        FROM analysis_line AS line_item
        WHERE line_item.analysis_revision_id = NEW.analysis_revision_id
            AND (
                line_item.decision = 'PENDING'
                OR (line_item.decision = 'EXCLUDED' AND (line_item.exclusion_reason IS NULL OR TRIM(line_item.exclusion_reason) = ''))
                OR (line_item.decision IN ('CONFIRMED', 'CORRECTED_CONFIRMED') AND (
                    line_item.resolved_sku IS NULL
                    OR line_item.resolved_description IS NULL
                    OR TRIM(line_item.resolved_description) = ''
                    OR line_item.quantity IS NULL
                    OR line_item.quantity <= 0
                    OR line_item.annual_usd_price IS NULL
                    OR line_item.annual_usd_price < 0
                    OR line_item.currency <> 'USD'
                    OR line_item.annual_term <> 'ANNUAL'
                    OR line_item.unit_basis IS NULL
                    OR TRIM(line_item.unit_basis) = ''
                    OR MOD(line_item.quantity * line_item.annual_usd_price, 0.0100) <> 0
                    OR NOT EXISTS (
                        SELECT 1 FROM option_selection
                        WHERE analysis_line_id = line_item.id
                            AND analysis_revision_id = NEW.analysis_revision_id
                    )
                ))
            )
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Calculation results require complete exact-cent line inputs';
    END IF;

    IF NEW.three_year_total <> NEW.annual_total * 3 OR NEW.five_year_total <> NEW.annual_total * 5 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Calculation totals must use exact annual multiples';
    END IF;
END$$

CREATE TRIGGER trg_calculation_result_immutable_update
BEFORE UPDATE ON calculation_result
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Calculation results are immutable';
END$$

CREATE TRIGGER trg_calculation_result_immutable_delete
BEFORE DELETE ON calculation_result
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Calculation results are immutable';
END$$

CREATE TRIGGER trg_audit_event_append_only_update
BEFORE UPDATE ON audit_event
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Audit events are append-only';
END$$

CREATE TRIGGER trg_audit_event_append_only_delete
BEFORE DELETE ON audit_event
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Audit events are append-only';
END$$

CREATE PROCEDURE record_catalog_reconciliation(
    IN p_id CHAR(36),
    IN p_catalog_version_id CHAR(36),
    IN p_workbook_sha256 CHAR(64),
    IN p_source_digest CHAR(64),
    IN p_sku_digest CHAR(64),
    IN p_alias_digest CHAR(64),
    IN p_mapping_digest CHAR(64),
    IN p_report JSON
)
SQL SECURITY DEFINER
BEGIN
    DECLARE v_source_digest CHAR(64);
    DECLARE v_sku_digest CHAR(64);
    DECLARE v_alias_digest CHAR(64);
    DECLARE v_mapping_digest CHAR(64);

    IF JSON_UNQUOTE(JSON_EXTRACT(p_report, '$.status')) <> 'PASS' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Catalog reconciliation report must pass';
    END IF;

    SET SESSION group_concat_max_len = 16777216;
    SELECT SHA2(CONCAT(
        '[',
        COALESCE(GROUP_CONCAT(CONCAT(
            '{"citation":', JSON_QUOTE(citation),
            ',"worksheet_reference":', JSON_QUOTE(worksheet_reference),
            '}'
        ) ORDER BY worksheet_reference, citation SEPARATOR ','), ''),
        ']'
    ), 256)
    INTO v_source_digest
    FROM catalog_source
    WHERE catalog_version_id = p_catalog_version_id;

    SELECT SHA2(CONCAT(
        '[',
        COALESCE(GROUP_CONCAT(CONCAT(
            '{"governed_sku":', JSON_QUOTE(sku.governed_sku),
            ',"description":', JSON_QUOTE(sku.description),
            ',"unit":', JSON_QUOTE(sku.unit),
            ',"annual_usd_price":', IF(sku.annual_usd_price IS NULL, 'null', JSON_QUOTE(CAST(sku.annual_usd_price AS CHAR))),
            ',"effective_from":', JSON_QUOTE(CAST(sku.effective_from AS CHAR)),
            ',"effective_to":', IF(sku.effective_to IS NULL, 'null', JSON_QUOTE(CAST(sku.effective_to AS CHAR))),
            ',"source_citation":', JSON_QUOTE(source.citation),
            ',"source_worksheet_reference":', JSON_QUOTE(source.worksheet_reference),
            '}'
        ) ORDER BY sku.governed_sku SEPARATOR ','), ''),
        ']'
    ), 256)
    INTO v_sku_digest
    FROM catalog_sku AS sku
    INNER JOIN catalog_source AS source
        ON source.id = sku.catalog_source_id
        AND source.catalog_version_id = sku.catalog_version_id
    WHERE sku.catalog_version_id = p_catalog_version_id;

    SELECT SHA2(CONCAT(
        '[',
        COALESCE(GROUP_CONCAT(CONCAT(
            '{"governed_sku":', JSON_QUOTE(sku.governed_sku),
            ',"normalized_alias":', JSON_QUOTE(alias.normalized_alias),
            '}'
        ) ORDER BY sku.governed_sku, alias.normalized_alias SEPARATOR ','), ''),
        ']'
    ), 256)
    INTO v_alias_digest
    FROM catalog_alias AS alias
    INNER JOIN catalog_sku AS sku
        ON sku.id = alias.catalog_sku_id
        AND sku.catalog_version_id = alias.catalog_version_id
    WHERE alias.catalog_version_id = p_catalog_version_id;

    SELECT SHA2(CONCAT(
        '[',
        COALESCE(GROUP_CONCAT(CONCAT(
            '{"source_sku":', JSON_QUOTE(source.governed_sku),
            ',"oracle_sku":', JSON_QUOTE(target.governed_sku),
            ',"rationale":', JSON_QUOTE(mapping.rationale),
            '}'
        ) ORDER BY source.governed_sku, target.governed_sku, mapping.rationale SEPARATOR ','), ''),
        ']'
    ), 256)
    INTO v_mapping_digest
    FROM catalog_mapping AS mapping
    INNER JOIN catalog_sku AS source
        ON source.id = mapping.source_catalog_sku_id
        AND source.catalog_version_id = mapping.catalog_version_id
    INNER JOIN catalog_sku AS target
        ON target.id = mapping.oracle_catalog_sku_id
        AND target.catalog_version_id = mapping.catalog_version_id
    WHERE mapping.catalog_version_id = p_catalog_version_id;

    IF NOT EXISTS (
        SELECT 1
        FROM catalog_version AS version
        INNER JOIN catalog_import_baseline AS baseline
            ON baseline.workbook_sha256 = version.source_sha256
        WHERE version.id = p_catalog_version_id
            AND version.status = 'DRAFT'
            AND version.source_sha256 = p_workbook_sha256
            AND baseline.rh_catalog_digest = JSON_UNQUOTE(JSON_EXTRACT(p_report, '$.digests.rh_catalog'))
            AND baseline.ol_catalog_digest = JSON_UNQUOTE(JSON_EXTRACT(p_report, '$.digests.ol_catalog'))
            AND baseline.mapping_digest = JSON_UNQUOTE(JSON_EXTRACT(p_report, '$.digests.mapping'))
            AND baseline.sources_digest = JSON_UNQUOTE(JSON_EXTRACT(p_report, '$.digests.sources'))
            AND baseline.checks_digest = JSON_UNQUOTE(JSON_EXTRACT(p_report, '$.digests.checks'))
            AND baseline.source_digest = p_source_digest
            AND baseline.sku_digest = p_sku_digest
            AND baseline.alias_digest = p_alias_digest
            AND baseline.staged_mapping_digest = p_mapping_digest
            AND v_source_digest = p_source_digest
            AND v_sku_digest = p_sku_digest
            AND v_alias_digest = p_alias_digest
            AND v_mapping_digest = p_mapping_digest
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Catalog reconciliation does not match a registered import baseline';
    END IF;

    INSERT INTO catalog_reconciliation (
        id,
        catalog_version_id,
        status,
        workbook_sha256,
        rh_catalog_digest,
        ol_catalog_digest,
        mapping_table_digest,
        sources_table_digest,
        checks_digest,
        source_digest,
        sku_digest,
        alias_digest,
        mapping_digest,
        report
    ) VALUES (
        p_id,
        p_catalog_version_id,
        'PASS',
        p_workbook_sha256,
        JSON_UNQUOTE(JSON_EXTRACT(p_report, '$.digests.rh_catalog')),
        JSON_UNQUOTE(JSON_EXTRACT(p_report, '$.digests.ol_catalog')),
        JSON_UNQUOTE(JSON_EXTRACT(p_report, '$.digests.mapping')),
        JSON_UNQUOTE(JSON_EXTRACT(p_report, '$.digests.sources')),
        JSON_UNQUOTE(JSON_EXTRACT(p_report, '$.digests.checks')),
        p_source_digest,
        p_sku_digest,
        p_alias_digest,
        p_mapping_digest,
        p_report
    );
END$$
DELIMITER ;
-- END upstream 003_audit_guards.sql

-- BEGIN upstream 004_demo_analysis_evidence.sql
ALTER TABLE analysis_revision
    MODIFY COLUMN extraction_mode VARCHAR(16) NULL,
    ADD COLUMN extraction_contract_version VARCHAR(64) NULL AFTER extraction_mode,
    ADD COLUMN prompt_template_version VARCHAR(64) NULL AFTER extraction_contract_version,
    ADD COLUMN ai_response_digest CHAR(64) NULL AFTER model_identifier,
    ADD CONSTRAINT chk_analysis_revision_response_digest
        CHECK (ai_response_digest IS NULL OR REGEXP_LIKE(ai_response_digest, '^[0-9a-f]{64}$', 'c'));

ALTER TABLE audit_event
    ADD CONSTRAINT chk_audit_event_demo_action CHECK (BINARY action IN (
        'ANALYSIS_CREATED', 'EXTRACTION_COMPLETED', 'EXTRACTION_FAILED',
        'LINE_CONFIRMED', 'LINE_CORRECTED', 'LINE_EXCLUDED',
        'ANALYSIS_CONFIRMED', 'CALCULATION_PRODUCED', 'CALCULATION_WITHHELD'
    )),
    ADD CONSTRAINT chk_audit_event_demo_payload CHECK (payload IS NULL);

ALTER TABLE calculation_result
    DROP CHECK chk_calculation_totals,
    RENAME COLUMN annual_total TO source_annual_total,
    RENAME COLUMN three_year_total TO source_three_year_total,
    RENAME COLUMN five_year_total TO source_five_year_total,
    ADD COLUMN option_annual_total DECIMAL(19,4) NOT NULL AFTER source_five_year_total,
    ADD COLUMN option_three_year_total DECIMAL(19,4) NOT NULL AFTER option_annual_total,
    ADD COLUMN option_five_year_total DECIMAL(19,4) NOT NULL AFTER option_three_year_total,
    ADD CONSTRAINT chk_calculation_totals CHECK (
        source_annual_total >= 0 AND source_three_year_total >= 0 AND source_five_year_total >= 0
        AND option_annual_total >= 0 AND option_three_year_total >= 0 AND option_five_year_total >= 0
    );

DROP TRIGGER trg_calculation_result_requires_complete_ready_revision;

DELIMITER $$
CREATE TRIGGER trg_calculation_result_requires_complete_ready_revision
BEFORE INSERT ON calculation_result
FOR EACH ROW
BEGIN
    IF (SELECT state FROM analysis_revision WHERE id = NEW.analysis_revision_id) <> 'READY_TO_CALCULATE' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Calculation results require a ready analysis revision';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM analysis_line
        WHERE analysis_revision_id = NEW.analysis_revision_id
            AND decision IN ('CONFIRMED', 'CORRECTED_CONFIRMED')
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Calculation results require an included confirmed line';
    END IF;

    IF EXISTS (
        SELECT 1
        FROM analysis_line AS line_item
        WHERE line_item.analysis_revision_id = NEW.analysis_revision_id
            AND (
                line_item.decision = 'PENDING'
                OR (line_item.decision = 'EXCLUDED' AND (line_item.exclusion_reason IS NULL OR TRIM(line_item.exclusion_reason) = ''))
                OR (line_item.decision IN ('CONFIRMED', 'CORRECTED_CONFIRMED') AND (
                    line_item.resolved_sku IS NULL
                    OR line_item.resolved_description IS NULL
                    OR TRIM(line_item.resolved_description) = ''
                    OR line_item.quantity IS NULL
                    OR line_item.quantity <= 0
                    OR line_item.annual_usd_price IS NULL
                    OR line_item.annual_usd_price < 0
                    OR line_item.currency <> 'USD'
                    OR line_item.annual_term <> 'ANNUAL'
                    OR line_item.unit_basis IS NULL
                    OR TRIM(line_item.unit_basis) = ''
                    OR MOD(line_item.quantity * line_item.annual_usd_price, 0.0100) <> 0
                    OR NOT EXISTS (
                        SELECT 1 FROM option_selection
                        WHERE analysis_line_id = line_item.id
                            AND analysis_revision_id = NEW.analysis_revision_id
                    )
                ))
            )
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Calculation results require complete exact-cent line inputs';
    END IF;

    IF NEW.source_three_year_total <> NEW.source_annual_total * 3
        OR NEW.source_five_year_total <> NEW.source_annual_total * 5
        OR NEW.option_three_year_total <> NEW.option_annual_total * 3
        OR NEW.option_five_year_total <> NEW.option_annual_total * 5 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Calculation totals must use exact annual multiples';
    END IF;
END$$
DELIMITER ;
-- END upstream 004_demo_analysis_evidence.sql

-- BEGIN upstream 005_extraction_candidates.sql
ALTER TABLE analysis_line
    ADD COLUMN extraction_line_id VARCHAR(128) NULL AFTER analysis_revision_id,
    ADD CONSTRAINT uq_analysis_line_extraction_id UNIQUE (analysis_revision_id, extraction_line_id);
-- END upstream 005_extraction_candidates.sql

-- BEGIN upstream 006_coverage_comparisons.sql
-- Additive, synthetic coverage workflow. No backfill or real catalog activation.
CREATE TABLE coverage_comparison (
    id CHAR(36) NOT NULL PRIMARY KEY,
    creator_subject VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_cs NOT NULL,
    current_revision_id CHAR(36) NULL,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6)
) ENGINE=InnoDB;

CREATE TABLE coverage_revision (
    id CHAR(36) NOT NULL PRIMARY KEY,
    comparison_id CHAR(36) NOT NULL,
    predecessor_id CHAR(36) NULL,
    input_json JSON NOT NULL,
    evaluation_json JSON NOT NULL,
    catalog_version VARCHAR(100) NOT NULL,
    catalog_sha256 CHAR(64) NOT NULL,
    rule_version VARCHAR(100) NOT NULL,
    state VARCHAR(32) NOT NULL,
    confirmed_by VARCHAR(255) NULL,
    confirmed_at DATETIME(6) NULL,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    UNIQUE KEY uq_coverage_revision_comparison (id, comparison_id),
    CONSTRAINT fk_coverage_revision_owner FOREIGN KEY (comparison_id) REFERENCES coverage_comparison(id),
    CONSTRAINT fk_coverage_predecessor FOREIGN KEY (predecessor_id, comparison_id) REFERENCES coverage_revision(id, comparison_id),
    CONSTRAINT chk_coverage_state CHECK (state IN ('INCOMPLETE', 'READY_TO_CALCULATE', 'CONFIRMED')),
    CONSTRAINT chk_coverage_confirmation CHECK ((state = 'CONFIRMED' AND confirmed_by IS NOT NULL AND confirmed_at IS NOT NULL) OR (state <> 'CONFIRMED' AND confirmed_by IS NULL AND confirmed_at IS NULL))
) ENGINE=InnoDB;

ALTER TABLE coverage_comparison ADD CONSTRAINT fk_coverage_current FOREIGN KEY (current_revision_id, id) REFERENCES coverage_revision(id, comparison_id);

CREATE TABLE coverage_audit (
    id CHAR(36) NOT NULL PRIMARY KEY,
    revision_id CHAR(36) NOT NULL,
    actor_subject VARCHAR(255) NOT NULL,
    action VARCHAR(32) NOT NULL,
    occurred_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    CONSTRAINT fk_coverage_audit_revision FOREIGN KEY (revision_id) REFERENCES coverage_revision(id),
    CONSTRAINT chk_coverage_audit_action CHECK (action IN ('REVISION_CREATED', 'COMPARISON_CONFIRMED'))
) ENGINE=InnoDB;

DELIMITER $$
CREATE TRIGGER coverage_revision_insert_guard BEFORE INSERT ON coverage_revision FOR EACH ROW
BEGIN
    IF NEW.state = 'CONFIRMED' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Coverage revisions must be reviewed before confirmation';
    END IF;
END$$
CREATE TRIGGER coverage_revision_update_guard BEFORE UPDATE ON coverage_revision FOR EACH ROW
BEGIN
    IF OLD.state <> 'READY_TO_CALCULATE' OR NEW.state <> 'CONFIRMED'
       OR NOT (OLD.id <=> NEW.id) OR NOT (OLD.comparison_id <=> NEW.comparison_id)
       OR NOT (OLD.predecessor_id <=> NEW.predecessor_id)
       OR NOT (OLD.input_json <=> NEW.input_json) OR NOT (OLD.evaluation_json <=> NEW.evaluation_json)
       OR NOT (OLD.catalog_version <=> NEW.catalog_version) OR NOT (OLD.catalog_sha256 <=> NEW.catalog_sha256)
       OR NOT (OLD.rule_version <=> NEW.rule_version) OR NOT (OLD.created_at <=> NEW.created_at) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Coverage revision facts are immutable; save a successor';
    END IF;
END$$
CREATE TRIGGER coverage_revision_delete_guard BEFORE DELETE ON coverage_revision FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Coverage revisions are immutable';
END$$
CREATE TRIGGER coverage_comparison_update_guard BEFORE UPDATE ON coverage_comparison FOR EACH ROW
BEGIN
    IF NOT (OLD.id <=> NEW.id) OR NOT (OLD.creator_subject <=> NEW.creator_subject) OR NOT (OLD.created_at <=> NEW.created_at) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Coverage ownership is immutable';
    END IF;
END$$
CREATE TRIGGER coverage_audit_update_guard BEFORE UPDATE ON coverage_audit FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Coverage audit is immutable';
END$$
CREATE TRIGGER coverage_audit_delete_guard BEFORE DELETE ON coverage_audit FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Coverage audit is immutable';
END$$
DELIMITER ;
-- END upstream 006_coverage_comparisons.sql

-- BEGIN upstream 007_coverage_revision_packages.sql
-- Exact comparison evidence, not a publication repository or activation record.
CREATE TABLE coverage_revision_package (
    revision_id CHAR(36) NOT NULL PRIMARY KEY,
    context_json JSON NOT NULL,
    manifest_bytes LONGBLOB NOT NULL,
    mapping_bytes LONGBLOB NOT NULL,
    price_bytes LONGBLOB NOT NULL,
    CONSTRAINT fk_coverage_package_revision FOREIGN KEY (revision_id) REFERENCES coverage_revision(id),
    CONSTRAINT chk_coverage_package_bounds CHECK (
        OCTET_LENGTH(manifest_bytes) BETWEEN 1 AND 65536
        AND OCTET_LENGTH(mapping_bytes) BETWEEN 1 AND 8388608
        AND OCTET_LENGTH(price_bytes) BETWEEN 1 AND 8388608
        AND OCTET_LENGTH(context_json) <= 8192
    )
) ENGINE=InnoDB;

DELIMITER $$
CREATE TRIGGER coverage_package_insert_guard BEFORE INSERT ON coverage_revision_package FOR EACH ROW
BEGIN
    DECLARE revision_state VARCHAR(32);
    DECLARE revision_version VARCHAR(100);
    DECLARE revision_digest CHAR(64);
    DECLARE revision_rule VARCHAR(100);
    DECLARE result_context JSON;
    -- Lock the parent so attachment insertion cannot race confirmation.
    SELECT state,catalog_version,catalog_sha256,rule_version,JSON_EXTRACT(evaluation_json,'$.catalogContext')
        INTO revision_state,revision_version,revision_digest,revision_rule,result_context
        FROM coverage_revision WHERE id=NEW.revision_id FOR UPDATE;
    IF revision_state IS NULL OR revision_state = 'CONFIRMED' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Catalog attachments require an unconfirmed revision';
    END IF;
    IF JSON_SCHEMA_VALID('{
        "type":"object","additionalProperties":false,
        "required":["format","channel","generation","releaseId","manifestSha256","mappingId","mappingSha256","priceId","priceSha256","profileId","inputSchema","resultSchema","ruleModel","olamRule","vmwareRule"],
        "properties":{
            "format":{"type":"string","enum":["catalog-snapshot-1"]},
            "channel":{"type":"string","pattern":"^[A-Za-z0-9][A-Za-z0-9._-]{0,99}$"},
            "generation":{"type":"integer","minimum":0,"maximum":9223372036854775807},
            "releaseId":{"type":"string","pattern":"^[A-Za-z0-9][A-Za-z0-9._-]{0,99}$"},
            "manifestSha256":{"type":"string","pattern":"^[0-9a-f]{64}$"},
            "mappingId":{"type":"string","pattern":"^[A-Za-z0-9][A-Za-z0-9._-]{0,99}$"},
            "mappingSha256":{"type":"string","pattern":"^[0-9a-f]{64}$"},
            "priceId":{"type":"string","pattern":"^[A-Za-z0-9][A-Za-z0-9._-]{0,99}$"},
            "priceSha256":{"type":"string","pattern":"^[0-9a-f]{64}$"},
            "profileId":{"type":"string","enum":["legacy-coverage","scenario-1.3.1"]},
            "inputSchema":{"type":"string"},"resultSchema":{"type":"string"},"ruleModel":{"type":"string"},
            "olamRule":{"type":"string","enum":["OLAM-2026-06-15"]},
            "vmwareRule":{"type":"string","enum":["VCF-CORE-PER-PROCESSOR-16-v1"]}
        }
    }',NEW.context_json) <> 1
       OR JSON_TYPE(JSON_EXTRACT(NEW.context_json,'$.generation')) <> 'INTEGER' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Catalog snapshot context shape is invalid';
    END IF;
    IF NOT (
        (NEW.context_json->>'$.profileId' = 'legacy-coverage'
          AND NEW.context_json->>'$.inputSchema' = 'legacy-coverage'
          AND NEW.context_json->>'$.resultSchema' = 'legacy-coverage'
          AND NEW.context_json->>'$.ruleModel' = 'coverage-1.2.0')
        OR (NEW.context_json->>'$.profileId' = 'scenario-1.3.1'
          AND NEW.context_json->>'$.inputSchema' = '1.3.1'
          AND NEW.context_json->>'$.resultSchema' = '1.3.1'
          AND NEW.context_json->>'$.ruleModel' = 'coverage-1.3.1')
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Catalog snapshot profile contract is invalid';
    END IF;
    IF NOT (BINARY revision_version <=> BINARY (NEW.context_json->>'$.releaseId'))
       OR NOT (BINARY revision_digest <=> BINARY (NEW.context_json->>'$.manifestSha256'))
       OR NOT (BINARY revision_rule <=> BINARY (NEW.context_json->>'$.olamRule'))
       OR NOT (JSON_TYPE(JSON_EXTRACT(result_context,'$.generation')) <=> 'INTEGER')
       OR NOT (result_context <=> NEW.context_json)
       OR NOT (BINARY SHA2(NEW.manifest_bytes,256) <=> BINARY (NEW.context_json->>'$.manifestSha256'))
       OR NOT (BINARY SHA2(NEW.mapping_bytes,256) <=> BINARY (NEW.context_json->>'$.mappingSha256'))
       OR NOT (BINARY SHA2(NEW.price_bytes,256) <=> BINARY (NEW.context_json->>'$.priceSha256')) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Catalog attachment and revision identities disagree';
    END IF;
    IF NOT JSON_VALID(CONVERT(NEW.manifest_bytes USING utf8mb4))
       OR NOT JSON_VALID(CONVERT(NEW.mapping_bytes USING utf8mb4))
       OR NOT JSON_VALID(CONVERT(NEW.price_bytes USING utf8mb4)) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Catalog attachment bytes must contain JSON';
    END IF;
    IF NOT (JSON_EXTRACT(CONVERT(NEW.manifest_bytes USING utf8mb4),'$.releaseId') <=> JSON_EXTRACT(NEW.context_json,'$.releaseId'))
       OR NOT (JSON_EXTRACT(CONVERT(NEW.mapping_bytes USING utf8mb4),'$.memberId') <=> JSON_EXTRACT(NEW.context_json,'$.mappingId'))
       OR NOT (JSON_EXTRACT(CONVERT(NEW.price_bytes USING utf8mb4),'$.memberId') <=> JSON_EXTRACT(NEW.context_json,'$.priceId')) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Catalog member identities disagree';
    END IF;
END$$
CREATE TRIGGER coverage_package_update_guard BEFORE UPDATE ON coverage_revision_package FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Coverage catalog attachments are immutable';
END$$
CREATE TRIGGER coverage_package_delete_guard BEFORE DELETE ON coverage_revision_package FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Coverage catalog attachments are immutable';
END$$
DELIMITER ;
-- END upstream 007_coverage_revision_packages.sql

-- BEGIN upstream 008_package_publication.sql
CREATE TABLE package_member (
    kind VARBINARY(8) NOT NULL,
    member_id VARBINARY(100) NOT NULL,
    schema_version VARBINARY(100) NOT NULL,
    sha256 VARBINARY(64) NOT NULL,
    json_bytes LONGBLOB NOT NULL,
    PRIMARY KEY (kind, member_id),
    CHECK (kind IN (_binary'mappings', _binary'prices')),
    CHECK (OCTET_LENGTH(json_bytes) BETWEEN 1 AND 8388608)
) ENGINE=InnoDB;
CREATE TABLE package_release (
    release_id VARBINARY(100) PRIMARY KEY,
    manifest_bytes LONGBLOB NOT NULL,
    manifest_sha256 VARBINARY(64) NOT NULL,
    mapping_kind VARBINARY(8) NOT NULL DEFAULT 'mappings',
    mapping_id VARBINARY(100) NOT NULL,
    price_kind VARBINARY(8) NOT NULL DEFAULT 'prices',
    price_id VARBINARY(100) NOT NULL,
    publisher VARBINARY(288) NOT NULL,
    published_at DATETIME(6) NOT NULL DEFAULT (UTC_TIMESTAMP(6)),
    FOREIGN KEY (mapping_kind, mapping_id) REFERENCES package_member(kind, member_id),
    FOREIGN KEY (price_kind, price_id) REFERENCES package_member(kind, member_id),
    CHECK (mapping_kind = _binary'mappings' AND price_kind = _binary'prices'),
    CHECK (OCTET_LENGTH(manifest_bytes) BETWEEN 1 AND 65536)
) ENGINE=InnoDB;
CREATE TABLE package_admission (
    release_id VARBINARY(100) PRIMARY KEY,
    acceptance_bytes LONGBLOB NOT NULL,
    acceptance_sha256 VARBINARY(64) NOT NULL,
    evidence_bytes LONGBLOB NOT NULL,
    evidence_sha256 VARBINARY(64) NOT NULL,
    publisher VARBINARY(288) NOT NULL,
    approval_reference VARBINARY(1024) NOT NULL,
    admitted_at DATETIME(6) NOT NULL DEFAULT (UTC_TIMESTAMP(6)),
    FOREIGN KEY (release_id) REFERENCES package_release(release_id),
    CHECK (OCTET_LENGTH(acceptance_bytes) BETWEEN 1 AND 4194304),
    CHECK (OCTET_LENGTH(evidence_bytes) BETWEEN 1 AND 65536),
    CHECK (OCTET_LENGTH(approval_reference) BETWEEN 1 AND 1024)
) ENGINE=InnoDB;
CREATE TABLE package_channel (
    channel VARBINARY(100) PRIMARY KEY,
    release_id VARBINARY(100) NULL,
    generation BIGINT NOT NULL,
    FOREIGN KEY (release_id) REFERENCES package_release(release_id),
    CHECK (generation >= 0),
    CHECK ((generation = 0 AND release_id IS NULL) OR (generation > 0 AND release_id IS NOT NULL))
) ENGINE=InnoDB;
INSERT INTO package_channel VALUES ('coverage', NULL, 0);
CREATE TABLE package_activation (
    channel VARBINARY(100) NOT NULL,
    generation BIGINT NOT NULL,
    previous_generation BIGINT NOT NULL,
    previous_release_id VARBINARY(100) NULL,
    release_id VARBINARY(100) NOT NULL,
    publisher VARBINARY(288) NOT NULL,
    reason VARBINARY(1024) NOT NULL,
    approval_reference VARBINARY(1024) NOT NULL,
    operation VARBINARY(8) NOT NULL,
    activated_at DATETIME(6) NOT NULL DEFAULT (UTC_TIMESTAMP(6)),
    PRIMARY KEY (channel, generation),
    FOREIGN KEY (channel) REFERENCES package_channel(channel),
    FOREIGN KEY (previous_release_id) REFERENCES package_release(release_id),
    FOREIGN KEY (release_id) REFERENCES package_admission(release_id),
    CHECK (generation > 0 AND previous_generation >= 0),
    CHECK (operation IN (_binary'activate', _binary'rollback')),
    CHECK (OCTET_LENGTH(reason) BETWEEN 1 AND 1024),
    CHECK (OCTET_LENGTH(approval_reference) BETWEEN 1 AND 1024)
) ENGINE=InnoDB;
DELIMITER $$
CREATE TRIGGER package_member_insert_guard BEFORE INSERT ON package_member FOR EACH ROW
BEGIN
    IF NOT (BINARY SHA2(NEW.json_bytes,256) <=> NEW.sha256)
       OR NOT JSON_VALID(CONVERT(NEW.json_bytes USING utf8mb4)) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Package member integrity failed';
    END IF;
    IF NOT (BINARY JSON_UNQUOTE(JSON_EXTRACT(CONVERT(NEW.json_bytes USING utf8mb4),'$.memberId')) <=> NEW.member_id)
       OR NOT (BINARY JSON_UNQUOTE(JSON_EXTRACT(CONVERT(NEW.json_bytes USING utf8mb4),'$.schemaVersion')) <=> NEW.schema_version) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Package member identity failed';
    END IF;
END$$
CREATE TRIGGER package_release_insert_guard BEFORE INSERT ON package_release FOR EACH ROW
BEGIN
    DECLARE mapping_hash VARBINARY(64);
    DECLARE mapping_schema VARBINARY(100);
    DECLARE price_hash VARBINARY(64);
    DECLARE price_schema VARBINARY(100);
    DECLARE manifest JSON;
    IF NOT (BINARY SHA2(NEW.manifest_bytes,256) <=> NEW.manifest_sha256)
       OR NOT JSON_VALID(CONVERT(NEW.manifest_bytes USING utf8mb4)) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Package manifest integrity failed';
    END IF;
    SET manifest=CONVERT(NEW.manifest_bytes USING utf8mb4);
    SELECT sha256,schema_version INTO mapping_hash,mapping_schema FROM package_member WHERE kind='mappings' AND member_id=NEW.mapping_id;
    SELECT sha256,schema_version INTO price_hash,price_schema FROM package_member WHERE kind='prices' AND member_id=NEW.price_id;
    IF NOT (BINARY JSON_UNQUOTE(JSON_EXTRACT(manifest,'$.releaseId')) <=> NEW.release_id)
       OR NOT (BINARY JSON_UNQUOTE(JSON_EXTRACT(manifest,'$.members.mappings.memberId')) <=> NEW.mapping_id)
       OR NOT (BINARY JSON_UNQUOTE(JSON_EXTRACT(manifest,'$.members.prices.memberId')) <=> NEW.price_id)
       OR NOT (BINARY JSON_UNQUOTE(JSON_EXTRACT(manifest,'$.members.mappings.sha256')) <=> mapping_hash)
       OR NOT (BINARY JSON_UNQUOTE(JSON_EXTRACT(manifest,'$.members.prices.sha256')) <=> price_hash)
       OR NOT (BINARY JSON_UNQUOTE(JSON_EXTRACT(manifest,'$.members.mappings.schemaVersion')) <=> mapping_schema)
       OR NOT (BINARY JSON_UNQUOTE(JSON_EXTRACT(manifest,'$.members.prices.schemaVersion')) <=> price_schema) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Package release identity failed';
    END IF;
END$$
CREATE TRIGGER package_admission_insert_guard BEFORE INSERT ON package_admission FOR EACH ROW
BEGIN
    DECLARE manifest_hash VARBINARY(64);
    DECLARE release_publisher VARBINARY(288);
    SELECT manifest_sha256,publisher INTO manifest_hash,release_publisher FROM package_release WHERE release_id=NEW.release_id;
    IF NOT (BINARY SHA2(NEW.acceptance_bytes,256) <=> NEW.acceptance_sha256)
       OR NOT (BINARY SHA2(NEW.evidence_bytes,256) <=> NEW.evidence_sha256)
       OR NOT JSON_VALID(CONVERT(NEW.acceptance_bytes USING utf8mb4))
       OR NOT JSON_VALID(CONVERT(NEW.evidence_bytes USING utf8mb4)) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Package admission integrity failed';
    END IF;
    IF NOT (release_publisher <=> NEW.publisher)
       OR NOT (BINARY JSON_UNQUOTE(JSON_EXTRACT(CONVERT(NEW.evidence_bytes USING utf8mb4),'$.manifestSha256')) <=> manifest_hash)
       OR NOT (BINARY JSON_UNQUOTE(JSON_EXTRACT(CONVERT(NEW.evidence_bytes USING utf8mb4),'$.acceptanceSha256')) <=> NEW.acceptance_sha256) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Package admission identity failed';
    END IF;
END$$
CREATE TRIGGER package_activation_insert_guard BEFORE INSERT ON package_activation FOR EACH ROW
BEGIN
    DECLARE current_generation BIGINT;
    DECLARE current_release VARBINARY(100);
    SELECT generation,release_id INTO current_generation,current_release FROM package_channel WHERE channel=NEW.channel FOR UPDATE;
    IF current_generation IS NULL OR current_generation=9223372036854775807
       OR NEW.previous_generation <> current_generation OR NEW.generation <> current_generation+1
       OR NOT (NEW.previous_release_id <=> current_release) OR (NEW.release_id <=> current_release) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Package transition is stale or invalid';
    END IF;
    IF NEW.operation=_binary'rollback' AND NOT EXISTS(SELECT 1 FROM package_activation WHERE channel=NEW.channel AND release_id=NEW.release_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Package rollback requires retained history';
    END IF;
END$$
CREATE TRIGGER package_channel_update_guard BEFORE UPDATE ON package_channel FOR EACH ROW
BEGIN
    IF NOT (NEW.channel <=> OLD.channel) OR OLD.generation=9223372036854775807
       OR NEW.generation <> OLD.generation+1 OR (NEW.release_id <=> OLD.release_id)
       OR NOT EXISTS(SELECT 1 FROM package_activation WHERE channel=OLD.channel AND generation=NEW.generation
           AND previous_generation=OLD.generation AND (previous_release_id <=> OLD.release_id) AND release_id=NEW.release_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Package pointer requires matching transition';
    END IF;
END$$
CREATE TRIGGER package_channel_insert_guard BEFORE INSERT ON package_channel FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Package channels are fixed';
END$$
CREATE TRIGGER package_channel_delete_guard BEFORE DELETE ON package_channel FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Package channels are fixed';
END$$
CREATE TRIGGER package_member_update_guard BEFORE UPDATE ON package_member FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Published package facts are immutable';
END$$
CREATE TRIGGER package_member_delete_guard BEFORE DELETE ON package_member FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Published package facts are immutable';
END$$
CREATE TRIGGER package_release_update_guard BEFORE UPDATE ON package_release FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Published package facts are immutable';
END$$
CREATE TRIGGER package_release_delete_guard BEFORE DELETE ON package_release FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Published package facts are immutable';
END$$
CREATE TRIGGER package_admission_update_guard BEFORE UPDATE ON package_admission FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Published package facts are immutable';
END$$
CREATE TRIGGER package_admission_delete_guard BEFORE DELETE ON package_admission FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Published package facts are immutable';
END$$
CREATE TRIGGER package_activation_update_guard BEFORE UPDATE ON package_activation FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Published package facts are immutable';
END$$
CREATE TRIGGER package_activation_delete_guard BEFORE DELETE ON package_activation FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Published package facts are immutable';
END$$
DELIMITER ;
-- END upstream 008_package_publication.sql

SELECT 'V3_SCHEMA_INSTALLED' AS checkpoint;
