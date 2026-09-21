# Seeding Guide - Production Database

## 🎯 Problem
You need to seed the accounting module data from `database/seeders/accounting_module_seeder.sql` into your Ubuntu live database, but you don't have direct database access.

## ✅ Solution - Use Artisan Command

I've created a custom Artisan command that reads and executes the SQL file via Laravel, so you can run it via SSH!

---

## 🚀 How to Use

### On Local Development (Windows/Laragon):

```bash
cd C:\laragon\www\anix\ahpczportal

# Seed accounting module
php artisan db:seed-accounting

# If you need to rollback (removes accounting module)
php artisan db:seed-accounting --rollback
```

### On Remote Production (Ubuntu via SSH):

```bash
# Step 1: SSH into server
ssh your-user@portal.mlcscz.org.zw

# Step 2: Navigate to project
cd /var/www/portal.mlcscz.org.zw

# Step 3: Seed accounting module
php artisan db:seed-accounting

# Step 4: Verify
php artisan db:seed-accounting --rollback  # (only if you need to undo)
```

---

## 📋 What The Command Does

### `php artisan db:seed-accounting`

Executes the SQL file and shows progress:

```
🚀 Seeding Accounting Module...

📝 Found 87 SQL statements

  ✓ Created Accounting system module
  ✓ Created accounting submodules
  ✓ Created 45 accounting permissions
  ✓ Created 12 accounting permissions
  ✓ Created 8 accounting permissions

✅ Successfully executed 87 SQL statements

📊 Summary:
  - System Module: Accounting (ID: 15)
  - Submodules: 13
  - Permissions: 65

🎉 Accounting module seeded successfully!
```

### `php artisan db:seed-accounting --rollback`

Removes all accounting module data (useful for testing):

```
🔄 Rolling back accounting module...

This will delete all accounting module data. Continue? (yes/no) [no]:
> yes

Found Accounting module (ID: 15)
  ✓ Deleted 65 permissions
  ✓ Deleted 13 submodules
  ✓ Deleted system module

✅ Accounting module rolled back successfully!
```

---

## 🎯 Full Production Deployment Flow

### Step 1: Deploy Code

```bash
# SSH to server
ssh user@portal.mlcscz.org.zw

# Navigate to project
cd /var/www/portal.mlcscz.org.zw

# Pull latest code (includes the SQL file)
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader
```

### Step 2: Run Migrations

```bash
# Mark existing migrations
php artisan migrate:mark-complete

# Run new migrations
php artisan migrate --force
```

### Step 3: Seed Accounting Module

```bash
# Seed accounting data
php artisan db:seed-accounting
```

### Step 4: Clear Caches

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 5: Verify

```bash
# Check if accounting module exists
mysql -u your_db_user -p mlcscdb2026 -e "SELECT * FROM systemmodules WHERE name='Accounting';"

# Or check via Laravel
php artisan tinker
>>> DB::table('systemmodules')->where('name', 'Accounting')->first();
```

---

## 🛠️ Alternative: Direct SQL (If Command Fails)

If the Artisan command doesn't work, you can run the SQL file directly via SSH:

### Option 1: Upload and Execute

```bash
# Upload SQL file to server (from local machine)
scp database/seeders/accounting_module_seeder.sql user@portal.mlcscz.org.zw:/tmp/

# SSH to server
ssh user@portal.mlcscz.org.zw

# Execute SQL file
mysql -u your_db_user -p mlcscdb2026 < /tmp/accounting_module_seeder.sql

# Clean up
rm /tmp/accounting_module_seeder.sql
```

### Option 2: Execute from Project Directory

```bash
# SSH to server
ssh user@portal.mlcscz.org.zw

# Navigate to project
cd /var/www/portal.mlcscz.org.zw

# Execute SQL file
mysql -u your_db_user -p mlcscdb2026 < database/seeders/accounting_module_seeder.sql
```

### Option 3: Inline SQL via SSH

If the file is large, you can execute it in chunks:

```bash
# SSH to server
ssh user@portal.mlcscz.org.zw

# Execute with heredoc
mysql -u your_db_user -p mlcscdb2026 <<'EOF'
-- Copy paste SQL content here
INSERT INTO systemmodules...
EOF
```

---

## 🔍 Verify Seeding Success

### Check via Artisan:

```bash
php artisan tinker

# Check system module
>>> DB::table('systemmodules')->where('name', 'Accounting')->first();

# Count submodules
>>> DB::table('submodules')->whereHas('systemmodule', fn($q) => $q->where('name', 'Accounting'))->count();

# Count permissions
>>> DB::table('permissions')->where('name', 'LIKE', 'accounting.%')->count();
```

### Check via MySQL:

```bash
# Count records
mysql -u your_db_user -p mlcscdb2026 -e "
  SELECT 
    (SELECT COUNT(*) FROM systemmodules WHERE name='Accounting') as modules,
    (SELECT COUNT(*) FROM submodules WHERE systemmodule_id IN (SELECT id FROM systemmodules WHERE name='Accounting')) as submodules,
    (SELECT COUNT(*) FROM permissions WHERE name LIKE 'accounting.%') as permissions;
"
```

Expected output:
```
+---------+------------+-------------+
| modules | submodules | permissions |
+---------+------------+-------------+
|       1 |         13 |          65 |
+---------+------------+-------------+
```

---

## 🚨 Troubleshooting

### Issue: "Duplicate entry" error

**Cause:** Accounting module already seeded

**Fix:** Rollback first, then re-seed:
```bash
php artisan db:seed-accounting --rollback
php artisan db:seed-accounting
```

### Issue: "Table doesn't exist" error

**Cause:** Migrations not run yet

**Fix:** Run migrations first:
```bash
php artisan migrate --force
php artisan db:seed-accounting
```

### Issue: SQL file not found

**Cause:** File not uploaded to server

**Fix:** Ensure the file is in your git repository:
```bash
# Check if file exists
ls -la database/seeders/accounting_module_seeder.sql

# If missing, pull from git
git pull origin main
```

### Issue: Permission denied

**Cause:** Database user doesn't have INSERT permissions

**Fix:** Grant permissions:
```sql
GRANT INSERT, SELECT ON mlcscdb2026.* TO 'your_db_user'@'localhost';
FLUSH PRIVILEGES;
```

---

## 📊 What Gets Seeded

The accounting module seeder creates:

1. **1 System Module:**
   - Accounting

2. **13 Submodules:**
   - Accounting Periods
   - Chart of Accounts
   - Journal Entries
   - Suppliers
   - AP Invoices
   - AP Payments
   - AR Invoices
   - AR Receipts
   - Cost Centers
   - Tax Rates
   - Budgets
   - Audit Trail
   - Financial Reports

3. **~65 Permissions:**
   - accounting.access
   - accounting.periods.access, create, update, delete, close
   - accounting.chart-of-accounts.access, create, update, delete
   - accounting.journal-entries.access, create, update, delete, post, reverse
   - accounting.suppliers.access, create, update, delete
   - accounting.ap-invoices.access, create, update, delete, post, cancel
   - accounting.ap-payments.access, create, update, delete
   - accounting.ar-invoices.access, create, update, delete, post, cancel
   - accounting.ar-receipts.access, create, update, delete
   - accounting.cost-centers.access, create, update, delete
   - accounting.tax-rates.access, create, update, delete
   - accounting.budgets.access, create, update, delete
   - accounting.audit-trail.access
   - accounting.financial-reports.access, view-all

---

## 🎉 Summary

**Recommended Approach:**
1. Use `php artisan db:seed-accounting` (works everywhere via SSH)
2. Verify with `php artisan tinker` or MySQL query
3. Access accounting module in portal

**Files Created:**
- ✅ `app/Console/Commands/SeedAccountingModule.php` - Artisan command
- ✅ `database/seeders/accounting_module_seeder.sql` - SQL data (already exists)
- ✅ This guide

**Deployment Order:**
1. ✅ Run migrations: `php artisan migrate`
2. ✅ Seed accounting: `php artisan db:seed-accounting`
3. ✅ Clear caches: `php artisan config:cache`
4. ✅ Verify in portal

---

## 📞 Quick Commands Reference

| Task | Command |
|------|---------|
| Seed accounting module | `php artisan db:seed-accounting` |
| Rollback accounting module | `php artisan db:seed-accounting --rollback` |
| Check seeding status | `php artisan tinker` → check tables |
| Direct SQL execution | `mysql -u user -p db < file.sql` |
| Verify in database | `SELECT * FROM systemmodules WHERE name='Accounting'` |

---

**Ready to Seed! 🚀**

Use the Artisan command for easy seeding on both local and production servers!
