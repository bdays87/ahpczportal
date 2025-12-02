<?php

namespace App\Livewire\Admin;

use App\Interfaces\icustomerprofessionInterface;
use App\Interfaces\iregistertypeInterface;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Mary\Traits\Toast;

class Viewregistration extends Component
{
    use Toast;
    public $uuid;
    protected $repo;
    public $customerprofession;
    public $customerprofession_id;  
    public $uploaddocuments=[];
    public $documenturl;
    public bool $documentview =false;
    public $breadcrumbs = []; 
    public bool $decisionmodal = false;
    public $comment;
    public $type="Registration";
    public $commentid;
    public $status;
    public $registertype_id;
    protected $registertyperrepo;
    public function boot(icustomerprofessionInterface $repo,iregistertypeInterface $registertyperrepo)
    {
        $this->repo = $repo;
        $this->registertyperrepo = $registertyperrepo;
    }

    public function mount($uuid)
    {
        $this->uuid = $uuid;
        $this->customerprofession = null;
        $this->uploaddocuments = [];
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Assessments',
                'link' => route('registrationapprovals.index'),
            ],
            [
                'label' => 'View Registration'
            ],
        ];
        $this->getcustomerprofession();
    }

    public function getcustomerprofession()
    {
      $data = $this->repo->getbyuuid($this->uuid);
    
      $this->customerprofession = $data["customerprofession"];
      $this->uploaddocuments = $data["uploaddocuments"];
    }

    public function getregistertypes(){
        return $this->registertyperrepo->getAll();
    }

    public function viewqualification($id)
    {
        $qualifications = $this->customerprofession->qualifications->where("id",$id)->first();
        $this->documenturl = Storage::url($qualifications->file);
        $this->documentview = true;
    }

    public function viewdocument($id)
    {
        $documents = $this->customerprofession->documents->where("id",$id)->first();
        $this->documenturl = Storage::url($documents->file);
        $this->documentview = true;
    }

    public function opencommentmodal($id){
        $this->customerprofession_id = $id;
        $this->decisionmodal = true;
    }

    public function savecomment(){
        $this->validate([
            'comment' => 'required',
            'type' => 'required',
            'registertype_id' => 'required',
            'status' => 'required',
        ]);
       $response = $this->repo->addcomment([
            'customerprofession_id' => $this->customerprofession->id,
            'comment' => $this->comment,
            'commenttype' => $this->type,
            'registertype_id' => $this->registertype_id,
            'status' => $this->status,
        ]);
        if($response['status'] == "success"){
            $this->success($response['message']);
        }else{
            $this->error($response['message']);
        }
        $this->decisionmodal = false;
    }

    public function closemodal(){
        $this->decisionmodal = false;
    }

    public function render()
    {
        return view('livewire.admin.viewregistration',[
            'registertypes' => $this->getregistertypes(),
        ]); 
    }
}
