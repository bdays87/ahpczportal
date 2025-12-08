<?php

namespace App\Livewire\Components;

use App\Interfaces\icustomerInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class Profilepicture extends Component
{
    use Toast, WithFileUploads;

    public $profile;

    public $uploadModal = false;

    protected $customerRepo;

    public function boot(icustomerInterface $customerRepo)
    {
        $this->customerRepo = $customerRepo;
    }

    public function openUploadModal()
    {
        $this->reset('profile');
        $this->uploadModal = true;
    }

    public function uploadProfile()
    {
        $this->validate([
            'profile' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $customer = Auth::user()->customer?->customer;
        if (! $customer) {
            $this->error('Customer profile not found');

            return;
        }

        try {
            // Delete old profile picture if exists
            if ($customer->profile && $customer->profile !== 'placeholder.jpg') {
                if (Storage::disk('public')->exists($customer->profile)) {
                    Storage::disk('public')->delete($customer->profile);
                }
            }

            $path = $this->profile->store('customers', 'public');

            $response = $this->customerRepo->updateprofile($customer->id, [
                'profile' => $path,
            ]);

            if ($response['status'] === 'success') {
                $this->success('Profile picture updated successfully');
                $this->uploadModal = false;
                $this->reset('profile');
            } else {
                $this->error($response['message']);
            }
        } catch (\Exception $e) {
            $this->error('An error occurred: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.components.profilepicture');
    }
}
