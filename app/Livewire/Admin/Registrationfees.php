<?php

namespace App\Livewire\Admin;

use App\Interfaces\icurrencyInterface;
use App\Interfaces\icustomertypeInterface;
use App\Interfaces\iemploymentlocationInterface;
use App\Interfaces\iqualificationcategoryInterface;
use App\Interfaces\iregistrationfeeInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Registrationfees extends Component
{
    use Toast;
    public $customertype_id;
    public $currency_id;
    public $employmentlocation_id;
    public $generalledger;
    public $amount;
    public $modal = false;
    public $year;
    public $breadcrumbs = [];
    public $id;

    protected $customertyperepo;
    protected $currencyrepo;
    protected $employmentlocationrepo;
    protected $registrationfeerepo;
    public function boot(icustomertypeInterface $customertypeInterface, icurrencyInterface $currencyInterface, iemploymentlocationInterface $employmentlocationInterface, iregistrationfeeInterface $registrationfeeInterface)
    {
        $this->customertyperepo = $customertypeInterface;
        $this->currencyrepo = $currencyInterface;
        $this->employmentlocationrepo = $employmentlocationInterface;
        $this->registrationfeerepo = $registrationfeeInterface;
    }

    public function mount()
    {
        $this->breadcrumbs =  [
            [
            'label' => 'Dashboard',
            'icon' => 'o-home',
            'link' => route('dashboard'),
        ],
        [
            'label' => 'Registration Fees'
        ],
    ];
    $this->year = now()->year;
    }

    public function getcurrencylist(){
        return $this->currencyrepo->getAll('active');
    }
    public function getcustomertypelist(){
        return $this->customertyperepo->getAll('active');
    }
    public function getemploymentlocationlist(){
        return $this->employmentlocationrepo->getAll();
    }

    public function registrationfees(){
        return $this->registrationfeerepo->getAll($this->year);
    }

    public function save(){
        $this->validate([
            'customertype_id' => 'required',
            'currency_id' => 'required',
            'employmentlocation_id' => 'required',
            'generalledger' => 'required',
            'amount' => 'required',
        ]);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }
        $this->reset(['id','customertype_id','currency_id','employmentlocation_id','generalledger','amount']);
    }

    public function create(){
       $response= $this->registrationfeerepo->create([
            'customertype_id' => $this->customertype_id,
            'currency_id' => $this->currency_id,
            'employmentlocation_id' => $this->employmentlocation_id,
            'generalledger' => $this->generalledger,
            'amount' => $this->amount,
            'year' => $this->year,
        ]);
        if($response['status']=='success'){
            $this->success($response['message']);
          
        }else{
            $this->error($response['message']);
        }
    }
    public function update(){
        $response = $this->registrationfeerepo->update($this->id, [
            'customertype_id' => $this->customertype_id,
            'currency_id' => $this->currency_id,
            'employmentlocation_id' => $this->employmentlocation_id,
            'generalledger' => $this->generalledger,
            'amount' => $this->amount,
            'year' => $this->year,
        ]);
        if($response['status']=='success'){
            $this->success($response['message']);
           
        }else{
            $this->error($response['message']);
        }
    }
    public function delete($id){
        $response = $this->registrationfeerepo->delete($id);
        if($response['status']=='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
    }
    public function edit($id){
        $this->id = $id;
       $record= $this->registrationfeerepo->get($id);
       $this->customertype_id = $record->customertype_id;
       $this->currency_id = $record->currency_id;
       $this->employmentlocation_id = $record->employmentlocation_id;
       $this->generalledger = $record->generalledger;
       $this->amount = $record->amount;
       $this->year = $record->year;
       $this->modal = true;
    }

    public function headers():array{
        return [
            ['key'=>'year','label'=>'Year'],
            ['key'=>'customertype.name','label'=>'Customer Type'],
            ['key'=>'currency.name','label'=>'Currency'],
            ['key'=>'employmentlocation.name','label'=>'Employment Location'],           
            ['key'=>'generalledger','label'=>'General Ledger'],
            ['key'=>'amount','label'=>'Amount'],
            ['key'=>'action','label'=>''],
          
        ];
    }
        
    public function render()
    {
        return view('livewire.admin.registrationfees',[
            'headers'=>$this->headers(),
            'records'=>$this->registrationfees(),
            'currencylist'=>$this->getcurrencylist(),
            'customertypelist'=>$this->getcustomertypelist(),
            'employmentlocationlist'=>$this->getemploymentlocationlist(),
        ]);
    }
}
