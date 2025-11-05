<?php

namespace App\Livewire\Newapplications\Practitioners;

use App\Interfaces\icustomerprofessionInterface;
use App\Interfaces\iqualificationcategoryInterface;
use App\Interfaces\iqualificationlevelInterface;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class Qualificationscapture extends Component
{
    use WithFileUploads,Toast;
    public $uuid;
    public  $breadcrumbs=[];
    public $customerprofession_id;
    public $customerprofessionqualification_id;
    public  $qualificationcategory_id;
    public $qualificationlevel_id;
    public $institution;
    public $year;
    public $qualificationfile;
    public $step = 2;
    public $qualifications;
    public $qualificationsmodal = false;
    public $name;
    public $qualificationmodal = false;
    
    protected $customerprofessionrepo;
    protected $qualificationcategoryrepo;
    protected $qualificationlevelrepo;
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

    public function boot(icustomerprofessionInterface $customerprofessionrepo,iqualificationcategoryInterface $qualificationcategoryrepo,iqualificationlevelInterface $qualificationlevelrepo){
        $this->customerprofessionrepo = $customerprofessionrepo;
        $this->qualificationcategoryrepo = $qualificationcategoryrepo;
        $this->qualificationlevelrepo = $qualificationlevelrepo;
    }

    public function getcustomerprofession(){
        $payload= $this->customerprofessionrepo->getbyuuid($this->uuid);
       
        if($payload["customerprofession"] != null){
            $this->customerprofession_id = $payload["customerprofession"]["id"];
        }
      
    
        return $payload;
     }
     public function getqualificationcategories(){
         return $this->qualificationcategoryrepo->getAll();
     }
     public function getqualificationlevels(){
         return $this->qualificationlevelrepo->getAll();
     }

     public  function savequalification(){
        $this->validate([
            "name"=>"required",
            "qualificationcategory_id"=>"required",
            "qualificationlevel_id"=>"required",
            "institution"=>"required",
            "year"=>"required",
            "qualificationfile"=>"required"
        ]);
        if($this->customerprofessionqualification_id){
            $this->updatequalification();
        }else{
            $this->createqualification();
        }
        $this->reset(['name','qualificationcategory_id','qualificationlevel_id','institution','year','qualificationfile','customerprofessionqualification_id']);
       
  
    }
    public function createqualification(){
        $path = $this->qualificationfile->store('documents','public');
        $response = $this->customerprofessionrepo->addqualification([
            "name"=>$this->name,
            "qualificationcategory_id"=>$this->qualificationcategory_id,
            "qualificationlevel_id"=>$this->qualificationlevel_id,
            "institution"=>$this->institution,
            "year"=>$this->year,
            "file"=>$path,
            "customerprofession_id"=>$this->customerprofession_id
        ]);
        if($response["status"] == "success"){
            $this->success($response["message"]);
        }else{
            $this->error($response["message"]);
        }
    }

    public function updatequalification(){
        $path = $this->qualificationfile->store('documents','public');
        $response = $this->customerprofessionrepo->updatequalification($this->customerprofessionqualification_id,[
            "name"=>$this->name,
            "qualificationcategory_id"=>$this->qualificationcategory_id,
            "qualificationlevel_id"=>$this->qualificationlevel_id,
            "institution"=>$this->institution,
            "year"=>$this->year,
            "file"=>$path,
            "customerprofession_id"=>$this->customerprofession_id
        ]);
        if($response["status"] == "success"){
            $this->success($response["message"]);
        }else{
            $this->error($response["message"]);
        }
    }
    public function removeQualification($id){
        $response = $this->customerprofessionrepo->removequalification($id);
        if($response["status"] == "success"){
            $this->success($response["message"]);
        }else{
            $this->error($response["message"]);
        }
    }
    public function editQualification($id){
        $qualification = $this->customerprofessionrepo->getqualification($id);
        $this->customerprofessionqualification_id = $id;
        $this->name = $qualification->name;
        $this->qualificationcategory_id = $qualification->qualificationcategory_id;
        $this->qualificationlevel_id = $qualification->qualificationlevel_id;
        $this->institution = $qualification->institution;
        $this->year = $qualification->year;
        $this->qualificationfile = $qualification->file;
        $this->qualificationmodal = true;
    }

    public function nextstep(){
       $response= $this->customerprofessionrepo->generatepractitionerinvoice($this->customerprofession_id);
       if($response["status"] == "success"){
           $this->redirect(route("newapplications.practitioners.assessmentinvoicing",[$this->uuid]));
       }else{
           $this->error($response["message"]);
       }
    }

    public function render()
    {
      
        return view('livewire.newapplications.practitioners.qualificationscapture',[
            "customerprofession"=>$this->getcustomerprofession()["customerprofession"],
            "categories"=>$this->getqualificationcategories(),
            "levels"=>$this->getqualificationlevels(),
        ]);
    }
}
