<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostCenter extends Model
{
    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(CostCenter::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(CostCenter::class, 'parent_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'createdby');
    }

    public function accounts()
    {
        return $this->hasMany(ChartOfAccount::class);
    }

    public function journalLines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function budgetLines()
    {
        return $this->hasMany(BudgetLine::class);
    }
}
