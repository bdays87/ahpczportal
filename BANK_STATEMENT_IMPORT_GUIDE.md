# Bank Statement Import Feature

## Overview
The Bank Statement Import feature allows you to import bank transactions from CSV or Excel files exported from different banks. The system automatically detects and parses the format for supported banks.

## Supported Banks

### 1. NMB Bank (Zimbabwe)
**Format:** CSV
**Sample File:** `public/imports/NMBstatementformat.csv`

**Expected Columns:**
- Transaction Date
- Value Date
- Narration
- Ref. No.
- Debit Amount
- Credit Amount
- Running Balance

**Header Format:**
```
ACCOUNT NAME : ,Account Name Here
ACCOUNT NUMBER : ,USD 00000260277286
Transaction Date, Value Date, Narration, Ref. No., Debit Amount, Credit Amount, Running Balance
14-01-2026,14-01-2026,Description here,1260,0,214,6 600.13
```

### 2. First Capital Bank (Zimbabwe)
**Format:** CSV
**Sample File:** `public/imports/FirstCapitalBankstatement.csv`

**Expected Columns:**
- Transaction Date
- Value Date
- Description
- Reference Number
- Debits
- Credits
- Balance

**Header Format:**
```
21576611145,Account Name,,,,,
Currency,ZWG,,,,,
Opening Balance,27742.67,,,,,
Closing Balance,40366.42,,,,,
Transaction Date,Value Date,Description,Reference Number,Debits,Credits,Balance
30-Jun-26,30-Jun-26,Description,QS1287265,600,,40366.42
```

### 3. Generic CSV Format
**Format:** CSV

The system can also parse generic CSV files with flexible column names. It looks for:
- Date columns: "Transaction Date", "Date", "Value Date"
- Amount columns: "Debit", "Credit", "Debits", "Credits"
- Description: "Description", "Narration", "Particulars"
- Reference: "Reference", "Ref. No.", "Reference Number"
- Balance: "Balance", "Running Balance"

## How to Use

### Step 1: Prepare Your File
1. Export your bank statement as CSV from your online banking portal
2. Keep the original format - don't edit or reformat the file
3. Ensure the file is less than 10MB

### Step 2: Import the Statement
1. Navigate to **Bank Transactions** page
2. Click **Import Bank Transaction** button
3. Select the **Bank Type** from dropdown:
   - NMB Bank
   - First Capital Bank
   - Generic CSV Format
4. Select the **Bank Account** to link transactions to
5. Click **Choose File** and select your statement file
6. Click **Next: Preview**

### Step 3: Review Transactions
1. The system will parse the file and show all transactions
2. Review the extracted data:
   - Transaction Date
   - Description
   - Reference Number
   - Debit/Credit amounts
   - Running Balance
3. **Select transactions** to import:
   - Check/uncheck individual transactions
   - Use "Select All" or "Deselect All" buttons
4. Click **Import Selected** to proceed

### Step 4: Complete Import
1. The system imports selected transactions
2. Each transaction is created with status "PENDING"
3. You'll see a success message with import statistics
4. Imported transactions appear in the main Bank Transactions list

## Transaction Matching

After import, you can:
- **Match transactions to customers** manually
- **Match to invoices/receipts** for reconciliation
- **Update status** from PENDING to CLAIMED
- **Edit transaction details** if needed

## Date Format Support

The parser automatically handles various date formats:
- `d-M-y` (30-Jun-26)
- `d-M-Y` (30-Jun-2026)
- `d/m/Y` (30/06/2026)
- `Y-m-d` (2026-06-30)
- `d-m-Y` (30-06-2026)
- `m/d/Y` (06/30/2026)

## Amount Parsing

The system automatically:
- Removes currency symbols ($, ZWG, USD, etc.)
- Removes thousand separators (spaces, commas)
- Handles negative amounts
- Converts debit/credit to signed amounts (credits positive, debits negative)

## Troubleshooting

### "Failed to parse statement" Error
- **Check file format:** Ensure it's CSV, XLS, or XLSX
- **Check file size:** Must be under 10MB
- **Check encoding:** Use UTF-8 encoding
- **Try Generic format:** If your bank isn't listed, use "Generic CSV Format"

### Missing Transactions
- **Check date format:** Ensure dates are in recognized format
- **Check column names:** Headers should match expected format
- **Check empty lines:** Remove blank rows from CSV

### Wrong Amounts
- **Check decimal separator:** System expects period (.) as decimal separator
- **Check thousand separator:** Commas are automatically removed
- **Check debit/credit columns:** Ensure correct columns are used

### Duplicate Transactions
- The system doesn't automatically detect duplicates
- Check the "Running Balance" column during preview
- Manually deselect duplicate transactions before import

## Adding New Bank Formats

To add support for a new bank:

1. Add sample statement to `public/imports/`
2. Add bank to `BankStatementParser::getSupportedBanks()`
3. Create parse method in `app/Services/BankStatementParser.php`:
   ```php
   private function parseYourBankFormat($file, &$metadata): array
   {
       // Your parsing logic here
   }
   ```
4. Add case in `parseCSV()` switch statement

## Excel File Support (Future Enhancement)

Currently, Excel files require conversion to CSV first. To add full Excel support:

1. Install PhpSpreadsheet:
   ```bash
   composer require phpoffice/phpspreadsheet
   ```

2. Implement `parseExcel()` method in `BankStatementParser.php`

## Security Notes

- Files are temporarily stored in `storage/app/temp-statements/`
- Files are not publicly accessible
- Original files are deleted after parsing
- Only CSV/Excel files are accepted
- File size limited to 10MB

## Sample Files Location

Reference sample files are located in:
```
public/imports/
├── NMBstatementformat.csv
├── FirstCapitalBankstatement.csv
├── USD Cashbookformatpastel.xls
└── ZWGCashbookformatpastel.xls
```

## API Reference

### BankStatementParser Service

```php
use App\Services\BankStatementParser;

$parser = new BankStatementParser();
$result = $parser->parse($filePath, 'NMB');

// Returns:
[
    'transactions' => [
        [
            'transaction_date' => '2026-01-14',
            'value_date' => '2026-01-14',
            'description' => 'Transaction description',
            'reference' => 'REF12345',
            'debit' => 0.00,
            'credit' => 214.00,
            'amount' => 214.00,
            'balance' => 6600.13,
            'type' => 'CREDIT',
        ],
        // ... more transactions
    ],
    'metadata' => [
        'account_name' => 'Account Name',
        'account_number' => 'USD 00000260277286',
        'currency' => 'USD',
        'opening_balance' => 27742.67,
        'closing_balance' => 40366.42,
    ],
]
```

## License & Credits

Bank Statement Import Feature
Created for AHPCZ Portal
© 2026 All Rights Reserved
