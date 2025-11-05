<?php

namespace App\Livewire\Admin\Components;

use App\Interfaces\ipaynowInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Customeronlinepayments extends Component
{
    use Toast;
    public $customer;
    protected $paynowtransaction;
    public function boot(ipaynowInterface $paynowtransaction){
        $this->paynowtransaction = $paynowtransaction;
    }
    public function gettransactions(){
        return $this->paynowtransaction->gettransactions($this->customer->id);
    }
    public function viewtransaction($id){
        $response = $this->paynowtransaction->checktransactionbyid($id);
        if($response['status']=='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
    }
    public function render()
    {
        return view('livewire.admin.components.customeronlinepayments',['transactions'=>$this->gettransactions()]);
    }
}
