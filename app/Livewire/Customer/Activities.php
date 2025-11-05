<?php

namespace App\Livewire\Customer;

use App\Interfaces\iactivityInterface;
use Livewire\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Auth;

class Activities extends Component
{
    use Toast;

    public $breadcrumbs = [];
    protected $activityRepo;

    public function mount()
    {
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'My Activities']
        ];
    }

    public function boot(iactivityInterface $activityRepo)
    {
        $this->activityRepo = $activityRepo;
    }

    public function getAvailableActivities()
    {
        return $this->activityRepo->getAvailableForCustomer(Auth::user()->customer->id);
    }

    public function getMyEnrollments()
    {
        return $this->activityRepo->getCustomerEnrollments(Auth::user()->customer->id);
    }

    public function enroll($activityId)
    {
        $response = $this->activityRepo->enroll($activityId, Auth::user()->customer->id);
        
        if ($response['status'] === 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }

    public function render()
    {
        return view('livewire.customer.activities', [
            'availableActivities' => $this->getAvailableActivities(),
            'myEnrollments' => $this->getMyEnrollments(),
        ]);
    }
}