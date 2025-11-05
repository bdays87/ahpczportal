<?php

namespace App\Livewire\Admin;

use App\Interfaces\icurrencyInterface;
use App\Interfaces\icustomerprofessionInterface;
use App\Interfaces\iqualificationcategoryInterface;
use App\Interfaces\iqualificationlevelInterface;
use App\Interfaces\istudentInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;

class Customerstudentshow extends Component
{
    use Toast,WithFileUploads;
    public $uuid;
    public  $breadcrumbs=[];
    public $qualificationmodal = false;
    public $id;
    public $customerprofession_id;
    public $institution;
    public $qualification;
    public $startyear;
    public $endyear;
    public $grade;
    public $qualificationlevel_id;
    public $uploadmodal = false;
    public $document_id;
    public $file;
    public $verified;

    public $placementmodal = false;
    public $placement_id;
    public $company;
    public $position;
    public $startdate;
    public $enddate;
    public $supervisorname;
    public $supervisorphone;
    public $supervisoremail;
    public $supervisorposition;
    public $is_supervisor_registered;
    public $regnumber;
    public $customer_id;
    protected $repo;
    protected $studentrepo;
    protected $qualificationlevelrepo;
    protected $currencyrepo;

    public function boot(icustomerprofessionInterface $repo,istudentInterface $studentrepo,iqualificationlevelInterface $qualificationlevelrepo,icurrencyInterface $currencyrepo){
        $this->repo = $repo;
        $this->studentrepo = $studentrepo;
        $this->qualificationlevelrepo = $qualificationlevelrepo;
        $this->currencyrepo = $currencyrepo;
    }
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
                'label' => 'Customer Student'
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
    public function getdata(){
        $data = $this->repo->getcustomerstudent($this->uuid);
  
        $this->customerprofession_id = $data['customerprofession']->id;
        return $data;
    }
    public function getcurrencies(){
        return $this->currencyrepo->getAll("active");
    }
    public function getqualificationlevels(){
        return $this->qualificationlevelrepo->getAll();
    }

    public function savequalification(){
        $this->validate([
            'institution'=>'required',
            'qualification'=>'required',
            'qualificationlevel_id'=>'required',
            'startyear'=>'required',
        ]);

        if($this->id){
            $this->updatequalification();
        }else{
            $this->createqualification();
        }

        $this->reset(['institution','qualification','startyear','endyear','grade','id']);
   
    }
    public function createqualification(){
        $response = $this->studentrepo->createqualification([
            'customerprofession_id'=>$this->customerprofession_id,
            'institution'=>$this->institution,
            'qualification'=>$this->qualification,
            'qualificationlevel_id'=>$this->qualificationlevel_id,
            'startyear'=>$this->startyear,
            'endyear'=>$this->endyear,
            'grade'=>$this->grade,
        ]);
        if($response['status']=='success'){
            $this->qualificationmodal = false;
            $this->success('Qualification saved successfully.');
          
        }else{
            $this->error($response['message']);
        }

    }

    public function updatequalification(){
        $response = $this->studentrepo->updatequalification($this->id, [
            'institution'=>$this->institution,
            'qualification'=>$this->qualification,
            'startyear'=>$this->startyear,
            'endyear'=>$this->endyear,
            'grade'=>$this->grade,
        ]);
        if($response['status']=='success'){
            $this->qualificationmodal = false;
            $this->success('Qualification updated successfully.');
          
        }else{
            $this->error($response['message']);
        }

    }
    public function editqualification($id){
        $this->id = $id;
        $qualification = $this->studentrepo->getqualification($id);
        $this->institution = $qualification->institution;
        $this->qualification = $qualification->qualification;
        $this->qualificationlevel_id = $qualification->qualificationlevel_id;
        $this->startyear = $qualification->startyear;
        $this->endyear = $qualification->endyear;
        $this->grade = $qualification->grade;
        $this->qualificationmodal = true;
    }

    public function deletequalification($id){
        $response = $this->studentrepo->deletequalification($id);
        if($response['status']=='success'){
            $this->success('Qualification deleted successfully.');
        }else{
            $this->error($response['message']);
        }
    }

    public function openuploadmodal($document_id){
        $this->document_id = $document_id;
        $this->uploadmodal = true;
    }
    public function uploadDocument(){
        $this->validate([
            'file'=>'required'
        ]);
        $path = $this->file->store('documents');
        $response = $this->repo->uploadDocument([ "document_id"=>$this->document_id,
        "file"=>$path,
        "verified"=>$this->verified,
        "customerprofession_id"=>$this->customerprofession_id]);
        if($response['status']=='success'){
            $this->success('Document uploaded successfully.');
        }else{
            $this->error($response['message']);
        }
        $this->reset(['file','verified']);
        $this->uploadmodal = false;
    }
    public function removeDocument($document_id){
       $response =  $this->repo->removedocument($document_id,$this->customerprofession_id);
       if($response['status']=='success'){
           $this->success('Document removed successfully.');
       }else{
           $this->error($response['message']);
       }
    }

    public function saveplacement(){  
        $this->validate([
          'company'=>'required',
          'position'=>'required',
          'startdate'=>'required',
          'supervisorname'=>'required',
          'supervisorphone'=>'required',
          'supervisoremail'=>'required',
          'supervisorposition'=>'required',
          'is_supervisor_registered'=>'required',
        ]);

        if($this->placement_id){
          $this->updateplacement();
        }else{
          $this->createplacement();
        }
        $this->reset(['company','position','startdate','enddate','supervisorname','supervisorphone','supervisoremail','supervisorposition','is_supervisor_registered','regnumber']);
        $this->placementmodal = false;


      }

      public function createplacement(){
        $response = $this->studentrepo->createplacement([
          'customerprofession_id'=>$this->customerprofession_id,
          'company'=>$this->company,
          'position'=>$this->position,
          'startdate'=>$this->startdate,
          'enddate'=>$this->enddate,
          'supervisorname'=>$this->supervisorname,
          'supervisorphone'=>$this->supervisorphone,
          'supervisoremail'=>$this->supervisoremail,
          'supervisorposition'=>$this->supervisorposition,
          'is_supervisor_registered'=>$this->is_supervisor_registered,
          'regnumber'=>$this->regnumber
        ]);
        if($response['status']=='success'){
            $this->placementmodal = false;
            $this->success('Placement created successfully.');
          
        }else{
            $this->error($response['message']);
        }
      }

      public function updateplacement(){
        $response = $this->studentrepo->updateplacement($this->placement_id, [
          'company'=>$this->company,
          'position'=>$this->position,
          'startdate'=>$this->startdate,
          'enddate'=>$this->enddate,
          'supervisorname'=>$this->supervisorname,
          'supervisorphone'=>$this->supervisorphone,
          'supervisoremail'=>$this->supervisoremail,
          'supervisorposition'=>$this->supervisorposition,
          'is_supervisor_registered'=>$this->is_supervisor_registered,
          'regnumber'=>$this->regnumber
        ]);
        if($response['status']=='success'){
            $this->placementmodal = false;
            $this->success('Placement updated successfully.');
          
        }else{
            $this->error($response['message']);
        }
        
      }
      public function editplacement($id){
        $this->placement_id = $id;
        $placement = $this->studentrepo->getplacement($id);
        $this->company = $placement->company;
        $this->position = $placement->position;
        $this->startdate = $placement->startdate;
        $this->enddate = $placement->enddate;
        $this->supervisorname = $placement->supervisorname;
        $this->supervisorphone = $placement->supervisorphone;
        $this->supervisoremail = $placement->supervisoremail;
        $this->supervisorposition = $placement->supervisorposition;
        $this->is_supervisor_registered = $placement->is_supervisor_registered;
        $this->regnumber = $placement->regnumber;
        $this->placementmodal = true;
    }
    public function deleteplacement($id){
        $response = $this->studentrepo->deleteplacement($id);
        if($response['status']=='success'){
            $this->success('Placement deleted successfully.');
        }else{
            $this->error($response['message']);
        }
    } 
    public function generateregistrationinvoice(){
        $response = $this->repo->generateregistrationinvoice($this->customerprofession_id);
    
        if($response['status']=='success'){
            $this->success('Registration invoice generated successfully.');
        }else{
            $this->error($response['message']);
        }
    }  
    
    public function downloadregistrationcertificate($id){
        $registration = $this->repo->generatestudentcertificate($id);
   
        //dd($registration);
        // Generate QR code as SVG string
        $qrcodeSvg = QrCode::size(300) 
            ->format('svg')
            ->generate(config('generalutils.base_url').'/verifications/registration/'.$registration->certificatenumber);
        
        // Generate PDF with the QR code as a data URI
        $pdf = Pdf::loadView('certificates.studentregistrations', [
            'qrcode' => 'data:image/svg+xml;base64,' . base64_encode($qrcodeSvg),
            'data'=>$registration
        ]);
        
        // Return the PDF for download
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, "registration_certificate.pdf");
    }


    public function render()
    {
        return view('livewire.admin.customerstudentshow',['data'=>$this->getdata(),'qualificationlevels'=>$this->getqualificationlevels(),'currencies'=>$this->getcurrencies()]);
    }
}
