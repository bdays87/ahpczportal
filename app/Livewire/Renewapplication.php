<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\icustomerprofessionInterface;
use Mary\Traits\Toast;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
class Renewapplication extends Component
{
    use Toast,WithFileUploads;
    public $breadcrumbs = [];
    public $uuid;
    protected $customerprofessionrepo;
    public $application;
    public $uploaddocuments;
    public $invoice;
    public $file;
    public $document_id;
    public $documenturl;
    public $documentview = false;
    public $uploadmodal = false;
    public function boot(icustomerprofessionInterface $customerprofessionrepo){
        $this->customerprofessionrepo = $customerprofessionrepo;
    }
    public function mount($uuid){
        $this->uuid = $uuid;
        $this->application = null;
        $this->uploaddocuments = [];
        $this->invoice = null;
        $this->getapplication();
        if(Auth::user()->accounttype_id == 1){
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
    #[On('proofofpaymentsubmitted')]
    public function getapplication(){
        $payload = $this->customerprofessionrepo->getapplicationbyuuid($this->uuid);
        $this->application = $payload["data"];
        $this->uploaddocuments = $payload["uploaddocuments"];
        $this->invoice = $payload["invoice"];
    }
    public function openuploaddocument($document_id){
        $this->document_id = $document_id;
        $this->uploadmodal = true;
    }
    public function uploadDocument(){
        $this->validate([
            "file"=>"required"
        ]);
        $path = $this->file->store('documents','public');
        $response = $this->customerprofessionrepo->uploadrenewaldocuments(["document_id"=>$this->document_id,"status"=>"PENDING","file"=>$path,"customerapplication_id"=>$this->application->id]);
        if($response["status"] == "success"){
            $this->uploadmodal = false;
            $this->success($response["message"]);
            $this->getapplication();
        }else{
            $this->error($response["message"]);
        }
    }

    public function removedocument($document_id){
        $response = $this->customerprofessionrepo->removerenewaldocument($document_id,$this->application->id);
        if($response["status"] == "success"){
            $this->success($response["message"]);
            $this->getapplication();
        }else{
            $this->error($response["message"]);
        }
    }

    public function viewdocument($path){
       
       $url = Storage::url($path);
       $this->documenturl = $url;
       $this->documentview = true;
    }

    public function render()
    {
        return view('livewire.renewapplication');
    }
}
