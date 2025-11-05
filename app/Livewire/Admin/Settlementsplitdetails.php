<?php

namespace App\Livewire\Admin;

use App\Interfaces\icurrencyInterface;
use App\Interfaces\iemploymentlocationInterface;
use App\Interfaces\iqualificationcategoryInterface;
use App\Interfaces\isettlementsplitInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Settlementsplitdetails extends Component
{
    use Toast;
    protected  $employmentlocationrepo;
    protected  $repo;
    protected $currencyrepo;
    public $type;
    public $currency_id;
    public $employmentlocation_id;
    public $percentage;
    public $id;
    public $modal = false;
    public $breadcrumbs=[];
    public function boot(isettlementsplitInterface $repo, iemploymentlocationInterface $employmentlocationrepo, icurrencyInterface $currencyrepo){
        $this->repo = $repo;
        $this->employmentlocationrepo = $employmentlocationrepo;
        $this->currencyrepo = $currencyrepo;
    }
    public function mount(){
        $this->breadcrumbs =[ [
            'label' => 'Dashboard',
            'icon' => 'o-home',
            'link' => route('dashboard'),
        ],
        [
            'label' => 'Settlement Split Details'
        ],
    ];
    }

    public function getcurrencylist(){
        return $this->currencyrepo->getAll('active');
    }
    public function getemploymentlocationlist(){
        return $this->employmentlocationrepo->getAll();
    }
    public function getsettlementsplits(){
        return $this->repo->getAll();
    }

    public function save(){
        $this->validate([
            'type'=>'required',
            'currency_id'=>'required',
            'employmentlocation_id'=>'required',
            'percentage'=>'required'
        ]);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }

        $this->reset(['type', 'currency_id', 'employmentlocation_id', 'percentage','id']);

    }

    public function create(){
     $response = $this->repo->create([
        'type'=>$this->type,
        'currency_id'=>$this->currency_id,
        'employmentlocation_id'=>$this->employmentlocation_id,
        'percentage'=>$this->percentage
     ]);
     if($response['status']=='success'){
        $this->success($response['message']);
     }else{
        $this->error($response['message']);
     }

    }

    public function update(){
     $response = $this->repo->update($this->id, [
        'type'=>$this->type,
        'currency_id'=>$this->currency_id,
        'employmentlocation_id'=>$this->employmentlocation_id,
        'percentage'=>$this->percentage
     ]);
     if($response['status']=='success'){
        $this->success($response['message']);
     }else{
        $this->error($response['message']);
     }
    }
    public function delete($id){
     $response = $this->repo->delete($id);
     if($response['status']=='success'){
        $this->success($response['message']);
     }else{
        $this->error($response['message']);
     }
    }

    public function edit($id){
     $settlementsplit = $this->repo->get($id);
     $this->id = $settlementsplit->id;
     $this->type = $settlementsplit->type;
     $this->currency_id = $settlementsplit->currency_id;
     $this->employmentlocation_id = $settlementsplit->employmentlocation_id;
     $this->percentage = $settlementsplit->percentage;
     $this->modal = true;
    }

    public function headers():array{
        return [
            ['key'=>'type', 'label'=>'Type'],
            ['key'=>'currency.name', 'label'=>'Currency'],
            ['key'=>'employmentlocation.name', 'label'=>'Employment Location'],
            ['key'=>'percentage', 'label'=>'Percentage'],
            ['key'=>'action', 'label'=>'']
        ];
    }
    public function render()
    {
        return view('livewire.admin.settlementsplitdetails', [
            'headers'=>$this->headers(),
            'records'=>$this->getsettlementsplits(),
            'currencies'=>$this->getcurrencylist(),
            'employmentlocations'=>$this->getemploymentlocationlist()
        ]);
    }
}
