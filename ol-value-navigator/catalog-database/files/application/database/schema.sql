CREATE DATABASE IF NOT EXISTS ol_value_navigator
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_0900_ai_ci;

USE ol_value_navigator;

CREATE TABLE IF NOT EXISTS calculation_rule_version (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  version_label VARCHAR(64) NOT NULL UNIQUE,
  description VARCHAR(500) NOT NULL,
  source_reference VARCHAR(255) NOT NULL,
  governance_status ENUM('DEMONSTRATION','APPROVED','RETIRED')
    NOT NULL DEFAULT 'DEMONSTRATION',
  approved_by VARCHAR(255) NULL,
  approved_at DATETIME NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS comparison (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  source_comparison_id BIGINT UNSIGNED NULL,
  name VARCHAR(255) NOT NULL,
  status ENUM('DRAFT','NEEDS_REVIEW','CONFIRMED','CALCULATED')
    NOT NULL DEFAULT 'DRAFT',
  rule_version_id BIGINT UNSIGNED NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_comparison_source
    FOREIGN KEY (source_comparison_id) REFERENCES comparison(id)
    ON DELETE SET NULL,
  CONSTRAINT fk_comparison_rule_version
    FOREIGN KEY (rule_version_id) REFERENCES calculation_rule_version(id)
);

CREATE TABLE IF NOT EXISTS comparison_input (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  comparison_id BIGINT UNSIGNED NOT NULL,
  input_side ENUM('RHEL','ORACLE_LINUX') NOT NULL,
  raw_text MEDIUMTEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_input_comparison
    FOREIGN KEY (comparison_id) REFERENCES comparison(id)
    ON DELETE CASCADE,
  UNIQUE KEY unique_comparison_input (comparison_id, input_side)
);

CREATE TABLE IF NOT EXISTS ai_formatting_run (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  comparison_input_id BIGINT UNSIGNED NOT NULL,
  model_id VARCHAR(128) NOT NULL,
  run_status ENUM('COMPLETED','FAILED') NOT NULL,
  response_text MEDIUMTEXT NULL,
  error_code VARCHAR(64) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_ai_run_input
    FOREIGN KEY (comparison_input_id) REFERENCES comparison_input(id)
    ON DELETE CASCADE,
  INDEX idx_ai_run_input (comparison_input_id, created_at)
);

CREATE TABLE IF NOT EXISTS comparison_line (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  comparison_input_id BIGINT UNSIGNED NOT NULL,
  ai_formatting_run_id BIGINT UNSIGNED NULL,
  line_number INT UNSIGNED NOT NULL,
  comparison_group INT UNSIGNED NULL,
  entry_method ENUM('AI','MANUAL') NOT NULL,
  suggested_sku VARCHAR(128) NULL,
  suggested_description VARCHAR(500) NULL,
  suggested_quantity DECIMAL(12,2) NULL,
  suggested_annual_unit_price DECIMAL(14,2) NULL,
  ai_confidence ENUM('high','medium','low','unknown') NULL,
  ai_warnings JSON NULL,
  sku VARCHAR(128) NULL,
  description VARCHAR(500) NULL,
  quantity DECIMAL(12,2) NULL,
  annual_unit_price DECIMAL(14,2) NULL,
  review_status ENUM('AI_SUGGESTED','CONFIRMED','EXCLUDED','UNRESOLVED')
    NOT NULL DEFAULT 'AI_SUGGESTED',
  representative_note VARCHAR(500) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_line_input
    FOREIGN KEY (comparison_input_id) REFERENCES comparison_input(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_line_ai_run
    FOREIGN KEY (ai_formatting_run_id) REFERENCES ai_formatting_run(id)
    ON DELETE SET NULL,
  UNIQUE KEY unique_input_line (comparison_input_id, line_number),
  CHECK (quantity IS NULL OR quantity > 0),
  CHECK (annual_unit_price IS NULL OR annual_unit_price >= 0)
);

CREATE TABLE IF NOT EXISTS comparison_result (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  comparison_id BIGINT UNSIGNED NOT NULL UNIQUE,
  rhel_annual_total DECIMAL(16,2) NOT NULL,
  oracle_linux_annual_total DECIMAL(16,2) NOT NULL,
  annual_difference DECIMAL(16,2) NOT NULL,
  rhel_three_year_total DECIMAL(16,2) NOT NULL,
  oracle_linux_three_year_total DECIMAL(16,2) NOT NULL,
  three_year_difference DECIMAL(16,2) NOT NULL,
  rhel_five_year_total DECIMAL(16,2) NOT NULL,
  oracle_linux_five_year_total DECIMAL(16,2) NOT NULL,
  five_year_difference DECIMAL(16,2) NOT NULL,
  calculated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_result_comparison
    FOREIGN KEY (comparison_id) REFERENCES comparison(id)
    ON DELETE CASCADE,
  CHECK (rhel_annual_total >= 0),
  CHECK (oracle_linux_annual_total >= 0)
);

CREATE TABLE IF NOT EXISTS application_event (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  comparison_id BIGINT UNSIGNED NOT NULL,
  event_type VARCHAR(64) NOT NULL,
  actor_type ENUM('AI','REPRESENTATIVE','APPLICATION') NOT NULL,
  outcome ENUM('COMPLETED','CONFIRMED','REJECTED','FAILED') NOT NULL,
  details JSON NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_event_comparison
    FOREIGN KEY (comparison_id) REFERENCES comparison(id)
    ON DELETE CASCADE,
  INDEX idx_event_comparison (comparison_id, created_at)
);

INSERT INTO calculation_rule_version
  (version_label, description, source_reference, governance_status)
VALUES
  ('workshop-v1',
   'Annual line cost equals quantity multiplied by annual unit price. Three-year and five-year costs equal the confirmed annual amount multiplied by three and five.',
   'Oracle Linux Value Navigator workshop demonstration rules',
   'DEMONSTRATION')
ON DUPLICATE KEY UPDATE description = VALUES(description);
