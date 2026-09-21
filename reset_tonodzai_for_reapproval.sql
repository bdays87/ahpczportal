-- Reset Tonodzai's registration to AWAITING_REG so admin can re-approve
-- This will trigger the (now fixed) code to create the application invoice

-- Step 1: Check current status
SELECT 
    'BEFORE RESET' as step,
    cp.id,
    cp.status as profession_status,
    cr.status as registration_status,
    c.name,
    c.surname
FROM customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
LEFT JOIN customerregistrations cr ON cr.customerprofession_id = cp.id
WHERE c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%';

-- Step 2: Reset customerprofession status back to AWAITING_REG
UPDATE customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
SET 
    cp.status = 'AWAITING_REG',
    cp.updated_at = NOW()
WHERE c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%';

-- Step 3: Reset customerregistration status back to AWAITING
UPDATE customerregistrations cr
JOIN customerprofessions cp ON cr.customerprofession_id = cp.id
JOIN customers c ON cp.customer_id = c.id
SET 
    cr.status = 'AWAITING',
    cr.registrationdate = NULL,
    cr.certificatenumber = NULL,
    cr.updated_at = NOW()
WHERE c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%';

-- Step 4: Delete any existing application invoice (if partially created)
DELETE i FROM invoices i
JOIN customers c ON i.customer_id = c.id
WHERE (c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%')
  AND i.description = 'New Application';

-- Step 5: Delete any existing application record (if partially created)
DELETE ca FROM customerapplications ca
JOIN customerprofessions cp ON ca.customerprofession_id = cp.id
JOIN customers c ON cp.customer_id = c.id
WHERE c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%';

-- Step 6: Verify reset
SELECT 
    'AFTER RESET - Ready for re-approval' as step,
    cp.id,
    cp.status as profession_status,
    cr.status as registration_status,
    c.name,
    c.surname,
    'Admin should now re-approve this registration' as action_needed
FROM customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
LEFT JOIN customerregistrations cr ON cr.customerprofession_id = cp.id
WHERE c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%';

-- Expected: 
-- profession_status: AWAITING_REG
-- registration_status: AWAITING

-- Next Step: Admin goes to Registration Approvals and re-approves
-- The fixed code will now create the application invoice correctly
