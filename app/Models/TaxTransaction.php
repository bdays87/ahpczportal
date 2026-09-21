<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxTransaction extends Model
{
    protected $guarded = [];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function accountingPeriod()
    {
        return $this->belongsTo(AccountingPeriod::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'createdby');
    }
}
