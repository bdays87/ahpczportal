<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Forget extends Component
{
    public $email = '';

    public $message = '';

    public $status = '';

    public function sendResetLink(): void
    {
        $this->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            ['email' => $this->email]
        );

        if ($status === Password::RESET_LINK_SENT) {
            $this->status = 'success';
            $this->message = 'We have emailed your password reset link.';
            $this->email = '';
        } elseif ($status === Password::RESET_THROTTLED) {
            $this->status = 'error';
            $this->message = 'Please wait before retrying.';
        } else {
            $this->status = 'error';
            $this->message = 'We could not find a user with that email address.';
        }
    }

    #[Layout('components.layouts.plain')]
    public function render()
    {
        return view('livewire.auth.forget');
    }
}
