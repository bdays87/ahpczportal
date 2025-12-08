<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Interfaces\iotherapplicationInterface;
use App\Interfaces\iapplicationsessionInterface;
use Illuminate\Support\Facades\Storage;
use Mary\Traits\Toast;
class Otherapplicationsapproval extends Component
{
    use Toast;
    public $breadcrumbs=[];
    public $search;
    public $status;
    public $decisionstatus;
    public $comment;
    public $uuid;
    public $decisionmodal = false;
    public $year;
    protected $repo;
    protected $sessionrepo;
    
    public $otherapplication;
    public $invoice;
    public $uploaddocuments=[];
    public $viewmodal = false;
    public $documenturl;
    public $documentview = false;
    public function boot(iotherapplicationInterface $repo,iapplicationsessionInterface $sessionrepo){
        $this->repo = $repo;
        $this->sessionrepo = $sessionrepo;
    }
   

    public function getotherapplications(){
        return $this->repo->getotherapplications($this->search,$this->status,$this->year);
    }
    public function mount(){
        $this->year = date('Y');
        $this->status = 'AWAITING';
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Other Applications Approvals'
            ],
        ];
    }
    public function getapplicationsessions(){
        return $this->sessionrepo->getAll();
    }

    public function getotherapplication($uuid){
        $this->uuid = $uuid;
        $payload = $this->repo->getbyuuid($uuid);
        $this->otherapplication = $payload["data"];
        $this->invoice = $payload["invoice"];
        $this->uploaddocuments = $payload["uploaddocuments"];
        $this->viewmodal = true;
    }

    public function opendecisionmodal(){
      
        $this->decisionmodal = true;
    }
    public function makedecision(){
        $this->validate([
            'decisionstatus' => 'required',
            'comment' => 'required if:decisionstatus,==,REJECTED',
        ]);
        $response = $this->repo->makedecision($this->uuid,$this->decisionstatus,$this->comment);
        if($response['status'] == "success"){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
        $this->decisionmodal = false;
    }

    public function viewdocument($file){
        $this->documenturl = Storage::url($file);
        $this->documentview = true;
    }
    public function render()
    {
        return view('livewire.admin.otherapplicationsapproval',[
            'otherapplications' => $this->getotherapplications(),
            'applicationsessions' => $this->getapplicationsessions()
        ]);
    } 
}
