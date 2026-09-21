<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    protected $guarded = [];

    // Account types
    const TYPE_ASSET     = 'ASSET';
    const TYPE_LIABILITY = 'LIABILITY';
    const TYPE_EQUITY    = 'EQUITY';
    const TYPE_INCOME    = 'INCOME';
    const TYPE_EXPENSE   = 'EXPENSE';

    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'createdby');
    }

    public function journalLines()
    {
        return $this->hasMany(JournalEntryLine::class, 'account_id');
    }

    public function budgetLines()
    {
        return $this->hasMany(BudgetLine::class, 'account_id');
    }

    /**
     * Compute the running balance for this account from posted journal lines.
     * Debits increase ASSET/EXPENSE accounts; Credits increase LIABILITY/EQUITY/INCOME.
     */
    public function getBalance(?int $periodId = null): float
    {
        $query = $this->journalLines()
            ->whereHas('journalEntry', fn($q) => $q->where('status', 'POSTED'));

        if ($periodId) {
            $query->whereHas('journalEntry', fn($q) => $q->where('accounting_period_id', $periodId));
        }

        $debit  = (float) $query->sum('debit');
        $credit = (float) $query->sum('credit');

        if (in_array($this->account_type, [self::TYPE_ASSET, self::TYPE_EXPENSE])) {
            return $debit - $credit;
        }

        return $credit - $debit;
    }
}
