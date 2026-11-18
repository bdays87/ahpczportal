<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Applicationsession extends Model
{
    protected $fillable = [
        'year',
        'user_id',
        'renewal_window_start_date',
        'renewal_window_end_date',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'renewal_window_start_date' => 'date',
        'renewal_window_end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isRenewalWindowOpen(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->renewal_window_start_date || !$this->renewal_window_end_date) {
            return false;
        }

        $today = Carbon::today();

        return $today->between(
            Carbon::parse($this->renewal_window_start_date)->startOfDay(),
            Carbon::parse($this->renewal_window_end_date)->endOfDay()
        );
    }

    public function getRenewalWindowLabelAttribute(): string
    {
        if (!$this->renewal_window_start_date || !$this->renewal_window_end_date) {
            return 'Not configured';
        }

        return Carbon::parse($this->renewal_window_start_date)->format('d M Y')
            . ' - '
            . Carbon::parse($this->renewal_window_end_date)->format('d M Y');
    }
}
