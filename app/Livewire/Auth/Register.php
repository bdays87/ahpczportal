<?php

namespace App\Livewire\Auth;

use App\Interfaces\iaccounttypeInterface;
use App\Interfaces\iuserInterface;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Mary\Traits\Toast;

class Register extends Component
{
    use Toast;

    public $name;

    public $surname;

    public $email;

    public $phone;

    public $password;

    public $password_confirmation;

    public $accounttype_id;

    public $signup_type;

    public $registration_number;

    protected $accounttyperepo;

    protected $userrepo;

    public function boot(iaccounttypeInterface $accounttyperepo, iuserInterface $userrepo)
    {
        $this->accounttyperepo = $accounttyperepo;
        $this->userrepo = $userrepo;
    }

    public function getaccounttypes()
    {
        return $this->accounttyperepo->getAll(null)->where('id', '!=', 1);
    }

    public function register()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => ['required', 'string', 'regex:/^(?:\+263|0)7[1378]\d{7}$/'],
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
            'accounttype_id' => 'required|exists:accounttypes,id',
        ], [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'Please enter a valid Zimbabwe mobile number (e.g., 0771234567 or +263771234567).',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password_confirmation.required' => 'Please confirm your password.',
        ]);
        $data = [
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => $this->password,
            'accounttype_id' => $this->accounttype_id,
        ];
        $response = $this->userrepo->register($data);
        if ($response['status'] == 'success') {
            $this->success('User registered successfully');
            $this->redirect(route('login'));
        } else {
            $this->error($response['message']);
        }

    }

    #[Layout('components.layouts.plain')]
    public function render()
    {
        return view('livewire.auth.register', [
            'accounttypes' => $this->getaccounttypes(),
        ]);
    }
}
