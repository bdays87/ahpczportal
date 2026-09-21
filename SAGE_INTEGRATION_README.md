# Sage Pastel Integration - README

## 🎯 Overview

Complete integration between AHPCZ Portal (Laravel) and Sage Pastel Evolution via SageConnector middleware.

## 📦 Files Created

### Core API Files
1. ✅ `app/Http/Controllers/Api/SageConnectorController.php` - 5 API endpoints
2. ✅ `app/Http/Middleware/VerifySageConnectorApiKey.php` - Authentication
3. ✅ `routes/api_sage_connector.php` - API routes

### Database Migrations
4. ✅ `database/migrations/*_add_sage_sync_fields_to_customerprofessions.php`
5. ✅ `database/migrations/*_add_sage_sync_fields_to_invoices.php`
6. ✅ `database/migrations/*_create_customer_sage_statements_table.php`

### Admin Dashboard
7. ✅ `app/Livewire/Admin/SageSyncStatus.php`
8. ✅ `resources/views/livewire/admin/sage-sync-status.blade.php`

### Documentation
9. ✅ `SAGE_PASTEL_INTEGRATION_GUIDE.md` - Architecture & overview
10. ✅ `SAGE_CONNECTOR_CONFIG.md` - Configuration steps
11. ✅ `SAGE_INTEGRATION_COMPLETE_SETUP.md` - Complete setup guide
12. ✅ `SAGE_INTEGRATION_README.md` - This file

## 🚀 Quick Start

### 1. Install Dependencies (Already done)
No additional packages needed - uses standard Laravel features.

### 2. Configuration (5 minutes)

```bash
# 1. Generate API key
php artisan tinker
>>> Str::random(64)

# 2. Add to .env
SAGE_CONNECTOR_ENABLED=true
SAGE_CONNECTOR_API_KEY=<paste-generated-key>

# 3. Run migrations
php artisan migrate

# 4. Clear cache
php artisan config:clear
php artisan route:clear
```

### 3. Update bootstrap/app.php

See `SAGE_INTEGRATION_COMPLETE_SETUP.md` for code to add.

### 4. Configure SageConnector

Update `C:\Users\Admin\Desktop\tutorial\lms\sageconnector\ConnectorService\appsettings.json`:

```json
{
  "Portals": [
    {
      "Name": "AHPCZ",
      "BaseUrl": "http://127.0.0.1:8000",
      "ApiKey": "<same-api-key-from-env>",
      "Enabled": true,
      "PollIntervalMinutes": 5
    }
  ]
}
```

### 5. Test

```bash
# Test authentication
curl -X GET "http://127.0.0.1:8000/api/connector/customers/pending" \
     -H "X-Api-Key: your-api-key"

# Should return: []  (empty array)

# Start connector
cd C:\Users\Admin\Desktop\tutorial\lms\sageconnector\ConnectorService
dotnet run
```

## 📡 API Endpoints

| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/api/connector/customers/pending` | Fetch customers to sync |
| GET | `/api/connector/orders/pending` | Fetch invoices to sync |
| POST | `/api/connector/ack` | Receive sync acknowledgment |
| POST | `/api/connector/invoices` | Receive invoice from Sage |
| POST | `/api/connector/statements` | Receive statement from Sage |

## 🎨 Admin Dashboard

Access at: `/sage-sync-status`

Features:
- Real-time sync statistics
- View synced/pending/failed records
- Retry failed syncs
- Search and filter
- View error messages

## 📊 Data Flow

```
Portal (Laravel) ←→ SageConnector (.NET) ←→ Sage Evolution
     │                    │                       │
     │  1. PULL           │                       │
     │  ←─────────────    │                       │
     │                    │  2. CREATE            │
     │                    │  ──────────────────→  │
     │  3. ACK            │                       │
     │  ←─────────────    │                       │
     │                    │  4. READ              │
     │                    │  ←──────────────────  │
     │  5. PUSH           │                       │
     │  ←─────────────    │                       │
```

## 🔐 Security

- ✅ API key authentication (X-Api-Key header)
- ✅ HTTPS recommended for production
- ✅ Different keys per environment
- ✅ Keys stored in .env (not committed)

## 📝 Documentation Files

| File | Purpose |
|------|---------|
| `SAGE_PASTEL_INTEGRATION_GUIDE.md` | Architecture overview |
| `SAGE_CONNECTOR_CONFIG.md` | Step-by-step configuration |
| `SAGE_INTEGRATION_COMPLETE_SETUP.md` | Complete setup guide with all code |
| `SAGE_INTEGRATION_README.md` | Quick reference (this file) |

## ✅ Checklist

Before going live:

- [ ] API key generated and configured in both systems
- [ ] Migrations run successfully
- [ ] Routes registered in bootstrap/app.php
- [ ] Middleware registered
- [ ] Test endpoints with curl
- [ ] SageConnector configured and running
- [ ] Admin dashboard accessible
- [ ] Logs monitored (storage/logs/laravel.log)
- [ ] Production API key different from dev
- [ ] HTTPS enabled in production

## 🐛 Troubleshooting

**401 Error**
- Check API key in .env matches appsettings.json
- Run `php artisan config:clear`

**No customers syncing**
- Check customerprofession.status = 'APPROVED'
- Check sage_sync_status is null or 'PENDING'
- Check logs: `storage/logs/laravel.log`

**SageConnector can't connect**
- Check BaseUrl in appsettings.json
- Test endpoint with curl first
- Check firewall/network

## 📞 Need Help?

1. Check `SAGE_INTEGRATION_COMPLETE_SETUP.md` for detailed setup
2. Review logs: `storage/logs/laravel.log`
3. Test endpoints individually with curl
4. Check SageConnector console output

---

**Ready to integrate!** Follow `SAGE_INTEGRATION_COMPLETE_SETUP.md` for complete installation guide.
