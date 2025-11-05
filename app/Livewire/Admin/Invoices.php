<?php

namespace App\Livewire\Admin;

use App\Interfaces\ibanktransactionInterface;
use App\Interfaces\invoiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Mary\Traits\Toast;

class Invoices extends Component
{
    use Toast;
    public $status="AWAITING";
    protected $invoicerepo;
    protected $banktransactionrepo;
    public $breadcrumbs=[];
    public $invoice;
    public $modal=false;
    public $documentmodal=false;
    public $documenturl;
    public $search;
    public $banktransactionmodal=false;
    public $banktransactions;
    public $proofofpayment_id;
    public $selectedTab="document";
    public function boot(invoiceInterface $invoicerepo,ibanktransactionInterface $banktransactionrepo)
    {
        $this->invoicerepo = $invoicerepo;
        $this->banktransactionrepo = $banktransactionrepo;
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Invoices'
            ],
        ];
    }
    public function mount()
    {
        $this->banktransactions = new Collection();
    }
    public function getinvoices()
    {
        return $this->invoicerepo->getinvoices($this->status);
    }
    public function UpdatedSearch(){
        $this->getbanktransactions();
    }
    public function getbanktransactions()
    {
        $this->banktransactions = $this->banktransactionrepo->search($this->search);
    }
    public function headers():array{
        return [
            ['key'=>'customer','label'=>'Customer'],
            ['key'=>'description','label'=>'Description'],
            ['key'=>'amount','label'=>'Amount'],
            ['key'=>'status','label'=>'Status'],
            ['key'=>'action','label'=>'']
        ];
    }
    public function viewinvoice($id)
    {
        $this->invoice = $this->invoicerepo->getinvoice($id);
        $this->modal = true;
    }    

    public function viewdocument($id)
    {
        $document = $this->invoice->proofofpayment()->where('id',$id)->first();
        $this->documenturl = Storage::url($document->file);
        $this->documentmodal = true;
    }
   
    public function banktransactionheaders():array{
        return [
            ['key'=>'statement_reference','label'=>'StaRef'],
            ['key'=>'bank.name','label'=>'Bank'],
            ['key'=>'description','label'=>'Desc'],
            ['key'=>'transaction_date','label'=>'TxnDate'],
            ['key'=>'amount','label'=>'Amt'],
            ['key'=>'status','label'=>'Status'],
            ['key'=>'action','label'=>'']
        ];
    }
    public function claimbanktransaction($id){
        $response = $this->banktransactionrepo->claim($id,$this->proofofpayment_id,$this->invoice->customer_id);
        if($response['status']=='success'){
            $this->success($response['message']);
            $this->documentmodal = false;
        }else{
            $this->error($response['message']);
        }
    }
    public function render()
    {
        return view('livewire.admin.invoices',['invoices'=>$this->getinvoices(),'headers'=>$this->headers(),'banktransactionheaders'=>$this->banktransactionheaders()]);
    }
}
