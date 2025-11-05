<?php

namespace App\Livewire\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
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
