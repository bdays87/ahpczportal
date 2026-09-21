-- Create accounting_audit_trails table
CREATE TABLE IF NOT EXISTS `accounting_audit_trails` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module` varchar(255) NOT NULL COMMENT 'JOURNAL, AP, AR, COA, BUDGET, etc.',
  `action` varchar(255) NOT NULL COMMENT 'CREATE, UPDATE, DELETE, POST, APPROVE, REVERSE, CLOSE',
  `record_type` varchar(255) NOT NULL COMMENT 'model class name',
  `record_id` bigint(20) UNSIGNED NOT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `old_values` text DEFAULT NULL COMMENT 'JSON',
  `new_values` text DEFAULT NULL COMMENT 'JSON',
  `ip_address` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `accounting_audit_trails_user_id_foreign` (`user_id`),
  CONSTRAINT `accounting_audit_trails_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
