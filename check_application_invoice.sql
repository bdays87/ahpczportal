-- Check if Application invoice was created for Tonodzai

-- 1. Check customerprofession status after registration approval
SELECT 
    'Step 1: Check profession status' as step,
    cp.id,
    cp.status,
    cp.customer_id,
    c.name,
    c.surname
FROM customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
WHERE c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%';

-- Expected: status should be 'PENDING' (not AWAITING_REG) after admin approved registration

-- 2. Check if customerregistration was approved
SELECT 
    'Step 2: Check registration approval' as step,
    cr.id,
    cr.status,
    cr.registrationdate,
    cr.certificatenumber
FROM customerregistrations cr
JOIN customerprofessions cp ON cr.customerprofession_id = cp.id
JOIN customers c ON cp.customer_id = c.id
WHERE c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%';

-- Expected: status = 'APPROVED', registrationdate should have a date

-- 3. Check if 'New Application' invoice was created
SELECT 
    'Step 3: Check Application invoice' as step,
    i.id,
    i.invoice_number,
    i.description,
    i.status,
    i.amount,
    i.created_at,
    c.name,
    c.surname
FROM invoices i
JOIN customers c ON i.customer_id = c.id
WHERE i.description = 'New Application'
  AND (c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%')
ORDER BY i.created_at DESC;

-- Expected: Should have a 'New Application' invoice with status 'PENDING'

-- 4. Check if customerapplication record exists
SELECT 
    'Step 4: Check application record' as step,
    ca.id,
    ca.customerprofession_id,
    ca.applicationtype_id,
    ca.status,
    ca.year,
    ca.created_at
FROM customerapplications ca
JOIN customerprofessions cp ON ca.customerprofession_id = cp.id
JOIN customers c ON cp.customer_id = c.id
WHERE c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%'
ORDER BY ca.created_at DESC;

-- Expected: Should have application record with status 'PENDING'

-- 5. Check ALL invoices for Tonodzai (to see what exists)
SELECT 
    'Step 5: All invoices' as step,
    i.id,
    i.invoice_number,
    i.description,
    i.status,
    i.source,
    i.source_id,
    i.amount,
    i.created_at
FROM invoices i
JOIN customers c ON i.customer_id = c.id
WHERE c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%'
ORDER BY i.created_at DESC;

-- This will show all invoices (Registration, Assessment, Application, etc.)

-- 6. Check if applicationfees exist for this profession
SELECT 
    'Step 6: Check application fees' as step,
    af.id as fee_id,
    af.name as fee_name,
    af.amount,
    af.currency_id,
    af.employmentlocation_id as fee_location,
    af.registertype_id as fee_registertype,
    cp.employmentlocation_id as prof_location,
    cp.registertype_id as prof_registertype,
    cp.customertype_id as prof_customertype,
    CASE 
        WHEN af.id IS NULL THEN '❌ NO FEE CONFIGURED'
        WHEN af.employmentlocation_id = cp.employmentlocation_id 
         AND af.registertype_id = cp.registertype_id 
         AND af.name = 'NEW' 
        THEN '✅ FEE EXISTS'
        ELSE '⚠️ FEE MISMATCH'
    END as status
FROM customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
LEFT JOIN applicationfees af ON af.employmentlocation_id = cp.employmentlocation_id 
    AND af.registertype_id = cp.registertype_id
    AND af.name = 'NEW'
WHERE c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%';

-- This checks if there are application fees configured for this profession
-- applicationfees table structure: id, name, amount, currency_id, employmentlocation_id, registertype_id

-- 7. Show ALL available application fees (for reference)
SELECT 
    'Step 7: All Application Fees Available' as step,
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
WHERE af.name = 'NEW'
ORDER BY af.employmentlocation_id, af.registertype_id;

-- This shows all configured application fees to see what's available
