<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Reset extends Component
{
    public $email = '';

    public $token = '';

    public $password = '';

    public $password_confirmation = '';

    public $message = '';

    public $status = '';

    public function mount(?string $token = null, ?string $email = null): void
    {
        $this->token = $token ?? request()->query('token', '');
        $this->email = $email ?? request()->query('email', '');
    }

    public function resetPassword(): void
    {
        $this->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function ($user, $password) {
                $user->password = $password;
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $this->status = 'success';
            $this->message = 'Your password has been reset successfully. You can now login with your new password.';
            $this->password = '';
            $this->password_confirmation = '';
        } else {
            $this->status = 'error';
            $this->message = 'Invalid or expired reset token. Please request a new password reset link.';
        }
    }

    #[Layout('components.layouts.plain')]
    public function render()
    {
        return view('livewire.auth.reset');
    }
}
