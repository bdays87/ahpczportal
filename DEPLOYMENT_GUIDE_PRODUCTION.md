# Deployment Guide - Production Setup

## 🏗️ Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     INTERNET (HTTPS)                        │
└─────────────────────────┬───────────────────────────────────┘
                          │
                          │ HTTPS
                          ▼
┌─────────────────────────────────────────────────────────────┐
│  Ubuntu Server (portal.mlcscz.org.zw)                       │
│  ┌──────────────────────────────────────────┐               │
│  │  Laravel Portal (AHPCZ)                  │               │
│  │  - Nginx + PHP 8.3                       │               │
│  │  - MySQL Database                        │               │
│  │  - SSL Certificate (Let's Encrypt)       │               │
│  │  - API Endpoints: /api/connector/*       │               │
│  └──────────────────────────────────────────┘               │
└─────────────────────────┬───────────────────────────────────┘
                          │
                          │ HTTPS (Outbound)
                          │ via NAT/Firewall
                          ▼
┌─────────────────────────────────────────────────────────────┐
│  Windows PC - Local LAN (Behind Firewall)                   │
│  ┌──────────────────────────────────────────┐               │
│  │  SageConnector (.NET Service)            │               │
│  │  - IIS Application                       │               │
│  │  - Windows Service (optional)            │               │
│  │  - Polls Portal API every 5 minutes      │               │
│  │  - Connects to Sage Evolution (Desktop)  │               │
│  └──────────────────────────────────────────┘               │
│                          │                                   │
│                          │ SDK API                           │
│                          ▼                                   │
│  ┌──────────────────────────────────────────┐               │
│  │  Sage Evolution (Desktop App)            │               │
│  │  - Database: SQL Server / Pervasive      │               │
│  └──────────────────────────────────────────┘               │
└─────────────────────────────────────────────────────────────┘
```

## 📋 Part 1: Ubuntu Server Setup (Portal)

### Step 1: Prepare Ubuntu Server

SSH into your Ubuntu server:

```bash
ssh your-user@portal.mlcscz.org.zw
```

### Step 2: Install Required Packages

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.3 and extensions
sudo apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-mbstring \
    php8.3-xml php8.3-mysql php8.3-curl php8.3-zip php8.3-gd \
    php8.3-intl php8.3-bcmath php8.3-soap php8.3-redis

# Install Nginx
sudo apt install -y nginx

# Install MySQL (if not already installed)
sudo apt install -y mysql-server

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Git
sudo apt install -y git unzip
```

### Step 3: Deploy Laravel Application

```bash
# Create web directory
sudo mkdir -p /var/www/portal.mlcscz.org.zw
cd /var/www/portal.mlcscz.org.zw

# Clone or upload your Laravel app
# Option A: Upload via SCP/FTP
# Option B: Git clone (if using git)
sudo git clone <your-repo-url> .

# Set permissions
sudo chown -R www-data:www-data /var/www/portal.mlcscz.org.zw
sudo chmod -R 755 /var/www/portal.mlcscz.org.zw/storage
sudo chmod -R 755 /var/www/portal.mlcscz.org.zw/bootstrap/cache

# Install Composer dependencies
cd /var/www/portal.mlcscz.org.zw
composer install --no-dev --optimize-autoloader
```

### Step 4: Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Edit .env file
nano .env
```

**Production .env configuration:**

```env
APP_NAME="MLCSCZ Portal"
APP_ENV=production
APP_KEY=base64:xxxxx
APP_DEBUG=false
APP_URL=https://portal.mlcscz.org.zw

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mlcscdb2026
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

# Sage Connector Integration
SAGE_CONNECTOR_ENABLED=true
SAGE_CONNECTOR_API_KEY=your-64-character-production-api-key-here
```

**Generate Production API Key:**

```bash
php artisan tinker
>>> Str::random(64)
# Copy the output - this is your production API key
# Save it securely - you'll need it for SageConnector config
```

### Step 5: Run Migrations

```bash
# If you have existing tables (accounting, smsbroadcasts with provider column)
# First mark them as complete to avoid conflicts:
php artisan migrate:mark-complete --check  # Check which tables exist
php artisan migrate:mark-complete          # Mark existing tables as migrated

# Now run new migrations (Sage integration)
php artisan migrate --force

# Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Note:** The `migrate:mark-complete` command will:
- Check if tables already exist in your database
- Mark those migrations as "already run" in the migrations table
- Allow new migrations (like Sage sync) to run without conflicts
- Safe to use - only marks migrations for tables that actually exist

### Step 6: Configure Nginx

Create Nginx configuration:

```bash
sudo nano /etc/nginx/sites-available/portal.mlcscz.org.zw
```

**Nginx Configuration:**

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name portal.mlcscz.org.zw;
    
    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name portal.mlcscz.org.zw;

    root /var/www/portal.mlcscz.org.zw/public;
    index index.php index.html;

    # SSL Certificates (will be configured with Certbot)
    ssl_certificate /etc/letsencrypt/live/portal.mlcscz.org.zw/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/portal.mlcscz.org.zw/privkey.pem;
    
    # SSL Configuration
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    # Logging
    access_log /var/log/nginx/portal.mlcscz.org.zw-access.log;
    error_log /var/log/nginx/portal.mlcscz.org.zw-error.log;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # PHP-FPM Configuration
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Step 7: Enable Site and Install SSL

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/portal.mlcscz.org.zw /etc/nginx/sites-enabled/

# Test Nginx configuration
sudo nginx -t

# Install Certbot for SSL
sudo apt install -y certbot python3-certbot-nginx

# Get SSL certificate
sudo certbot --nginx -d portal.mlcscz.org.zw

# Restart Nginx
sudo systemctl restart nginx

# Enable Nginx on boot
sudo systemctl enable nginx
```

### Step 8: Configure Firewall

```bash
# Allow HTTP, HTTPS, and SSH
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### Step 9: Set Up Scheduled Tasks

```bash
# Edit crontab
sudo crontab -e
```

Add Laravel scheduler:

```bash
* * * * * cd /var/www/portal.mlcscz.org.zw && php artisan schedule:run >> /dev/null 2>&1
```

### Step 10: Test Portal

```bash
# Test from Ubuntu server
curl -X GET "https://portal.mlcscz.org.zw/api/connector/customers/pending" \
     -H "X-Api-Key: your-production-api-key"

# Should return: []
```

---

## 📋 Part 2: Windows LAN Setup (SageConnector)

### Step 1: Install Prerequisites

On the Windows PC where Sage Evolution is installed:

1. **Install .NET 8 SDK**
   - Download from: https://dotnet.microsoft.com/download/dotnet/8.0
   - Install both SDK and Runtime

2. **Install IIS** (if not already)
   - Open "Turn Windows features on or off"
   - Enable: Internet Information Services
   - Enable: IIS Management Console
   - Enable: ASP.NET 4.8
   - Enable: .NET Extensibility

3. **Install IIS Hosting Bundle**
   - Download: https://dotnet.microsoft.com/download/dotnet/8.0
   - Install "ASP.NET Core Runtime - Windows Hosting Bundle"
   - Restart IIS: `iisreset`

### Step 2: Publish SageConnector

On your development machine:

```bash
cd C:\Users\Admin\Desktop\tutorial\lms\sageconnector\ConnectorService

# Publish for production
dotnet publish -c Release -o C:\inetpub\sageconnector
```

### Step 3: Configure Production Settings

Edit `C:\inetpub\sageconnector\appsettings.Production.json`:

```json
{
  "Logging": {
    "LogLevel": {
      "Default": "Information",
      "Microsoft.AspNetCore": "Warning"
    }
  },
  "Portals": [
    {
      "Name": "MLCSCZ",
      "BaseUrl": "https://portal.mlcscz.org.zw",
      "ApiKey": "your-production-api-key-same-as-ubuntu-env",
      "Enabled": true,
      "PollIntervalMinutes": 5
    }
  ],
  "Sage": {
    "CompanyName": "Your Company Name",
    "Username": "ADMIN",
    "Password": "your-sage-password",
    "Server": "localhost",
    "DatabaseType": "SQLServer"
  }
}
```

**CRITICAL:** Use the SAME API key you generated on Ubuntu!

### Step 4: Create IIS Application

1. Open IIS Manager
2. Right-click "Sites" → "Add Website"
3. Configure:
   - **Site name:** SageConnector
   - **Physical path:** `C:\inetpub\sageconnector`
   - **Binding:**
     - Type: http
     - IP: All Unassigned
     - Port: 8080 (or any available port)
   - **Application Pool:** SageConnectorAppPool
4. Click OK

5. Configure Application Pool:
   - Select "SageConnectorAppPool"
   - Advanced Settings:
     - **.NET CLR Version:** No Managed Code
     - **Start Mode:** AlwaysRunning
     - **Idle Time-out:** 0 (never timeout)
     - **Identity:** LocalSystem (or account with Sage access)

### Step 5: Configure as Windows Service (Recommended)

This ensures SageConnector runs even when no one is logged in.

**Option A: Using NSSM (Recommended)**

```powershell
# Download NSSM from https://nssm.cc/download
# Extract to C:\nssm

# Install service
C:\nssm\nssm.exe install SageConnector "C:\Program Files\dotnet\dotnet.exe" "C:\inetpub\sageconnector\ConnectorService.dll"

# Set environment
C:\nssm\nssm.exe set SageConnector AppEnvironmentExtra ASPNETCORE_ENVIRONMENT=Production

# Set working directory
C:\nssm\nssm.exe set SageConnector AppDirectory "C:\inetpub\sageconnector"

# Start service
C:\nssm\nssm.exe start SageConnector
```

**Option B: Using SC command**

```powershell
# Create service
sc create SageConnector binPath="C:\Program Files\dotnet\dotnet.exe C:\inetpub\sageconnector\ConnectorService.dll" DisplayName="Sage Connector Service"

# Set to auto-start
sc config SageConnector start=auto

# Start service
sc start SageConnector
```

### Step 6: Test Connection

On the Windows PC:

```powershell
# Test outbound connection to portal
curl https://portal.mlcscz.org.zw/api/connector/customers/pending -Headers @{"X-Api-Key"="your-production-api-key"}
```

### Step 7: Monitor Logs

Check logs at:
- `C:\inetpub\sageconnector\logs\`
- Event Viewer → Windows Logs → Application

---

## 🔥 Firewall Configuration

### Ubuntu Server (Inbound)

```bash
# Only allow HTTPS from internet
sudo ufw status
# Should show: 22, 80, 443 allowed
```

### Windows PC (Outbound)

**No inbound firewall rules needed!** SageConnector only makes outbound HTTPS requests.

1. Ensure Windows Firewall allows outbound HTTPS (port 443) - usually allowed by default
2. If behind corporate firewall, whitelist: `portal.mlcscz.org.zw:443`

---

## ✅ Verification Checklist

### Portal (Ubuntu) - portal.mlcscz.org.zw

- [ ] SSL certificate installed and working
- [ ] Laravel application accessible via HTTPS
- [ ] Migrations run successfully
- [ ] API key configured in .env
- [ ] Test API endpoint with curl returns valid JSON
- [ ] Nginx logs show no errors
- [ ] PHP-FPM running

```bash
# Check services
sudo systemctl status nginx
sudo systemctl status php8.3-fpm
sudo systemctl status mysql

# Test API
curl -X GET "https://portal.mlcscz.org.zw/api/connector/customers/pending" \
     -H "X-Api-Key: your-key" -v
```

### SageConnector (Windows LAN)

- [ ] .NET 8 installed
- [ ] IIS hosting bundle installed
- [ ] Application published to C:\inetpub\sageconnector
- [ ] appsettings.Production.json configured with correct API key
- [ ] IIS application created and running
- [ ] Windows Service installed and started
- [ ] Can connect to portal (outbound HTTPS)
- [ ] Can connect to Sage Evolution
- [ ] Logs show successful polling

```powershell
# Check service status
sc query SageConnector

# Test connection
curl https://portal.mlcscz.org.zw/api/connector/customers/pending -Headers @{"X-Api-Key"="your-key"}

# View logs
Get-Content C:\inetpub\sageconnector\logs\connector-*.log -Tail 50
```

---

## 🔍 Troubleshooting

### Issue: SageConnector can't connect to portal

**Check:**
1. ✅ Windows PC can access internet
2. ✅ Portal URL correct: `https://portal.mlcscz.org.zw`
3. ✅ API key matches in both systems
4. ✅ Corporate firewall allows outbound HTTPS
5. ✅ Test with curl/browser from Windows PC

**Fix:**
```powershell
# Test DNS resolution
nslookup portal.mlcscz.org.zw

# Test HTTPS connection
curl https://portal.mlcscz.org.zw

# Test API with correct key
curl https://portal.mlcscz.org.zw/api/connector/customers/pending -Headers @{"X-Api-Key"="your-key"}
```

### Issue: 401 Unauthorized

**Cause:** API key mismatch

**Fix:**
1. Check Ubuntu `.env`: `SAGE_CONNECTOR_API_KEY`
2. Check Windows `appsettings.Production.json`: `ApiKey`
3. Keys must match EXACTLY (case-sensitive)
4. Clear Laravel config: `php artisan config:clear`
5. Restart SageConnector service

### Issue: SSL Certificate Errors

**Fix:**
```bash
# Renew certificate
sudo certbot renew

# Test renewal
sudo certbot renew --dry-run

# Auto-renewal (should be automatic)
sudo systemctl status certbot.timer
```

---

## 📊 Monitoring

### Ubuntu (Portal)

```bash
# Check logs
tail -f /var/log/nginx/portal.mlcscz.org.zw-error.log
tail -f /var/www/portal.mlcscz.org.zw/storage/logs/laravel.log

# Monitor sync activity
mysql -u root -p mlcscdb2026
> SELECT COUNT(*) FROM customerprofessions WHERE sage_sync_status = 'SYNCED';
> SELECT COUNT(*) FROM invoices WHERE sage_sync_status = 'SYNCED';
```

### Windows (SageConnector)

```powershell
# Check service
Get-Service SageConnector

# View logs
Get-Content C:\inetpub\sageconnector\logs\connector-*.log -Tail 100

# Monitor in real-time
Get-Content C:\inetpub\sageconnector\logs\connector-*.log -Wait
```

---

## 🎯 Production Best Practices

1. ✅ **Different API keys** for dev/staging/production
2. ✅ **Backup database** before deploying
3. ✅ **Monitor logs** daily for errors
4. ✅ **Set up alerts** for failed syncs
5. ✅ **Test in staging** before production
6. ✅ **Document API key** in secure password manager
7. ✅ **Schedule regular certificate renewals** (auto with certbot)
8. ✅ **Keep .NET runtime updated** on Windows
9. ✅ **Keep Ubuntu packages updated**
10. ✅ **Regular backups** of both systems

---

## 📞 Quick Reference

### Portal URLs
- **Production:** https://portal.mlcscz.org.zw
- **API Endpoint:** https://portal.mlcscz.org.zw/api/connector/*

### File Locations

**Ubuntu:**
- App: `/var/www/portal.mlcscz.org.zw`
- Logs: `/var/www/portal.mlcscz.org.zw/storage/logs/laravel.log`
- Nginx config: `/etc/nginx/sites-available/portal.mlcscz.org.zw`
- SSL certs: `/etc/letsencrypt/live/portal.mlcscz.org.zw/`

**Windows:**
- App: `C:\inetpub\sageconnector`
- Logs: `C:\inetpub\sageconnector\logs\`
- Config: `C:\inetpub\sageconnector\appsettings.Production.json`

---

**Ready to Deploy!** Follow this guide step by step for production deployment.
