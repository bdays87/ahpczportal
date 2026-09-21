<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetLine extends Model
{
    protected $guarded = [];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }
}
