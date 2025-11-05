<?php

namespace App\Livewire\Admin\Components;

use Livewire\Component;

class Customerprofile extends Component
{
    public $customer;
    public function mount($customer)
    {
        $this->customer = $customer;
    }
    public function render()
    {
        return view('livewire.admin.components.customerprofile');
    }
}
