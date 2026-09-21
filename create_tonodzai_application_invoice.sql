-- Manually create Application invoice and record for Tonodzai
-- Use this because registration was approved before the fix was applied

-- Step 1: Get Tonodzai's details
SELECT 
    '@profession_id' as var,
    cp.id as value,
    c.name,
    c.surname,
    cp.customer_id,
    cp.registertype_id,
    cp.employmentlocation_id,
    cp.status
FROM customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
WHERE c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%'
LIMIT 1;

-- Step 2: Check if application fee exists
SELECT 
    'Application Fee Check' as step,
    af.id as fee_id,
    af.amount,
    af.currency_id,
    af.employmentlocation_id,
    af.registertype_id,
    cp.employmentlocation_id as prof_location,
    cp.registertype_id as prof_registertype
FROM customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
LEFT JOIN applicationfees af ON af.employmentlocation_id = cp.employmentlocation_id 
    AND af.registertype_id = cp.registertype_id
    AND af.name = 'NEW'
WHERE c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%';

-- If fee_id is NULL above, you need to configure application fee first!

-- Step 3: Create customerapplication record (if doesn't exist)
INSERT INTO customerapplications (
    customerprofession_id,
    uuid,
    customer_id,
    registertype_id,
    applicationtype_id,
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
    1,  -- applicationtype_id = 1 for New Application
    YEAR(CURDATE()),
    'PENDING',
    NOW(),
    NOW()
FROM customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
WHERE (c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%')
  AND NOT EXISTS (
      SELECT 1 FROM customerapplications ca
      WHERE ca.customerprofession_id = cp.id
  )
LIMIT 1;

-- Step 4: Create the invoice
INSERT INTO invoices (
    uuid,
    invoice_number,
    customer_id,
    description,
    amount,
    currency_id,
    status,
    source,
    source_id,
    createdby,
    created_at,
    updated_at
)
SELECT 
    UUID(),
    CONCAT('INV-', YEAR(CURDATE()), '-', cp.id, '-', LPAD(FLOOR(RAND() * 1000), 3, '0')),  -- Generate invoice number
    cp.customer_id,
    'New Application',
    af.amount,
    af.currency_id,
    'PENDING',
    'customerprofession',
    cp.id,
    1,  -- createdby user_id (adjust if needed)
    NOW(),
    NOW()
FROM customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
JOIN applicationfees af ON af.employmentlocation_id = cp.employmentlocation_id 
    AND af.registertype_id = cp.registertype_id
    AND af.name = 'NEW'
WHERE (c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%')
  AND NOT EXISTS (
      SELECT 1 FROM invoices i
      WHERE i.source = 'customerprofession'
        AND i.source_id = cp.id
        AND i.description = 'New Application'
  )
LIMIT 1;

-- Step 5: Verify everything was created
SELECT 
    'VERIFICATION' as step,
    i.id as invoice_id,
    i.invoice_number,
    i.description,
    i.amount,
    cur.name as currency,
    i.status,
    ca.id as application_id,
    ca.status as app_status,
    c.name,
    c.surname
FROM invoices i
JOIN customers c ON i.customer_id = c.id
LEFT JOIN currencies cur ON i.currency_id = cur.id
LEFT JOIN customerprofessions cp ON i.source_id = cp.id AND i.source = 'customerprofession'
LEFT JOIN customerapplications ca ON ca.customerprofession_id = cp.id
WHERE (c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%')
  AND i.description = 'New Application';

-- Expected: Should show New Application invoice with PENDING status and application_id
