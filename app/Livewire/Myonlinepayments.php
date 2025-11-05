<?php

namespace App\Livewire;

use Livewire\Component;

class Myonlinepayments extends Component
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
                'label' => 'My online payments'
            ],
        ];
    }
    public function render()
    {
        return view('livewire.myonlinepayments');
    }
}
