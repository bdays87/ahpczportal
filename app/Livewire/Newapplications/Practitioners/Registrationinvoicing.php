<?php

namespace App\Livewire\Newapplications\Practitioners;

use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\icustomerprofessionInterface;
use App\Interfaces\invoiceInterface;
use App\Interfaces\icurrencyInterface;
use Livewire\Attributes\On;
class Registrationinvoicing extends Component
{
    use WithFileUploads,Toast;
    public $uuid;
    public $breadcrumbs=[];
    public $customerprofession_id;
    public $applicationtype_id;
    public $step = 4;
    protected $customerprofessionrepo;
    protected $invoicerepo;
    protected $currencyrepo;
    public function mount($uuid){
        $this->uuid = $uuid;

        if(Auth::user()->accounttype_id == 1){
            $this->breadcrumbs = [
                [
                    'label' => 'Dashboard',
                    'icon' => 'o-home',
                    'link' => route('dashboard'),
                ],
                [
                    'label' => 'Customer',
                    'icon' => 'o-home',
                    'link' => route('customers.index'),
                ],
                [
                    'label' => 'Customer Professions'
                ],
            ];
            
        }else{
            $this->breadcrumbs = [
                [
                    'label' => 'Dashboard',
                    'icon' => 'o-home',
                    'link' => route('dashboard'),
                ],
                [
                    'label' => 'My Profession'
                ],
            ];
        }
  
    }

    public function boot(icustomerprofessionInterface $customerprofessionrepo,invoiceInterface $invoicerepo,icurrencyInterface $currencyrepo){
        $this->customerprofessionrepo = $customerprofessionrepo;
        $this->invoicerepo = $invoicerepo;
        $this->currencyrepo = $currencyrepo;
    }
    public function getcurrencies(){
        return $this->currencyrepo->getAll("active");
    }
    public function getcustomerprofession(){
        $payload= $this->customerprofessionrepo->getbyuuid($this->uuid);
        if($payload["customerprofession"]->applications->count() > 0){
            $this->applicationtype_id = $payload["customerprofession"]->applications->last()->applicationtype_id;
        }else{
            $this->applicationtype_id = 1;
        }
        $this->customerprofession_id = $payload["customerprofession"]["id"];
    
        return $payload;
     }
      #[On('invoicesettled')]
     public function getinvoice(){
        $invoices = $this->invoicerepo->getcustomerprofessioninvoices($this->customerprofession_id,'Registration');
        
        if(count($invoices) > 0){
        $invoice = collect($invoices["data"])->where("description","Registration")->first();
      
        return $invoice;
    }
    return null;
     }
    public function render()
    {
        return view('livewire.newapplications.practitioners.registrationinvoicing',[
            "customerprofession"=>$this->getcustomerprofession()["customerprofession"],
            "invoice"=>$this->getinvoice(),
            "currencies"=>$this->getcurrencies(),
        ]);
    }
} 
