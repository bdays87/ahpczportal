<?php

namespace App\Livewire\Auth;

use App\Audit\AuditWriter;
use App\Services\LoginResolver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Login extends Component
{
    public $redirectRoute = 'dashboard';

    /** Username, registration number or email */
    public $identifier;

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
            'identifier' => 'required|string|max:120',
            'password' => 'required',
        ]);

        $identifier = trim($this->identifier);

        // Registration numbers are easy to guess, so slow down repeated wrong passwords
        $throttle = 'login:'.strtolower($identifier).'|'.request()->ip();
        if (RateLimiter::tooManyAttempts($throttle, 5)) {
            $this->error = 'Too many attempts. Please wait '.RateLimiter::availableIn($throttle).' seconds and try again.';

            return;
        }

        $user = app(LoginResolver::class)->resolve($identifier);

        if ($user && Auth::attempt(['id' => $user->id, 'password' => $this->password], $this->remember)) {
            RateLimiter::clear($throttle);

            return $this->redirect(route($this->redirectRoute));
        }

        RateLimiter::hit($throttle, 60);

        if (! $user) {
            AuditWriter::event('login_failed', 'Failed sign-in for: '.$identifier.' (no such user)');
        }

        $this->error = 'Invalid credentials';
    }

    #[Layout('components.layouts.plain')]
    public function render()
    {
        return view('livewire.auth.login');
    }
}
