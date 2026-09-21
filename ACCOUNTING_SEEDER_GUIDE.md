# Accounting Module Menu Seeder - Quick Guide

## 📋 What This Seeder Does

The `accounting_module_seeder.sql` file creates:
- **1 System Module:** Accounting (ID: 7)
- **13 Submodules:** All accounting features
- **63 Permissions:** Complete access control (IDs: 67-129)
- **Role Assignment:** Grants all permissions to Super Admin (role_id = 1)

---

## 🚀 How to Import

### Method 1: MySQL Command Line
```bash
mysql -u root -p ahpcz_database < database/seeders/accounting_module_seeder.sql
```

### Method 2: phpMyAdmin (Recommended for Laragon/XAMPP)
1. Open phpMyAdmin
2. Select your database (e.g., `practitioner_db`)
3. Click **Import** tab
4. Click **Choose File**
5. Select `database/seeders/accounting_module_seeder.sql`
6. Click **Go**
7. Wait for success message

### Method 3: Direct SQL Execution
1. Open the SQL file in your text editor
2. Copy all contents
3. Open phpMyAdmin → SQL tab
4. Paste the contents
5. Click **Go**

---

## ✅ Verification

After import, run these queries to verify:

```sql
-- Should return 1 row: Accounting module
SELECT * FROM systemmodules WHERE id = 7;

-- Should return 13 rows: All submodules
SELECT * FROM submodules WHERE systemmodule_id = 7;

-- Should return 63 rows: All permissions
SELECT * FROM permissions WHERE id BETWEEN 67 AND 129;

-- Should return 63 rows: Super admin permissions
SELECT * FROM role_has_permissions WHERE permission_id BETWEEN 67 AND 129;
```

---

## 📊 What Gets Created

### System Module (ID: 7)
```
Name: Accounting
Icon: o-calculator
Permission: accounting.access
```

### Submodules (IDs: 25-37)
| ID | Name | Icon | Route |
|----|------|------|-------|
| 25 | Accounting Periods | o-calendar | accounting.periods |
| 26 | Chart of Accounts | o-list-bullet | accounting.chart-of-accounts |
| 27 | Journal Entries | o-book-open | accounting.journal-entries |
| 28 | Suppliers | o-building-office | accounting.suppliers |
| 29 | AP Invoices | o-document-text | accounting.ap-invoices |
| 30 | AP Payments | o-banknotes | accounting.ap-payments |
| 31 | AR Invoices | o-document-currency-dollar | accounting.ar-invoices |
| 32 | AR Receipts | o-currency-dollar | accounting.ar-receipts |
| 33 | Cost Centers | o-building-office-2 | accounting.cost-centers |
| 34 | Tax Rates | o-receipt-percent | accounting.tax-rates |
| 35 | Budgets | o-chart-bar | accounting.budgets |
| 36 | Audit Trail | o-clipboard-document-list | accounting.audit-trail |
| 37 | Financial Reports | o-document-chart-bar | accounting.financial-reports |

### Permissions (IDs: 67-129)

**Module Level:**
- 67: `accounting.access`

**Per Submodule (typical pattern):**
- `.access` - View
- `.create` - Create
- `.update` - Edit
- `.delete` - Delete

**Special Permissions:**
- `accounting.periods.close`
- `accounting.journal-entries.post`
- `accounting.journal-entries.reverse`
- `accounting.ap-invoices.post`
- `accounting.ap-invoices.cancel`
- `accounting.ar-invoices.post`
- `accounting.ar-invoices.cancel`
- `accounting.budgets.activate`
- `accounting.budgets.close`
- `accounting.financial-reports.trial-balance`
- `accounting.financial-reports.profit-loss`
- `accounting.financial-reports.balance-sheet`
- `accounting.financial-reports.cash-flow`
- `accounting.financial-reports.general-ledger`
- `accounting.financial-reports.export`

---

## 🔧 Troubleshooting

### Error: "Duplicate entry for key 'PRIMARY'"
**Cause:** IDs 7, 25-37, or 67-129 already exist  
**Solution:** 
1. Check existing IDs:
   ```sql
   SELECT MAX(id) FROM systemmodules;
   SELECT MAX(id) FROM submodules;
   SELECT MAX(id) FROM permissions;
   ```
2. Update the seeder file with next available IDs
3. Re-import

### Error: "Cannot add or update a child row"
**Cause:** Foreign key constraint (e.g., role_id = 1 doesn't exist)  
**Solution:**
1. Verify Super Admin role exists:
   ```sql
   SELECT * FROM roles WHERE id = 1;
   ```
2. If not exists, create it or change role_id in seeder

### Module Doesn't Appear in Menu
**Cause:** Navigation component needs refresh  
**Solution:**
1. Clear Laravel cache:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```
2. Refresh browser (Ctrl+F5)
3. Log out and log back in

### Permissions Not Working
**Cause:** User role doesn't have permissions  
**Solution:**
1. Assign permissions to your role via Role Management UI
2. Or run SQL:
   ```sql
   -- Grant all accounting permissions to role_id = 2
   INSERT INTO role_has_permissions (permission_id, role_id)
   SELECT id, 2 FROM permissions WHERE id BETWEEN 67 AND 129;
   ```

---

## 🎯 Next Steps After Import

1. **Run Migrations** (if not done yet):
   ```bash
   php artisan migrate
   ```

2. **Check Menu:**
   - Log into the system
   - Look for "Accounting" in the main navigation
   - Click to expand and see 13 submodules

3. **Configure Initial Data:**
   - Create first Accounting Period
   - Set up Chart of Accounts
   - Configure Tax Rates
   - Add Cost Centers (optional)

4. **Assign Permissions:**
   - Go to Roles management
   - Select roles that need accounting access
   - Grant appropriate permissions

5. **Test Each Module:**
   - Click through all 13 submodules
   - Verify each page loads correctly
   - Test create/edit functionality

---

## 📝 Customization

### Change Module Icon
Edit line 11 in the seeder:
```sql
'icon', 'o-calculator'  -- Change to your preferred icon
```

### Adjust Account Type
If your Admin account type has a different ID, change line 11:
```sql
'accounttype_id', 1  -- Change to your admin account type ID
```

### Change Submodule Order
Reorder the submodule INSERT statements (lines 14-26) as desired.

### Adjust Permissions
Add/remove permissions in the INSERT statements (lines 32-95) based on your needs.

### Grant to Different Role
Change `role_id = 1` to your target role ID in lines 101-108.

---

## 📞 Quick Commands Reference

```bash
# Import seeder
mysql -u root -p database_name < database/seeders/accounting_module_seeder.sql

# Check what's imported
mysql -u root -p database_name -e "SELECT * FROM systemmodules WHERE id = 7"

# Clear Laravel cache
php artisan optimize:clear

# Rollback last 17 migrations (if needed)
php artisan migrate:rollback --step=17

# Re-run all migrations
php artisan migrate
```

---

**File Location:** `database/seeders/accounting_module_seeder.sql`  
**Created:** September 1, 2026  
**Total Records:** 1 module + 13 submodules + 63 permissions + 63 role assignments = 140 records
