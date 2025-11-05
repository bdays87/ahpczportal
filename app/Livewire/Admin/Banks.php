<?php

namespace App\Livewire\Admin;

use App\Interfaces\ibankInterface;
use App\Interfaces\icurrencyInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Banks extends Component
{
    use Toast;
    public $search;
    public $name;
    public $id;
    public $modal;
    public $accountmodal=false;
    public $bank;
    public $currency_id;
    public $account_number;
    public $account_id;
    public $breadcrumbs=[];


    protected $bankRepository;
    protected $currencyRepository;

    public function boot(ibankInterface $bankRepository, icurrencyInterface $currencyRepository)
    {
        $this->bankRepository = $bankRepository;
        $this->currencyRepository = $currencyRepository;
    }
    public function mount(){
        $this->breadcrumbs=[
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Banks'
            ],
        ];
    }

    public function getcurrencylist()
    {
        return $this->currencyRepository->getAll("active");
    }

    public function getbanklist()
    {
        return $this->bankRepository->getAll($this->search);
    }

    public function save(){
        $this->validate([
            "name"=>"required"
        ]);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }
        $this->reset(['name','id']);
       
      
    }

    public function create(){

        $response = $this->bankRepository->create([
            "name"=>$this->name
        ]);
        if($response['status']=='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }

    }
   public function update(){

        $response = $this->bankRepository->update($this->id, [
            "name"=>$this->name
        ]);
        if($response['status']=='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
   }

   public function delete($id){
        $response = $this->bankRepository->delete($id);
        if($response['status']=='success'){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
   }

   public function edit($id){
        $this->id = $id;
        $bank= $this->bankRepository->get($id);
        $this->name = $bank->name;
        $this->modal = true;
   }

   public function getbank($id){
    $this->bank= $this->bankRepository->getaccounts($id);
    $this->accountmodal=true;
   }

   public function saveaccount(){
    $this->validate([
        "account_number"=>"required",
        "currency_id"=>"required"
    ]);
    if($this->account_id){
        $this->updateaccount();
    }else{
        $this->createaccount();
    }
    $this->reset(['account_number','currency_id']);
   }

   public function createaccount(){
    $response = $this->bankRepository->createaccount([
        "account_number"=>$this->account_number,
        "currency_id"=>$this->currency_id,
        "bank_id"=>$this->bank->id
    ]);
    if($response['status']=='success'){
        $this->success($response['message']);
    }else{
        $this->error($response['message']);
    }
   

   }

   public function updatedaccount(){
    $response = $this->bankRepository->updateaccount($this->account_id, [
        "account_number"=>$this->account_number,
        "currency_id"=>$this->currency_id,
    ]);
    if($response['status']=='success'){
        $this->success($response['message']);
    }else{
        $this->error($response['message']);
    }
   }

   public function deleteaccount($id){
    $response = $this->bankRepository->deleteaccount($id);
    if($response['status']=='success'){
        $this->success($response['message']);
    }else{
        $this->error($response['message']);
    }
   }

   public function editaccount($id){
    $this->account_id = $id;
    $account= $this->bankRepository->getaccount($id);
    $this->account_number = $account->account_number;
    $this->currency_id = $account->currency_id;
    $this->accountmodal = true;
   }

   public function headers():array{
    return [
       ['key'=>'name','label'=>'Bank Name'],
       ['key'=>'action','label'=>'']
    ];
   }

    public function render()
    {
        return view('livewire.admin.banks',[
            'banks'=>$this->getbanklist(),
            'headers'=>$this->headers(),
            'currencies'=>$this->getcurrencylist()

        ]);
    }
}
