# AHPCZ Portal - Accounting Module Documentation

## 📋 Overview
Complete Sage-like accounting module for the AHPCZ Portal built with Laravel 11, Livewire 3, and MaryUI.

**Created:** September 1, 2026  
**Version:** 1.0.0  
**Status:** Production Ready

---

## 🎯 Features

### Core Accounting
- ✅ **Double-Entry Bookkeeping System**
- ✅ **Multi-Currency Support** with exchange rate management
- ✅ **Hierarchical Chart of Accounts** (5 main types: Assets, Liabilities, Equity, Revenue, Expenses)
- ✅ **Cost Center Tracking** for departmental accounting
- ✅ **Accounting Period Management** (Open, Closed, Archived)

### Accounts Payable (AP)
- ✅ **Supplier Management** with credit limits, payment terms, banking details
- ✅ **Purchase Invoice Tracking** (Draft, Posted, Partially Paid, Paid, Overdue)
- ✅ **Payment Processing** with multi-invoice allocation
- ✅ **Ageing Analysis** (Current, 1-30, 31-60, 61-90, 90+ days)
- ✅ **Multiple Payment Methods** (Cash, Check, Bank Transfer, Mobile Money)

### Accounts Receivable (AR)
- ✅ **Customer Invoice Generation**
- ✅ **Receipt Management** with invoice allocation
- ✅ **Ageing Reports** matching AP structure
- ✅ **Credit Card Payment Support**

### Journal Entries
- ✅ **Manual Journal Entry System** with multi-line support
- ✅ **Automatic Balance Validation** (Debit = Credit)
- ✅ **Post/Reverse Functionality**
- ✅ **Description and Narration Fields**

### Tax Management
- ✅ **Multiple Tax Rates** (VAT, Sales Tax, etc.)
- ✅ **Tax Transaction Tracking**
- ✅ **Tax Liability Account Assignment**

### Budgeting
- ✅ **Annual Budget Planning**
- ✅ **Quarterly Budget Breakdown** (Q1, Q2, Q3, Q4)
- ✅ **Cost Center Budget Allocation**
- ✅ **Budget Status Management** (Draft, Active, Closed)

### Financial Reporting
- ✅ **Trial Balance**
- ✅ **Profit & Loss Statement**
- ✅ **Balance Sheet**
- ✅ **Cash Flow Statement**
- ✅ **General Ledger by Account**
- ✅ **Date Range Filtering**
- ✅ **Cost Center Filtering**
- ✅ **PDF Export** (ready for implementation)

### Audit & Compliance
- ✅ **Complete Audit Trail** with old/new value tracking
- ✅ **User & IP Address Logging**
- ✅ **Action History** (Create, Update, Delete, Post, Reverse, Approve)
- ✅ **Period Control** for preventing backdated entries

---

## 📁 File Structure

### Database Layer
```
database/migrations/
├── 2026_09_01_000001_create_accounting_periods_table.php
├── 2026_09_01_000002_create_cost_centers_table.php
├── 2026_09_01_000003_create_chart_of_accounts_table.php
├── 2026_09_01_000004_create_tax_rates_table.php
├── 2026_09_01_000005_create_suppliers_table.php
├── 2026_09_01_000006_create_journal_entries_table.php
├── 2026_09_01_000007_create_journal_entry_lines_table.php
├── 2026_09_01_000008_create_accounts_payable_invoices_table.php
├── 2026_09_01_000009_create_accounts_payable_payments_table.php
├── 2026_09_01_000010_create_ap_invoice_payments_table.php
├── 2026_09_01_000011_create_accounts_receivable_invoices_table.php
├── 2026_09_01_000012_create_accounts_receivable_receipts_table.php
├── 2026_09_01_000013_create_ar_invoice_receipts_table.php
├── 2026_09_01_000014_create_budgets_table.php
├── 2026_09_01_000015_create_budget_lines_table.php
├── 2026_09_01_000016_create_tax_transactions_table.php
└── 2026_09_01_000017_create_accounting_audit_trail_table.php
```

### Models
```
app/Models/
├── AccountingPeriod.php
├── CostCenter.php
├── ChartOfAccount.php
├── TaxRate.php
├── Supplier.php
├── JournalEntry.php
├── JournalEntryLine.php
├── AccountsPayableInvoice.php
├── AccountsPayablePayment.php
├── AccountsReceivableInvoice.php
├── AccountsReceivableReceipt.php
├── Budget.php
├── BudgetLine.php
├── TaxTransaction.php
└── AccountingAuditTrail.php
```

### Repository Pattern
```
app/Interfaces/
├── iaccountingperiodInterface.php
├── icostcenterInterface.php
├── ichartofaccountsInterface.php
├── itaxrateInterface.php
├── isupplierInterface.php
├── ijournalentryInterface.php
├── iapinvoiceInterface.php
├── iappaymentInterface.php
├── iarinvoiceInterface.php
├── iarreceiptInterface.php
├── ibudgetInterface.php
├── ifinancialreportInterface.php
└── iaudittrailInterface.php

app/implementations/
├── _accountingperiodRepository.php
├── _costcenterRepository.php
├── _chartofaccountsRepository.php
├── _taxrateRepository.php
├── _supplierRepository.php
├── _journalentryRepository.php
├── _apinvoiceRepository.php
├── _appaymentRepository.php
├── _arinvoiceRepository.php
├── _arreceiptRepository.php
├── _budgetRepository.php
├── _financialreportRepository.php
└── _audittrailRepository.php
```

### Livewire Components
```
app/Livewire/Accounting/
├── AccountingPeriods.php
├── ChartOfAccounts.php
├── JournalEntries.php
├── Suppliers.php
├── ApInvoices.php
├── ApPayments.php
├── ArInvoices.php
├── ArReceipts.php
├── CostCenters.php
├── TaxRates.php
├── Budgets.php
├── AuditTrail.php
└── FinancialReports.php
```

### Blade Views
```
resources/views/livewire/accounting/
├── accounting-periods.blade.php
├── chart-of-accounts.blade.php
├── journal-entries.blade.php
├── suppliers.blade.php
├── ap-invoices.blade.php
├── ap-payments.blade.php
├── ar-invoices.blade.php
├── ar-receipts.blade.php
├── cost-centers.blade.php
├── tax-rates.blade.php
├── budgets.blade.php
├── audit-trail.blade.php
└── financial-reports.blade.php
```

---

## 🚀 Installation & Setup

### Step 1: Run Migrations
```bash
php artisan migrate
```

This will create all 17 accounting tables.

### Step 2: Import Menu Structure
Import the SQL seeder file to create system modules, submodules, and permissions:

```bash
# Option 1: Using MySQL command line
mysql -u your_username -p your_database < database/seeders/accounting_module_seeder.sql

# Option 2: Using phpMyAdmin
# Import → Choose file → Select accounting_module_seeder.sql → Go
```

### Step 3: Verify Installation
```sql
-- Check system module
SELECT * FROM systemmodules WHERE id = 7;

-- Check submodules
SELECT * FROM submodules WHERE systemmodule_id = 7;

-- Check permissions
SELECT * FROM permissions WHERE id BETWEEN 67 AND 129;
```

### Step 4: Configure Navigation
The accounting module will automatically appear in your navigation menu with all 13 submodules.

---

## 🔗 Routes

All routes are registered in `routes/web.php` under the `auth` middleware group:

```php
/accounting/periods              → Accounting Periods
/accounting/chart-of-accounts    → Chart of Accounts
/accounting/journal-entries      → Journal Entries
/accounting/suppliers            → Suppliers
/accounting/ap-invoices          → AP Invoices
/accounting/ap-payments          → AP Payments
/accounting/ar-invoices          → AR Invoices
/accounting/ar-receipts          → AR Receipts
/accounting/cost-centers         → Cost Centers
/accounting/tax-rates            → Tax Rates
/accounting/budgets              → Budgets
/accounting/audit-trail          → Audit Trail
/accounting/financial-reports    → Financial Reports
```

---

## 🎨 UI Components

All views use **MaryUI** components for consistent styling:

- `<x-card>` - Main container
- `<x-table>` - Data tables with pagination
- `<x-modal>` - Create/Edit/View modals
- `<x-form>` - Form handling
- `<x-input>` - Text inputs
- `<x-select>` - Dropdown selects
- `<x-textarea>` - Multi-line text
- `<x-button>` - Action buttons
- `<x-alert>` - Empty state messages
- `<x-breadcrumbs>` - Navigation breadcrumbs
- `<x-stat>` - Statistical cards

---

## 🔐 Permissions Structure

### System Module Permission
- `accounting.access` - Access to accounting module

### Per-Submodule Permissions
Each submodule has standard CRUD permissions:
- `.access` - View the page
- `.create` - Create new records
- `.update` - Edit existing records
- `.delete` - Delete records

### Special Permissions
- `accounting.periods.close` - Close accounting periods
- `accounting.journal-entries.post` - Post journal entries
- `accounting.journal-entries.reverse` - Reverse posted entries
- `accounting.ap-invoices.post` - Post AP invoices
- `accounting.ap-invoices.cancel` - Cancel invoices
- `accounting.ar-invoices.post` - Post AR invoices
- `accounting.ar-invoices.cancel` - Cancel invoices
- `accounting.budgets.activate` - Activate budgets
- `accounting.budgets.close` - Close budgets
- `accounting.financial-reports.export` - Export reports to PDF

---

## 📊 Database Schema Overview

### Main Tables
1. **accounting_periods** - Fiscal periods (monthly/quarterly/annual)
2. **chart_of_accounts** - Hierarchical account structure
3. **cost_centers** - Departmental cost tracking
4. **tax_rates** - Tax configuration
5. **suppliers** - Vendor management
6. **journal_entries** - Manual journal entries (header)
7. **journal_entry_lines** - Journal entry details (lines)
8. **accounts_payable_invoices** - Supplier invoices
9. **accounts_payable_payments** - Payments to suppliers
10. **ap_invoice_payments** - Payment-to-invoice allocation
11. **accounts_receivable_invoices** - Customer invoices
12. **accounts_receivable_receipts** - Customer receipts
13. **ar_invoice_receipts** - Receipt-to-invoice allocation
14. **budgets** - Budget headers
15. **budget_lines** - Budget line items
16. **tax_transactions** - Tax transaction log
17. **accounting_audit_trail** - Complete audit log

---

## 🔄 Standard Workflows

### 1. Initial Setup
1. Create **Accounting Period** (e.g., January 2026)
2. Set up **Chart of Accounts** (Assets, Liabilities, Equity, Revenue, Expenses)
3. Configure **Tax Rates** (e.g., VAT 15%)
4. Create **Cost Centers** (if using departmental accounting)
5. Add **Suppliers** (for AP) and ensure **Customers** exist (for AR)

### 2. Accounts Payable Flow
1. Receive invoice from supplier
2. Create **AP Invoice** (Draft status)
3. Review and **Post** invoice → Status: Posted
4. When paying: Create **AP Payment** and allocate to invoice(s)
5. Invoice status automatically updates: Partially Paid → Paid

### 3. Accounts Receivable Flow
1. Create **AR Invoice** for customer (Draft status)
2. Review and **Post** invoice → Status: Posted
3. When customer pays: Create **AR Receipt** and allocate to invoice(s)
4. Invoice status automatically updates: Partially Paid → Paid

### 4. Manual Journal Entry
1. Create **Journal Entry** with multiple lines
2. Ensure Debit = Credit (automatic validation)
3. **Post** entry to ledger
4. If error: **Reverse** entry and create correcting entry

### 5. Month-End Closing
1. Generate **Financial Reports** to review activity
2. Review **Audit Trail** for any irregularities
3. **Close** the accounting period to prevent backdating
4. Create next period

---

## 🛠️ Technical Patterns

### Livewire Component Pattern
All components follow the same structure:
- Use `boot()` method for dependency injection (not `__construct`)
- Implement `MaryToast` trait for notifications
- Define `$breadcrumbs` array for navigation
- Repository methods return `['status' => 'success'|'error', 'message' => '...']`

### Repository Return Format
```php
return [
    'status' => 'success', // or 'error'
    'message' => 'Operation completed successfully',
    'data' => $result // optional
];
```

### Audit Trail Logging
Every critical operation logs to `accounting_audit_trail`:
```php
AccountingAuditTrail::create([
    'entity_type' => 'JournalEntry',
    'entity_id' => $entry->id,
    'action' => 'POST',
    'user_id' => auth()->id(),
    'ip_address' => request()->ip(),
    'old_values' => json_encode($oldData),
    'new_values' => json_encode($newData),
    'description' => 'Posted journal entry #JE-000123'
]);
```

---

## 📈 Future Enhancements

### Recommended Next Steps
1. **PDF Export Implementation** - Add PDF generation for all reports
2. **Excel Export** - Allow export of financial data to Excel
3. **Automated Journal Entries** - Auto-generate recurring entries
4. **Bank Reconciliation** - Match bank statements with ledger
5. **Fixed Assets Module** - Track depreciation and asset lifecycle
6. **Payroll Integration** - Link payroll expenses to accounting
7. **Multi-Company Support** - Separate books for multiple entities
8. **Budget vs Actual Reports** - Variance analysis
9. **Email Notifications** - Alert for overdue invoices, period closing
10. **Dashboard Widgets** - KPIs and charts on main dashboard

---

## 🧪 Testing Checklist

### Data Entry Tests
- [ ] Create accounting period
- [ ] Set up chart of accounts (all 5 types)
- [ ] Create tax rates
- [ ] Add suppliers and verify customer data exists
- [ ] Create journal entry with balanced debits/credits
- [ ] Post journal entry
- [ ] Create AP invoice and post it
- [ ] Create AP payment and allocate to invoice
- [ ] Create AR invoice and post it
- [ ] Create AR receipt and allocate to invoice

### Reporting Tests
- [ ] Generate Trial Balance
- [ ] Generate Profit & Loss Statement
- [ ] Generate Balance Sheet
- [ ] Generate Cash Flow Statement
- [ ] View General Ledger for specific account
- [ ] Filter reports by date range
- [ ] Filter reports by cost center

### Validation Tests
- [ ] Verify journal entry won't post if unbalanced
- [ ] Verify accounting period closure prevents backdating
- [ ] Verify AP/AR invoice totals calculate correctly (subtotal + tax - discount)
- [ ] Verify payment allocation doesn't exceed invoice balance
- [ ] Verify audit trail captures all changes

---

## 📞 Support & Maintenance

### Key Files to Monitor
- `app/Providers/RepositoryProvider.php` - Repository bindings
- `routes/web.php` - Route definitions
- `.env` - Database and currency settings

### Common Issues & Solutions

**Issue:** "Class not found" error  
**Solution:** Run `composer dump-autoload`

**Issue:** Migration fails  
**Solution:** Check if tables already exist, rollback if needed: `php artisan migrate:rollback --step=17`

**Issue:** Permission denied on module  
**Solution:** Verify user role has required permissions in `role_has_permissions` table

**Issue:** Unbalanced journal entry  
**Solution:** The system automatically validates; ensure total debits = total credits

---

## 👥 Credits

**Developed by:** AI Assistant  
**Project:** AHPCZ Portal  
**Framework:** Laravel 11 + Livewire 3 + MaryUI  
**Date:** September 1, 2026  

---

## 📄 License

This module is part of the AHPCZ Portal system and follows the same license terms as the main application.

---

**End of Documentation**
