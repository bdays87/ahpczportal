<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountsPayableInvoice extends Model
{
    protected $guarded = [];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date'     => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
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

    public function expenseAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'expense_account_id');
    }

    public function apAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'ap_account_id');
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

    public function payments()
    {
        return $this->belongsToMany(
            AccountsPayablePayment::class,
            'ap_invoice_payments',
            'ap_invoice_id',
            'ap_payment_id'
        )->withPivot('amount_allocated')->withTimestamps();
    }

    public function isOverdue(): bool
    {
        return $this->due_date->isPast() && !in_array($this->status, ['PAID', 'CANCELLED']);
    }
}
