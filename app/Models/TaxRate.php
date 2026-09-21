<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    protected $guarded = [];

    public function salesAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'sales_account_id');
    }

    public function purchaseAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'purchase_account_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'createdby');
    }

    public function taxTransactions()
    {
        return $this->hasMany(TaxTransaction::class);
    }

    /**
     * Calculate tax amount from a gross or net amount.
     */
    public function calculateTax(float $amount, bool $inclusive = false): float
    {
        if ($inclusive) {
            return $amount - ($amount / (1 + $this->rate / 100));
        }
        return $amount * $this->rate / 100;
    }
}
