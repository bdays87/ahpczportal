<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;

class Forget extends Component
{
    #[Layout('components.layouts.plain')]
    public function render()
    {
        return view('livewire.auth.forget');
    }
}
