# 📋 Accounting Module - Complete Installation Guide

## ⚠️ IMPORTANT: You Must Run Migrations First!

The error you're seeing means the accounting database tables haven't been created yet.

```
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'mlcscdb2026.chart_of_accounts' doesn't exist
```

This happens because you need to run migrations **BEFORE** the module can work.

---

## 🚀 Complete Installation Process

### Step 1: Run Migrations (CREATE TABLES) ⭐ **START HERE**

You have **3 options** to run the migrations:

#### **Option A: Use the Batch File (Easiest for Windows)**

1. Double-click: `run_accounting_migrations.bat`
2. Watch it create all 17 tables automatically
3. Wait for completion message
4. ✅ Done!

#### **Option B: Artisan Command (All at Once)**

Open Command Prompt or Laragon Terminal:
```bash
cd c:\laragon\www\anix\ahpczportal
php artisan migrate
```

This will run ALL pending migrations including the 17 accounting ones.

#### **Option C: Manual One-by-One (If Option A/B Fail)**

Run these commands in order:
```bash
cd c:\laragon\www\anix\ahpczportal

php artisan migrate --path=database/migrations/2026_09_01_000001_create_accounting_periods_table.php
php artisan migrate --path=database/migrations/2026_09_01_000002_create_cost_centers_table.php
php artisan migrate --path=database/migrations/2026_09_01_000003_create_chart_of_accounts_table.php
php artisan migrate --path=database/migrations/2026_09_01_000004_create_tax_rates_table.php
php artisan migrate --path=database/migrations/2026_09_01_000005_create_suppliers_table.php
php artisan migrate --path=database/migrations/2026_09_01_000006_create_journal_entries_table.php
php artisan migrate --path=database/migrations/2026_09_01_000007_create_journal_entry_lines_table.php
php artisan migrate --path=database/migrations/2026_09_01_000008_create_accounts_payable_invoices_table.php
php artisan migrate --path=database/migrations/2026_09_01_000009_create_accounts_payable_payments_table.php
php artisan migrate --path=database/migrations/2026_09_01_000010_create_ap_invoice_payments_table.php
php artisan migrate --path=database/migrations/2026_09_01_000011_create_accounts_receivable_invoices_table.php
php artisan migrate --path=database/migrations/2026_09_01_000012_create_accounts_receivable_receipts_table.php
php artisan migrate --path=database/migrations/2026_09_01_000013_create_ar_invoice_receipts_table.php
php artisan migrate --path=database/migrations/2026_09_01_000014_create_budgets_table.php
php artisan migrate --path=database/migrations/2026_09_01_000015_create_budget_lines_table.php
php artisan migrate --path=database/migrations/2026_09_01_000016_create_tax_transactions_table.php
php artisan migrate --path=database/migrations/2026_09_01_000017_create_accounting_audit_trail_table.php
```

---

### Step 2: Import Menu Structure (SQL SEEDER)

After migrations are complete, import the menu seeder:

#### **Using phpMyAdmin (Recommended)**:
1. Open **phpMyAdmin**
2. Select database: **mlcscdb2026**
3. Click **Import** tab
4. Click **Choose File**
5. Select: `database/seeders/accounting_module_seeder.sql`
6. Click **Go**
7. ✅ Wait for success message

#### **Using MySQL Command**:
```bash
mysql -u root -p mlcscdb2026 < database/seeders/accounting_module_seeder.sql
```

---

### Step 3: Clear Cache

```bash
cd c:\laragon\www\anix\ahpczportal
php artisan optimize:clear
```

Or in PowerShell:
```powershell
php artisan cache:clear; php artisan config:clear; php artisan view:clear
```

---

### Step 4: Verify Installation

1. **Check Database Tables** (in phpMyAdmin or MySQL):
   ```sql
   SHOW TABLES LIKE 'accounting_%';
   SHOW TABLES LIKE '%_of_accounts';
   SHOW TABLES LIKE 'suppliers';
   SHOW TABLES LIKE 'journal_%';
   SHOW TABLES LIKE 'accounts_%';
   SHOW TABLES LIKE 'budgets%';
   SHOW TABLES LIKE 'cost_centers';
   SHOW TABLES LIKE 'tax_%';
   ```
   Should show 17 tables.

2. **Check Menu** (in phpMyAdmin):
   ```sql
   SELECT * FROM systemmodules WHERE name = 'Accounting';
   SELECT COUNT(*) FROM submodules WHERE systemmodule_id = (SELECT id FROM systemmodules WHERE name = 'Accounting');
   ```
   Should show 1 module and 13 submodules.

3. **Log into System**:
   - Look for **"Accounting"** in navigation menu
   - Click to expand
   - Should see 13 submodules

4. **Test a Page**:
   - Visit: `http://your-domain/accounting/chart-of-accounts`
   - Should load without errors

---

## 📊 What Gets Created

### Database Tables (17 total):
1. ✅ `accounting_periods`
2. ✅ `cost_centers`
3. ✅ `chart_of_accounts`
4. ✅ `tax_rates`
5. ✅ `suppliers`
6. ✅ `journal_entries`
7. ✅ `journal_entry_lines`
8. ✅ `accounts_payable_invoices`
9. ✅ `accounts_payable_payments`
10. ✅ `ap_invoice_payments`
11. ✅ `accounts_receivable_invoices`
12. ✅ `accounts_receivable_receipts`
13. ✅ `ar_invoice_receipts`
14. ✅ `budgets`
15. ✅ `budget_lines`
16. ✅ `tax_transactions`
17. ✅ `accounting_audit_trail`

### Menu Structure:
- ✅ 1 System Module: "Accounting"
- ✅ 13 Submodules (Accounting Periods, Chart of Accounts, etc.)
- ✅ 63 Permissions
- ✅ All permissions granted to Super Admin

---

## 🔧 Troubleshooting

### Error: "Table doesn't exist"
**Cause:** Migrations not run  
**Solution:** Run Step 1 above (migrations)

### Error: "Duplicate entry for key PRIMARY"
**Cause:** SQL seeder already imported  
**Solution:** Skip Step 2, go to Step 3

### Error: "Unknown column 'status'"
**Cause:** Old version of SQL seeder  
**Solution:** Use the latest `accounting_module_seeder.sql` (v2.0)

### Error: "Class not found"
**Cause:** Autoloader not updated  
**Solution:** 
```bash
composer dump-autoload
php artisan optimize:clear
```

### Batch file doesn't work
**Cause:** Execution policy or path issues  
**Solution:** Use Option B or C from Step 1

### Module doesn't appear in menu
**Cause:** Cache or SQL seeder not imported  
**Solution:** 
1. Verify SQL seeder was imported
2. Run: `php artisan optimize:clear`
3. Log out and log back in

---

## ✅ Installation Checklist

Use this checklist to track your progress:

- [ ] **Step 1:** Run migrations (17 tables created)
  - [ ] Verify tables exist in database
  
- [ ] **Step 2:** Import SQL seeder
  - [ ] Verify systemmodules has "Accounting"
  - [ ] Verify 13 submodules exist
  - [ ] Verify 63 permissions exist
  
- [ ] **Step 3:** Clear cache
  - [ ] Run `php artisan optimize:clear`
  
- [ ] **Step 4:** Verify installation
  - [ ] Log in to system
  - [ ] See "Accounting" in navigation
  - [ ] Can expand to see 13 submodules
  - [ ] Can visit `/accounting/chart-of-accounts`
  - [ ] Page loads without errors

---

## 🎯 Quick Command Reference

### Laragon Terminal (Recommended):
```bash
# Navigate to project
cd c:\laragon\www\anix\ahpczportal

# Run all migrations
php artisan migrate

# Clear cache
php artisan optimize:clear

# Check migration status
php artisan migrate:status
```

### Check Database:
```sql
-- Count accounting tables (should be 17)
SELECT COUNT(*) FROM information_schema.tables 
WHERE table_schema = 'mlcscdb2026' 
AND (table_name LIKE 'accounting_%' 
     OR table_name LIKE '%_of_accounts'
     OR table_name LIKE 'suppliers'
     OR table_name LIKE 'journal_%'
     OR table_name LIKE 'accounts_%'
     OR table_name LIKE 'budgets%'
     OR table_name LIKE 'cost_centers'
     OR table_name LIKE 'tax_%');

-- Verify menu structure
SELECT * FROM systemmodules WHERE name = 'Accounting';
```

---

## 📞 Need Help?

1. **Check the error message** - It usually tells you what's wrong
2. **Verify migrations ran** - Check if tables exist in database
3. **Check SQL seeder imported** - Verify Accounting module in systemmodules
4. **Clear all caches** - `php artisan optimize:clear`
5. **Check documentation** - Read the 3 guide files provided

---

## 📚 Documentation Files

1. **ACCOUNTING_MODULE_README.md** - Complete feature documentation
2. **ACCOUNTING_SEEDER_GUIDE.md** - SQL import instructions
3. **ACCOUNTING_SQL_FIX_NOTICE.md** - SQL fixes and troubleshooting
4. **ACCOUNTING_INSTALLATION_GUIDE.md** - This file
5. **run_accounting_migrations.bat** - Automated migration script

---

**Status:** Complete Installation Guide  
**Created:** September 2, 2026  
**For:** AHPCZ Portal Accounting Module
