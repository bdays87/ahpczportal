<?php

namespace App\Livewire\Admin\Components;

use App\Interfaces\iapplicationsessionInterface;
use App\Interfaces\iapplicationtypeInterface;
use App\Interfaces\icustomerprofessionInterface;
use App\Interfaces\icustomertypeInterface;
use App\Interfaces\iemploymentlocationInterface;
use App\Interfaces\iemploymentstatusInterface;
use App\Interfaces\iprofessionInterface;
use App\Interfaces\iregistertypeInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use App\RenewalType;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Customerprofessions extends Component
{
    use Toast;

    public $customer;
    protected $customertyperepo;
    protected $employmentstatusrepo;
    protected $employmentlocationrepo;
    protected $registertyperepo;
    protected $professionrepo;
    protected $applicationsessionrepo;
    protected $customerprofessionrepo;
    protected $applicationtyperepo;
    protected $sessionrepo;
    public $addmodal = false;
    public $customertype_id;
    public $profession_id;
    public $registertype_id;
    public $employmentstatus_id;
    public $session_id;
    public $employmentlocation_id;
    public $errormessage="";
    public $openmodal = false;
    public $renewmodal = false;
    public $customerprofession_id;
    public $customerprofession=null;
    public $renewaltype;
    public $message;



    public function boot(icustomertypeInterface $customertype,iemploymentstatusInterface $employmentstatus,iemploymentlocationInterface $employmentlocation,iregistertypeInterface $registertype,iprofessionInterface $profession,icustomerprofessionInterface $customerprofessionrepo,iapplicationsessionInterface $applicationsessionrepo,iapplicationtypeInterface $applicationtyperepo,iapplicationsessionInterface $sessionrepo){
        $this->customertyperepo = $customertype;
        $this->employmentstatusrepo = $employmentstatus;
        $this->employmentlocationrepo = $employmentlocation;
        $this->registertyperepo = $registertype;
        $this->professionrepo = $profession;
        $this->customerprofessionrepo = $customerprofessionrepo;
        $this->applicationsessionrepo = $applicationsessionrepo;
        $this->applicationtyperepo = $applicationtyperepo;
        $this->sessionrepo = $sessionrepo;
    }

    public function mount($customer){
        $this->customer = $customer;
    }  
    
    public function getapplicationtype(){
        return $this->applicationtyperepo->getAll();
    }

    public function getemploymentstatus(){
        return $this->employmentstatusrepo->getAll();
    }
    public function getapplicationsession(){
        return $this->applicationsessionrepo->getAll();
    }

    public function getcustomertype(){
        return $this->customertyperepo->getAll();
    }

    public function getemploymentlocation(){
        return $this->employmentlocationrepo->getAll();
    }

    public function getregistertype(){
        if($this->customertype_id==null){
            return [];
        }
        $registertypes = $this->customertyperepo->getregistertypes($this->customertype_id);
      
        return $registertypes;
    }

    public function selectrenewaltype($type){
       
        $this->renewaltype=$type;
    }

    public function getprofession(){
        return $this->professionrepo->getAll(null,null);
    }

    public function addprofession(){
        $this->validate([
            'customertype_id'=>'required',
            'profession_id'=>'required',
            'registertype_id'=>'required',
            'employmentstatus_id'=>'required',
            'employmentlocation_id'=>'required',
        ]);
        $response =$this->customerprofessionrepo->create([
            'customer_id'=>$this->customer->id,
            'customertype_id'=>$this->customertype_id,
            'profession_id'=>$this->profession_id,
            'registertype_id'=>$this->registertype_id,
            'employmentstatus_id'=>$this->employmentstatus_id,
            'employmentlocation_id'=>$this->employmentlocation_id,
        ]);
        if($response['status']=='success'){
            $this->addmodal = false; 
            $this->success('Profession added successfully.');
            $customertypes = $this->getcustomertype();
            if($customertypes->where('id',$this->customertype_id)->first()->name=='Student'){ 
                $this->redirect(route('customer.student.show',$response['data']));
            }else{
                $this->redirect(route('customer.profession.show',$response['data']));
            }
            $this->dispatch('refresh');
   
        }else{
            $this->errormessage=$response['message'];
        }
    }

    public function delete($id){
        $response = $this->customerprofessionrepo->delete($id);
        if($response['status']=='success'){
            $this->success('Profession deleted successfully.');
        }else{
            $this->error($response['message']);
        }
    }

    public function viewapplication($id){
        $this->customerprofession = $this->customerprofessionrepo->get($id);        
        $this->openmodal = true;
    }

        public function downloadregistrationcertificate($id){
            $registration = $this->customerprofessionrepo->generateregistrationcertificate($id);
            //dd($registration);
            // Generate QR code as SVG string
            $qrcodeSvg = QrCode::size(300) 
                ->format('svg')
                ->generate("Practitioner Name:".$registration->customerprofession->customer->name." ".$registration->customerprofession->customer->surname."\n".config('generalutils.base_url').'/verifications/registration/'.$registration->certificatenumber);
            
            // Generate PDF with the QR code as a data URI
            $pdf = Pdf::loadView('certificates.registrations', [
                'qrcode' => 'data:image/svg+xml;base64,' . base64_encode($qrcodeSvg),
                'data'=>$registration
            ]);
            
            // Return the PDF for download
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->stream();
            }, "registration_certificate.pdf");
        }

        

        public function downloadpractisingcertificate($id){
            $practising = $this->customerprofessionrepo->generatepractisingcertificate($id);
            $qrcodeSvg = QrCode::size(300) 
            ->format('svg')
            ->generate("Practitioner Name:".$practising->customerprofession->customer->name." ".$practising->customerprofession->customer->surname."\n expire date:".$practising->certificate_expiry_date."\n verification link:".config('generalutils.base_url').'/verifications/practising/'.$practising->certificate_number);
        
        // Generate PDF with the QR code as a data URI
        $pdf = Pdf::loadView('certificates.practising', [
            'qrcode' => 'data:image/svg+xml;base64,' . base64_encode($qrcodeSvg),
            'data'=>$practising
        ]);
        
        // Return the PDF for download
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, "practising_certificate.pdf");
             
        }

        public function renew($id){
            $this->customerprofession_id = $id;
            $this->renewmodal = true;
        }

        public function getTypeOptions()
    {
        return RenewalType::options();
    }
    public function getapplicationsessions(){
        return $this->sessionrepo->getactive();
    }

    public function proceedwithrenewal(){
        $this->validate([
            'session_id'=>'required',
            'renewaltype'=>'required',
        ]);
        $response = $this->customerprofessionrepo->renew($this->customerprofession_id,['year'=>$this->session_id,'applicationtype_id'=>$this->renewaltype]);
        if($response['status'] == "success"){
            $this->success($response['message']);
            $this->renewmodal = false;
        }else{
            $this->message=$response['message'];
        }
    }
  


    public function render()
    {
        return view('livewire.admin.components.customerprofessions',['employmentstatuses'=>$this->getemploymentstatus(),'customertypes'=>$this->getcustomertype(),'employmentlocations'=>$this->getemploymentlocation(),'registertypes'=>$this->getregistertype(),'professions'=>$this->getprofession(),'applicationsessions'=>$this->getapplicationsession(),'applicationtypes'=>$this->getapplicationtype(),'types'=>$this->getTypeOptions(),'sessions'=>$this->getapplicationsessions()]);
    }
}
