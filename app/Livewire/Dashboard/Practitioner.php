<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class Practitioner extends Component
{
    public $selectedTab = 'profession-tab';
    public function render()
    {
        return view('livewire.dashboard.practitioner');
    }
}
