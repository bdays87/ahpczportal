<?php

namespace App\Livewire\Admin\Components;

use App\Interfaces\icurrencyInterface;
use App\Interfaces\iregistertypeInterface;
use App\Interfaces\irenewalInterface;
use App\Interfaces\itireInterface;
use App\Interfaces\iapplicationtypeInterface;
use App\RenewalType;
use Livewire\Component;
use Mary\Traits\Toast;

class Renewalfees extends Component
{
    use Toast;
    
    public $tire_id;
    public $currency_id;
    public $registertype_id;
    public $applicationtype_id;
    public $generalledger;
    public $amount;
    public $modal = false;
    public $breadcrumbs = [];
    public $id;

    protected $tirerepo;
    protected $currencyrepo;
    protected $registertyperepo;
    protected $renewalrepo;
    protected $applicationtyperepo;

    public function boot(
        itireInterface $tireInterface,
        icurrencyInterface $currencyInterface,
        iregistertypeInterface $registertypeInterface,
        irenewalInterface $renewalInterface,
        iapplicationtypeInterface $applicationtypeInterface
    ) {
        $this->tirerepo = $tireInterface;
        $this->currencyrepo = $currencyInterface;
        $this->registertyperepo = $registertypeInterface;
        $this->renewalrepo = $renewalInterface;
        $this->applicationtyperepo = $applicationtypeInterface;
    }

   

    public function getTireList()
    {
        return $this->tirerepo->getAll();
    }

    public function getCurrencyList()
    {
        return $this->currencyrepo->getAll('active');
    }

    public function getRegistertypeList()
    {
        return $this->registertyperepo->getAll();
    }

    public function renewalfees()
    {
        return $this->renewalrepo->getfees();
    }

    public function getTypeOptions()
    {
        return RenewalType::options();
    }
    public function getApplicationtypeList()
    {
        return $this->applicationtyperepo->getAll()->where('name','!=','NEW');
    }

    public function save()
    {
        $this->validate([
            'tire_id' => 'required',
            'currency_id' => 'required',
            'registertype_id' => 'required',
            'applicationtype_id' => 'required',
            'generalledger' => 'required',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($this->id) {
            $this->update();
        } else {
            $this->create();
        }
        $this->reset(['id', 'tire_id', 'currency_id', 'registertype_id', 'applicationtype_id', 'generalledger', 'amount']);
    }

    public function create()
    {
        $response = $this->renewalrepo->createfee([
            'tire_id' => $this->tire_id,
            'currency_id' => $this->currency_id,
            'registertype_id' => $this->registertype_id,
            'applicationtype_id' => $this->applicationtype_id,
            'generalledger' => $this->generalledger,
            'amount' => $this->amount,
        ]);

        if ($response['success']) {
            $this->success($response['message']);
            $this->modal = false;
        } else {
            $this->error($response['message']);
        }
    }

    public function update()
    {
        $response = $this->renewalrepo->updatefee($this->id, [
            'tire_id' => $this->tire_id,
            'currency_id' => $this->currency_id,
            'registertype_id' => $this->registertype_id,
            'applicationtype_id' => $this->applicationtype_id,
            'generalledger' => $this->generalledger,
            'amount' => $this->amount,
        ]);

        if ($response['success']) {
            $this->success($response['message']);
            $this->modal = false;
        } else {
            $this->error($response['message']);
        }
    }

    public function delete($id)
    {
        $response = $this->renewalrepo->deletefee($id);
        if ($response['success']) {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }

    public function edit($id)
    {
        $this->id = $id;
        $record = $this->renewalrepo->getfee($id);
        $this->tire_id = $record->tire_id;
        $this->currency_id = $record->currency_id;
        $this->registertype_id = $record->registertype_id;
        $this->applicationtype_id = $record->applicationtype_id;
        $this->generalledger = $record->generalledger;
        $this->amount = $record->amount;
        $this->modal = true;
    }

    public function headers(): array
    {
        return [
            ['key' => 'tire.name', 'label' => 'Tire'],
            ['key' => 'currency.name', 'label' => 'Currency'],
            ['key' => 'registertype.name', 'label' => 'Register Type'],
            ['key' => 'applicationtype.name', 'label' => 'Type'],
            ['key' => 'generalledger', 'label' => 'General Ledger'],
            ['key' => 'amount', 'label' => 'Amount'],
            ['key' => 'action', 'label' => ''],
        ];
    }

    public function render()
    {
        return view('livewire.admin.components.renewalfees', [
            'headers' => $this->headers(),
            'records' => $this->renewalfees(),
            'tirelist' => $this->getTireList(),
            'currencylist' => $this->getCurrencyList(),
            'registertypelist' => $this->getRegistertypeList(),
            'typeoptions' => $this->getTypeOptions(),
            'applicationtypelist' => $this->getApplicationtypeList(),
        ]);
    }
}
