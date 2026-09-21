<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountsReceivableInvoice extends Model
{
    protected $guarded = [];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date'     => 'date',
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

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function incomeAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'income_account_id');
    }

    public function arAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'ar_account_id');
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'createdby');
    }

    public function receipts()
    {
        return $this->belongsToMany(
            AccountsReceivableReceipt::class,
            'ar_invoice_receipts',
            'ar_invoice_id',
            'ar_receipt_id'
        )->withPivot('amount_allocated')->withTimestamps();
    }

    public function isOverdue(): bool
    {
        return $this->due_date->isPast() && !in_array($this->status, ['PAID', 'CANCELLED']);
    }
}
