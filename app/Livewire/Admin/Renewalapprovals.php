<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Interfaces\icustomerapplicationInterface;
use App\Interfaces\iapplicationsessionInterface;
use App\Interfaces\iapplicationtypeInterface;
use Illuminate\Support\Facades\Storage;
use Mary\Traits\Toast;
class Renewalapprovals extends Component
{
    use Toast;
    public $year;
    public $status;
    public $search;
    public $decisionstatus;
    public $comment;
    public $applicationtype_id;
    public $application=null;
    public $selectedTab = 'cdppoints';
    public bool $viewmodal = false;
    public $documenturl;
    public $documentview = false;
    protected $repo;
 protected $sessionrepo;
 protected $applicationtyperepo;
    public $breadcrumbs=[];
    public function boot(icustomerapplicationInterface $repo,iapplicationsessionInterface $sessionrepo,iapplicationtypeInterface $applicationtyperepo   ){
        $this->repo = $repo;
        $this->sessionrepo = $sessionrepo;
        $this->applicationtyperepo = $applicationtyperepo;
    }
    public function mount(){
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Renewal Approvals'
            ],
        ];
        $this->year = date('Y');
        $this->status = 'AWAITING';
    }
    public function getapplicationsessions(){
        return $this->sessionrepo->getAll();
    }
    public function getcustomerapplications(){
        return $this->repo->retrieve($this->year,$this->status,$this->search,$this->applicationtype_id);
    }
    public function getapplicationtypes(){
        return $this->applicationtyperepo->getAll()->where('name','!=','NEW');
    }
    public function getapplication($uuid){
        $this->application = $this->repo->getbyuuid($uuid);
        $this->viewmodal = true;
    }
    public function viewdocument($path){
        $this->documenturl = Storage::url($path);
        $this->documentview = true;
    }
    public function savedecision(){
        $this->validate([
            'decisionstatus' => 'required',
            'comment' => 'required',
        ]);
    
        $response = $this->repo->makedecision([
            'id' => $this->application['customerapplication']->id,
            'status' => $this->decisionstatus,
            'comment' => $this->comment,
        ]);
        if($response['status'] == "success"){
            $this->success($response['message']);
            $this->viewmodal = false;
        }else{
            $this->error($response['message']);
        }
    }
   



    public function render()
    {
        return view('livewire.admin.renewalapprovals',[
            'customerapplications'=>$this->getcustomerapplications(),
            'applicationsessions'=>$this->getapplicationsessions(),
            'applicationtypes'=>$this->getapplicationtypes(),
        ]);
    }
}
