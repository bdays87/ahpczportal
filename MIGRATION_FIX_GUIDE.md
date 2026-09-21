# Migration Fix Guide - Tables Already Exist

## 🔥 Problem
You're getting errors like:
```
SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'chart_of_accounts' already exists
SQLSTATE[42S21]: Column already exists: 1060 Duplicate column name 'provider'
```

This happens because tables/columns were already created manually or by a previous migration run.

---

## ✅ Solution: Use Artisan Command (Works Everywhere!)

### **This is the BEST solution** because:
- ✅ Works on **local dev** (Windows/Mac/Linux)
- ✅ Works on **remote Ubuntu server** via SSH
- ✅ No need for direct database access
- ✅ Safe - only marks tables that actually exist
- ✅ Interactive - asks for confirmation

---

## 🚀 How to Use

### On Local Development (Windows/Laragon):

```bash
cd C:\laragon\www\anix\ahpczportal

# Step 1: Check which tables exist
php artisan migrate:mark-complete --check

# Step 2: Mark existing migrations as complete
php artisan migrate:mark-complete

# Step 3: Run new migrations (Sage integration)
php artisan migrate
```

### On Remote Production (Ubuntu via SSH):

```bash
# Step 1: SSH into server
ssh your-user@portal.mlcscz.org.zw

# Step 2: Navigate to project
cd /var/www/portal.mlcscz.org.zw

# Step 3: Check which tables exist
php artisan migrate:mark-complete --check

# Step 4: Mark existing migrations
php artisan migrate:mark-complete

# Step 5: Run new migrations
php artisan migrate --force
```

---

## 📋 What The Command Does

### `php artisan migrate:mark-complete --check`
Shows you which tables exist:
```
🔍 Checking existing tables:

✅ smsbroadcasts exists
✅ accounting_periods exists
✅ cost_centers exists
✅ chart_of_accounts exists
❌ sage_sync_fields not found

Summary: 15 exist, 3 missing
```

### `php artisan migrate:mark-complete`
Marks existing tables as migrated:
```
🔍 Checking which tables already exist...

✅ Table exists, will mark: 2026_09_01_000001_create_accounting_periods_table
✅ Table exists, will mark: 2026_09_01_000002_create_cost_centers_table
✅ Table exists, will mark: 2026_09_01_000003_create_chart_of_accounts_table
⏭️  Already marked: 2026_07_09_055434_add_provider_test_numbers...

📋 Summary:
  - To mark as complete: 15
  - Already marked: 2
  - Tables not found: 0

Mark 15 migrations as complete? (yes/no) [yes]:
> yes

✅ Successfully marked 15 migrations as complete!

🎉 You can now run: php artisan migrate
```

---

## 🎯 Full Deployment Flow

### Local Testing:
```bash
# 1. Mark existing tables
php artisan migrate:mark-complete

# 2. Run new migrations
php artisan migrate

# 3. Test Sage endpoints
curl http://127.0.0.1:8000/api/connector/customers/pending -H "X-Api-Key: your-key"
```

### Production Deployment:
```bash
# 1. SSH to server
ssh user@portal.mlcscz.org.zw

# 2. Navigate to project
cd /var/www/portal.mlcscz.org.zw

# 3. Pull latest code
git pull origin main

# 4. Install dependencies
composer install --no-dev --optimize-autoloader

# 5. Mark existing migrations
php artisan migrate:mark-complete

# 6. Run new migrations
php artisan migrate --force

# 7. Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Test
curl https://portal.mlcscz.org.zw/api/connector/customers/pending \
     -H "X-Api-Key: your-production-key"
```

---

## �️ Alternative: SQL Script (If Command Doesn't Work)

**Only use this if the Artisan command fails or you prefer direct SQL:**

```sql
-- Connect to database
USE mlcscdb2026;

-- Mark migrations as complete
INSERT IGNORE INTO migrations (migration, batch) VALUES
('2026_07_09_055434_add_provider_test_numbers_filters_to_smsbroadcasts_table', 1),
('2026_09_01_000001_create_accounting_periods_table', 1),
('2026_09_01_000002_create_cost_centers_table', 1),
('2026_09_01_000003_create_chart_of_accounts_table', 1),
('2026_09_01_000004_create_tax_rates_table', 1),
('2026_09_01_000005_create_suppliers_table', 1),
('2026_09_01_000006_create_journal_entries_table', 1),
('2026_09_01_000007_create_journal_entry_lines_table', 1),
('2026_09_01_000008_create_accounts_payable_invoices_table', 1),
('2026_09_01_000009_create_accounts_payable_payments_table', 1),
('2026_09_01_000010_create_ap_invoice_payments_table', 1),
('2026_09_01_000011_create_accounts_receivable_invoices_table', 1),
('2026_09_01_000012_create_accounts_receivable_receipts_table', 1),
('2026_09_01_000013_create_ar_invoice_receipts_table', 1),
('2026_09_01_000014_create_budgets_table', 1),
('2026_09_01_000015_create_budget_lines_table', 1),
('2026_09_01_000016_create_tax_transactions_table', 1),
('2026_09_01_000017_create_accounting_audit_trail_table', 1);

-- Verify
SELECT COUNT(*) as marked_migrations FROM migrations 
WHERE migration LIKE '2026_09_01%';
```

**Run SQL via SSH (if no phpMyAdmin access):**
```bash
# On Ubuntu server
mysql -u root -p mlcscdb2026 < /path/to/sql_script.sql

# Or inline
mysql -u root -p mlcscdb2026 -e "INSERT IGNORE INTO migrations..."
```

---

## ✅ After Fix

Once migrations are marked, run the new Sage integration migrations:

```bash
php artisan migrate
```

You should see:
```
✓ 2026_09_21_000001_add_sage_sync_fields_to_customerprofessions [42.15ms] DONE
✓ 2026_09_21_000002_add_sage_sync_fields_to_invoices [38.92ms] DONE
✓ 2026_09_21_000003_create_customer_sage_statements_table [45.33ms] DONE

INFO  3 migrations completed successfully.
```

---

## 🎉 Summary

**Best Practice:**
1. Use `php artisan migrate:mark-complete` (works everywhere)
2. Run `php artisan migrate` to create new tables
3. Deploy with confidence!

**Why This Works:**
- The command checks if tables exist before marking
- Safe for production - won't break existing data
- Works without direct database access
- Can be run via SSH

**Files Created:**
- ✅ `app/Console/Commands/MarkMigrationsComplete.php` - The Artisan command
- ✅ Fixed migration files with `Schema::hasTable()` checks
- ✅ This guide

---

## 📞 Need Help?

If you encounter issues:

1. **Check migration status:**
   ```bash
   php artisan migrate:status
   ```

2. **Check which tables exist:**
   ```bash
   php artisan migrate:mark-complete --check
   ```

3. **View Laravel logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Manually verify tables:**
   ```bash
   mysql -u root -p -e "SHOW TABLES FROM mlcscdb2026"
   ```

---

**Ready to Deploy!** 🚀

Use the Artisan command for a smooth migration experience on both local and production!
