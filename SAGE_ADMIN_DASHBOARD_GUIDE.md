# Sage Admin Dashboard - Quick Reference Guide

## 📍 Access Dashboard

**URL:** `https://portal.mlcscz.org.zw/sage-sync-status`

**Requirements:** Admin login

---

## 🎯 Dashboard Overview

### Stats Cards (Top)

```
┌─────────────────────┐  ┌─────────────────────┐  ┌─────────────────────┐
│  Customers Sync     │  │  Invoices Sync      │  │  Sage Statements    │
│  ─────────────────  │  │  ─────────────────  │  │  ─────────────────  │
│  Pending: 15        │  │  Pending: 8         │  │  Total Balance:     │
│  Synced:  245       │  │  Synced:  1,203     │  │  USD 15,430.50      │
│  Failed:  3         │  │  Failed:  12        │  │  With Balance: 45   │
└─────────────────────┘  └─────────────────────┘  └─────────────────────┘
```

---

## 🔘 Bulk Action Buttons

### Customers Tab:

| Button | Action | When to Use |
|--------|--------|-------------|
| **Push All Approved** | Queues ALL approved customers with approved applications | Initial setup or mass sync |
| **Push Selected (X)** | Queues only checked customers | Selective sync |
| **Retry All Failed** | Resets all failed customer syncs | After fixing Sage issues |

### Invoices Tab:

| Button | Action | When to Use |
|--------|--------|-------------|
| **Push All Paid** | Queues ALL paid invoices (for synced customers) | Initial setup or mass sync |
| **Push Selected (X)** | Queues only checked invoices | Selective sync |
| **Retry All Failed** | Resets all failed invoice syncs | After fixing Sage issues |

---

## 📋 How to Use

### Scenario 1: Bulk Sync All Approved Customers

```
1. Login as admin
2. Go to /sage-sync-status
3. Click "Customers" tab
4. Click "Push All Approved" button
5. Confirm the action
6. Wait 5-10 minutes for SageConnector to process
7. Refresh page to see updated status
```

**Result:** All approved customers with approved applications queued for sync.

---

### Scenario 2: Push Selected Customers

```
1. Go to /sage-sync-status
2. Click "Customers" tab
3. Check boxes next to customers you want to sync
4. Click "Push Selected (X)" button (X = number selected)
5. Confirm the action
6. Wait for next connector poll (~5 minutes)
```

**Result:** Only selected customers queued for sync.

---

### Scenario 3: Push All Paid Invoices

```
1. Go to /sage-sync-status
2. Click "Invoices" tab
3. Click "Push All Paid" button
4. Confirm the action
5. Wait for connector to process
```

**Result:** All paid invoices (for customers already in Sage) queued for sync.

---

### Scenario 4: Retry Failed Syncs

```
1. Go to /sage-sync-status
2. Select filter: "Failed" (top right dropdown)
3. Click "Retry All Failed" button
4. OR check individual failed items
5. OR click "Retry" button on individual row
6. Wait for connector to retry
```

**Result:** Failed records reset to PENDING and will be retried.

---

### Scenario 5: Push Individual Customer/Invoice

```
1. Go to /sage-sync-status
2. Find the customer/invoice
3. Click "Push" button on that row
4. Wait for connector poll
```

**Result:** Single record queued for sync.

---

## 🎨 Status Badges

| Badge | Color | Meaning |
|-------|-------|---------|
| **PENDING** | Yellow | Queued, waiting for connector |
| **SYNCING** | Yellow | Currently being processed |
| **SYNCED** | Green | Successfully synced to Sage |
| **FAILED** | Red | Sync failed (click "Error" to see why) |

---

## 🔍 Filters and Search

### Filter Dropdown (Top Right):
- **All Status** - Show everything
- **Pending** - Show only queued items
- **Synced** - Show only successful syncs
- **Failed** - Show only failures (reveals "Retry All Failed" button)

### Search Box:
- **Customers:** Search by name, surname, or email
- **Invoices:** Search by invoice number, sage invoice number, or customer name

---

## ⏱️ Timeline

### What Happens After You Click "Push":

```
[You Click]
    ↓
[Status: PENDING]
    ↓
[Wait 1-5 minutes] ← SageConnector polls every 5 minutes
    ↓
[Status: SYNCING]
    ↓
[Connector creates in Sage]
    ↓
[Status: SYNCED] ✅ Success!
    OR
[Status: FAILED] ❌ Error (click "Error" button to see details)
```

**Typical Sync Time:** 
- Single customer: < 10 seconds
- Single invoice: < 10 seconds
- 100 customers: 5-10 minutes
- 500 invoices: 10-20 minutes

---

## 🚨 Troubleshooting

### Issue: "Push All" button clicked but nothing happens

**Check:**
1. ✅ Do items meet criteria?
   - Customers: APPROVED registration + APPROVED application
   - Invoices: PAID status + customer already synced
2. ✅ Is SageConnector running on Windows PC?
3. ✅ Check logs: `/storage/logs/laravel.log`

**Fix:** Verify filters are correct, check connector logs

---

### Issue: Status stuck on PENDING

**Cause:** SageConnector not polling or not running

**Check:**
1. ✅ Windows PC running?
2. ✅ SageConnector service status:
   ```powershell
   sc query SageConnector
   ```
3. ✅ Connector logs:
   ```powershell
   Get-Content C:\inetpub\sageconnector\logs\*.log -Tail 50
   ```

**Fix:** Restart SageConnector service

---

### Issue: Many items showing FAILED

**Cause:** Sage connection issue or invalid data

**Check:**
1. ✅ Click "Error" button to see specific error
2. ✅ Common errors:
   - Customer code already exists in Sage
   - Missing required fields
   - Sage Evolution not running
   - Database connection lost

**Fix:** 
1. Fix root cause (Sage connection, data validation)
2. Click "Retry All Failed"
3. Wait for next poll

---

### Issue: Customer code clash in Sage

**Cause:** Duplicate certificate number or registration ID

**Resolution:** 
- System now uses certificate number or PREFIX-REGID format
- NEVER uses raw customer_id
- Should not clash unless duplicate registrations exist

**Fix:** 
1. Check for duplicate certificate numbers in database
2. Ensure registration IDs are unique
3. Retry sync

---

## 📊 Understanding the Tables

### Customers Table Columns:

| Column | Description |
|--------|-------------|
| ☑️ Checkbox | Select for bulk push |
| Customer | Full name from portal |
| Email | Contact email |
| Profession | Practitioner type |
| **Sage Code** | Customer code in Sage (certificate number or PREFIX-REGID) |
| Status | Sync status badge |
| Synced At | When last synced |
| Actions | Push/Retry/Error buttons |

### Invoices Table Columns:

| Column | Description |
|--------|-------------|
| ☑️ Checkbox | Select for bulk push |
| Invoice # | Portal invoice number |
| Customer | Customer name |
| Amount | Invoice total |
| **Sage Invoice #** | Invoice number in Sage Evolution |
| Outstanding | Remaining balance from Sage |
| Status | Sync status badge |
| Synced At | When last synced |
| Actions | Push/Retry/Error buttons |

---

## 🎓 Best Practices

### ✅ DO:

1. **Test with one customer first**
   - Check one box, push selected
   - Verify it appears correctly in Sage
   - Then proceed with bulk

2. **Use filters**
   - Filter by "Pending" to see what's queued
   - Filter by "Failed" to troubleshoot issues
   - Filter by "Synced" to verify success

3. **Check Sage after bulk sync**
   - Open Sage Evolution
   - Verify customers created correctly
   - Verify customer codes match

4. **Monitor regularly**
   - Check dashboard daily
   - Address failed syncs promptly
   - Keep connector running 24/7

### ❌ DON'T:

1. **Don't spam "Push All"**
   - Clicking multiple times creates duplicates
   - Wait for previous sync to complete
   - Check status before re-pushing

2. **Don't ignore failed syncs**
   - Failed syncs indicate data issues
   - Click "Error" to see why
   - Fix root cause before retry

3. **Don't manually edit Sage customer codes**
   - System manages codes automatically
   - Manual changes cause mismatches
   - Let integration handle it

---

## 📱 Quick Actions Reference

| I Want To... | Steps |
|--------------|-------|
| Sync all approved customers | Customers tab → "Push All Approved" |
| Sync specific customers | Customers tab → Check boxes → "Push Selected" |
| Sync all paid invoices | Invoices tab → "Push All Paid" |
| Sync specific invoices | Invoices tab → Check boxes → "Push Selected" |
| Retry a failed sync | Click "Retry" button on failed row |
| Retry all failures | Filter: Failed → "Retry All Failed" |
| See why sync failed | Click "Error" button on failed row |
| Find a customer | Use search box (name/email) |
| See what's queued | Filter: Pending |
| See what's synced | Filter: Synced |

---

## 🔗 Related Links

- **SageConnector Logs:** `C:\inetpub\sageconnector\logs\`
- **Laravel Logs:** `/var/www/portal.mlcscz.org.zw/storage/logs/laravel.log`
- **Full Deployment Guide:** `DEPLOYMENT_GUIDE_PRODUCTION.md`
- **Technical Summary:** `SAGE_FINAL_IMPLEMENTATION_SUMMARY.md`

---

## 📞 Need Help?

### Check Logs First:
```bash
# Ubuntu
tail -f /var/www/portal.mlcscz.org.zw/storage/logs/laravel.log

# Windows
Get-Content C:\inetpub\sageconnector\logs\connector-*.log -Tail 50
```

### Common Log Messages:

✅ **Success:**
```
[INFO] Customer created successfully: CERT-2024-001
[INFO] Invoice created successfully: INV-2024-001
```

❌ **Errors:**
```
[ERROR] Customer code already exists in Sage
[ERROR] Cannot connect to Sage Evolution
[ERROR] Missing required field: email
```

---

**Dashboard Ready to Use! 🎉**

Access at: `https://portal.mlcscz.org.zw/sage-sync-status`
