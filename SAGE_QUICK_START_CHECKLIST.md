# Sage Integration - Quick Start Checklist

## ✅ Files Ready

All integration files have been created and are ready to deploy!

### What's Been Done:
1. ✅ `bootstrap/app.php` - Updated with Sage routes and middleware
2. ✅ `config/services.php` - Added Sage connector configuration
3. ✅ All API controllers, middleware, migrations created
4. ✅ Admin dashboard created
5. ✅ Complete deployment guide created

---

## 📋 Your Setup: Production Deployment

### Architecture:
```
Ubuntu Server (portal.mlcscz.org.zw) ←HTTPS→ Windows LAN PC (SageConnector) → Sage Evolution
```

---

## 🚀 Step-by-Step Deployment

### Phase 1: Local Testing (Development - Windows)

#### 1.1 Configure Local Laravel

```bash
# In your local Laravel project
cd C:\laragon\www\anix\ahpczportal

# Generate API key
php artisan tinker
>>> Str::random(64)
# Copy the output
```

#### 1.2 Add to .env

```env
# Add these lines to .env
SAGE_CONNECTOR_ENABLED=true
SAGE_CONNECTOR_API_KEY=your-generated-key-here
```

#### 1.3 Run Migrations

```bash
php artisan migrate

# Clear cache
php artisan config:clear
php artisan route:clear
```

#### 1.4 Test Locally

```bash
# Start Laravel
php artisan serve

# In another terminal, test API
curl -X GET "http://127.0.0.1:8000/api/connector/customers/pending" \
     -H "X-Api-Key: your-api-key"

# Should return: []
```

---

### Phase 2: Deploy to Ubuntu (portal.mlcscz.org.zw)

#### 2.1 Upload Files

```bash
# From your local machine, upload to Ubuntu
scp -r C:\laragon\www\anix\ahpczportal user@portal.mlcscz.org.zw:/var/www/
```

OR use Git:

```bash
# On Ubuntu server
cd /var/www
git clone your-repo-url portal.mlcscz.org.zw
```

#### 2.2 Configure Ubuntu

Follow: `DEPLOYMENT_GUIDE_PRODUCTION.md` - Part 1

**Key steps:**
1. Install PHP 8.3, Nginx, MySQL
2. Set up Laravel
3. Configure .env with production settings
4. Generate NEW production API key (different from dev!)
5. Run migrations
6. Configure Nginx with SSL
7. Test API endpoints

#### 2.3 Verify Ubuntu Setup

```bash
# On Ubuntu
curl -X GET "https://portal.mlcscz.org.zw/api/connector/customers/pending" \
     -H "X-Api-Key: your-production-api-key"

# Should return: []
```

---

### Phase 3: Configure Windows LAN PC

#### 3.1 Publish SageConnector

```bash
# On your development PC
cd C:\Users\Admin\Desktop\tutorial\lms\sageconnector\ConnectorService

# Publish for production
dotnet publish -c Release -o C:\Deploy\SageConnector
```

#### 3.2 Copy to LAN PC

Transfer `C:\Deploy\SageConnector` folder to the Windows PC where Sage is installed:
- Recommended location: `C:\inetpub\sageconnector`

#### 3.3 Configure for Production

Edit `C:\inetpub\sageconnector\appsettings.Production.json`:

```json
{
  "Portals": [
    {
      "Name": "MLCSCZ",
      "BaseUrl": "https://portal.mlcscz.org.zw",
      "ApiKey": "SAME-API-KEY-AS-UBUNTU-ENV",
      "Enabled": true,
      "PollIntervalMinutes": 5
    }
  ],
  "Sage": {
    "CompanyName": "Your Company",
    "Username": "ADMIN",
    "Password": "your-sage-password"
  }
}
```

**CRITICAL:** Use the SAME production API key from Ubuntu!

#### 3.4 Set Up IIS or Windows Service

Follow: `DEPLOYMENT_GUIDE_PRODUCTION.md` - Part 2

Choose one:
- **Option A:** IIS Application (easier for web monitoring)
- **Option B:** Windows Service (runs in background)

#### 3.5 Test Connection

```powershell
# On Windows LAN PC
curl https://portal.mlcscz.org.zw/api/connector/customers/pending `
     -Headers @{"X-Api-Key"="your-production-api-key"}

# Should return: []
```

#### 3.6 Start SageConnector

```powershell
# If using Windows Service
sc start SageConnector

# Check status
sc query SageConnector

# View logs
Get-Content C:\inetpub\sageconnector\logs\*.log -Tail 50
```

---

## ✅ Final Verification

### Test Complete Flow

#### 1. Create Test Customer in Portal

1. Login to https://portal.mlcscz.org.zw
2. Create and approve a practitioner registration
3. Wait 5 minutes (or less, depending on poll interval)

#### 2. Check SageConnector Logs

```powershell
# On Windows
Get-Content C:\inetpub\sageconnector\logs\connector-*.log -Tail 100
```

**Look for:**
```
[INFO] Polling MLCSCZ...
[INFO] Found 1 pending customers
[INFO] Creating customer in Sage: CUST-12345
[INFO] Customer created successfully: CUST-12345
[INFO] Sending ack to portal...
```

#### 3. Check Portal Database

```bash
# On Ubuntu
mysql -u root -p mlcscdb2026

SELECT * FROM customerprofessions WHERE sage_sync_status = 'SYNCED';
```

#### 4. Check Sage Evolution

Open Sage Evolution and verify customer was created.

#### 5. Test Invoice Sync

1. Create and pay an invoice in portal
2. Wait for next poll (5 minutes)
3. Check SageConnector logs
4. Verify invoice in Sage
5. Check portal for sage_invoice_number

---

## 🔧 Configuration Files Summary

### Ubuntu: /var/www/portal.mlcscz.org.zw/.env

```env
APP_URL=https://portal.mlcscz.org.zw
DB_DATABASE=mlcscdb2026
SAGE_CONNECTOR_ENABLED=true
SAGE_CONNECTOR_API_KEY=production-api-key-64-chars
```

### Windows: C:\inetpub\sageconnector\appsettings.Production.json

```json
{
  "Portals": [{
    "Name": "MLCSCZ",
    "BaseUrl": "https://portal.mlcscz.org.zw",
    "ApiKey": "SAME-production-api-key-64-chars",
    "Enabled": true,
    "PollIntervalMinutes": 5
  }],
  "Sage": {
    "CompanyName": "Your Company",
    "Username": "ADMIN",
    "Password": "sage-password"
  }
}
```

---

## 📊 Monitoring

### Portal Admin Dashboard

Access: https://portal.mlcscz.org.zw/sage-sync-status

- View sync statistics
- Monitor synced/pending/failed records
- Retry failed syncs
- View error messages

### Logs to Monitor

**Ubuntu:**
```bash
tail -f /var/www/portal.mlcscz.org.zw/storage/logs/laravel.log
```

**Windows:**
```powershell
Get-Content C:\inetpub\sageconnector\logs\connector-*.log -Wait
```

---

## 🚨 Common Issues & Fixes

### Issue: 401 Unauthorized

**Fix:** API keys don't match
```bash
# Check Ubuntu
cat /var/www/portal.mlcscz.org.zw/.env | grep SAGE_CONNECTOR_API_KEY

# Check Windows
type C:\inetpub\sageconnector\appsettings.Production.json | findstr ApiKey

# They MUST match exactly!
```

### Issue: SageConnector can't connect

**Fix:** Test connection from Windows
```powershell
# Test DNS
nslookup portal.mlcscz.org.zw

# Test HTTPS
curl https://portal.mlcscz.org.zw

# Test API
curl https://portal.mlcscz.org.zw/api/connector/customers/pending `
     -Headers @{"X-Api-Key"="your-key"}
```

### Issue: Customers not syncing

**Check:**
1. Status is 'APPROVED'
2. sage_sync_status is NULL or 'PENDING'
3. Updated within timeframe
4. SageConnector is running
5. Logs for errors

---

## 📚 Documentation Reference

1. **DEPLOYMENT_GUIDE_PRODUCTION.md** - Complete deployment steps
2. **SAGE_INTEGRATION_COMPLETE_SETUP.md** - Full setup with code
3. **SAGE_PASTEL_INTEGRATION_GUIDE.md** - Architecture overview
4. **SAGE_CONNECTOR_CONFIG.md** - Configuration details

---

## 🎯 Deployment Order

1. ✅ **Local testing** (Windows dev environment)
2. ✅ **Deploy to Ubuntu** (https://portal.mlcscz.org.zw)
3. ✅ **Test Ubuntu API** endpoints
4. ✅ **Configure Windows LAN PC** (SageConnector)
5. ✅ **Test connection** from Windows to Ubuntu
6. ✅ **Start SageConnector** service
7. ✅ **Monitor logs** for successful sync
8. ✅ **Verify in Sage** Evolution
9. ✅ **Train staff** on monitoring dashboard

---

## ✅ Pre-Deployment Checklist

- [ ] Local Laravel tested successfully
- [ ] Production API key generated (64 chars)
- [ ] Ubuntu server accessible via SSH
- [ ] Domain points to Ubuntu server IP
- [ ] Windows PC has internet access (outbound HTTPS)
- [ ] Sage Evolution installed and accessible
- [ ] IIS or Windows Service configured
- [ ] API keys match in both systems
- [ ] Migrations run on production
- [ ] SSL certificate installed on Ubuntu
- [ ] Firewall rules configured
- [ ] Backup taken before deployment

---

**Ready to Deploy!** 🚀

Start with local testing, then follow the deployment guide step by step!
