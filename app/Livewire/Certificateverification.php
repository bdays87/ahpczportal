<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
class Certificateverification extends Component
{
      #[Layout('components.layouts.plain')]
    public function render()
    {
        return view('livewire.certificateverification');
    }
}
