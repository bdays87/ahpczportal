<?php

namespace App\Livewire;

use Livewire\Component;
use App\Interfaces\iotherapplicationInterface;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
class Showotherapplicant extends Component
{
    use Toast,WithFileUploads;
    public $uuid;
    public $otherapplication;
    public $invoice;
    public $uploaddocuments=[];
    public $breadcrumbs = [];
    public $otherservicedocument_id;
    public $document_id;
    public $uploadmodal = false;
    public $file;
    public $documenturl;
    public $documentview = false;
    public function boot(iotherapplicationInterface $otherapplicationrepo){
        $this->otherapplicationrepo = $otherapplicationrepo;
    }
    public function mount($uuid){
        $this->uuid = $uuid;
   
        $this->getotherapplication();
    }
    public function getotherapplication(){
        $payload = $this->otherapplicationrepo->getbyuuid($this->uuid);
    
        $this->otherapplication = $payload["data"];
        $this->invoice = $payload["invoice"];
        $this->uploaddocuments = $payload["uploaddocuments"];

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
                    'link' => route('customers.show',$this->otherapplication->customer->uuid),
                ],
                [
                    'label' => 'Other Application'
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
                    'label' => 'Other Application'
                ],
            ];
        }
    }
    public function openuploaddocument($otherservicedocument_id,$document_id){
        $this->otherservicedocument_id = $otherservicedocument_id;
        $this->document_id = $document_id;
        $this->uploadmodal = true;
    }
    public function uploaddocument(){
        $this->validate([
            'file'=>'required|file|mimes:pdf',
        ]);
        $path = $this->file->store('documents','public');
        $response = $this->otherapplicationrepo->createdocument([
            'otherapplication_id'=>$this->otherapplication->id,
            'otherservicedocument_id'=>$this->otherservicedocument_id,
            'file'=>$path,
        ]);
        if($response["status"] == "success"){
        $this->uploadmodal = false;
        $this->getotherapplication();
        $this->success('Document uploaded successfully');
        }else{
            $this->error($response["message"]);
        }
    }
    public function removedocument($id){
        $response = $this->otherapplicationrepo->deletedocument($id);
        if($response["status"] == "success"){
            $this->success($response["message"]);
            $this->getotherapplication();
        }else{
            $this->error($response["message"]);
        }
    }
    public function viewdocument($file){
      
        $url = Storage::url($file);
     
        $this->documenturl = $url;
        $this->documentview = true;
    }
    public function render()
    {
        return view('livewire.showotherapplicant');
    }
}
