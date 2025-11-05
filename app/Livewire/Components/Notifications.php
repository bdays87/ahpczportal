<?php

namespace App\Livewire\Components;

use Livewire\Component;

class Notifications extends Component
{
    public bool $modal = false;
    public function render()
    {
        return view('livewire.components.notifications');
    }
}
