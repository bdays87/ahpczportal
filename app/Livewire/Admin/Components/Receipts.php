<?php

namespace App\Livewire\Admin\Components;

use App\Interfaces\icurrencyInterface;
use App\Interfaces\iexchangerateInterface;
use App\Interfaces\invoiceInterface;
use App\Interfaces\isuspenseInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Receipts extends Component
{
    use Toast;
    public $invoice;
    protected $currencyrepo;
    protected $exchangeraterepo;
    protected $suspenserepo;
    protected $invoicerepo;
    public $exchangerate;
    public $totalpable;
    public $walletbalance;
    public $currency_id;
    public $paymentmodal =false;
    public $prefix = "USD";
    public function boot(icurrencyInterface $currencyrepo,invoiceInterface $invoicerepo,iexchangerateInterface $exchangeraterepo,isuspenseInterface $suspenserepo){
        $this->currencyrepo = $currencyrepo;
        $this->exchangeraterepo = $exchangeraterepo;
        $this->suspenserepo = $suspenserepo;
        $this->invoicerepo = $invoicerepo;
    }
    public function mount($invoice){
        $this->invoice = $invoice;
    }

    public function getcurrencies(){
        return $this->currencyrepo->getAll("active");
    }
    public function updatedCurrencyId(){
        $this->exchangerate = $this->exchangeraterepo->getlatestrate($this->currency_id);
        $this->prefix = $this->exchangerate->secondarycurrency->name;
         $this->totalpable = $this->invoicerepo->getinvoicebalance($this->invoice->id,$this->currency_id);
        $this->walletbalance = $this->suspenserepo->getbalance($this->invoice->customer_id, $this->currency_id)["balance"];
        
     }

     public function computetotalpaid($receipts){
       
        $totalpaid = 0;
        foreach($receipts as $receipt){
            $totalpaid += $receipt->amount/$receipt->exchangerate->rate;
        }
        return $totalpaid;
    }
     public function settleinvoice(){
        $this->validate([
            "totalpable"=>"required",
            "currency_id"=>"required",
        ]);
      $response=  $this->invoicerepo->settleinvoice([
            "invoice_id"=>$this->invoice->id,
            "amount"=>$this->totalpable, 
            "currency_id"=>$this->currency_id,
            "customer_id"=>$this->invoice->customer_id,
            "exchangerate_id"=>$this->exchangerate->id,
        ]);
        if($response['status'] == "success"){
            $this->paymentmodal = false;
             $this->success($response['message']);
            $this->dispatch('invoicesettled',invoice_id:$this->invoice->id);
        }else{
            $this->error($response['message']);
        }
     }
    public function render()
    {
        return view('livewire.admin.components.receipts',[
            "currencies"=>$this->getcurrencies(),
        ]);
    }
}
