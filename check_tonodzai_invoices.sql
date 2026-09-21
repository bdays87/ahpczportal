-- Check all invoices for Tonodzai to see what exists

SELECT 
    i.id,
    i.invoice_number,
    i.description,
    i.status,
    i.source,
    i.source_id,
    i.amount,
    c.name as currency,
    i.created_at,
    cust.name,
    cust.surname
FROM invoices i
JOIN customers cust ON i.customer_id = cust.id
LEFT JOIN currencies c ON i.currency_id = c.id
WHERE cust.name LIKE '%Tonodzai%' OR cust.surname LIKE '%Chirandu%'
ORDER BY i.created_at DESC;

-- Check customerprofession ID for Tonodzai
SELECT 
    cp.id as profession_id,
    cp.status,
    cp.registertype_id,
    c.name,
    c.surname
FROM customerprofessions cp
JOIN customers c ON cp.customer_id = c.id
WHERE c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%';

-- Check if 'New Application' invoice exists specifically
SELECT 
    i.id,
    i.invoice_number,
    i.description,
    i.source,
    i.source_id,
    i.status,
    cp.id as profession_id,
    'MATCH!' as note
FROM invoices i
JOIN customers cust ON i.customer_id = cust.id
JOIN customerprofessions cp ON cust.id = cp.customer_id
WHERE (cust.name LIKE '%Tonodzai%' OR cust.surname LIKE '%Chirandu%')
  AND i.description LIKE '%New Application%'
  AND ((i.source = 'customerprofession' AND i.source_id = cp.id)
       OR i.source = 'customerapplication');

-- Check customerapplications for Tonodzai
SELECT 
    ca.id,
    ca.customerprofession_id,
    ca.status,
    ca.year,
    cp.id as profession_id
FROM customerapplications ca
JOIN customerprofessions cp ON ca.customerprofession_id = cp.id
JOIN customers c ON cp.customer_id = c.id
WHERE c.name LIKE '%Tonodzai%' OR c.surname LIKE '%Chirandu%';
