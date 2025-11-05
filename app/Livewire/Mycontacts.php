<?php

namespace App\Livewire;

use Livewire\Component;

class Mycontacts extends Component
{
    public $breadcrumbs=[];
    public function mount(){
        $this->breadcrumbs=[
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'My contacts'
            ],
        ];
    }
    public function render()
    {
        return view('livewire.mycontacts');
    }
}
