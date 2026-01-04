<?php

namespace App\Livewire\Newapplications\Practitioners;

use App\Interfaces\icustomerprofessionInterface;
use App\Interfaces\invoiceInterface;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use App\Interfaces\icurrencyInterface;
use Livewire\Attributes\On;
class Assesmentinvoicing extends Component
{
    use WithFileUploads,Toast;
    public $uuid;
    public  $breadcrumbs=[];
    public $customerprofession_id;
    public $step = 3;
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

    public function getcustomerprofession(){
        $payload= $this->customerprofessionrepo->getbyuuid($this->uuid);
        $this->customerprofession_id = $payload["customerprofession"]["id"];
    
        return $payload;
     }
     public function getcurrencies(){
        return $this->currencyrepo->getAll("active");
    }

    #listen to invoicesettled
    #[On('invoicesettled')]
     public function getinvoice(){
      
        $invoices = $this->invoicerepo->getcustomerprofessioninvoices($this->customerprofession_id,'Qualification Assessment');
       
        if($invoices && !is_null($invoices)){
        $invoice = collect($invoices["data"])->where("description","Qualification Assessment")->first();
        if($invoice){
            return $invoice["data"];
        }else{
            return null;
        }
    }
     }
    public function render()
    {
        return view('livewire.newapplications.practitioners.assesmentinvoicing',[
            "customerprofession"=>$this->getcustomerprofession()["customerprofession"],
            "invoice"=>$this->getinvoice(),
            "currencies"=>$this->getcurrencies(),
        ]);
    }
}
