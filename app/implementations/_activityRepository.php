<?php

namespace App\implementations;

use App\Interfaces\iactivityInterface;
use App\Models\Activity;
use App\Models\ActivityEnrollment;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class _activityRepository implements iactivityInterface
{
    protected $activity;
    protected $enrollment;
    protected $customer;

    public function __construct(Activity $activity, ActivityEnrollment $enrollment, Customer $customer)
    {
        $this->activity = $activity;
        $this->enrollment = $enrollment;
        $this->customer = $customer;
    }

    public function create(array $data)
    {
        try {
            DB::beginTransaction();
            
            $data['created_by'] = Auth::id();
            $profession_ids = $data['profession_ids'];
            unset($data['profession_ids']);
            $activity = $this->activity->create($data);
            
            // Attach professions if provided
            if (isset($profession_ids) && is_array($profession_ids)) {
                $activity->professions()->attach($profession_ids);
            }
            
            DB::commit();
            return ['status' => 'success', 'message' => 'Activity created successfully', 'data' => $activity];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $activity = $this->activity->find($id);
            if (!$activity) {
                return ['status' => 'error', 'message' => 'Activity not found'];
            }

            DB::beginTransaction();
            
            $activity->update($data);
            
            // Update professions if provided
            if (isset($data['profession_ids']) && is_array($data['profession_ids'])) {
                $activity->professions()->sync($data['profession_ids']);
            }
            
            DB::commit();
            return ['status' => 'success', 'message' => 'Activity updated successfully', 'data' => $activity];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete(int $id)
    {
        try {
            $activity = $this->activity->find($id);
            if (!$activity) {
                return ['status' => 'error', 'message' => 'Activity not found'];
            }

            // Check if there are enrollments
            if ($activity->enrollments()->exists()) {
                return ['status' => 'error', 'message' => 'Cannot delete activity with existing enrollments'];
            }

            $activity->delete();
            return ['status' => 'success', 'message' => 'Activity deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function get(int $id)
    {
        return $this->activity->with(['professions', 'enrollments', 'quiz.questions.answers', 'creator'])->find($id);
    }

    public function getAll()
    {
        return $this->activity->with(['professions', 'enrollments', 'creator'])->get();
    }

    public function getPublished()
    {
        return $this->activity->with(['professions', 'enrollments'])
            ->where('status', 'PUBLISHED')
            ->get();
    }

    public function getByProfession(int $professionId)
    {
        return $this->activity->with(['professions', 'enrollments'])
            ->whereHas('professions', function ($query) use ($professionId) {
                $query->where('profession_id', $professionId);
            })
            ->where('status', 'PUBLISHED')
            ->get();
    }

    public function getAvailableForCustomer(int $customerId)
    {
        $customer = $this->customer->with('customerprofessions.profession')->find($customerId);
        if (!$customer) {
            return collect();
        }

        $professionIds = $customer->customerprofessions->pluck('profession_id')->toArray();

        return $this->activity->with(['professions', 'enrollments' => function ($query) use ($customerId) {
                $query->where('customer_id', $customerId);
            }])
            ->whereHas('professions', function ($query) use ($professionIds) {
                $query->whereIn('profession_id', $professionIds);
            })
            ->where('status', 'PUBLISHED')
            ->get();
    }

    public function attachProfessions(int $activityId, array $professionIds)
    {
        try {
            $activity = $this->activity->find($activityId);
            if (!$activity) {
                return ['status' => 'error', 'message' => 'Activity not found'];
            }

            $activity->professions()->attach($professionIds);
            return ['status' => 'success', 'message' => 'Professions attached successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function detachProfessions(int $activityId, array $professionIds)
    {
        try {
            $activity = $this->activity->find($activityId);
            if (!$activity) {
                return ['status' => 'error', 'message' => 'Activity not found'];
            }

            $activity->professions()->detach($professionIds);
            return ['status' => 'success', 'message' => 'Professions detached successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function enroll(int $activityId, int $customerId)
    {
        try {
            $activity = $this->activity->find($activityId);
            if (!$activity) {
                return ['status' => 'error', 'message' => 'Activity not found'];
            }

            $customer = $this->customer->find($customerId);
            if (!$customer) {
                return ['status' => 'error', 'message' => 'Customer not found'];
            }

            // Check if customer can enroll
            if (!$activity->canEnroll($customer)) {
                return ['status' => 'error', 'message' => 'You are not eligible for this activity'];
            }

            // Check if already enrolled
            if ($activity->isEnrolled($customer)) {
                return ['status' => 'error', 'message' => 'Already enrolled in this activity'];
            }

            $enrollment = $this->enrollment->create([
                'activity_id' => $activityId,
                'customer_id' => $customerId,
                'status' => 'ENROLLED',
                'enrolled_at' => now()
            ]);

            return ['status' => 'success', 'message' => 'Enrolled successfully', 'data' => $enrollment];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getEnrollments(int $activityId)
    {
        return $this->enrollment->with(['customer', 'activity'])
            ->where('activity_id', $activityId)
            ->get();
    }

    public function getCustomerEnrollments(int $customerId)
    {
        return $this->enrollment->with(['activity.professions', 'quizAttempts'])
            ->where('customer_id', $customerId)
            ->get();
    }

    public function updateEnrollmentStatus(int $enrollmentId, string $status)
    {
        try {
            $enrollment = $this->enrollment->find($enrollmentId);
            if (!$enrollment) {
                return ['status' => 'error', 'message' => 'Enrollment not found'];
            }

            $enrollment->update(['status' => $status]);
            return ['status' => 'success', 'message' => 'Enrollment status updated successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getEnrollmentStats(int $activityId)
    {
        $enrollments = $this->enrollment->where('activity_id', $activityId);
        
        return [
            'total_enrollments' => $enrollments->count(),
            'enrolled' => $enrollments->where('status', 'ENROLLED')->count(),
            'in_progress' => $enrollments->where('status', 'IN_PROGRESS')->count(),
            'completed' => $enrollments->where('status', 'COMPLETED')->count(),
            'dropped' => $enrollments->where('status', 'DROPPED')->count(),
            'completion_rate' => $enrollments->count() > 0 
                ? round(($enrollments->where('status', 'COMPLETED')->count() / $enrollments->count()) * 100, 2)
                : 0
        ];
    }
}
