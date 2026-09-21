<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountsPayablePayment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'payment_date' => 'date',
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

    public function bankAccount()
    {
        return $this->belongsTo(Bankaccount::class, 'bank_account_id');
    }

    public function apAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'ap_account_id');
    }

    public function bankGlAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'bank_gl_account_id');
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
            AccountsPayableInvoice::class,
            'ap_invoice_payments',
            'ap_payment_id',
            'ap_invoice_id'
        )->withPivot('amount_allocated')->withTimestamps();
    }
}
