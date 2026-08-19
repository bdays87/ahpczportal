<?php

namespace App\Livewire\Admin;

use App\Interfaces\invoiceInterface;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Mary\Traits\Toast;

class Receiptprint extends Component
{
    use Toast;

    public $invoice;

    public $uuid;

    protected $invoicerepo;

    public function boot(invoiceInterface $invoicerepo)
    {
        $this->invoicerepo = $invoicerepo;
    }

    public function mount($uuid)
    {
        $this->uuid = $uuid;
        $this->loadinvoice();
    }

    protected function loadinvoice()
    {
        $invoice = $this->invoicerepo->getInvoiceByUuid($this->uuid);

        abort_if(! $invoice || $invoice->status !== 'PAID', 404);

        $this->invoice = $invoice;
    }

    public function deletereceipt($id)
    {
        $response = $this->invoicerepo->deletereceipt($id);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
            $this->loadinvoice();
        } else {
            $this->error($response['message']);
        }
    }

    #[Layout('components.layouts.print')]
    public function render()
    {
        return view('livewire.admin.receiptprint');
    }
}
