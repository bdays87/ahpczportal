<?php

namespace App\Livewire\Admin;

use App\Interfaces\iapplicationfeeInterface;
use App\Interfaces\icurrencyInterface;
use App\Interfaces\iemploymentlocationInterface;
use App\Interfaces\iqualificationcategoryInterface;
use App\Interfaces\iregistertypeInterface;
use App\Interfaces\itireInterface;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Applicationfees extends Component
{
    use Toast;
    use WithPagination;
    public $year;
    public $tire_id;
    public $currency_id;
    public $registertype_id;
    public $employmentlocation_id;
    public $name;
    public $generalledger;
    public $amount;
    public $id;
    public $modal=false;
    public $breadcrumbs=[];

    protected $applicationfeerepo;
    protected $tirerepo;
    protected $currencyrepo;
    protected $registertyperepo;
    protected $employmentlocationrepo;
    public function boot(iapplicationfeeInterface $applicationfeerepo, itireInterface $tirerepo, icurrencyInterface $currencyrepo, iregistertypeInterface $registertyperepo, iemploymentlocationInterface $employmentlocationrepo)
    {
        $this->applicationfeerepo = $applicationfeerepo;
        $this->tirerepo = $tirerepo;
        $this->currencyrepo = $currencyrepo;
        $this->registertyperepo = $registertyperepo;
        $this->employmentlocationrepo = $employmentlocationrepo;
    }

    public function mount(){
        $this->year = date("Y");
        $this->breadcrumbs=[
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Application Fees'
            ],
        ];
    }


    public function getcurrencies()
    {
        return $this->currencyrepo->getAll("active");
    }
    
    public function getregistertypes()
    {
        return $this->registertyperepo->getAll();
    }
    
    public function getemploymentlocations()
    {
        return $this->employmentlocationrepo->getAll();
    }
    
    public function gettires()
    {
        return $this->tirerepo->getAll();
    }

    public function getapplicationfees()
    {
        return $this->applicationfeerepo->getAll($this->year);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'tire_id' => 'required',
            'registertype_id' => 'required',
            'employmentlocation_id' => 'required',
            'currency_id' => 'required',
            'amount' => 'required',
            'year' => 'required',
            'generalledger' => 'required',
        ]);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }
        $this->reset(['name','tire_id','registertype_id','employmentlocation_id','currency_id','amount','generalledger','id']);
    }

    public function create()
    {
        $response = $this->applicationfeerepo->create([
            'name' => $this->name,
            'tire_id' => $this->tire_id,
            'registertype_id' => $this->registertype_id,
            'employmentlocation_id' => $this->employmentlocation_id,
            'currency_id' => $this->currency_id,
            'amount' => $this->amount,
            'year' => $this->year,
            'generalledger' => $this->generalledger,
        ]);
        if ($response["status"] == "success") {
            $this->success($response["message"]);
        } else{
            $this->error($response["message"]);
        }
    }
    
    public function update()
    {
        $response = $this->applicationfeerepo->update($this->id, [
            'name' => $this->name,
            'tire_id' => $this->tire_id,
            'registertype_id' => $this->registertype_id,
            'employmentlocation_id' => $this->employmentlocation_id,
            'currency_id' => $this->currency_id,
            'amount' => $this->amount,
            'year' => $this->year,
            'generalledger' => $this->generalledger,
        ]);
        if ($response["status"] == "success") {
            $this->success($response["message"]);
        } else{
            $this->error($response["message"]);
        }
    }
    
    public function delete($id)
    {
        $response = $this->applicationfeerepo->delete($id);
        if ($response['status'] == "success") {
            $this->success($response['message']);
        } else{
            $this->error($response['message']);
        }
    }
    
    public function edit($id)
    {
        $this->id = $id;
       
       $fee = $this->applicationfeerepo->get($id);
       $this->tire_id = $fee->tire_id;
       $this->name = $fee->name;
       $this->registertype_id = $fee->registertype_id;
       $this->employmentlocation_id = $fee->employmentlocation_id;
       $this->currency_id = $fee->currency_id;
       $this->amount = $fee->amount;
       $this->year = $fee->year;
       $this->generalledger = $fee->generalledger;
       $this->modal = true;
    }
    
    public function headers():array{
        return [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'tire.name', 'label' => 'Tire'],
            ['key' => 'registertype.name', 'label' => 'Register Type'],
            ['key' => 'employmentlocation.name', 'label' => 'Employment Location'],
            ['key' => 'currency.name', 'label' => 'Currency'],
            ['key' => 'amount', 'label' => 'Amount'],
            ['key' => 'year', 'label' => 'Year'],
            ['key' => 'generalledger', 'label' => 'General Ledger']
        ];
    }
    public function render()
    {
        return view('livewire.admin.applicationfees',[
            'applicationfees' => $this->getapplicationfees(),
            'headers' => $this->headers(),
            'tires' => $this->gettires(),
            'registertypes' => $this->getregistertypes(),
            'employmentlocations' => $this->getemploymentlocations(),
            'currencies' => $this->getcurrencies()
        ]);
    }
}
