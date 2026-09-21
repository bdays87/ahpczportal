<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $guarded = [];

    protected $casts = [
        'entry_date'  => 'date',
        'approved_at' => 'datetime',
        'posted_at'   => 'datetime',
    ];

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class)->orderBy('line_number');
    }

    public function accountingPeriod()
    {
        return $this->belongsTo(AccountingPeriod::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function reversedByEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'reversed_by_entry_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'createdby');
    }

    public function taxTransactions()
    {
        return $this->hasMany(TaxTransaction::class);
    }

    public function isBalanced(): bool
    {
        $debit  = $this->lines->sum('debit');
        $credit = $this->lines->sum('credit');
        return round($debit, 2) === round($credit, 2);
    }

    public function isPosted(): bool
    {
        return $this->status === 'POSTED';
    }

    public function isDraft(): bool
    {
        return $this->status === 'DRAFT';
    }
}
