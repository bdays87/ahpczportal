<?php

namespace App\Livewire\Admin\Components;

use Livewire\Component;

class Personaldetails extends Component
{
    public $customer;
    public function mount($customer){
        $this->customer = $customer;
    }
    public function render()
    {
        return view('livewire.admin.components.personaldetails');
    }
}
