<?php

namespace App\Livewire\Auth;

use App\Models\Customer;
use App\Models\Customeruser;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Forget extends Component
{
    // Step 1 — National ID
    public $step = 1;
    public $nationalid = '';
    public $foundcustomer = null;

    // Step 2 — Email verification
    public $email = '';

    // Step 3 — New password
    public $new_password = '';
    public $new_password_confirmation = '';

    public $error = '';

    // ── Step 1: verify National ID ────────────────────────────────────────────
    public function searchByNationalId()
    {
        $this->validate(['nationalid' => 'required|string|min:3']);
        $this->error = '';

        $customer = Customer::where('identificationnumber', $this->nationalid)->first();

        if (! $customer) {
            $this->error = 'No customer found with that National ID. Please contact the administrator.';
            return;
        }

        if (! $customer->customeruser) {
            $this->error = 'This record does not have a login account yet. Please register first.';
            return;
        }

        $this->foundcustomer = $customer;
        $this->step = 2;
    }

    // ── Step 2: verify email ──────────────────────────────────────────────────
    public function verifyEmail()
    {
        $this->validate(['email' => 'required|email']);
        $this->error = '';

        $user = $this->foundcustomer->customeruser->user;

        if (strtolower($user->email) !== strtolower($this->email)) {
            $this->error = 'Email address does not match our records.';
            return;
        }

        $this->step = 3;
    }

    // ── Step 3: set new password ──────────────────────────────────────────────
    public function resetPassword()
    {
        $this->validate([
            'new_password'              => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required',
        ]);

        $user = $this->foundcustomer->customeruser->user;
        $user->update(['password' => Hash::make($this->new_password)]);

        session()->flash('success', 'Password reset successfully. You can now sign in.');
        $this->redirect(route('login'));
    }

    public function back()
    {
        $this->step  = max(1, $this->step - 1);
        $this->error = '';
        if ($this->step === 1) {
            $this->foundcustomer = null;
            $this->email         = '';
        }
    }

    #[Layout('components.layouts.plain')]
    public function render()
    {
        return view('livewire.auth.forget');
    }
}
