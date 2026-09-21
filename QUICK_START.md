# ⚡ Accounting Module - QUICK START

## 🚨 YOU MUST DO THIS FIRST!

The error you're seeing means **the database tables don't exist yet**.

```
❌ Table 'mlcscdb2026.chart_of_accounts' doesn't exist
```

---

## ✅ Fix in 3 Easy Steps

### **Step 1: Run Migrations** (Creates 17 tables)

**Easiest Way:**
1. Double-click: `run_accounting_migrations.bat`
2. Wait for completion
3. ✅ Done!

**Alternative Way:**
```bash
cd c:\laragon\www\anix\ahpczportal
php artisan migrate
```

---

### **Step 2: Import Menu** (Creates navigation)

1. Open **phpMyAdmin**
2. Select database: **mlcscdb2026**
3. Click **Import** tab
4. Choose: `database/seeders/accounting_module_seeder.sql`
5. Click **Go**
6. ✅ Done!

---

### **Step 3: Clear Cache**

```bash
cd c:\laragon\www\anix\ahpczportal
php artisan optimize:clear
```

---

## 🎉 That's It!

Now:
- Log in to your system
- Look for **"Accounting"** in the menu
- Click to see 13 submodules
- Start using the accounting module!

---

## 📋 Verification

After Step 1, check in phpMyAdmin:
```sql
SHOW TABLES LIKE '%accounting%';
SHOW TABLES LIKE '%chart_of_accounts%';
SHOW TABLES LIKE '%suppliers%';
```
Should see 17 new tables.

After Step 2, check:
```sql
SELECT * FROM systemmodules WHERE name = 'Accounting';
```
Should return 1 row.

---

## 🆘 Still Having Issues?

Read: **ACCOUNTING_INSTALLATION_GUIDE.md** for detailed troubleshooting.

---

**Remember:** Migrations FIRST, then SQL seeder, then cache clear!
