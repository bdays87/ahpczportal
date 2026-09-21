# Bank Statement Import - Visual Flowchart

## 📊 User Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    BANK TRANSACTIONS PAGE                    │
│                                                              │
│  [+ New Transaction]  [📥 Import Bank Transaction]          │
└──────────────────────────────┬──────────────────────────────┘
                               │
                               │ Click Import
                               ▼
┌─────────────────────────────────────────────────────────────┐
│             STEP 1: UPLOAD FILE                              │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Bank Type:      [▼ NMB Bank                    ]           │
│                   - NMB Bank                                 │
│                   - First Capital Bank                       │
│                   - Generic CSV Format                       │
│                                                              │
│  Bank Account:   [▼ Select Bank                 ]           │
│                                                              │
│  Statement File: [Choose File] ← Upload CSV/Excel           │
│                  📄 statement.csv                           │
│                  ✓ File uploaded successfully               │
│                                                              │
│  [Cancel]                      [Next: Preview →]            │
└──────────────────────────────┬──────────────────────────────┘
                               │
                               │ Parse & Validate
                               ▼
┌─────────────────────────────────────────────────────────────┐
│             STEP 2: PREVIEW TRANSACTIONS                     │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ✓ 15 transactions found                                    │
│  Account: USD 00000260277286 | MLCSCZ- National Cert...    │
│                                                              │
│  12 of 15 selected          [Select All] [Deselect All]    │
│                                                              │
│  ┌────────────────────────────────────────────────────────┐ │
│  │ ☑ │ Date       │ Description      │ Ref    │ Dr  │ Cr │ │
│  ├───┼────────────┼──────────────────┼────────┼─────┼────┤ │
│  │ ☑ │ 14-01-2026 │ LAB PARTNERS CLI │ 1260   │     │214 │ │
│  │ ☑ │ 19-01-2026 │ LAB PARTNERS CLI │ 2057   │     │165 │ │
│  │ ☐ │ 31-01-2026 │ Account Maint... │ 1532   │ 30  │    │ │
│  │ ☑ │ 06-02-2026 │ LAB PARTNERS CLI │ 1850   │     │891 │ │
│  │   │    ...     │       ...        │  ...   │ ... │... │ │
│  └────────────────────────────────────────────────────────┘ │
│                          ↑                                   │
│                    Scroll to view all                        │
│                                                              │
│  [← Back]                          [Import Selected →]      │
└──────────────────────────────┬──────────────────────────────┘
                               │
                               │ Confirm Import
                               ▼
┌─────────────────────────────────────────────────────────────┐
│             STEP 3: IMPORT IN PROGRESS                       │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│                    ⌛ Importing transactions...              │
│                                                              │
│                    [Progress Spinner]                        │
│                                                              │
└──────────────────────────────┬──────────────────────────────┘
                               │
                               │ Complete
                               ▼
┌─────────────────────────────────────────────────────────────┐
│                   IMPORT COMPLETE                            │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ✓ Import completed successfully!                           │
│                                                              │
│  📊 Statistics:                                              │
│     • 12 transactions imported                               │
│     • 0 skipped                                              │
│     • 0 errors                                               │
│                                                              │
│                          [Close]                             │
└──────────────────────────────┬──────────────────────────────┘
                               │
                               ▼
                    Back to Transactions List
```

---

## 🔄 Data Flow Diagram

```
┌──────────────┐
│  User Device │
└──────┬───────┘
       │ Upload CSV
       ▼
┌──────────────────────────────────┐
│  Livewire Component               │
│  (Banktransactions.php)          │
└──────┬───────────────────────────┘
       │ Send to Parser
       ▼
┌──────────────────────────────────┐
│  BankStatementParser Service     │
│  - Detect format                 │
│  - Parse CSV                     │
│  - Extract data                  │
│  - Format dates/amounts          │
└──────┬───────────────────────────┘
       │ Return parsed data
       ▼
┌──────────────────────────────────┐
│  Preview Data                    │
│  {                               │
│    transactions: [...],          │
│    metadata: {...}               │
│  }                               │
└──────┬───────────────────────────┘
       │ User selects
       ▼
┌──────────────────────────────────┐
│  Repository Layer                │
│  (banktransactionRepository)     │
└──────┬───────────────────────────┘
       │ Create records
       ▼
┌──────────────────────────────────┐
│  Database                        │
│  banktransactions table          │
└──────────────────────────────────┘
```

---

## 🏗️ Component Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     PRESENTATION LAYER                       │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  banktransactions.blade.php                                 │
│  • Import Modal UI                                           │
│  • File Upload Widget                                        │
│  • Transaction Preview Table                                 │
│  • Selection Checkboxes                                      │
│                                                              │
└──────────────────────────┬──────────────────────────────────┘
                           │ Wire Events
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                    APPLICATION LAYER                         │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Banktransactions.php (Livewire)                            │
│  • uploadStatement()                                         │
│  • confirmImport()                                           │
│  • executeImport()                                           │
│  • toggleTransaction()                                       │
│  • selectAll() / deselectAll()                              │
│                                                              │
└──────────────────────────┬──────────────────────────────────┘
                           │ Use Service
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                      SERVICE LAYER                           │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  BankStatementParser.php                                     │
│  • parse(file, bankType)                                     │
│  • parseNMBFormat()                                          │
│  • parseFCBFormat()                                          │
│  • parseGenericCSV()                                         │
│  • parseDate() / parseAmount()                              │
│                                                              │
└──────────────────────────┬──────────────────────────────────┘
                           │ Interface
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                    REPOSITORY LAYER                          │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ibanktransactionInterface                                   │
│  _banktranactionRepository                                   │
│  • create(data)                                              │
│  • getAll(search)                                            │
│                                                              │
└──────────────────────────┬──────────────────────────────────┘
                           │ Eloquent
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                        DATA LAYER                            │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Banktransaction Model                                       │
│  • Fillable fields                                           │
│  • Relationships (bank, currency, customer)                  │
│  • Casts (date, amount)                                      │
│                                                              │
└──────────────────────────┬──────────────────────────────────┘
                           │ Database
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                       DATABASE                               │
│                  banktransactions table                      │
└─────────────────────────────────────────────────────────────┘
```

---

## 🎯 Decision Tree: Bank Format Selection

```
                    Start Import
                         │
                         ▼
              ┌──────────────────┐
              │ Which bank is    │
              │ your statement   │
              │ from?            │
              └────┬────────┬────┘
                   │        │
        ┌──────────┘        └──────────┐
        │                               │
        ▼                               ▼
┌───────────────┐              ┌───────────────┐
│ NMB Bank      │              │ First Capital │
│               │              │ Bank          │
│ Select: NMB   │              │ Select: FCB   │
└───────┬───────┘              └───────┬───────┘
        │                               │
        └───────────┬───────────────────┘
                    │
                    ▼
            ┌───────────────┐
            │ Other bank or │
            │ custom format?│
            │               │
            │ Select: Generic
            └───────────────┘
```

---

## 🔍 Transaction Selection Flow

```
                Parse Complete
                      │
                      ▼
         ┌─────────────────────┐
         │ Show all 15 txns    │
         │ All selected by     │
         │ default             │
         └──────────┬──────────┘
                    │
         ┌──────────┼──────────┐
         │          │           │
         ▼          ▼           ▼
    [Select     [Deselect  [Individual
     All]        All]       Checkbox]
         │          │           │
         └──────────┼───────────┘
                    │
                    ▼
         ┌─────────────────────┐
         │ Update selection    │
         │ counter             │
         └──────────┬──────────┘
                    │
                    ▼
         ┌─────────────────────┐
         │ User clicks         │
         │ "Import Selected"   │
         └──────────┬──────────┘
                    │
                    ▼
         ┌─────────────────────┐
         │ Import only         │
         │ selected items      │
         └─────────────────────┘
```

---

## ⚠️ Error Handling Flow

```
            Upload File
                │
                ▼
        ┌───────────────┐      ❌ File too large
        │ Validate File │─────────→ Show error
        └───────┬───────┘
                │ ✓
                ▼
        ┌───────────────┐      ❌ Invalid format
        │ Parse File    │─────────→ Show error
        └───────┬───────┘
                │ ✓
                ▼
        ┌───────────────┐      ❌ No transactions
        │ Extract Data  │─────────→ Show warning
        └───────┬───────┘
                │ ✓
                ▼
        ┌───────────────┐
        │ Show Preview  │
        └───────┬───────┘
                │
                ▼
        ┌───────────────┐      ❌ None selected
        │ User Selects  │─────────→ Show error
        └───────┬───────┘
                │ ✓
                ▼
        ┌───────────────┐
        │ Import Loop   │
        └───────┬───────┘
                │
       ┌────────┼────────┐
       │        │         │
       ▼        ▼         ▼
   ✓Success  ⚠️Skip   ❌Error
       │        │         │
       └────────┼─────────┘
                │
                ▼
        ┌───────────────┐
        │ Show Summary  │
        │ • Imported: 12│
        │ • Skipped: 2  │
        │ • Errors: 1   │
        └───────────────┘
```

---

## 📦 File Processing Pipeline

```
CSV File (on disk)
      │
      │ fopen()
      ▼
File Handle
      │
      │ fgetcsv() loop
      ▼
Raw Rows (arrays)
      │
      │ Skip headers
      │ Skip empty lines
      ▼
Data Rows
      │
      │ parseDate()
      │ parseAmount()
      ▼
Cleaned Data
      │
      │ Format structure
      ▼
Transaction Objects
      │
      │ Return array
      ▼
{
  transactions: [...],
  metadata: {...}
}
```

---

## 🎨 UI State Machine

```
        ┌──────────────┐
        │  CLOSED      │
        │  (Initial)   │
        └──────┬───────┘
               │ Click Import
               ▼
        ┌──────────────┐
        │  STEP 1      │
        │  Upload      │
        └──────┬───────┘
               │ Submit
               ▼
        ┌──────────────┐
        │  STEP 2      │◄───┐
        │  Preview     │    │ Back button
        └──────┬───────┘    │
               │ Import      │
               ▼             │
        ┌──────────────┐    │
        │  STEP 3      │────┘
        │  Processing  │
        └──────┬───────┘
               │ Complete
               ▼
        ┌──────────────┐
        │  SUCCESS     │
        │  (Auto-close)│
        └──────────────┘
               │
               ▼
          Back to List
```

---

## 🧩 Module Integration Points

```
┌─────────────────────┐
│ Bank Transactions   │
│ Import Module       │
└──────────┬──────────┘
           │
           ├─────────────────┐
           │                 │
           ▼                 ▼
    ┌─────────────┐   ┌─────────────┐
    │   Banks     │   │ Currencies  │
    │   Module    │   │  Module     │
    └──────┬──────┘   └──────┬──────┘
           │                 │
           │    ┌────────────┘
           │    │
           ▼    ▼
    ┌─────────────────┐
    │ Banktransaction │
    │     Model       │
    └────────┬────────┘
             │
             ├──────────────────┐
             │                  │
             ▼                  ▼
      ┌───────────┐      ┌──────────┐
      │ Customers │      │ Invoices │
      │ (optional)│      │(optional)│
      └───────────┘      └──────────┘
```

This visual guide should help users and developers understand the complete flow! 🎯
