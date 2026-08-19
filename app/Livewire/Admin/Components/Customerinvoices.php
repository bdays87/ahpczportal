<?php

namespace App\Livewire\Admin\Components;

use App\Interfaces\invoiceInterface;
use Livewire\Component;

class Customerinvoices extends Component
{
    public $customer;

    protected $invoicerepo;

    public function mount($customer)
    {
        $this->customer = $customer;
    }

    public function boot(invoiceInterface $invoicerepo)
    {
        $this->invoicerepo = $invoicerepo;
    }

    public function getinvoices()
    {
        return $this->invoicerepo->getcustomerinvoices($this->customer->id);
    }

    public function render()
    {
        return view('livewire.admin.components.customerinvoices', [
            'invoices' => $this->getinvoices(),
        ]);
    }
}
