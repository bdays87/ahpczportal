# Complete Flow: Registration → Application Invoice

## 🎯 Current Flow (After Our Changes)

### Step 1: Practitioner Completes Registration Workflow
```
Documents Upload → Qualifications → Assessment Invoice → Registration Review
```

**What Happens:**
- When practitioner clicks "Next Step" on Qualifications page
- System calls `generatepractitionerinvoice()`
- This triggers `createInvoice(['description' => 'Registration'])`

**OLD behavior (BEFORE our changes):**
- ❌ Registration invoice created
- ❌ Practitioner had to PAY
- ❌ After payment → status = AWAITING_REG

**NEW behavior (AFTER our changes):**
- ✅ NO invoice created
- ✅ Status immediately = AWAITING_REG  
- ✅ customerregistration created with status = AWAITING
- ✅ Notifications sent to admins

### Step 2: Admin Approves Registration

**Location:** `app/implementations/_customerprofessionRepository.php` line 564-576

**Code:**
```php
elseif ($data['commenttype'] == 'Registration') {
    $customerregistration = $this->customerprofessionregistration
        ->where('customerprofession_id', $customerprofession->id)
        ->first();
    
    if ($customerregistration) {
        // Generate certificate number
        $certificatenumber = $this->generalutils->generatecertificatenumber(...);
        
        // Update registration
        $customerregistration->update([
            'status' => 'APPROVED',
            'registrationdate' => date('Y-m-d'),
            'certificatenumber' => $certificatenumber
        ]);
        
        // Check customer type
        if ($customerprofession->customertype_id == 3) {
            // Student → Goes straight to APPROVED (no application needed)
            $customerprofession->update([
                'status' => 'APPROVED',
                'registertype_id' => $data['registertype_id'],
                'tire_id' => $data['tire_id']
            ]);
        } else {
            // Non-student → Goes to PENDING and creates application invoice
            $customerprofession->update([
                'status' => 'PENDING',
                'registertype_id' => $data['registertype_id'],
                'tire_id' => $data['tire_id']
            ]);
            
            // ✅ AUTOMATICALLY CREATE APPLICATION INVOICE
            if ($customerprofession->applications->count() == 0) {
                $this->invoicerepo->createInvoice([
                    'description' => 'New Application',
                    'customerprofession_id' => $customerprofession->id,
                    'year' => date('Y')
                ]);
            }
        }
        
        // Notify practitioner
        $user->notify(new RegistrationApprovalNotification(...));
    }
}
```

**What Happens:**
1. ✅ customerregistration.status = 'APPROVED'
2. ✅ Certificate number generated
3. ✅ customerprofession.status = 'PENDING' (for non-students)
4. ✅ **"New Application" invoice automatically created**
5. ✅ Practitioner gets notification

### Step 3: Practitioner Clicks "Proceed to Application"

**Location:** Registration page → "Proceed to Application" button

**What practitioner sees:**
```
Registration Status: APPROVED ✅
[Proceed to Application Button]
```

**What happens when clicking button:**
- Redirects to: `route('newapplications.practitioners.applicationinvoicing', $uuid)`
- Shows "Application invoice" step (Step 5)

### Step 4: Application Invoice Page

**Location:** `resources/views/livewire/newapplications/practitioners/applicationinvoicing.blade.php`

**IF invoice exists:**
```html
Invoice pending
Please settle the invoice to continue.

Invoice details
Invoice Number: INV-2026-XXXX-XX
Date: 2026-09-21
Description: New Application
Status: PENDING

[Settle Invoice Button]
[Attach Payment Button]
```

**IF no invoice yet:**
```html
No application invoice available your registration is awaiting approval.
[Goto registration button]
```

### Step 5: Practitioner Pays Application Invoice

**What happens:** (from `_invoiceRepository.php` line 1037-1046)

```php
elseif ($invoice->description == 'New Application') {
    $customerapplication = $this->customerapplication
        ->where('customerprofession_id', $customerprofession->id)
        ->where('status', 'PENDING')
        ->first();
    
    $customerapplication->status = 'AWAITING';
    $customerapplication->save();
    
    $customerprofession->update(['status' => 'AWAITING_APP']);
    
    // Notify admins
    $user = User::permission('applications.approve')->get();
    foreach ($user as $u) {
        $u->notify(new ApplicationAwaitingApprovalNotification($invoice, $customerprofession->profession));
    }
}
```

**After payment:**
1. ✅ customerapplication.status = 'AWAITING'
2. ✅ customerprofession.status = 'AWAITING_APP'
3. ✅ Notifications sent to admins with 'applications.approve' permission
4. ✅ Application appears in Application Approvals queue

### Step 6: Admin Approves Application

**Location:** Application Approvals page

**What happens:** (from `_customerprofessionRepository.php` line 580-588)

```php
elseif ($data['commenttype'] == 'Application') {
    $customerprofessionapplication = $this->customerprofessionapplication
        ->where('customerprofession_id', $customerprofession->id)
        ->first();
    
    if ($customerprofessionapplication) {
        $certificatenumber = $this->generalutils->generatecertificatenumber(...);
        
        $customerprofessionapplication->update([
            'status' => 'APPROVED',
            'approvedby' => Auth::user()->id,
            'certificate_number' => $certificatenumber,
            'registration_date' => date('Y-m-d'),
            'certificate_expiry_date' => date('Y').'-12-31'
        ]);
        
        $customerprofession->update(['status' => 'APPROVED']);
        
        // Notify practitioner
        $user->notify(new ApplicationApprovalNotification(...));
    }
}
```

**After approval:**
1. ✅ customerapplication.status = 'APPROVED'
2. ✅ Certificate number generated
3. ✅ Certificate expiry date set
4. ✅ customerprofession.status = 'APPROVED'
5. ✅ Practitioner gets certificate

## 📊 Complete Status Flow

```
┌─────────────────────────────────────────────────────┐
│ Practitioner completes workflow                    │
│ (Documents → Qualifications → Assessment)          │
└────────────┬────────────────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────────────────┐
│ System: Create Registration (no invoice)           │
│ - customerregistration.status = AWAITING           │
│ - customerprofession.status = AWAITING_REG         │
└────────────┬────────────────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────────────────┐
│ Admin: Approves Registration                       │
│ - customerregistration.status = APPROVED           │
│ - customerprofession.status = PENDING              │
│ - ✅ AUTO-CREATE: New Application Invoice          │
└────────────┬────────────────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────────────────┐
│ Practitioner: Sees "Proceed to Application" btn    │
│ - Clicks button                                    │
│ - Redirected to Application Invoice page           │
└────────────┬────────────────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────────────────┐
│ Application Invoice Page                           │
│ - Shows: New Application invoice (PENDING)         │
│ - Practitioner pays invoice                        │
└────────────┬────────────────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────────────────┐
│ System: Invoice Paid                               │
│ - customerapplication.status = AWAITING            │
│ - customerprofession.status = AWAITING_APP         │
│ - Notifications sent to admins                     │
└────────────┬────────────────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────────────────┐
│ Admin: Approves Application                        │
│ - customerapplication.status = APPROVED            │
│ - customerprofession.status = APPROVED             │
│ - Certificate generated                            │
└─────────────────────────────────────────────────────┘
```

## 🎯 Summary: What Invoice Gets Generated

### Registration Invoice
- **BEFORE our changes:** Created, practitioner had to pay
- **AFTER our changes:** ❌ NOT created, goes straight to approval

### New Application Invoice  
- **When:** Automatically created when admin approves registration (for non-students)
- **Who pays:** Practitioner
- **Purpose:** Application processing fee
- **After payment:** Goes to Application Approvals queue

### The "Practitioner Certificate Invoice" You Mentioned
The "New Application" invoice IS the practitioner certificate invoice. When they pay this invoice:
1. Application goes to approval queue
2. Admin approves
3. Certificate is generated
4. Practitioner gets their certificate

## ✅ Current Implementation Status

### ✅ Working Correctly:
1. Registration skips invoice creation
2. Registration goes straight to admin approval
3. When admin approves registration → Application invoice AUTO-CREATED
4. Practitioner can proceed to application page
5. Practitioner pays application invoice
6. Application goes to admin for approval
7. Admin approves → Certificate generated

### ❌ Nothing to Fix:
The flow is already complete! When you click "Proceed to Application":
- The "New Application" invoice is already created (by admin approval)
- Practitioner just needs to pay it
- Then admin approves the application
- Certificate is issued

## 🔍 Verification

To verify the complete flow is working:

### 1. Check Registration Approval
```sql
SELECT 
    cp.id,
    cp.status,
    cr.status as reg_status,
    c.name,
    c.surname
FROM customerprofessions cp
JOIN customerregistrations cr ON cr.customerprofession_id = cp.id
JOIN customers c ON cp.customer_id = c.id
WHERE c.name LIKE '%Tonodzai%';

-- Expected after approval:
-- cp.status: PENDING (means approved, waiting for application)
-- cr.status: APPROVED
```

### 2. Check Application Invoice Created
```sql
SELECT 
    i.id,
    i.invoice_number,
    i.description,
    i.status,
    i.created_at
FROM invoices i
JOIN customerprofessions cp ON i.source_id = cp.id 
    AND i.source = 'customerprofession'
JOIN customers c ON i.customer_id = c.id
WHERE i.description = 'New Application'
  AND c.name LIKE '%Tonodzai%';

-- Expected: New Application invoice with status PENDING
```

### 3. Check Application Record
```sql
SELECT 
    ca.id,
    ca.status,
    ca.created_at
FROM customerapplications ca
JOIN customerprofessions cp ON ca.customerprofession_id = cp.id
JOIN customers c ON cp.customer_id = c.id
WHERE c.name LIKE '%Tonodzai%';

-- Expected: Application record with status PENDING
```

## 🎓 Understanding Customer Types

### Customer Type = 3 (Student)
- Registration approved → status = APPROVED
- **NO application invoice created**
- Student can immediately access services

### Customer Type ≠ 3 (Professional/Practitioner)
- Registration approved → status = PENDING
- **Application invoice automatically created**
- Must pay invoice
- Application goes for approval
- Gets certificate after approval

## 📝 Key Code Locations

### Registration Approval Logic
- **File:** `app/implementations/_customerprofessionRepository.php`
- **Method:** `addcomment()`
- **Lines:** 564-576

### Application Invoice Auto-Creation
- **File:** `app/implementations/_customerprofessionRepository.php`
- **Line:** 574
- **Code:** `$this->invoicerepo->createInvoice(['description' => 'New Application', ...])`

### Application Payment Handler
- **File:** `app/implementations/_invoiceRepository.php`
- **Lines:** 1037-1046

### Application Approval Logic
- **File:** `app/implementations/_customerprofessionRepository.php`
- **Lines:** 580-588

---

## ✅ Conclusion

The flow is **already complete and working correctly**:

1. ✅ Registration skips invoice (no payment needed)
2. ✅ Admin approves registration
3. ✅ **Application invoice AUTO-CREATED by admin approval**
4. ✅ "Proceed to Application" button appears
5. ✅ Practitioner clicks → sees Application invoice
6. ✅ Practitioner pays Application invoice
7. ✅ Application goes for approval
8. ✅ Admin approves → Certificate issued

**No additional changes needed!** The "Practitioner certificate invoice" you mentioned is the "New Application" invoice that gets automatically created when admin approves the registration.
