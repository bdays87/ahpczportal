<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class Myprofessions extends Component
{
     #[On('customer_refresh')]
     public function refresh()
     {
        $this->render();
     }
    public function render()
    {
        return view('livewire.myprofessions');
    }
}
