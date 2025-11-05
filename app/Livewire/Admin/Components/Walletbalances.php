<?php

namespace App\Livewire\Admin\Components;

use App\Interfaces\isuspenseInterface;
use App\Interfaces\icurrencyInterface;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Interfaces\ipaynowInterface;
use App\Interfaces\imanualpaymentInterface;
class Walletbalances extends Component
{
    public $customer;
    public bool $topupmodal = false;
    public bool $cash = false;
    public bool $swipe = false;
    public $amount;
    public $currency;
    public $currency_id;
    public $errormessage = "";


    public bool $paynow = false;
    protected $suspenseRepository;
    protected $currencyRepository;
    protected $paynowrepository;
    protected $manualpaymentrepo;
    public function mount($customer)
    {
        $this->customer = $customer;
    }
    public function boot(isuspenseInterface $suspenseRepository, icurrencyInterface $currencyRepository,ipaynowInterface $paynowrepository,imanualpaymentInterface $manualpaymentrepo)
    {
        $this->suspenseRepository = $suspenseRepository;
        $this->currencyRepository = $currencyRepository;
        $this->paynowrepository = $paynowrepository;
        $this->manualpaymentrepo = $manualpaymentrepo;
    }
    public function getcurrencies(){
        return $this->currencyRepository->getAll("active");
    }
    #[On('wallet_refresh')]
    #[On('invoicesettled')]
    public function getbalances()
    {
        $response = $this->suspenseRepository->getbalances($this->customer->id);
      
        return $response;
    }

    public function opentopup($currency)
    {
        $this->currency = $this->getcurrencies()->where('id', $currency)->first()->name;
        $this->currency_id = $currency;
        $this->topupmodal = true;
    }

    public function processtopup(){
        $this->validate([
            'amount'=>'required|numeric|min:1',
        ]);
        if($this->paynow){
            $this->paynowtopup();
        }elseif($this->cash){
            $this->savemanual();
        }
    }


    
    public function paynowtopup(){
        $response = $this->paynowrepository->initiatetransaction([
            'amount'=>$this->amount,
            'currency_id'=>$this->currency_id,
            'customer_id'=>$this->customer->id,
        ]);
   
    
        if($response['status']=='success'){
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
        return view('livewire.admin.components.walletbalances', [
            'balances' => $this->getbalances(),
            'currencies' => $this->getcurrencies()
        ]);
    }
}
