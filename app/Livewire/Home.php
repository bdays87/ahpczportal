<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class Home extends Component
{
    #[On('customer_refresh')]
    public function render()
    {
        return view('livewire.home');
    }
}
