# Production Deployment - Complete Checklist

## 🎯 Ubuntu Production Server: portal.mlcscz.org.zw

---

## ✅ Pre-Deployment (Local Testing)

```bash
cd C:\laragon\www\anix\ahpczportal

# 1. Mark existing migrations
php artisan migrate:mark-complete

# 2. Run new migrations
php artisan migrate

# 3. Seed accounting module
php artisan db:seed-accounting

# 4. Test Sage API
curl http://127.0.0.1:8000/api/connector/customers/pending -H "X-Api-Key: your-dev-key"

# 5. Commit and push
git add .
git commit -m "Add Sage integration and accounting module"
git push origin main
```

---

## 🚀 Production Deployment

### Step 1: SSH to Server

```bash
ssh your-user@portal.mlcscz.org.zw
```

### Step 2: Navigate to Project

```bash
cd /var/www/portal.mlcscz.org.zw
```

### Step 3: Pull Latest Code

```bash
git pull origin main
```

### Step 4: Install Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

### Step 5: Update Environment

```bash
nano .env
```

Add these lines:
```env
SAGE_CONNECTOR_ENABLED=true
SAGE_CONNECTOR_API_KEY=your-production-api-key-64-chars
```

Generate production API key:
```bash
php artisan tinker
>>> Str::random(64)
# Copy output and save securely!
```

### Step 6: Fix Existing Migrations

```bash
# Mark existing tables as migrated
php artisan migrate:mark-complete
```

### Step 7: Run New Migrations

```bash
# Create Sage integration tables
php artisan migrate --force
```

### Step 8: Seed Accounting Module

```bash
# Seed accounting data
php artisan db:seed-accounting
```

### Step 9: Clear Caches

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 10: Set Permissions

```bash
sudo chown -R www-data:www-data /var/www/portal.mlcscz.org.zw/storage
sudo chown -R www-data:www-data /var/www/portal.mlcscz.org.zw/bootstrap/cache
sudo chmod -R 755 /var/www/portal.mlcscz.org.zw/storage
sudo chmod -R 755 /var/www/portal.mlcscz.org.zw/bootstrap/cache
```

### Step 11: Restart Services

```bash
sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx
```

---

## ✅ Verification

### Test Sage API Endpoints

```bash
# Test customers endpoint
curl -X GET "https://portal.mlcscz.org.zw/api/connector/customers/pending" \
     -H "X-Api-Key: your-production-api-key"

# Should return: []

# Test orders endpoint  
curl -X GET "https://portal.mlcscz.org.zw/api/connector/orders/pending" \
     -H "X-Api-Key: your-production-api-key"

# Should return: []
```

### Check Database

```bash
mysql -u your_db_user -p mlcscdb2026 -e "
  SELECT 
    (SELECT COUNT(*) FROM customerprofessions WHERE sage_sync_status IS NOT NULL) as sage_customers,
    (SELECT COUNT(*) FROM invoices WHERE sage_sync_status IS NOT NULL) as sage_invoices,
    (SELECT COUNT(*) FROM systemmodules WHERE name='Accounting') as accounting_module,
    (SELECT COUNT(*) FROM submodules WHERE systemmodule_id IN (SELECT id FROM systemmodules WHERE name='Accounting')) as accounting_submodules;
"
```

Expected:
```
+----------------+---------------+-------------------+-----------------------+
| sage_customers | sage_invoices | accounting_module | accounting_submodules |
+----------------+---------------+-------------------+-----------------------+
|              0 |             0 |                 1 |                    13 |
+----------------+---------------+-------------------+-----------------------+
```

### Access Admin Dashboard

Open browser:
```
https://portal.mlcscz.org.zw/sage-sync-status
```

Should see:
- Stats cards showing 0 pending, 0 synced, 0 failed
- Customers and Invoices tabs
- Bulk action buttons

---

## 🖥️ Windows SageConnector Setup

### Step 1: Publish Connector

On your dev machine:
```bash
cd C:\Users\Admin\Desktop\tutorial\lms\sageconnector\ConnectorService
dotnet publish -c Release -o C:\Deploy\SageConnector
```

### Step 2: Copy to LAN PC

Transfer `C:\Deploy\SageConnector` to Windows PC where Sage is installed:
- Recommended: `C:\inetpub\sageconnector`

### Step 3: Configure

Edit `C:\inetpub\sageconnector\appsettings.Production.json`:

```json
{
  "Portals": [{
    "Name": "MLCSCZ",
    "BaseUrl": "https://portal.mlcscz.org.zw",
    "ApiKey": "SAME-production-api-key-from-ubuntu",
    "Enabled": true,
    "PollIntervalMinutes": 5
  }],
  "Sage": {
    "CompanyName": "Your Company Name",
    "Username": "ADMIN",
    "Password": "your-sage-password"
  }
}
```

**CRITICAL:** Use the SAME API key from Ubuntu .env!

### Step 4: Set Up as Windows Service

Using NSSM (recommended):
```powershell
# Install NSSM from https://nssm.cc/download
C:\nssm\nssm.exe install SageConnector "C:\Program Files\dotnet\dotnet.exe" "C:\inetpub\sageconnector\ConnectorService.dll"
C:\nssm\nssm.exe set SageConnector AppEnvironmentExtra ASPNETCORE_ENVIRONMENT=Production
C:\nssm\nssm.exe set SageConnector AppDirectory "C:\inetpub\sageconnector"
C:\nssm\nssm.exe start SageConnector
```

### Step 5: Test Connection

```powershell
# Test from Windows PC
curl https://portal.mlcscz.org.zw/api/connector/customers/pending `
     -Headers @{"X-Api-Key"="your-production-api-key"}
```

### Step 6: Monitor Logs

```powershell
Get-Content C:\inetpub\sageconnector\logs\connector-*.log -Tail 50 -Wait
```

Look for:
```
[INFO] Polling MLCSCZ...
[INFO] Found 0 pending customers
[INFO] Found 0 pending orders
```

---

## 📋 Post-Deployment Checklist

- [ ] Ubuntu server accessible via HTTPS
- [ ] SSL certificate valid (Let's Encrypt)
- [ ] Environment variables configured (.env)
- [ ] Production API key generated and saved
- [ ] Migrations marked and run successfully
- [ ] Accounting module seeded
- [ ] Sage API endpoints respond correctly
- [ ] Admin dashboard accessible
- [ ] Windows SageConnector configured
- [ ] SageConnector service running
- [ ] Connection between Windows and Ubuntu working
- [ ] Logs showing successful polling
- [ ] Staff trained on admin dashboard

---

## 🎯 Quick Commands Summary

| Task | Command |
|------|---------|
| **SSH to server** | `ssh user@portal.mlcscz.org.zw` |
| **Pull code** | `git pull origin main` |
| **Install deps** | `composer install --no-dev --optimize-autoloader` |
| **Mark migrations** | `php artisan migrate:mark-complete` |
| **Run migrations** | `php artisan migrate --force` |
| **Seed accounting** | `php artisan db:seed-accounting` |
| **Clear caches** | `php artisan config:cache` |
| **Test API** | `curl https://portal.mlcscz.org.zw/api/connector/customers/pending -H "X-Api-Key: key"` |
| **Check logs** | `tail -f storage/logs/laravel.log` |
| **Restart services** | `sudo systemctl restart php8.3-fpm nginx` |

---

## 📚 Documentation Reference

1. **DEPLOYMENT_GUIDE_PRODUCTION.md** - Full Ubuntu + Windows setup
2. **MIGRATION_FIX_GUIDE.md** - Fix existing table conflicts
3. **SEEDING_GUIDE_PRODUCTION.md** - Seed accounting module
4. **SAGE_ADMIN_DASHBOARD_GUIDE.md** - Admin user guide
5. **SAGE_FINAL_IMPLEMENTATION_SUMMARY.md** - Technical details

---

## 🆘 Troubleshooting

### Issue: 401 Unauthorized

**Fix:** API keys don't match
```bash
# Ubuntu
cat /var/www/portal.mlcscz.org.zw/.env | grep SAGE_CONNECTOR_API_KEY

# Windows
type C:\inetpub\sageconnector\appsettings.Production.json | findstr ApiKey

# Must be identical!
```

### Issue: Migration errors

**Fix:** Mark existing migrations
```bash
php artisan migrate:mark-complete
php artisan migrate --force
```

### Issue: SageConnector can't connect

**Fix:** Test connection
```bash
# From Windows
nslookup portal.mlcscz.org.zw
curl https://portal.mlcscz.org.zw
```

### Issue: Tables already exist

**Fix:** Already handled by migration fix command
```bash
php artisan migrate:mark-complete
```

---

## 🎉 Deployment Complete!

After following this checklist:
- ✅ Portal deployed to Ubuntu with HTTPS
- ✅ Sage integration tables created
- ✅ Accounting module seeded
- ✅ SageConnector running on Windows LAN
- ✅ Bi-directional sync operational
- ✅ Admin dashboard accessible

**Next:** Test with a real customer sync!

---

**Total Deployment Time:** ~45-60 minutes

**Prerequisites:**
- Ubuntu server access (SSH)
- Windows PC on same LAN as Sage Evolution
- Domain pointing to Ubuntu IP
- Basic knowledge of Linux/Windows commands

**Ready to Deploy! 🚀**
