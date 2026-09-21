# Registration Approvals - Troubleshooting Guide

## 🔍 Problem

Tonodzai Chirandu's registration is not showing up in Registration Approvals page.

## 🎯 Why Records Appear in Registration Approvals

For a registration to appear in the Registration Approvals page, it must meet **ALL** these conditions:

```php
1. customerprofession.status = 'AWAITING_REG'
2. customerprofession.year = current year (2026)
```

## 🛠️ Diagnostic Tools Created

### 1. SQL Diagnostic Script
**File:** `check_tonodzai_registration.sql`

Run this in HeidiSQL or MySQL client to check:
- Customer record exists
- Customerprofession record and its status
- Customerregistration record
- Invoice records
- Why it's not appearing

### 2. Laravel Artisan Command
**File:** `app/Console/Commands/CheckRegistrationStatus.php`

Run this command:
```bash
php artisan check:registration Tonodzai
```

This will show:
- Customer details
- All professions for that customer
- Current status and year
- Whether it should appear in approvals
- Why it's not appearing (if not)
- List of all AWAITING_REG registrations

## ✅ Checklist to Verify

Run these checks in order:

### Check 1: Customer Exists
```sql
SELECT * FROM customers 
WHERE name LIKE '%Tonodzai%' 
   OR surname LIKE '%Chirandu%';
```
✅ **Expected:** Should return at least one record

### Check 2: Customerprofession Record
```sql
SELECT 
    cp.id,
    cp.status,
    cp.year,
    c.name,
    c.surname
FROM customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
WHERE c.name LIKE '%Tonodzai%' 
   OR c.surname LIKE '%Chirandu%';
```
✅ **Expected Status:** `AWAITING_REG`  
✅ **Expected Year:** `2026`

### Check 3: Customerregistration Record
```sql
SELECT 
    cr.*,
    cp.status as profession_status
FROM customerregistrations cr
JOIN customerprofessions cp ON cr.customerprofession_id = cp.id
JOIN customers c ON cr.customer_id = c.id
WHERE c.name LIKE '%Tonodzai%' 
   OR c.surname LIKE '%Chirandu%';
```
✅ **Expected:** Record should exist with `status = 'AWAITING'`

### Check 4: Year Filter
The registration approvals page filters by current year:
```php
$this->year = date("Y"); // Must be 2026
```

If the profession was created with a different year, it won't show up!

## 🔧 Common Issues & Fixes

### Issue 1: Status is Not AWAITING_REG

**Check:**
```sql
SELECT id, status FROM customerprofessions 
WHERE customer_id = (SELECT id FROM customers WHERE name LIKE '%Tonodzai%' LIMIT 1);
```

**If status is PENDING or something else:**
```sql
UPDATE customerprofessions 
SET status = 'AWAITING_REG'
WHERE customer_id = (SELECT id FROM customers WHERE name LIKE '%Tonodzai%' LIMIT 1);
```

### Issue 2: Wrong Year

**Check:**
```sql
SELECT id, year FROM customerprofessions 
WHERE customer_id = (SELECT id FROM customers WHERE name LIKE '%Tonodzai%' LIMIT 1);
```

**If year is not 2026:**
```sql
UPDATE customerprofessions 
SET year = 2026
WHERE customer_id = (SELECT id FROM customers WHERE name LIKE '%Tonodzai%' LIMIT 1);
```

### Issue 3: No Customerregistration Record

**Check:**
```sql
SELECT COUNT(*) FROM customerregistrations 
WHERE customer_id = (SELECT id FROM customers WHERE name LIKE '%Tonodzai%' LIMIT 1);
```

**If count is 0, create the record:**
```sql
INSERT INTO customerregistrations (
    customerprofession_id,
    customer_id,
    year,
    status,
    created_at,
    updated_at
)
SELECT 
    cp.id,
    cp.customer_id,
    2026,
    'AWAITING',
    NOW(),
    NOW()
FROM customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
WHERE c.name LIKE '%Tonodzai%'
LIMIT 1;
```

### Issue 4: Registration Already Exists (Duplicate Check)

The code checks for existing registration:
```php
if ($customerregistration) {
    return ['status' => 'error', 'message' => 'Registration already submitted'];
}
```

If you see this error, the registration record already exists. Check its status:
```sql
SELECT cr.*, cp.status 
FROM customerregistrations cr
JOIN customerprofessions cp ON cr.customerprofession_id = cp.id
JOIN customers c ON cr.customer_id = c.id
WHERE c.name LIKE '%Tonodzai%';
```

## 🎯 Quick Fix Script

If all else fails, run this to force the record into approval state:

```sql
-- Find Tonodzai's profession ID
SET @profession_id = (
    SELECT cp.id 
    FROM customerprofessions cp
    JOIN customers c ON cp.customer_id = c.id
    WHERE c.name LIKE '%Tonodzai%' 
    LIMIT 1
);

-- Update profession status
UPDATE customerprofessions 
SET status = 'AWAITING_REG', year = 2026
WHERE id = @profession_id;

-- Ensure registration record exists
INSERT IGNORE INTO customerregistrations (
    customerprofession_id,
    customer_id,
    year,
    status,
    created_at,
    updated_at
)
SELECT 
    @profession_id,
    customer_id,
    2026,
    'AWAITING',
    NOW(),
    NOW()
FROM customerprofessions
WHERE id = @profession_id;

-- Update registration status if exists
UPDATE customerregistrations 
SET status = 'AWAITING'
WHERE customerprofession_id = @profession_id;

-- Verify it will show in approvals
SELECT 
    cp.id,
    cp.uuid,
    cp.status,
    cp.year,
    c.name,
    c.surname,
    cr.status as reg_status
FROM customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
LEFT JOIN customerregistrations cr ON cr.customerprofession_id = cp.id
WHERE cp.id = @profession_id;
```

## 📊 Registration Approvals Query Logic

The page uses this query logic:
```php
// From Registrationapprovals.php
$this->status = "AWAITING_REG";
$this->year = date("Y");

// From _customerprofessionRepository.php
return $this->customerprofession
    ->with('customer', 'profession', ..., 'registration', ...)
    ->when($status, function ($query) use ($status) {
        $query->where('status', $status);
    })
    ->when($year, function ($query) use ($year) {
        $query->where('year', $year);
    })
    ->paginate(500);
```

So it's looking for:
```sql
SELECT * FROM customerprofessions
WHERE status = 'AWAITING_REG'
  AND year = '2026'
LIMIT 500;
```

## 🔍 Debug Mode - Check What's Showing

To see what's currently showing in the approvals page:

```sql
SELECT 
    cp.id,
    c.name,
    c.surname,
    p.name as profession,
    cp.status,
    cp.year,
    cr.status as reg_status,
    cp.updated_at
FROM customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
LEFT JOIN professions p ON cp.profession_id = p.id
LEFT JOIN customerregistrations cr ON cr.customerprofession_id = cp.id
WHERE cp.status = 'AWAITING_REG'
  AND cp.year = YEAR(CURDATE())
ORDER BY cp.updated_at DESC;
```

This shows exactly what the approval page should show.

## 💡 Testing Steps

1. **Run the artisan command:**
   ```bash
   php artisan check:registration Tonodzai
   ```

2. **Check output:**
   - Does it say "SHOULD APPEAR IN REGISTRATION APPROVALS"?
   - If NO, note the reason given
   - If reason is wrong status, run fix script
   - If reason is wrong year, run fix script

3. **Refresh the approvals page in browser**

4. **If still not showing:**
   - Clear Laravel cache: `php artisan cache:clear`
   - Clear config cache: `php artisan config:clear`
   - Clear view cache: `php artisan view:clear`

## 🎓 Understanding the Flow

```
Registration Submitted
        ↓
customerprofession.status = 'AWAITING_REG'
        ↓
customerregistration.status = 'AWAITING'
        ↓
Appears in Registration Approvals List
        ↓
Admin Approves
        ↓
customerprofession.status = 'APPROVED'
customerregistration.status = 'APPROVED'
        ↓
Removed from Registration Approvals
        ↓
User can proceed to Application
```

## 📝 Quick Reference

**Where is the code?**
- Controller: `app/Livewire/Admin/Registrationapprovals.php`
- Repository: `app/implementations/_customerprofessionRepository.php`
- View: `resources/views/livewire/admin/registrationapprovals.blade.php`

**Status values:**
- `PENDING` - Initial state
- `AWAITING_REG` - Waiting for registration approval
- `APPROVED` - Registration approved
- `REJECTED` - Registration rejected

**Key tables:**
- `customers` - Customer details
- `customerprofessions` - Profession registrations
- `customerregistrations` - Registration records

---

**Next Steps:**
1. Run: `php artisan check:registration Tonodzai`
2. Check the output
3. Apply the appropriate fix based on the diagnostic results
4. Refresh the Registration Approvals page

**If still having issues, check:**
- Browser cache (Ctrl+F5 to hard refresh)
- User permissions (`registrations.access` permission)
- Database connection
- Laravel logs: `storage/logs/laravel.log`
