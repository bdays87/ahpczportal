# Sage Connector Configuration

## Step 1: Add to config/services.php

Add this to your `config/services.php` file:

```php
'sage_connector' => [
    'api_key' => env('SAGE_CONNECTOR_API_KEY'),
    'enabled' => env('SAGE_CONNECTOR_ENABLED', false),
],
```

## Step 2: Add to .env

Add these variables to your `.env` file:

```env
# Sage Connector Integration
SAGE_CONNECTOR_ENABLED=true
SAGE_CONNECTOR_API_KEY=your-secret-api-key-here
```

**Generate a secure API key:**
```bash
php artisan tinker
>>> Str::random(64)
```

## Step 3: Register Middleware

Add to `bootstrap/app.php` (Laravel 11) or `app/Http/Kernel.php` (Laravel 10):

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        // ... existing aliases
        'sage.connector' => \App\Http\Middleware\VerifySageConnectorApiKey::class,
    ]);
})
```

## Step 4: Register Routes

Add to `bootstrap/app.php`:

```php
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
```

## Step 5: Run Migrations

```bash
php artisan migrate
```

This creates:
- `sage_sync_*` columns in `customerprofessions` table
- `sage_*` columns in `invoices` table
- `customer_sage_statements` table

## Step 6: Configure SageConnector

In the SageConnector project, update `appsettings.json`:

```json
{
  "Portals": [
    {
      "Name": "AHPCZ",
      "BaseUrl": "https://portal.ahpcz.co.zw",
      "ApiKey": "your-secret-api-key-here",
      "Enabled": true,
      "PollIntervalMinutes": 5
    }
  ]
}
```

**Important:** Use the SAME API key in both places!

## Step 7: Test the Connection

### Test from Portal side:

```bash
# Test authentication
curl -X GET "https://portal.ahpcz.co.zw/api/connector/customers/pending" \
     -H "X-Api-Key: your-secret-api-key-here"

# Should return JSON array (empty or with customers)
```

### Test from SageConnector side:

Run the connector service and check logs:

```bash
cd C:\Users\Admin\Desktop\tutorial\lms\sageconnector\ConnectorService
dotnet run
```

Look for:
- ✅ "Polling AHPCZ..."
- ✅ "Found X pending customers"
- ✅ "Found Y pending orders"

## API Endpoints Summary

| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/api/connector/customers/pending?since={datetime}` | Fetch customers awaiting sync |
| GET | `/api/connector/orders/pending?since={datetime}` | Fetch invoices awaiting sync |
| POST | `/api/connector/ack` | Receive sync acknowledgment |
| POST | `/api/connector/invoices` | Receive invoice from Sage |
| POST | `/api/connector/statements` | Receive statement from Sage |

## Monitoring

### Check Sync Status:

```sql
-- Customers pending sync
SELECT COUNT(*) FROM customerprofessions 
WHERE status = 'APPROVED' 
  AND (sage_sync_status IS NULL OR sage_sync_status = 'PENDING');

-- Invoices pending sync
SELECT COUNT(*) FROM invoices 
WHERE status = 'PAID' 
  AND (sage_sync_status IS NULL OR sage_sync_status = 'PENDING');

-- Failed syncs
SELECT * FROM customerprofessions 
WHERE sage_sync_status = 'FAILED'
ORDER BY updated_at DESC;

SELECT * FROM invoices 
WHERE sage_sync_status = 'FAILED'
ORDER BY updated_at DESC;
```

### View Statements:

```sql
SELECT 
    c.name,
    c.surname,
    s.current_balance,
    s.days_30,
    s.days_60,
    s.days_90_plus,
    s.as_of_date
FROM customer_sage_statements s
JOIN customers c ON s.customer_id = c.id
ORDER BY s.current_balance DESC;
```

## Troubleshooting

### Issue: 401 Unauthorized

**Cause:** API key mismatch

**Fix:**
1. Check `.env` has `SAGE_CONNECTOR_API_KEY`
2. Check SageConnector `appsettings.json` has same key
3. Clear config cache: `php artisan config:clear`

### Issue: Customers not syncing

**Check:**
1. Status is 'APPROVED'
2. Not already synced (sage_sync_status)
3. Updated within timeframe
4. Check logs: `storage/logs/laravel.log`

### Issue: Invoices not syncing

**Check:**
1. Status is 'PAID'
2. Customer is already synced to Sage
3. Invoice has lines or valid amount
4. Check logs: `storage/logs/laravel.log`

## Security Best Practices

1. ✅ Use strong random API key (64+ characters)
2. ✅ Use HTTPS in production
3. ✅ Different API key per environment (dev, staging, prod)
4. ✅ Rotate keys periodically
5. ✅ Monitor failed auth attempts in logs
6. ✅ IP whitelist if possible (connector's IP)

## Next Steps

1. ✅ Install migrations
2. ✅ Configure API key
3. ✅ Register routes
4. ✅ Test endpoints
5. ✅ Configure SageConnector
6. ✅ Monitor sync status
7. ✅ Create admin dashboard to view sync status

---

**Need Help?**
- Check logs: `storage/logs/laravel.log`
- SageConnector logs: Check connector service output
- Review: `SAGE_PASTEL_INTEGRATION_GUIDE.md`
