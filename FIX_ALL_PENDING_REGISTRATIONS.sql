-- =============================================
-- FIX ALL PENDING REGISTRATIONS
-- =============================================
-- This script fixes registrations that have invoices but status is still PENDING
-- It updates them to AWAITING_REG so they appear in Registration Approvals
-- =============================================

-- STEP 1: Check current state
SELECT 
    'BEFORE FIX - Current State' as step,
    c.id as customer_id,
    c.name,
    c.surname,
    cp.id as profession_id,
    cp.status as profession_status,
    cp.year,
    i.invoice_number,
    i.status as invoice_status,
    cr.id as registration_id,
    cr.status as registration_status
FROM customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
LEFT JOIN invoices i ON i.source_id = cp.id 
    AND i.source = 'customerprofession' 
    AND i.description = 'Registration'
LEFT JOIN customerregistrations cr ON cr.customerprofession_id = cp.id
WHERE cp.status = 'PENDING'
  AND EXISTS (
      SELECT 1 FROM invoices 
      WHERE source_id = cp.id 
      AND source = 'customerprofession' 
      AND description = 'Registration'
  )
ORDER BY cp.created_at DESC;

-- =============================================
-- STEP 2: Fix customerprofession status
-- =============================================
-- Update all professions that have Registration invoice but status is still PENDING
UPDATE customerprofessions cp
SET 
    cp.status = 'AWAITING_REG',
    cp.year = YEAR(CURDATE()),
    cp.updated_at = NOW()
WHERE cp.status = 'PENDING'
  AND EXISTS (
      SELECT 1 FROM invoices 
      WHERE source_id = cp.id 
      AND source = 'customerprofession' 
      AND description = 'Registration'
  );

-- =============================================
-- STEP 3: Create missing customerregistration records
-- =============================================
-- For professions that have Registration invoice but NO customerregistration record
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
    YEAR(CURDATE()),
    'AWAITING',
    NOW(),
    NOW()
FROM customerprofessions cp
WHERE cp.status = 'AWAITING_REG'
  AND NOT EXISTS (
      SELECT 1 FROM customerregistrations cr 
      WHERE cr.customerprofession_id = cp.id
  )
  AND EXISTS (
      SELECT 1 FROM invoices 
      WHERE source_id = cp.id 
      AND source = 'customerprofession' 
      AND description = 'Registration'
  );

-- =============================================
-- STEP 4: Update existing customerregistration records
-- =============================================
-- Ensure existing registrations have correct status
UPDATE customerregistrations cr
JOIN customerprofessions cp ON cr.customerprofession_id = cp.id
SET 
    cr.status = 'AWAITING',
    cr.year = YEAR(CURDATE()),
    cr.updated_at = NOW()
WHERE cp.status = 'AWAITING_REG'
  AND cr.status != 'AWAITING';

-- =============================================
-- STEP 5: Verify the fix
-- =============================================
SELECT 
    'AFTER FIX - Should appear in Registration Approvals' as step,
    c.id as customer_id,
    c.name,
    c.surname,
    cp.id as profession_id,
    cp.status as profession_status,
    cp.year,
    i.invoice_number,
    i.status as invoice_status,
    cr.id as registration_id,
    cr.status as registration_status,
    'YES - Will show in approvals!' as appears_in_list
FROM customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
LEFT JOIN invoices i ON i.source_id = cp.id 
    AND i.source = 'customerprofession' 
    AND i.description = 'Registration'
LEFT JOIN customerregistrations cr ON cr.customerprofession_id = cp.id
WHERE cp.status = 'AWAITING_REG'
  AND cp.year = YEAR(CURDATE())
ORDER BY cp.updated_at DESC;

-- =============================================
-- SUMMARY STATS
-- =============================================
SELECT 
    'Summary' as info,
    COUNT(*) as total_awaiting_reg,
    COUNT(DISTINCT cp.customer_id) as unique_customers
FROM customerprofessions cp
WHERE cp.status = 'AWAITING_REG'
  AND cp.year = YEAR(CURDATE());
