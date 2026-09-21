-- =============================================
-- QUICK FIX: Tonodzai Chirandu Registration
-- =============================================
-- This script updates Tonodzai's registration status from PENDING to AWAITING_REG
-- so it appears in the Registration Approvals admin page
-- =============================================

-- STEP 1: Check current status (BEFORE FIX)
SELECT 
    '=== BEFORE FIX ===' as info,
    c.id as customer_id,
    c.name,
    c.surname,
    cp.id as profession_id,
    cp.status as current_status,
    cp.year,
    cr.id as registration_id,
    cr.status as registration_status
FROM customers c
LEFT JOIN customerprofessions cp ON c.id = cp.customer_id
LEFT JOIN customerregistrations cr ON cr.customerprofession_id = cp.id
WHERE c.name LIKE '%Tonodzai%' 
   OR c.surname LIKE '%Chirandu%';

-- =============================================
-- STEP 2: Apply the fix
-- =============================================

-- Update customerprofession status to AWAITING_REG
UPDATE customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
SET 
    cp.status = 'AWAITING_REG',
    cp.year = 2026,
    cp.updated_at = NOW()
WHERE (c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%');

-- Create customerregistration if it doesn't exist
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
WHERE (c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%')
  AND NOT EXISTS (
      SELECT 1 FROM customerregistrations cr 
      WHERE cr.customerprofession_id = cp.id
  );

-- Update existing customerregistration if it exists
UPDATE customerregistrations cr
JOIN customerprofessions cp ON cr.customerprofession_id = cp.id
JOIN customers c ON cp.customer_id = c.id
SET 
    cr.status = 'AWAITING',
    cr.year = 2026,
    cr.updated_at = NOW()
WHERE (c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%');

-- =============================================
-- STEP 3: Verify the fix (AFTER FIX)
-- =============================================
SELECT 
    '=== AFTER FIX - SHOULD APPEAR IN APPROVALS ===' as info,
    c.name,
    c.surname,
    cp.id as profession_id,
    cp.status as profession_status,
    cp.year as profession_year,
    cr.status as registration_status,
    cr.year as registration_year,
    CASE 
        WHEN cp.status = 'AWAITING_REG' AND cp.year = 2026 
        THEN '✅ YES - Will appear in Registration Approvals'
        ELSE '❌ NO - Something is wrong'
    END as appears_in_list
FROM customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
LEFT JOIN customerregistrations cr ON cr.customerprofession_id = cp.id
WHERE (c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%');

-- =============================================
-- DONE! 
-- =============================================
-- After running this script:
-- 1. Refresh the Registration Approvals page in admin panel
-- 2. Tonodzai should now appear in the list
-- 3. Practitioner portal should show "Under Review" message
-- =============================================
