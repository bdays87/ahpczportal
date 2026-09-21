<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $guarded = [];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function apAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'ap_account_id');
    }

    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'createdby');
    }

    public function invoices()
    {
        return $this->hasMany(AccountsPayableInvoice::class);
    }

    public function payments()
    {
        return $this->hasMany(AccountsPayablePayment::class);
    }

    /**
     * Outstanding balance (total invoiced minus total paid).
     */
    public function getBalance(): float
    {
        $invoiced = (float) $this->invoices()->whereIn('status', ['POSTED','PARTIALLY_PAID','OVERDUE'])->sum('balance_due');
        return $invoiced;
    }
}
