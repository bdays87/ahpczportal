<?php

namespace App\Livewire\Components;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Topbar extends Component
{
  
    public function logout(){
        Auth::logout();
        return $this->redirect(route('login'), navigate: true);
    }
    
    public function render()
    {
        return view('livewire.components.topbar');
    }
}
