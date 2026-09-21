<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $guarded = [];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'createdby');
    }

    public function lines()
    {
        return $this->hasMany(BudgetLine::class);
    }

    public function getTotalBudgeted(): float
    {
        return (float) $this->lines->sum('budgeted_amount');
    }

    public function getTotalActual(): float
    {
        return (float) $this->lines->sum('actual_amount');
    }

    public function getTotalVariance(): float
    {
        return $this->getTotalActual() - $this->getTotalBudgeted();
    }
}
