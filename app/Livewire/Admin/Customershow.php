<?php

namespace App\Livewire\Admin;

use App\Interfaces\icurrencyInterface;
use App\Interfaces\icustomerInterface;
use Livewire\Attributes\On;
use Livewire\Component;
use Mary\Traits\Toast;

class Customershow extends Component
{
    use Toast;
    public $uuid;
    protected $customerrepo;
    protected $currencyrepo;
    public $breadcrumbs=[];
    public $tabSelected = 'profile-tab';
    public function boot(icustomerInterface $customerrepo,icurrencyInterface $currencyrepo){
        $this->customerrepo = $customerrepo;
        $this->currencyrepo = $currencyrepo;
    }
    public function mount($uuid){
        $this->uuid = $uuid;
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Customers',
                'link' => route('customers.index'),
            ],
            [
                'label' => 'Customer',
            ],
        ];
    }

    #[On('refresh')]
    public function getcustomerprofile(){
        $profile= $this->customerrepo->getcustomerprofile($this->uuid);
        if($profile){
            return $profile;
        }
        abort(404);
    }
    public function getcurrencies(){
        return $this->currencyrepo->getAll("active");
    }
    
    public function render()
    {
        return view('livewire.admin.customershow',['customerprofile'=>$this->getcustomerprofile(),'currencies'=>$this->getcurrencies()]);
    }
}
