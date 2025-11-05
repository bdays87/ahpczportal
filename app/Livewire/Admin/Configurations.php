<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class Configurations extends Component
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
                'label' => 'Configurations'
            ],
        ];
    }
    public function render()
    {
        return view('livewire.admin.configurations');
    }
}
