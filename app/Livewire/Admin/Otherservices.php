<?php

namespace App\Livewire\Admin;

use App\Interfaces\icurrencyInterface;
use App\Interfaces\idocumentInterface;
use App\Interfaces\iotherserviceInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Otherservices extends Component
{
    use Toast;
    public $search;
    public $documentsearch;
    public $name;
    public $currency_id;
    public $amount;
    public $generalledger;
    public $requireapproval;
    public $expiretype;
    public $generatecertificate;
    public $id;
    public $modal = false;
    public $documentmodal = false;
    public $breadcrumbs = [];
    public $otherservice = null;
    public $document_id;

    protected $repository;
    protected $currencyrepository;
    protected $documentrepository;
  

    public function boot(iotherserviceInterface $repository,icurrencyInterface $currencyrepository,idocumentInterface $documentrepository){
        $this->repository = $repository;
        $this->currencyrepository = $currencyrepository;
        $this->documentrepository = $documentrepository;
    }

    public function mount(){
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Other Services'
            ],
        ];
    }

    public function getdocuments(){
        return $this->documentrepository->getAll($this->documentsearch);
    }

    public function getcurrencies(){
        return $this->currencyrepository->getAll("active");
    }
    public function getotherservices(){
        return $this->repository->getAll($this->search);
    }

   
    public function save(){
        $this->validate([
            "name"=>"required",
            "currency_id"=>"required",
            "amount"=>"required",
            "generalledger"=>"required",
            "requireapproval"=>"required",
            "expiretype"=>"required",
            "generatecertificate"=>"required",
        ]);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }
        $this->reset([
            "name",
            "currency_id",
            "amount",
            "generalledger",
            "requireapproval",
            "expiretype",
            "generatecertificate",
            "id"
            ]);
    }

    public function create(){
      $response = $this->repository->create([
        "name"=>$this->name,
        "currency_id"=>$this->currency_id,
        "amount"=>$this->amount,
        "generalledger"=>$this->generalledger,
        "requireapproval"=>$this->requireapproval,
        "expiretype"=>$this->expiretype,
        "generatecertificate"=>$this->generatecertificate,
      ]);
      if($response["status"]=="success"){
        $this->success($response["message"]);
      }else{
        $this->error($response["message"]);
      }
    }
    public function update(){
      $response = $this->repository->update($this->id, [
        "name"=>$this->name,
        "currency_id"=>$this->currency_id,
        "amount"=>$this->amount,
        "generalledger"=>$this->generalledger,
        "requireapproval"=>$this->requireapproval,
        "expiretype"=>$this->expiretype,
        "generatecertificate"=>$this->generatecertificate,
      ]);
      if($response["status"]=="success"){
        $this->success($response["message"]);
      }else{
        $this->error($response["message"]);
      }
    }
    public function delete($id){
      $response = $this->repository->delete($id);
      if($response["status"]=="success"){
        $this->success($response["message"]);
      }else{
        $this->error($response["message"]);
      }
    }

    public function edit($id){
        $this->id = $id;
        $this->modal = true;
        $data = $this->repository->get($id);
        $this->name = $data->name;
        $this->currency_id = $data->currency_id;
        $this->amount = $data->amount;
        $this->generalledger = $data->generalledger;
        $this->requireapproval = $data->requireapproval;
        $this->expiretype = $data->expiretype;
        $this->generatecertificate = $data->generatecertificate;
    }

    public function getotherservice($id){
        $this->otherservice = $this->repository->getdocuments($id);
        $this->documentmodal = true;
    }

    public function assignDocument(){
      $this->validate([
        "document_id"=>"required",
      ]);
        $response = $this->repository->createdocument($this->otherservice->id, $this->document_id);
        if($response["status"]=="success"){
            $this->success($response["message"]);
        }else{
            $this->error($response["message"]);
        }
    }
    public function unassignDocument($document_id){
      $response = $this->repository->deletedocument($this->otherservice->id, $document_id);
      if($response["status"]=="success"){
          $this->success($response["message"]);
      }else{
          $this->error($response["message"]);
      }
    }

    public function headers():array{
        return [
            ["key"=>"name","label"=>"Name"],
            ["key"=>"currency.name","label"=>"Currency"],
            ["key"=>"amount","label"=>"Amount"],
            ["key"=>"generalledger","label"=>"General ledger"],
            ["key"=>"requireapproval","label"=>"Require approval"],
            ["key"=>"expiretype","label"=>"Expire type"],
            ["key"=>"generatecertificate","label"=>"Generate certificate"],
        ];
    }

    
    public function render()
    {
        return view('livewire.admin.otherservices',[
            "headers"=>$this->headers(),
            "otherservices"=>$this->getotherservices(),
            "currencies"=>$this->getcurrencies(),
            "documents"=>$this->getdocuments()
        ]);
    }
}
