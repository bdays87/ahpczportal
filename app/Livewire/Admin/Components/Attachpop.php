<?php

namespace App\Livewire\Admin\Components;

use App\Interfaces\invoiceInterface;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class Attachpop extends Component
{
    use Toast,WithFileUploads;

    public $invoice;

    public $proofofpayments;

    public $attachmodal = false;

    public $paymentfile;

    protected $invoicerepo;

    public function mount($invoice)
    {
        $this->invoice = $invoice;
        $this->proofofpayments = new collection;
    }

    public function boot(invoiceInterface $invoicerepo)
    {
        $this->invoicerepo = $invoicerepo;
    }

    public function getattachpayments()
    {
        $this->proofofpayments = $this->invoicerepo->getinvoiceproof($this->invoice->id);
        $this->attachmodal = true;
    }

    public function Updatedpaymentfile()
    {
        $this->addattachpayment();
    }

    public function addattachpayment()
    {
        $this->validate([
            'paymentfile' => 'required',
        ]);
        $path = $this->paymentfile->store('documents', 's3');
        $response = $this->invoicerepo->createinvoiceproof([
            'invoice_id' => $this->invoice->id,
            'file' => $path,
        ]);
        if ($response['status'] == 'success') {
            $this->reset('paymentfile');
            $this->proofofpayments = $this->invoicerepo->getinvoiceproof($this->invoice->id);
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }

    public function deleteattachpayment($id)
    {
        $response = $this->invoicerepo->deleteinvoiceproof($id);
        if ($response['status'] == 'success') {
            $this->proofofpayments = $this->invoicerepo->getinvoiceproof($this->invoice->id);
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }

    public function submitforverification()
    {
        $response = $this->invoicerepo->submitforverification($this->invoice->id);
        if ($response['status'] == 'success') {
            $this->dispatch('proofofpaymentsubmitted', invoice_id: $this->invoice->id);
            $this->success($response['message']);
            $this->reset('paymentfile');
            $this->proofofpayments = new collection;
            $this->attachmodal = false;
        } else {
            $this->error($response['message']);
        }
    }

    public function render()
    {
        return view('livewire.admin.components.attachpop');
    }
}
