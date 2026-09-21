<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountsReceivableReceipt extends Model
{
    protected $guarded = [];

    protected $casts = [
        'receipt_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function accountingPeriod()
    {
        return $this->belongsTo(AccountingPeriod::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(Bankaccount::class, 'bank_account_id');
    }

    public function arAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'ar_account_id');
    }

    public function cashGlAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'cash_gl_account_id');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'createdby');
    }

    public function invoices()
    {
        return $this->belongsToMany(
            AccountsReceivableInvoice::class,
            'ar_invoice_receipts',
            'ar_receipt_id',
            'ar_invoice_id'
        )->withPivot('amount_allocated')->withTimestamps();
    }
}
