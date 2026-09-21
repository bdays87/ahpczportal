# Application Invoice Query Fix

## 🔴 Problem

Application Invoice page is blank even though invoices exist in the database.

## 🔍 Root Cause

The SQL query in `getcustomerprofessioninvoices()` had improper WHERE clause grouping with `orWhere()`, causing it to return incorrect results or miss invoices.

### Original Query (BROKEN):

```php
$invoices = $this->invoice
    ->where('source_id', $customerprofession_id)
    ->where('source', 'customerprofession')
    ->orWhere('source', 'customerapplication')  // ❌ Breaks the AND chain
    ->where('description', 'like', '%'.$type.'%')
    ->get();
```

This generated SQL like:
```sql
WHERE source_id = X 
  AND source = 'customerprofession'
   OR source = 'customerapplication'  -- ❌ This matches ALL customerapplication invoices!
  AND description LIKE '%New Application%'
```

### Fixed Query:

```php
$invoices = $this->invoice->with('currency', 'customer', 'settlementsplit')
    ->where('description', 'like', '%'.$type.'%')
    ->where(function ($query) use ($customerprofession_id) {
        // Invoices directly linked to customerprofession
        $query->where(function ($q) use ($customerprofession_id) {
            $q->where('source', 'customerprofession')
              ->where('source_id', $customerprofession_id);
        })
        // OR invoices linked to customerapplication that belongs to this profession
        ->orWhere(function ($q) use ($customerprofession_id) {
            $q->where('source', 'customerapplication')
              ->whereIn('source_id', function ($subquery) use ($customerprofession_id) {
                  $subquery->select('id')
                      ->from('customerapplications')
                      ->where('customerprofession_id', $customerprofession_id);
              });
        });
    })
    ->get();
```

This generates proper SQL:
```sql
WHERE description LIKE '%New Application%'
  AND (
      (source = 'customerprofession' AND source_id = X)
      OR 
      (source = 'customerapplication' AND source_id IN (
          SELECT id FROM customerapplications WHERE customerprofession_id = X
      ))
  )
```

## ✅ Fixes Applied

**File:** `app/implementations/_invoiceRepository.php`

### Fix 1: Proper Query Grouping (line 552-571)
- ✅ Wrapped OR conditions in proper closure
- ✅ Added subquery for customerapplication invoices
- ✅ Ensures only invoices related to this profession are returned

### Fix 2: Refresh Customerprofession (line 402)  
- ✅ Added `$customerprofession->refresh()` before using registertype_id
- ✅ Ensures latest data is used when creating invoice

### Fix 3: Validation Checks (lines 448-461)
- ✅ Check registertype_id exists before proceeding
- ✅ Check applicationfee exists before using it
- ✅ Proper error messages

## 🎯 Testing

### Test 1: Check if invoices exist
Run: `check_tonodzai_invoices.sql`

Expected results:
- Should show invoices with description like 'New Application'
- Should show matching customerapplication records

### Test 2: Clear cache and retry
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

Then refresh the Application Invoice page in browser (Ctrl+F5).

### Test 3: Check Livewire response
Add temporary debug in `Applicationinvoicing.php` line 104:

```php
public function getinvoice()
{
    $type = 'New Application';
    if ($this->applicationtype_id != 1) {
        $type = 'Renewal';
    }

    $invoices = $this->invoicerepo->getcustomerprofessioninvoices($this->customerprofession_id, $type);
    
    // TEMPORARY DEBUG
    logger('Application Invoice Debug', [
        'customerprofession_id' => $this->customerprofession_id,
        'type' => $type,
        'invoices_count' => count($invoices),
        'invoices_data' => $invoices
    ]);

    if (count($invoices) > 0) {
        $invoice = collect($invoices['data'])->last();
        return $invoice;
    }

    return null;
}
```

Then check `storage/logs/laravel.log` for the debug output.

## 🚨 Possible Issues

### Issue 1: No Invoice Created Yet

**Check:**
```sql
SELECT COUNT(*) FROM invoices i
JOIN customers c ON i.customer_id = c.id
WHERE (c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%')
  AND i.description = 'New Application';
```

**If count = 0:** Invoice wasn't created during registration approval.

**Solution:** 
1. Reset registration status to `AWAITING_REG`
2. Have admin re-approve
3. With the fixes in place, invoice should now be created

### Issue 2: Invoice Description Mismatch

**Check:**
```sql
SELECT DISTINCT description FROM invoices
WHERE customer_id IN (
    SELECT id FROM customers 
    WHERE name LIKE '%Tonodzai%' OR surname LIKE '%Chirandu%'
);
```

**If description shows** something like 'Application' instead of 'New Application':

The query uses `LIKE '%New Application%'`, so:
- ✅ 'New Application' - matches
- ✅ 'NEW APPLICATION' - matches (case insensitive)
- ❌ 'Application' - doesn't match
- ❌ 'New App' - doesn't match

**Solution:** Check what the actual description is and either:
- Update the invoice description in database
- OR adjust the query to match

### Issue 3: customerapplication Record Missing

**Check:**
```sql
SELECT COUNT(*) FROM customerapplications ca
JOIN customerprofessions cp ON ca.customerprofession_id = cp.id
JOIN customers c ON cp.customer_id = c.id
WHERE c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%';
```

**If count = 0:** Application record wasn't created.

**Solution:** Create it manually:
```sql
INSERT INTO customerapplications (
    customerprofession_id,
    uuid,
    customer_id,
    registertype_id,
    year,
    status,
    created_at,
    updated_at
)
SELECT 
    cp.id,
    UUID(),
    cp.customer_id,
    cp.registertype_id,
    YEAR(CURDATE()),
    'PENDING',
    NOW(),
    NOW()
FROM customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
WHERE (c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%')
  AND NOT EXISTS (
      SELECT 1 FROM customerapplications 
      WHERE customerprofession_id = cp.id
  );
```

## 📋 Verification Checklist

After applying fixes:

- [ ] Run `check_tonodzai_invoices.sql` to see what exists
- [ ] Clear Laravel caches (`php artisan cache:clear`)
- [ ] Refresh browser (Ctrl+F5)
- [ ] Check Application Invoice page shows invoice
- [ ] Verify payment buttons are displayed
- [ ] Check `storage/logs/laravel.log` for any errors

## 🎯 Expected Results

After all fixes:

### On Application Invoice Page:
```
Application invoice
━━━━━━━━━━━━━━━━━━━━━━━━━━

Invoice pending
Please settle the invoice to continue.

Invoice details
Invoice Number: INV-2026-XXXX-XX
Date: 2026-09-21
Description: New Application
Status: PENDING
Amount: USD 100.00

[Settle Invoice Button]
[Attach Payment Button]
```

### Invoice Query Should Return:
```php
[
    'data' => [
        [
            'id' => 123,
            'invoice_number' => 'INV-2026-XXXX-XX',
            'description' => 'New Application',
            'status' => 'PENDING',
            'amount' => 100.00,
            'currency' => 'USD',
            'button' => 'enabled',  // or 'disabled' if registration not approved
            'comment' => '',
            ...
        ]
    ],
    'total_invoice' => 100.00,
    ...
]
```

## 📝 Summary

**3 fixes applied:**
1. ✅ Fixed query grouping in `getcustomerprofessioninvoices()`
2. ✅ Added `refresh()` before using registertype_id in invoice creation
3. ✅ Added validation checks for registertype_id and applicationfee

**Next step:** Run diagnostic SQL to confirm invoice exists and query is working.

---

**Diagnostic Files Created:**
- `check_tonodzai_invoices.sql` - Check what invoices exist
- `APPLICATION_INVOICE_QUERY_FIX.md` - This document
