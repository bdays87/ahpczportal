<?php

namespace App\Livewire\Admin\Components;

use App\Interfaces\isuspenseInterface;
use Livewire\Component;

class Customerstatements extends Component
{
    public $customer;
    protected $suspenseRepository;
    public function mount($customer)
    {
        $this->customer = $customer;
    }
    public function boot(isuspenseInterface $suspenseRepository)
    {
        $this->suspenseRepository = $suspenseRepository;
    }
    public function getstatement()
    {
        return $this->suspenseRepository->getstatement($this->customer->id);
    }
    public function render()
    {
        return view('livewire.admin.components.customerstatements', [
            'statement' => $this->getstatement()
        ]);
    }
}
