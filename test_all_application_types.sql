-- Test query to verify all application types work correctly
-- This simulates what the fixed PHP query does

-- Test 1: New Application (applicationtype_id = 1)
SELECT 
    'Test 1: New Application' as test,
    i.id,
    i.invoice_number,
    i.description,
    i.source,
    i.source_id,
    ca.customerprofession_id,
    'Should match New Application invoices' as expected
FROM invoices i
LEFT JOIN customerapplications ca ON i.source = 'customerapplication' AND i.source_id = ca.id
WHERE i.description LIKE '%New Application%'
  AND (
      (i.source = 'customerprofession' AND i.source_id = 58)  -- Replace 58 with actual profession_id
      OR 
      (i.source = 'customerapplication' AND ca.customerprofession_id = 58)
  );

-- Test 2: Renewal (applicationtype_id = 2 or 3)
SELECT 
    'Test 2: Renewal' as test,
    i.id,
    i.invoice_number,
    i.description,
    i.source,
    i.source_id,
    ca.customerprofession_id,
    ca.applicationtype_id,
    'Should match Renewal invoices only for this profession' as expected
FROM invoices i
LEFT JOIN customerapplications ca ON i.source = 'customerapplication' AND i.source_id = ca.id
WHERE i.description LIKE '%Renewal%'
  AND (
      (i.source = 'customerprofession' AND i.source_id = 58)
      OR 
      (i.source = 'customerapplication' AND ca.customerprofession_id = 58)
  );

-- Test 3: Verify NO cross-contamination
-- This should ONLY return invoices for profession_id = 58, NOT other professions
SELECT 
    'Test 3: Verify isolation' as test,
    i.id,
    i.invoice_number,
    i.description,
    i.source,
    i.source_id,
    ca.customerprofession_id,
    CASE 
        WHEN ca.customerprofession_id = 58 THEN '✅ CORRECT'
        WHEN ca.customerprofession_id IS NULL AND i.source_id = 58 THEN '✅ CORRECT'
        ELSE '❌ WRONG - Should not appear!'
    END as validation
FROM invoices i
LEFT JOIN customerapplications ca ON i.source = 'customerapplication' AND i.source_id = ca.id
WHERE i.description LIKE '%Application%'  -- Matches both New Application and Renewal
  AND (
      (i.source = 'customerprofession' AND i.source_id = 58)
      OR 
      (i.source = 'customerapplication' AND ca.customerprofession_id = 58)
  );

-- Test 4: Check what application types exist
SELECT 
    'Test 4: Available Application Types' as test,
    at.id,
    at.name,
    COUNT(ca.id) as count
FROM applicationtypes at
LEFT JOIN customerapplications ca ON ca.applicationtype_id = at.id
GROUP BY at.id, at.name
ORDER BY at.id;

-- Test 5: Check invoices by application type
SELECT 
    'Test 5: Invoices by Application Type' as test,
    at.name as application_type,
    i.description as invoice_description,
    i.source,
    COUNT(*) as count
FROM invoices i
LEFT JOIN customerapplications ca ON i.source = 'customerapplication' AND i.source_id = ca.id
LEFT JOIN applicationtypes at ON ca.applicationtype_id = at.id
WHERE i.description IN ('New Application', 'Renewal', 'Maintenance')
GROUP BY at.name, i.description, i.source;
