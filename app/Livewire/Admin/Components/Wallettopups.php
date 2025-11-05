<?php

namespace App\Livewire\Admin\Components;

use App\Interfaces\imanualpaymentInterface;
use App\Interfaces\ipaynowInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Wallettopups extends Component
{
    use Toast;
    public $amount;
    public $currency;
    public $paynowmodal = false;
    public $manualmodal = false;
    public $currencies;
    public $customer;
    public $errormessage ="";
    public $mode;
    protected $paynowrepository;
    protected $manualpaymentrepo;

    public function boot(ipaynowInterface $paynowrepository,imanualpaymentInterface $manualpaymentrepo){
        $this->paynowrepository = $paynowrepository;
        $this->manualpaymentrepo = $manualpaymentrepo;
    }
    public function mount($currencies,$customer){
        $this->currencies = $currencies;
        $this->customer = $customer;
    }
    public function paynow(){
        $response = $this->paynowrepository->initiatetransaction([
            'amount'=>$this->amount,
            'currency_id'=>$this->currency,
            'customer_id'=>$this->customer->id,
        ]);
    
        if($response['status']=='success'){
            $this->paynowmodal = false;
            $this->js("window.open('".$response['redirecturl']."', '_blank');"); 
        }else{
            $this->errormessage = $response['message'];
        }
    }
    public function savemanual(){
        $response = $this->manualpaymentrepo->create([
            'amount'=>$this->amount,
            'currency_id'=>$this->currency,
            'customer_id'=>$this->customer->id,
            'mode'=>$this->mode,
        ]);
    
        if($response['status']=='success'){
            $this->manualmodal = false;
          $this->success($response['message']);
        }else{
            $this->errormessage = $response['message'];
        }
    }
    public function render()
    {
        return view('livewire.admin.components.wallettopups');
    }
}
