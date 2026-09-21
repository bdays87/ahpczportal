-- Check Tonodzai Chirandu's registration status

-- 1. Find the customer
SELECT 
    c.id as customer_id,
    c.name,
    c.surname,
    c.email
FROM customers c
WHERE c.name LIKE '%Tonodzai%' 
   OR c.surname LIKE '%Chirandu%';

-- 2. Find customer professions for Tonodzai
SELECT 
    cp.id,
    cp.uuid,
    cp.customer_id,
    cp.profession_id,
    cp.status,
    cp.year,
    cp.created_at,
    cp.updated_at,
    c.name,
    c.surname,
    p.name as profession_name
FROM customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
LEFT JOIN professions p ON cp.profession_id = p.id
WHERE c.name LIKE '%Tonodzai%' 
   OR c.surname LIKE '%Chirandu%'
ORDER BY cp.created_at DESC;

-- 3. Check customerregistration records for Tonodzai
SELECT 
    cr.id,
    cr.customerprofession_id,
    cr.customer_id,
    cr.status as registration_status,
    cr.year as registration_year,
    cr.created_at,
    cr.updated_at,
    cp.status as profession_status,
    cp.year as profession_year,
    c.name,
    c.surname
FROM customerregistrations cr
JOIN customerprofessions cp ON cr.customerprofession_id = cp.id
JOIN customers c ON cr.customer_id = c.id
WHERE c.name LIKE '%Tonodzai%' 
   OR c.surname LIKE '%Chirandu%'
ORDER BY cr.created_at DESC;

-- 4. Check invoices for Registration
SELECT 
    i.id,
    i.invoice_number,
    i.description,
    i.status as invoice_status,
    i.source,
    i.source_id,
    i.created_at,
    cp.status as profession_status,
    c.name,
    c.surname
FROM invoices i
JOIN customerprofessions cp ON i.source_id = cp.id AND i.source = 'customerprofession'
JOIN customers c ON i.customer_id = c.id
WHERE i.description = 'Registration'
  AND (c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%')
ORDER BY i.created_at DESC;

-- 5. Check what should appear in registration approvals (status = AWAITING_REG, current year)
SELECT 
    cp.id,
    cp.uuid,
    cp.status,
    cp.year,
    c.name,
    c.surname,
    p.name as profession_name,
    cr.status as registration_status,
    cp.updated_at
FROM customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
LEFT JOIN professions p ON cp.profession_id = p.id
LEFT JOIN customerregistrations cr ON cr.customerprofession_id = cp.id
WHERE (c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%')
  AND cp.status = 'AWAITING_REG'
  AND cp.year = YEAR(CURDATE());

-- 6. Check all AWAITING_REG records (to see if query is working)
SELECT 
    cp.id,
    cp.uuid,
    cp.status,
    cp.year,
    c.name,
    c.surname,
    p.name as profession_name,
    cr.status as registration_status,
    cp.updated_at
FROM customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
LEFT JOIN professions p ON cp.profession_id = p.id
LEFT JOIN customerregistrations cr ON cr.customerprofession_id = cp.id
WHERE cp.status = 'AWAITING_REG'
  AND cp.year = YEAR(CURDATE())
ORDER BY cp.updated_at DESC
LIMIT 10;
