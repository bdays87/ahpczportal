<?php

namespace App\Livewire\Admin;

use App\Interfaces\iuserInterface;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Mary\Traits\Toast;

class ProfileSettings extends Component
{
    use Toast;

    public $name;

    public $surname;

    public $email;

    public $phone;

    public $current_password;

    public $new_password;

    public $new_password_confirmation;

    protected $userrepo;

    public function boot(iuserInterface $userrepo)
    {
        $this->userrepo = $userrepo;
    }

    public function mount()
    {
        $user = Auth::user();
        if (! $user) {
            return $this->redirect(route('dashboard'));
        }

        $this->name = $user->name;
        $this->surname = $user->surname;
        $this->email = $user->email;
        $this->phone = $user->phone;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required',
            'surname' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
        ]);

        $user = Auth::user();

        $response = $this->userrepo->update($user->id, [
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        if ($response['status'] == 'success') {
            $this->success('Profile updated successfully!');
        } else {
            $this->error($response['message']);
        }
    }

    public function changepassword(): void
    {
        $this->validate([
            'current_password'          => 'required',
            'new_password'              => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required',
        ]);

        $user = Auth::user();

        if (! \Illuminate\Support\Facades\Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Current password is incorrect.');
            return;
        }

        $user->update(['password' => \Illuminate\Support\Facades\Hash::make($this->new_password)]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->success('Password changed successfully!');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.admin.profile-settings');
    }
}
