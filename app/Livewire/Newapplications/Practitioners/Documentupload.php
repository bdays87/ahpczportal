<?php

namespace App\Livewire\Newapplications\Practitioners;

use App\Interfaces\icustomerprofessionInterface;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class Documentupload extends Component
{
    use WithFileUploads,Toast;
    public $uuid;
    public  $breadcrumbs=[];
    public $customerprofession_id;
    public $step = 1;
    public $documents = [];    
    public $uploadmodal = false;
    public $file;
    public $document_id;
    public bool $verified = false;
    protected $customerprofessionrepo;
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

    public function boot(icustomerprofessionInterface $customerprofessionrepo){
        $this->customerprofessionrepo = $customerprofessionrepo;
    }

    public function getcustomerprofession(){
        $payload= $this->customerprofessionrepo->getbyuuid($this->uuid);
        $this->customerprofession_id = $payload["customerprofession"]["id"];
    
        return $payload;
     }

     public function openuploadmodal($document_id){
        $this->document_id = $document_id;
        $this->uploadmodal = true;
    }

     public function removeDocument($document_id){
        $this->customerprofessionrepo->removedocument($document_id,$this->customerprofession_id);
    }
    public function uploadDocument(){
        $this->validate([
            "file"=>"required"
        ]);
        $path = $this->file->store('documents','public');
       $response = $this->customerprofessionrepo->uploadDocument([
            "document_id"=>$this->document_id,
            "file"=>$path,
            "verified"=>$this->verified,
            "customerprofession_id"=>$this->customerprofession_id
        ]);
        if($response["status"] == "success"){
            $this->uploadmodal = false;
            $this->success($response["message"]);
        }else{
            $this->error($response["message"]);
        }
    }
    public function render()
    {
        return view('livewire.newapplications.practitioners.documentupload',[
            "customerprofession"=>$this->getcustomerprofession()["customerprofession"],
            "uploaddocuments"=>$this->getcustomerprofession()["uploaddocuments"],
        ]);
    }
}
