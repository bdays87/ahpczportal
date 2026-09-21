# Sage Pastel Integration - Complete Setup Guide

## 📦 What Was Created

### Laravel Portal Files (AHPCZ Portal)

#### 1. Controllers
- ✅ `app/Http/Controllers/Api/SageConnectorController.php` - Main API controller with 5 endpoints

#### 2. Middleware
- ✅ `app/Http/Middleware/VerifySageConnectorApiKey.php` - API key authentication

#### 3. Migrations (3 files)
- ✅ `2026_09_21_000001_add_sage_sync_fields_to_customerprofessions.php`
- ✅ `2026_09_21_000002_add_sage_sync_fields_to_invoices.php`
- ✅ `2026_09_21_000003_create_customer_sage_statements_table.php`

#### 4. Livewire Admin Dashboard
- ✅ `app/Livewire/Admin/SageSyncStatus.php` - Component
- ✅ `resources/views/livewire/admin/sage-sync-status.blade.php` - View

#### 5. Routes
- ✅ `routes/api_sage_connector.php` - API routes

#### 6. Documentation
- ✅ `SAGE_PASTEL_INTEGRATION_GUIDE.md` - Overview
- ✅ `SAGE_CONNECTOR_CONFIG.md` - Configuration steps
- ✅ `SAGE_INTEGRATION_COMPLETE_SETUP.md` - This file

---

## 🚀 Installation Steps

### Step 1: Add Configuration

**File: `config/services.php`**

Add this array to the file:

```php
'sage_connector' => [
    'api_key' => env('SAGE_CONNECTOR_API_KEY'),
    'enabled' => env('SAGE_CONNECTOR_ENABLED', false),
],
```

### Step 2: Environment Variables

**File: `.env`**

Add these lines:

```env
# Sage Connector Integration
SAGE_CONNECTOR_ENABLED=true
SAGE_CONNECTOR_API_KEY=
```

**Generate API Key:**

```bash
php artisan tinker
>>> Str::random(64)
# Copy the output and paste it as the API key value
```

### Step 3: Register Middleware

**Laravel 11 - File: `bootstrap/app.php`**

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Sage Connector routes
            Route::middleware('api')
                ->group(base_path('routes/api_sage_connector.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'sage.connector' => \App\Http\Middleware\VerifySageConnectorApiKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

**Laravel 10 - File: `app/Http/Kernel.php`**

```php
protected $middlewareAliases = [
    // ... existing aliases
    'sage.connector' => \App\Http\Middleware\VerifySageConnectorApiKey::class,
];
```

### Step 4: Run Migrations

```bash
php artisan migrate
```

Expected output:
```
Migrating: 2026_09_21_000001_add_sage_sync_fields_to_customerprofessions
Migrated:  2026_09_21_000001_add_sage_sync_fields_to_customerprofessions (25.67ms)
Migrating: 2026_09_21_000002_add_sage_sync_fields_to_invoices
Migrated:  2026_09_21_000002_add_sage_sync_fields_to_invoices (31.22ms)
Migrating: 2026_09_21_000003_create_customer_sage_statements_table
Migrated:  2026_09_21_000003_create_customer_sage_statements_table (18.45ms)
```

### Step 5: Add Admin Dashboard Route

**File: `routes/web.php`**

Add this route:

```php
use App\Livewire\Admin\SageSyncStatus;

// In your admin routes section
Route::middleware(['auth'])->group(function () {
    // ... existing routes
    
    // Sage Sync Status Dashboard
    Volt::route('/sage-sync-status', SageSyncStatus::class)
        ->name('sage.sync.status')
        ->middleware('can:view-sage-sync'); // Adjust permission as needed
});
```

### Step 6: Clear Cache

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 🔧 SageConnector Configuration

### Step 1: Update appsettings.json

**File: `C:\Users\Admin\Desktop\tutorial\lms\sageconnector\ConnectorService\appsettings.json`**

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
      "Name": "AHPCZ",
      "BaseUrl": "http://127.0.0.1:8000",
      "ApiKey": "paste-your-64-character-api-key-here",
      "Enabled": true,
      "PollIntervalMinutes": 5
    }
  ],
  "Sage": {
    "CompanyName": "Your Company Name",
    "Username": "ADMIN",
    "Password": "your-sage-password"
  }
}
```

**Important:** Use the SAME API key you generated for Laravel!

### Step 2: For Production

**File: `appsettings.Production.json`**

```json
{
  "Portals": [
    {
      "Name": "AHPCZ",
      "BaseUrl": "https://portal.ahpcz.co.zw",
      "ApiKey": "production-api-key-different-from-dev",
      "Enabled": true,
      "PollIntervalMinutes": 5
    }
  ]
}
```

---

## ✅ Testing

### Test 1: Authentication

```bash
# Replace with your actual API key
curl -X GET "http://127.0.0.1:8000/api/connector/customers/pending" \
     -H "X-Api-Key: your-api-key-here"
```

**Expected:** JSON array (empty `[]` or with customers)

**If 401 error:** API key is wrong or not configured

### Test 2: Create Test Customer

1. Go to portal and create a test practitioner
2. Approve their registration
3. Run curl command again
4. Should see the customer in response

### Test 3: Create Test Invoice

1. Create and pay an invoice for the test customer
2. Test pending orders endpoint:

```bash
curl -X GET "http://127.0.0.1:8000/api/connector/orders/pending" \
     -H "X-Api-Key: your-api-key-here"
```

**Expected:** JSON array with the paid invoice

### Test 4: Run SageConnector

```bash
cd C:\Users\Admin\Desktop\tutorial\lms\sageconnector\ConnectorService
dotnet run
```

**Look for in output:**
```
[12:30:45 INF] Polling AHPCZ...
[12:30:45 INF] Found 1 pending customers
[12:30:45 INF] Found 1 pending orders
[12:30:46 INF] Created customer in Sage: CUST-123
[12:30:47 INF] Created invoice in Sage: INV-001
```

### Test 5: Check Admin Dashboard

1. Login as admin
2. Go to `/sage-sync-status`
3. Should see:
   - Sync statistics
   - Customer sync status
   - Invoice sync status
   - Retry failed syncs button

---

## 📊 API Contract Reference

### 1. GET /api/connector/customers/pending

**Query Params:**
- `since` (optional): ISO-8601 datetime, defaults to 7 days ago

**Response:**
```json
[
  {
    "externalId": "CUST-12345",
    "name": "John Doe",
    "contact": "John Doe",
    "email": "john@example.com",
    "phone": "+263771234567",
    "vatNumber": null,
    "physicalAddress": "123 Main St, Harare",
    "createdUtc": "2026-09-21T10:00:00.0000000Z"
  }
]
```

### 2. GET /api/connector/orders/pending

**Query Params:**
- `since` (optional): ISO-8601 datetime

**Response:**
```json
[
  {
    "externalId": "INV-2026-123-45",
    "customerExternalId": "CUST-12345",
    "sageCustomerCode": "CUST-12345",
    "orderDate": "2026-09-21T10:30:00.0000000Z",
    "reference": "Registration - INV-2026-123-45",
    "lines": [
      {
        "stockCode": "SERVICE-REGISTRATION",
        "description": "Registration",
        "quantity": 1,
        "unitPrice": 100.00
      }
    ]
  }
]
```

### 3. POST /api/connector/ack

**Request:**
```json
{
  "externalId": "CUST-12345",
  "kind": "customer",
  "success": true,
  "sageReference": "CUST-12345",
  "errorMessage": null,
  "processedUtc": "2026-09-21T10:31:00.0000000Z"
}
```

### 4. POST /api/connector/invoices

**Request:**
```json
{
  "invoiceNumber": "SI-001234",
  "customerCode": "CUST-12345",
  "invoiceDate": "2026-09-21T00:00:00.0000000Z",
  "totalExclTax": 100.00,
  "totalTax": 15.00,
  "totalInclTax": 115.00,
  "amountOutstanding": 115.00,
  "status": "Unprocessed"
}
```

### 5. POST /api/connector/statements

**Request:**
```json
{
  "customerCode": "CUST-12345",
  "currentBalance": 115.00,
  "current": 115.00,
  "days30": 0,
  "days60": 0,
  "days90Plus": 0,
  "asOfUtc": "2026-09-21T10:00:00.0000000Z"
}
```

---

## 🔐 Security Checklist

- [ ] Generated strong random API key (64+ characters)
- [ ] Different API keys for dev/staging/production
- [ ] HTTPS enabled in production
- [ ] API key in `.env`, not committed to git
- [ ] `.env` in `.gitignore`
- [ ] Sage database credentials secure
- [ ] Connector server has firewall rules
- [ ] Only necessary ports open
- [ ] Regular security audits
- [ ] Monitor failed authentication attempts

---

## 📋 Database Schema

### customerprofessions (New Columns)

| Column | Type | Description |
|--------|------|-------------|
| sage_sync_status | VARCHAR(20) | PENDING, SYNCING, SYNCED, FAILED |
| sage_customer_code | VARCHAR(50) | Customer code in Sage |
| sage_sync_error | TEXT | Error message if failed |
| sage_synced_at | TIMESTAMP | Last sync timestamp |

### invoices (New Columns)

| Column | Type | Description |
|--------|------|-------------|
| sage_sync_status | VARCHAR(20) | Sync status |
| sage_invoice_number | VARCHAR(50) | Sage invoice number |
| sage_sync_error | TEXT | Error message |
| sage_synced_at | TIMESTAMP | Last sync timestamp |
| sage_total_excl_tax | DECIMAL(15,2) | Excl tax from Sage |
| sage_total_tax | DECIMAL(15,2) | Tax from Sage |
| sage_total_incl_tax | DECIMAL(15,2) | Incl tax from Sage |
| sage_outstanding | DECIMAL(15,2) | Outstanding amount |
| sage_status | VARCHAR(50) | Sage invoice status |
| sage_invoice_date | TIMESTAMP | Invoice date from Sage |
| sage_last_updated_at | TIMESTAMP | Last update from Sage |

### customer_sage_statements (New Table)

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| customer_id | BIGINT | Foreign key to customers |
| sage_customer_code | VARCHAR(50) | Sage customer code |
| current_balance | DECIMAL(15,2) | Total balance |
| current | DECIMAL(15,2) | Current (not due) |
| days_30 | DECIMAL(15,2) | 1-30 days overdue |
| days_60 | DECIMAL(15,2) | 31-60 days overdue |
| days_90_plus | DECIMAL(15,2) | 90+ days overdue |
| as_of_date | TIMESTAMP | Statement date |

---

## 🎯 Next Steps

1. ✅ Complete installation steps above
2. ✅ Test all endpoints with curl
3. ✅ Configure SageConnector
4. ✅ Run test sync
5. ✅ Monitor logs for errors
6. ✅ Add admin dashboard to menu
7. ✅ Train staff on monitoring sync status
8. ✅ Set up alerts for failed syncs
9. ✅ Schedule regular audits

---

## 📞 Support

**Issues?**
- Check `storage/logs/laravel.log`
- Check SageConnector console output
- Verify API key matches in both systems
- Ensure migrations ran successfully
- Test endpoints with curl first

**Success Indicators:**
- ✅ Curl tests return data
- ✅ SageConnector polls without errors
- ✅ Customers/invoices sync to Sage
- ✅ Ack responses update portal
- ✅ Admin dashboard shows synced records

---

**Integration Complete!** 🎉

Your AHPCZ Portal is now ready to sync with Sage Pastel Evolution!
