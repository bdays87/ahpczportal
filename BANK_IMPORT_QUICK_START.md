# Bank Statement Import - Quick Start Guide

## 🚀 Quick Steps

1. **Go to:** Bank Transactions page
2. **Click:** "Import Bank Transaction" button
3. **Select:** Your bank type from dropdown
4. **Select:** Bank account
5. **Upload:** Your CSV file
6. **Review:** Parsed transactions
7. **Select:** Which transactions to import
8. **Click:** "Import Selected"

## 📋 Checklist Before Import

- [ ] Downloaded bank statement as CSV
- [ ] File is less than 10MB
- [ ] File has not been edited or reformatted
- [ ] Know which bank account this belongs to
- [ ] Ready to review transactions before importing

## 🏦 Supported Banks

| Bank | Format | Sample File |
|------|--------|-------------|
| **NMB Bank** | CSV | `NMBstatementformat.csv` |
| **First Capital Bank** | CSV | `FirstCapitalBankstatement.csv` |
| **Other Banks** | CSV | Use "Generic CSV Format" |

## 📊 What Gets Imported

Each transaction includes:
- ✅ Transaction Date
- ✅ Description
- ✅ Reference Number
- ✅ Amount (Debit/Credit)
- ✅ Running Balance
- ✅ Status: PENDING (you can change later)

## ⚠️ Common Issues & Solutions

### Issue: "Failed to parse statement"
**Solutions:**
1. Check file is CSV format (not Excel)
2. Try selecting "Generic CSV Format"
3. Ensure file size < 10MB
4. Don't edit the CSV before uploading

### Issue: "Missing transactions"
**Solutions:**
1. Check dates are in correct format
2. Remove blank rows from CSV
3. Ensure all columns are present

### Issue: "Wrong amounts"
**Solutions:**
1. Check decimal separator is period (.)
2. Don't manually edit amounts in CSV
3. Re-download statement from bank

### Issue: "Duplicate transactions"
**Solutions:**
1. Check "Running Balance" during preview
2. Deselect duplicates manually before import
3. Compare with existing transactions

## 💡 Pro Tips

1. **Always review before importing** - Use the preview step to verify data
2. **Start small** - Test with a short statement first
3. **Select carefully** - You can deselect transactions you don't want
4. **Use "Select All"** - Then deselect only what you don't need
5. **Check balances** - Running balance helps verify accuracy
6. **Match after import** - Link transactions to customers/invoices later

## 🔍 Testing the Parser (For Developers)

Test the parser from command line:

```bash
# Test NMB format
php artisan bank:test-parser public/imports/NMBstatementformat.csv --bank=NMB

# Test FCB format
php artisan bank:test-parser public/imports/FirstCapitalBankstatement.csv --bank=FCB

# Test generic format
php artisan bank:test-parser path/to/statement.csv --bank=Generic
```

## 📁 Sample Files Location

Reference files are in:
```
public/imports/
├── NMBstatementformat.csv          ← NMB Bank format
├── FirstCapitalBankstatement.csv   ← First Capital Bank format
├── USD Cashbookformatpastel.xls    ← Pastel USD format
└── ZWGCashbookformatpastel.xls     ← Pastel ZWG format
```

## 🎯 After Import Workflow

1. **Review imported transactions** in Bank Transactions list
2. **Match to customers** if known
3. **Update status** from PENDING to CLAIMED
4. **Reconcile with invoices/receipts** if applicable
5. **Edit details** if needed (before claiming)

## ❓ Need Help?

- **User Guide:** See `BANK_STATEMENT_IMPORT_GUIDE.md` for detailed instructions
- **Technical Details:** See `BANK_IMPORT_SUMMARY.md` for implementation details
- **Support:** Contact your system administrator

## 🎬 Video Tutorial (Coming Soon)

Watch a quick video walkthrough:
- How to download bank statement
- How to import into system
- How to match transactions
- How to reconcile accounts

## 📞 Support Contact

For technical issues:
- Email: support@ahpcz.co.zw
- Phone: +263 XXX XXXX
- Hours: Mon-Fri, 8AM-5PM

---

**Remember:** Always review transactions in the preview step before importing!
