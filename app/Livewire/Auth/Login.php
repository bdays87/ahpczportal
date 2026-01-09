<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Login extends Component
{
    public $redirectRoute = 'dashboard';

    public $email;

    public $password;

    public $remember;

    public $error = '';

    public function mount()
    {
        if (Auth::check()) {
            return $this->redirect(route($this->redirectRoute));
        }
    }

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $check = Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
        ], $this->remember);

        if ($check) {
            $user = Auth::user();

            // Check if user has a customer profile
            if ($user->customer && $user->customer->customer) {
                $customer = $user->customer->customer;

                // Check if first login and profile needs to be completed
                //   if (! $customer->first_login_completed || ! $customer->profile_complete) {
                // return $this->redirect(route('customer.profile-update'));
                //  }
            }

            return $this->redirect(route($this->redirectRoute));
        }
        $this->error = 'Invalid credentials';
    }

    #[Layout('components.layouts.plain')]
    public function render()
    {
        return view('livewire.auth.login');
    }
}
