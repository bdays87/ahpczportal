@echo off
echo Running Accounting Module Migrations...
echo.

cd /d "c:\laragon\www\anix\ahpczportal"

echo [1/17] Creating accounting_periods table...
php artisan migrate --path=database/migrations/2026_09_01_000001_create_accounting_periods_table.php

echo [2/17] Creating cost_centers table...
php artisan migrate --path=database/migrations/2026_09_01_000002_create_cost_centers_table.php

echo [3/17] Creating chart_of_accounts table...
php artisan migrate --path=database/migrations/2026_09_01_000003_create_chart_of_accounts_table.php

echo [4/17] Creating tax_rates table...
php artisan migrate --path=database/migrations/2026_09_01_000004_create_tax_rates_table.php

echo [5/17] Creating suppliers table...
php artisan migrate --path=database/migrations/2026_09_01_000005_create_suppliers_table.php

echo [6/17] Creating journal_entries table...
php artisan migrate --path=database/migrations/2026_09_01_000006_create_journal_entries_table.php

echo [7/17] Creating journal_entry_lines table...
php artisan migrate --path=database/migrations/2026_09_01_000007_create_journal_entry_lines_table.php

echo [8/17] Creating accounts_payable_invoices table...
php artisan migrate --path=database/migrations/2026_09_01_000008_create_accounts_payable_invoices_table.php

echo [9/17] Creating accounts_payable_payments table...
php artisan migrate --path=database/migrations/2026_09_01_000009_create_accounts_payable_payments_table.php

echo [10/17] Creating ap_invoice_payments table...
php artisan migrate --path=database/migrations/2026_09_01_000010_create_ap_invoice_payments_table.php

echo [11/17] Creating accounts_receivable_invoices table...
php artisan migrate --path=database/migrations/2026_09_01_000011_create_accounts_receivable_invoices_table.php

echo [12/17] Creating accounts_receivable_receipts table...
php artisan migrate --path=database/migrations/2026_09_01_000012_create_accounts_receivable_receipts_table.php

echo [13/17] Creating ar_invoice_receipts table...
php artisan migrate --path=database/migrations/2026_09_01_000013_create_ar_invoice_receipts_table.php

echo [14/17] Creating budgets table...
php artisan migrate --path=database/migrations/2026_09_01_000014_create_budgets_table.php

echo [15/17] Creating budget_lines table...
php artisan migrate --path=database/migrations/2026_09_01_000015_create_budget_lines_table.php

echo [16/17] Creating tax_transactions table...
php artisan migrate --path=database/migrations/2026_09_01_000016_create_tax_transactions_table.php

echo [17/17] Creating accounting_audit_trail table...
php artisan migrate --path=database/migrations/2026_09_01_000017_create_accounting_audit_trail_table.php

echo.
echo ========================================
echo All accounting migrations completed!
echo ========================================
echo.
echo Next steps:
echo 1. Import the SQL seeder in phpMyAdmin: database/seeders/accounting_module_seeder.sql
echo 2. Run: php artisan optimize:clear
echo 3. Log in and check the Accounting module in navigation
echo.
pause
