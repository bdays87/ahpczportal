<?php

namespace App\Livewire\Admin\Components;

use App\Interfaces\imanualpaymentInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Manualpayments extends Component
{
    use Toast;
    public $customer;
    protected $manualpaymentrepo;
    public function boot(imanualpaymentInterface $manualpaymentrepo){
        $this->manualpaymentrepo = $manualpaymentrepo;
    }
    public function mount($customer){
        $this->customer = $customer;
    }
    public function getmanualpayments(){
        return $this->manualpaymentrepo->getbycustomer($this->customer->id);
    }
    public function deletemanualpayment($id){
        $response = $this->manualpaymentrepo->deletemanualpayment($id);
        if($response['status'] == 'success'){
           $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
    }
    public function render()
    {
        return view('livewire.admin.components.manualpayments',['manualpayments'=>$this->getmanualpayments()]);
    }
}
