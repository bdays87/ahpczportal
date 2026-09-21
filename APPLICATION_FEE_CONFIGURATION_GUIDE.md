# Application Fee Configuration Guide

## 🎯 Purpose

Application fees are required for creating "New Application" invoices. When a registration is approved, the system automatically creates an application invoice based on the configured fees.

## 📋 Required Configuration

### Application Fee Lookup Logic

When creating a "New Application" invoice, the system looks for an application fee that matches:

```php
$applicationfee = DB::table('applicationfees')
    ->where('employmentlocation_id', $customerprofession->employmentlocation_id)
    ->where('registertype_id', $customerprofession->registertype_id)
    ->where('name', 'NEW')
    ->latest()
    ->first();
```

### Required Fields Match:

1. **employmentlocation_id** - Must match practitioner's employment location
2. **registertype_id** - Must match the register type selected during registration approval
3. **name** - Must be 'NEW' (for new applications, vs 'RENEWAL' for renewals)

## 🔍 Checking Current Configuration

### Run Diagnostic SQL:

```sql
-- Check Tonodzai's profession details
SELECT 
    cp.id as profession_id,
    cp.employmentlocation_id,
    el.name as employment_location,
    cp.registertype_id,
    rt.name as register_type,
    cp.customertype_id,
    ct.name as customer_type
FROM customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
LEFT JOIN employmentlocations el ON cp.employmentlocation_id = el.id
LEFT JOIN registertypes rt ON cp.registertype_id = rt.id
LEFT JOIN customertypes ct ON cp.customertype_id = ct.id
WHERE c.name LIKE '%Tonodzai%';
```

### Check if matching fee exists:

```sql
-- Check for matching application fee
SELECT 
    af.id,
    af.name,
    af.amount,
    c.name as currency,
    af.employmentlocation_id,
    af.registertype_id
FROM applicationfees af
JOIN currencies c ON af.currency_id = c.id
WHERE af.employmentlocation_id = [employment_location_id_from_above]
  AND af.registertype_id = [registertype_id_from_above]
  AND af.name = 'NEW';
```

### See all available fees:

```sql
-- List ALL application fees
SELECT 
    af.id,
    af.name,
    af.amount,
    c.name as currency,
    el.name as employment_location,
    rt.name as register_type
FROM applicationfees af
LEFT JOIN currencies c ON af.currency_id = c.id
LEFT JOIN employmentlocations el ON af.employmentlocation_id = el.id
LEFT JOIN registertypes rt ON af.registertype_id = rt.id
WHERE af.name = 'NEW';
```

## ⚠️ Common Issues

### Issue 1: No Register Type Selected During Approval

**Symptom:** `registertype_id` is NULL after registration approval

**Cause:** Admin didn't select a register type when approving

**Solution:** 
1. Check if register type dropdown is showing in approval modal
2. Make sure admin selects register type before approving
3. Update manually if needed:
   ```sql
   UPDATE customerprofessions 
   SET registertype_id = [correct_id]
   WHERE id = [profession_id];
   ```

### Issue 2: Application Fee Not Configured

**Symptom:** Error message "Application fee not configured for this register type and location."

**Cause:** No application fee exists in database for the specific combination of employment location + register type

**Solution:** Add application fee via admin panel or SQL:

```sql
-- Example: Add application fee
INSERT INTO applicationfees (
    name,
    amount,
    currency_id,
    employmentlocation_id,
    registertype_id,
    created_at,
    updated_at
) VALUES (
    'NEW',
    100.00,  -- amount
    1,       -- currency_id (check currencies table)
    1,       -- employmentlocation_id
    1,       -- registertype_id
    NOW(),
    NOW()
);
```

### Issue 3: Wrong Employment Location or Register Type

**Symptom:** Fee exists but not matching

**Cause:** Practitioner's employment location or register type doesn't match any configured fee

**Solution:** 
1. Check what fees are available
2. Either update practitioner's details OR add missing fee configuration

## 🛠️ Creating Application Fees

### Via Admin Panel:

1. Go to Admin → Settings → Application Fees
2. Click "Add New"
3. Fill in:
   - Name: `NEW`
   - Amount: (e.g., 100.00)
   - Currency: (e.g., USD)
   - Employment Location: (e.g., Urban, Rural)
   - Register Type: (e.g., Full Registration, Temporary, etc.)
4. Save

### Via SQL (Quick):

```sql
-- Get reference data first
SELECT * FROM employmentlocations;  -- Get location IDs
SELECT * FROM registertypes;        -- Get register type IDs
SELECT * FROM currencies;           -- Get currency IDs

-- Then insert fee
INSERT INTO applicationfees (
    name,
    amount,
    currency_id,
    employmentlocation_id,
    registertype_id,
    created_at,
    updated_at
) VALUES (
    'NEW',
    100.00,
    1,  -- USD (adjust based on your currencies table)
    1,  -- Urban (adjust based on your employmentlocations table)
    1,  -- Full Registration (adjust based on your registertypes table)
    NOW(),
    NOW()
);
```

## 📊 Complete Application Fee Matrix

You should have application fees configured for ALL combinations of:

```
Employment Locations × Register Types × Application Types
```

Example matrix:

| Employment Location | Register Type | Application Type | Amount |
|---------------------|---------------|------------------|--------|
| Urban               | Full          | NEW              | 100    |
| Urban               | Temporary     | NEW              | 50     |
| Rural               | Full          | NEW              | 75     |
| Rural               | Temporary     | NEW              | 40     |
| Urban               | Full          | RENEWAL          | 80     |
| etc...              | etc...        | etc...           | etc... |

## 🔧 Troubleshooting Steps

### Step 1: Run Diagnostics
```bash
# Use the diagnostic SQL script
# Open: check_application_invoice.sql
# Run all queries to see what's missing
```

### Step 2: Identify Missing Pieces

**Check:**
- ✅ Is `registertype_id` set on customerprofession?
- ✅ Is `employmentlocation_id` set on customerprofession?
- ✅ Does matching application fee exist?

### Step 3: Fix What's Missing

**If registertype_id is NULL:**
```sql
UPDATE customerprofessions 
SET registertype_id = [correct_id]
WHERE id = [profession_id];
```

**If application fee doesn't exist:**
- Add via admin panel OR
- Insert via SQL (see above)

### Step 4: Test

After fixing:
1. Try creating invoice again OR
2. Reset registration status to `AWAITING_REG` and re-approve

## ✅ Verification

After configuration, verify with:

```sql
-- This should return a matching fee
SELECT 
    cp.id as profession_id,
    cp.employmentlocation_id,
    cp.registertype_id,
    af.id as fee_id,
    af.amount,
    'Fee exists!' as status
FROM customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
LEFT JOIN applicationfees af ON af.employmentlocation_id = cp.employmentlocation_id 
    AND af.registertype_id = cp.registertype_id
    AND af.name = 'NEW'
WHERE c.name LIKE '%Tonodzai%';
```

Expected result:
- `fee_id` should NOT be NULL
- `amount` should show the fee amount
- `status` should say "Fee exists!"

---

## 📝 Summary

**For application invoice to be created automatically:**

1. ✅ Practitioner has `employmentlocation_id` set
2. ✅ Admin selects `registertype_id` during registration approval  
3. ✅ Application fee exists for that combination
4. ✅ Code calls `createInvoice(['description' => 'New Application', ...])`
5. ✅ Invoice is created successfully

**If any step fails:** Use diagnostic SQL to identify and fix!
