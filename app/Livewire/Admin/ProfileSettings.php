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

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.admin.profile-settings');
    }
}
