<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
class Practitioner extends Component
{
    public $selectedTab = 'profession-tab';
 
    public function render()
    {
        return view('livewire.dashboard.practitioner');
    }
}
