<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingPeriod extends Model
{
    protected $guarded = [];

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'createdby');
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function apInvoices()
    {
        return $this->hasMany(AccountsPayableInvoice::class);
    }

    public function arInvoices()
    {
        return $this->hasMany(AccountsReceivableInvoice::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'OPEN';
    }

    public function isClosed(): bool
    {
        return in_array($this->status, ['CLOSED', 'LOCKED']);
    }
}
