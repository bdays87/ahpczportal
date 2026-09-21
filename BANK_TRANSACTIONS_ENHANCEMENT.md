# Bank Transactions - Pagination, Status Filter & Customer Search

## ✅ Enhancements Added

### 1. Pagination
- **20 transactions per page** (configurable)
- Automatic pagination with MaryUI `with-pagination` attribute
- Resets to page 1 when filters change
- Page numbers at bottom of table

### 2. Status Filtering with Tabs
Three separate tabs for better organization:
- **All** - Shows all transactions (PENDING + CLAIMED)
- **Pending** - Shows only unclaimed transactions
- **Claimed** - Shows only claimed transactions with customer info

### 3. Customer Search
- **Search by customer name, surname, or email**
- Separate search input from transaction search
- Real-time filtering as you type
- Works across all tabs

### 4. Transaction Search
Enhanced search functionality:
- Search by statement reference
- Search by source reference
- Search by description
- Search by account number

### 5. Claim/Unclaim Functionality
**Claim Transaction (PENDING → CLAIMED):**
- Click "Claim" button on pending transaction
- Opens modal with customer selector
- Searchable dropdown with customer names
- Links transaction to selected customer
- Changes status to CLAIMED

**Unclaim Transaction (CLAIMED → PENDING):**
- Click "Unclaim" button on claimed transaction
- Removes customer link
- Changes status back to PENDING
- Requires confirmation

## 🎯 User Flow

### Claiming a Transaction
```
1. Go to "Pending" tab
2. Find transaction to claim
3. Click green "Claim" button
4. Search and select customer
5. Click "Claim" button
6. Transaction moves to "Claimed" tab
```

### Unclaiming a Transaction
```
1. Go to "Claimed" tab
2. Find transaction to unclaim
3. Click yellow "Unclaim" button
4. Confirm action
5. Transaction moves back to "Pending" tab
```

### Searching Transactions
```
1. Use "Search transactions..." input for:
   - Reference numbers
   - Descriptions
   - Account numbers

2. Use "Search customer..." input for:
   - Customer name
   - Customer surname
   - Customer email
```

## 📊 UI Layout

```
┌─────────────────────────────────────────────────────────┐
│  Bank Transactions                                      │
├─────────────────────────────────────────────────────────┤
│  [🔍 Search transactions] [👤 Search customer] [+New] [Import] │
│                                                         │
│  [ All ] [ Pending ] [ Claimed ]                       │
│  ─────────────────────────────────                     │
│                                                         │
│  ┌─────────────────────────────────────────────────┐   │
│  │ Date │ Ref │ Description │ Bank │ Amt │ Cust │ │   │
│  ├──────┼─────┼─────────────┼──────┼─────┼──────┤ │   │
│  │ 14 Jan│1260 │ LAB PARTNERS│ NMB  │ 214 │ John │ │   │
│  │ 19 Jan│2057 │ LAB PARTNERS│ NMB  │ 165 │ Jane │ │   │
│  │  ...  │ ... │     ...     │ ...  │ ... │ ...  │ │   │
│  └─────────────────────────────────────────────────┘   │
│                                                         │
│  ← Prev  1 2 3 4 5  Next →                            │
└─────────────────────────────────────────────────────────┘
```

## 🎨 Visual Indicators

### Status Badges
- **PENDING** - Yellow/Warning badge
- **CLAIMED** - Green/Success badge

### Action Buttons
- **✓ Claim** (Green) - Claim pending transaction
- **✗ Unclaim** (Yellow) - Unclaim transaction
- **✏️ Edit** (Blue) - Edit pending transaction
- **🗑️ Delete** (Red) - Delete pending transaction

## 💡 Features by Tab

### All Tab
- Shows all transactions regardless of status
- Full action buttons based on status
- Both claimed and unclaimed visible

### Pending Tab
- Only unclaimed transactions
- Shows "Claim", "Edit", "Delete" buttons
- Customer column shows "Not claimed"
- Focus on transactions needing attention

### Claimed Tab
- Only claimed transactions
- Shows "Unclaim" button only
- Customer names visible
- Focus on processed transactions

## 🔧 Technical Details

### Database Queries
```php
// Filtered by status
when($this->statusFilter !== 'all', function($q) {
    $q->where('status', $this->statusFilter);
})

// Search transactions
when($this->search, function($q) {
    $q->where('statement_reference', 'like', '%'.$this->search.'%')
      ->orWhere('description', 'like', '%'.$this->search.'%')
      // ... more fields
})

// Search by customer
when($this->customerSearch, function($q) {
    $q->whereHas('customer', function($query) {
        $query->where('name', 'like', '%'.$this->customerSearch.'%')
              ->orWhere('surname', 'like', '%'.$this->customerSearch.'%')
              ->orWhere('email', 'like', '%'.$this->customerSearch.'%');
    });
})

// Pagination
->paginate(20);
```

### Livewire Properties
```php
public $search = '';           // Transaction search
public $customerSearch = '';   // Customer search
public $statusFilter = 'all';  // Status tab filter
public $customer_id;           // Selected customer for claiming
public $claimModal = false;    // Claim modal visibility
```

### New Methods
```php
claimTransaction($id)      // Open claim modal
saveClaimCustomer()        // Save customer claim
unclaimTransaction($id)    // Unclaim transaction
getCustomerList()          // Get customers for dropdown
updatingSearch()           // Reset page on search
updatingStatusFilter()     // Reset page on tab change
updatingCustomerSearch()   // Reset page on customer search
```

## 📈 Performance

### Optimizations
1. **Eager Loading** - Loads bank, currency, customer relationships
2. **Pagination** - Only loads 20 records at a time
3. **Customer Limit** - Limits customer dropdown to 50 results
4. **Indexed Queries** - Uses database indexes for fast filtering
5. **Search Debouncing** - Livewire's `wire:model.live` debounces input

## 🎓 Usage Tips

### For Users
1. **Use tabs** to focus on what matters
2. **Search transactions** by reference or description
3. **Search customers** when claiming transactions
4. **Claim in bulk** - Stay in Pending tab, claim multiple
5. **Review claimed** - Switch to Claimed tab to verify

### For Admins
1. **Monitor Pending** tab for unclaimed transactions
2. **Check Claimed** tab for reconciliation
3. **Use customer search** to find specific customer's transactions
4. **Export data** (future feature) for reporting

## 🚀 Future Enhancements

### Potential Additions
1. **Bulk claiming** - Select multiple transactions, claim to one customer
2. **Auto-matching** - Automatically match transactions to customers by amount/date
3. **Export to Excel** - Export filtered transactions
4. **Date range filter** - Filter by transaction date range
5. **Amount filter** - Filter by amount range
6. **Bank filter** - Filter by specific bank
7. **Quick claim** - Type customer name directly on row without modal
8. **Transaction notes** - Add notes to transactions
9. **Audit trail** - Track who claimed/unclaimed transactions
10. **Email notifications** - Notify customers when transaction is claimed

## 📝 Testing Checklist

- [ ] Pagination works (20 per page)
- [ ] "All" tab shows all transactions
- [ ] "Pending" tab shows only PENDING
- [ ] "Claimed" tab shows only CLAIMED
- [ ] Transaction search filters correctly
- [ ] Customer search filters correctly
- [ ] Claim modal opens correctly
- [ ] Customer dropdown is searchable
- [ ] Claim transaction updates status
- [ ] Unclaim transaction resets status
- [ ] Page resets when changing filters
- [ ] Edit button works on pending
- [ ] Delete button works on pending
- [ ] Edit/Delete disabled on claimed
- [ ] Customer names show on claimed
- [ ] Status badges show correct colors

## 🐛 Troubleshooting

### "No transactions found"
- Check if transactions exist in database
- Try "All" tab to see all transactions
- Clear search filters
- Check pagination (might be on wrong page)

### Customer dropdown empty
- Ensure customers exist in database
- Check customerSearch is working
- Verify Customer model exists
- Check database relationship

### Claim button not working
- Check permissions (banktransactions.modify)
- Verify customer_id exists in banktransactions table
- Check validation rules
- Look at browser console for errors

### Pagination not showing
- Need more than 20 transactions
- Check `with-pagination` attribute on table
- Verify WithPagination trait is used

---

**Status:** ✅ **COMPLETE**
**Date:** September 2, 2026
**Features:** Pagination, Status Tabs, Customer Search, Claim/Unclaim
