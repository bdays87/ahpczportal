<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'content_url',
        'content_text',
        'attachment_path',
        'points',
        'duration_minutes',
        'status',
        'created_by'
    ];

    protected $casts = [
        'points' => 'integer',
        'duration_minutes' => 'integer',
    ];

    /**
     * Professions that can access this activity
     */
    public function professions(): BelongsToMany
    {
        return $this->belongsToMany(Profession::class, 'activity_professions');
    }

    /**
     * Customer enrollments for this activity
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(ActivityEnrollment::class);
    }

    /**
     * Quiz associated with this activity
     */
    public function quiz(): HasOne
    {
        return $this->hasOne(ActivityQuiz::class);
    }

    /**
     * Admin who created this activity
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if customer can enroll in this activity
     */
    public function canEnroll(Customer $customer): bool
    {
        // Check if customer has a profession that matches this activity
        $customerProfessions = $customer->customerprofessions->pluck('profession_id');
        $activityProfessions = $this->professions->pluck('id');
        
        return $customerProfessions->intersect($activityProfessions)->isNotEmpty();
    }

    /**
     * Check if customer is enrolled in this activity
     */
    public function isEnrolled(Customer $customer): bool
    {
        return $this->enrollments()->where('customer_id', $customer->id)->exists();
    }

    /**
     * Get customer's enrollment for this activity
     */
    public function getEnrollment(Customer $customer): ?ActivityEnrollment
    {
        return $this->enrollments()->where('customer_id', $customer->id)->first();
    }
}