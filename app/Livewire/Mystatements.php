<?php

namespace App\Livewire;

use Livewire\Component;

class Mystatements extends Component
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
                'label' => 'My statement'
            ],
        ];
    }
    public function render()
    {
        return view('livewire.mystatements');
    }
}
