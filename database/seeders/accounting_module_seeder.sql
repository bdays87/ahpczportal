-- =====================================================
-- Accounting Module Seeder
-- Created: 2026-09-01
-- Updated: 2026-09-02 (Fixed column names & auto-increment IDs)
-- Description: Seeds system module, submodules, and permissions for the Accounting Module
-- =====================================================

-- IMPORTANT: First, check your current max IDs by running these queries:
-- SELECT MAX(id) FROM systemmodules;
-- SELECT MAX(id) FROM submodules;
-- SELECT MAX(id) FROM permissions;
-- Then update the IDs below if needed to avoid duplicates.

-- Insert Accounting System Module
-- Using NULL for id to let auto_increment handle it, or specify next available ID
-- Note: accounttype_id = 1 (assuming this is the Admin account type)
INSERT INTO `systemmodules` (`name`, `accounttype_id`, `icon`, `default_permission`, `created_at`, `updated_at`) 
VALUES ('Accounting', 1, 'o-calculator', 'accounting.access', NOW(), NOW());

-- Get the last inserted system module ID
SET @system_module_id = LAST_INSERT_ID();

-- Insert Accounting Submodules
-- Using @system_module_id from above to ensure correct foreign key
INSERT INTO `submodules` (`systemmodule_id`, `name`, `icon`, `default_permission`, `url`, `created_at`, `updated_at`) VALUES
(@system_module_id, 'Accounting Periods', 'o-calendar', 'accounting.periods.access', 'accounting.periods', NOW(), NOW()),
(@system_module_id, 'Chart of Accounts', 'o-list-bullet', 'accounting.chart-of-accounts.access', 'accounting.chart-of-accounts', NOW(), NOW()),
(@system_module_id, 'Journal Entries', 'o-book-open', 'accounting.journal-entries.access', 'accounting.journal-entries', NOW(), NOW()),
(@system_module_id, 'Suppliers', 'o-building-office', 'accounting.suppliers.access', 'accounting.suppliers', NOW(), NOW()),
(@system_module_id, 'AP Invoices', 'o-document-text', 'accounting.ap-invoices.access', 'accounting.ap-invoices', NOW(), NOW()),
(@system_module_id, 'AP Payments', 'o-banknotes', 'accounting.ap-payments.access', 'accounting.ap-payments', NOW(), NOW()),
(@system_module_id, 'AR Invoices', 'o-document-currency-dollar', 'accounting.ar-invoices.access', 'accounting.ar-invoices', NOW(), NOW()),
(@system_module_id, 'AR Receipts', 'o-currency-dollar', 'accounting.ar-receipts.access', 'accounting.ar-receipts', NOW(), NOW()),
(@system_module_id, 'Cost Centers', 'o-building-office-2', 'accounting.cost-centers.access', 'accounting.cost-centers', NOW(), NOW()),
(@system_module_id, 'Tax Rates', 'o-receipt-percent', 'accounting.tax-rates.access', 'accounting.tax-rates', NOW(), NOW()),
(@system_module_id, 'Budgets', 'o-chart-bar', 'accounting.budgets.access', 'accounting.budgets', NOW(), NOW()),
(@system_module_id, 'Audit Trail', 'o-clipboard-document-list', 'accounting.audit-trail.access', 'accounting.audit-trail', NOW(), NOW()),
(@system_module_id, 'Financial Reports', 'o-document-chart-bar', 'accounting.financial-reports.access', 'accounting.financial-reports', NOW(), NOW());

-- Store first submodule ID for permission mapping
SET @first_submodule_id = LAST_INSERT_ID();

-- Insert Accounting Permissions
-- Using auto-increment for permission IDs and dynamically created submodule IDs

-- System Module Permission
INSERT INTO `permissions` (`submodule_id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(@first_submodule_id, 'accounting.access', 'web', NOW(), NOW());

-- Store the first permission ID for later role assignment
SET @first_permission_id = LAST_INSERT_ID();

-- Accounting Periods Permissions (submodule_id = @first_submodule_id + 0)
INSERT INTO `permissions` (`submodule_id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(@first_submodule_id, 'accounting.periods.access', 'web', NOW(), NOW()),
(@first_submodule_id, 'accounting.periods.create', 'web', NOW(), NOW()),
(@first_submodule_id, 'accounting.periods.update', 'web', NOW(), NOW()),
(@first_submodule_id, 'accounting.periods.delete', 'web', NOW(), NOW()),
(@first_submodule_id, 'accounting.periods.close', 'web', NOW(), NOW());

-- Chart of Accounts Permissions (submodule_id = @first_submodule_id + 1)
INSERT INTO `permissions` (`submodule_id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(@first_submodule_id + 1, 'accounting.chart-of-accounts.access', 'web', NOW(), NOW()),
(@first_submodule_id + 1, 'accounting.chart-of-accounts.create', 'web', NOW(), NOW()),
(@first_submodule_id + 1, 'accounting.chart-of-accounts.update', 'web', NOW(), NOW()),
(@first_submodule_id + 1, 'accounting.chart-of-accounts.delete', 'web', NOW(), NOW());

-- Journal Entries Permissions (submodule_id = @first_submodule_id + 2)
INSERT INTO `permissions` (`submodule_id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(@first_submodule_id + 2, 'accounting.journal-entries.access', 'web', NOW(), NOW()),
(@first_submodule_id + 2, 'accounting.journal-entries.create', 'web', NOW(), NOW()),
(@first_submodule_id + 2, 'accounting.journal-entries.update', 'web', NOW(), NOW()),
(@first_submodule_id + 2, 'accounting.journal-entries.delete', 'web', NOW(), NOW()),
(@first_submodule_id + 2, 'accounting.journal-entries.post', 'web', NOW(), NOW()),
(@first_submodule_id + 2, 'accounting.journal-entries.reverse', 'web', NOW(), NOW());

-- Suppliers Permissions (submodule_id = @first_submodule_id + 3)
INSERT INTO `permissions` (`submodule_id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(@first_submodule_id + 3, 'accounting.suppliers.access', 'web', NOW(), NOW()),
(@first_submodule_id + 3, 'accounting.suppliers.create', 'web', NOW(), NOW()),
(@first_submodule_id + 3, 'accounting.suppliers.update', 'web', NOW(), NOW()),
(@first_submodule_id + 3, 'accounting.suppliers.delete', 'web', NOW(), NOW());

-- AP Invoices Permissions (submodule_id = @first_submodule_id + 4)
INSERT INTO `permissions` (`submodule_id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(@first_submodule_id + 4, 'accounting.ap-invoices.access', 'web', NOW(), NOW()),
(@first_submodule_id + 4, 'accounting.ap-invoices.create', 'web', NOW(), NOW()),
(@first_submodule_id + 4, 'accounting.ap-invoices.update', 'web', NOW(), NOW()),
(@first_submodule_id + 4, 'accounting.ap-invoices.delete', 'web', NOW(), NOW()),
(@first_submodule_id + 4, 'accounting.ap-invoices.post', 'web', NOW(), NOW()),
(@first_submodule_id + 4, 'accounting.ap-invoices.cancel', 'web', NOW(), NOW());

-- AP Payments Permissions (submodule_id = @first_submodule_id + 5)
INSERT INTO `permissions` (`submodule_id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(@first_submodule_id + 5, 'accounting.ap-payments.access', 'web', NOW(), NOW()),
(@first_submodule_id + 5, 'accounting.ap-payments.create', 'web', NOW(), NOW()),
(@first_submodule_id + 5, 'accounting.ap-payments.update', 'web', NOW(), NOW()),
(@first_submodule_id + 5, 'accounting.ap-payments.delete', 'web', NOW(), NOW());

-- AR Invoices Permissions (submodule_id = @first_submodule_id + 6)
INSERT INTO `permissions` (`submodule_id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(@first_submodule_id + 6, 'accounting.ar-invoices.access', 'web', NOW(), NOW()),
(@first_submodule_id + 6, 'accounting.ar-invoices.create', 'web', NOW(), NOW()),
(@first_submodule_id + 6, 'accounting.ar-invoices.update', 'web', NOW(), NOW()),
(@first_submodule_id + 6, 'accounting.ar-invoices.delete', 'web', NOW(), NOW()),
(@first_submodule_id + 6, 'accounting.ar-invoices.post', 'web', NOW(), NOW()),
(@first_submodule_id + 6, 'accounting.ar-invoices.cancel', 'web', NOW(), NOW());

-- AR Receipts Permissions (submodule_id = @first_submodule_id + 7)
INSERT INTO `permissions` (`submodule_id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(@first_submodule_id + 7, 'accounting.ar-receipts.access', 'web', NOW(), NOW()),
(@first_submodule_id + 7, 'accounting.ar-receipts.create', 'web', NOW(), NOW()),
(@first_submodule_id + 7, 'accounting.ar-receipts.update', 'web', NOW(), NOW()),
(@first_submodule_id + 7, 'accounting.ar-receipts.delete', 'web', NOW(), NOW());

-- Cost Centers Permissions (submodule_id = @first_submodule_id + 8)
INSERT INTO `permissions` (`submodule_id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(@first_submodule_id + 8, 'accounting.cost-centers.access', 'web', NOW(), NOW()),
(@first_submodule_id + 8, 'accounting.cost-centers.create', 'web', NOW(), NOW()),
(@first_submodule_id + 8, 'accounting.cost-centers.update', 'web', NOW(), NOW()),
(@first_submodule_id + 8, 'accounting.cost-centers.delete', 'web', NOW(), NOW());

-- Tax Rates Permissions (submodule_id = @first_submodule_id + 9)
INSERT INTO `permissions` (`submodule_id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(@first_submodule_id + 9, 'accounting.tax-rates.access', 'web', NOW(), NOW()),
(@first_submodule_id + 9, 'accounting.tax-rates.create', 'web', NOW(), NOW()),
(@first_submodule_id + 9, 'accounting.tax-rates.update', 'web', NOW(), NOW()),
(@first_submodule_id + 9, 'accounting.tax-rates.delete', 'web', NOW(), NOW());

-- Budgets Permissions (submodule_id = @first_submodule_id + 10)
INSERT INTO `permissions` (`submodule_id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(@first_submodule_id + 10, 'accounting.budgets.access', 'web', NOW(), NOW()),
(@first_submodule_id + 10, 'accounting.budgets.create', 'web', NOW(), NOW()),
(@first_submodule_id + 10, 'accounting.budgets.update', 'web', NOW(), NOW()),
(@first_submodule_id + 10, 'accounting.budgets.delete', 'web', NOW(), NOW()),
(@first_submodule_id + 10, 'accounting.budgets.activate', 'web', NOW(), NOW()),
(@first_submodule_id + 10, 'accounting.budgets.close', 'web', NOW(), NOW());

-- Audit Trail Permissions (submodule_id = @first_submodule_id + 11)
INSERT INTO `permissions` (`submodule_id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(@first_submodule_id + 11, 'accounting.audit-trail.access', 'web', NOW(), NOW()),
(@first_submodule_id + 11, 'accounting.audit-trail.view', 'web', NOW(), NOW());

-- Financial Reports Permissions (submodule_id = @first_submodule_id + 12)
INSERT INTO `permissions` (`submodule_id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(@first_submodule_id + 12, 'accounting.financial-reports.access', 'web', NOW(), NOW()),
(@first_submodule_id + 12, 'accounting.financial-reports.trial-balance', 'web', NOW(), NOW()),
(@first_submodule_id + 12, 'accounting.financial-reports.profit-loss', 'web', NOW(), NOW()),
(@first_submodule_id + 12, 'accounting.financial-reports.balance-sheet', 'web', NOW(), NOW()),
(@first_submodule_id + 12, 'accounting.financial-reports.cash-flow', 'web', NOW(), NOW()),
(@first_submodule_id + 12, 'accounting.financial-reports.general-ledger', 'web', NOW(), NOW()),
(@first_submodule_id + 12, 'accounting.financial-reports.export', 'web', NOW(), NOW());

-- =====================================================
-- Grant all accounting permissions to Super Admin role (role_id = 1)
-- =====================================================
-- This will grant all permissions created above (63 total) to the Super Admin role
-- The permissions start from @first_permission_id and go for 63 consecutive IDs

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT id, 1 
FROM `permissions` 
WHERE id >= @first_permission_id 
ORDER BY id 
LIMIT 63;

-- =====================================================
-- INSTRUCTIONS FOR USE
-- =====================================================
-- 1. Run migrations first (if not done yet):
--    php artisan migrate
--
-- 2. Import this SQL file:
--    mysql -u your_user -p your_database < database/seeders/accounting_module_seeder.sql
--    OR in phpMyAdmin: Import > Choose file > Go
--
-- 3. Verify data:
--    -- Check system module
--    SELECT * FROM systemmodules WHERE name = 'Accounting';
--    
--    -- Get the system module ID
--    SET @sm_id = (SELECT id FROM systemmodules WHERE name = 'Accounting');
--    
--    -- Check submodules (should be 13)
--    SELECT COUNT(*) FROM submodules WHERE systemmodule_id = @sm_id;
--    
--    -- Check permissions (should be 63)
--    SELECT COUNT(*) FROM permissions WHERE name LIKE 'accounting.%';
--    
--    -- Check role assignments (should be 63)
--    SELECT COUNT(*) FROM role_has_permissions WHERE permission_id IN 
--    (SELECT id FROM permissions WHERE name LIKE 'accounting.%');
--
-- 4. Clear cache:
--    php artisan optimize:clear
--
-- 5. The accounting module will appear in your navigation menu
--    with all 13 submodules and proper permissions.
--
-- NOTES:
-- - This version uses AUTO_INCREMENT so it won't conflict with existing IDs
-- - All IDs are assigned dynamically using LAST_INSERT_ID() and variables
-- - accounttype_id = 1 is assumed for Admin - change if needed
-- - Permissions are granted to role_id = 1 (Super Admin)
-- =====================================================
