<?php

namespace App\Livewire;

use Livewire\Component;

class Mymanualpayments extends Component
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
                'label' => 'Mnnual payment'
            ],
        ];
    }
    public function render()
    {
        return view('livewire.mymanualpayments');
    }
}
