<?php

namespace App\Livewire\Admin;

use App\Interfaces\icurrencyInterface;
use App\Interfaces\icustomerprofessionInterface;
use App\Interfaces\iexchangerateInterface;
use App\Interfaces\invoiceInterface;
use App\Interfaces\iqualificationcategoryInterface;
use App\Interfaces\iqualificationlevelInterface;
use App\Interfaces\isuspenseInterface;
use App\Interfaces\iqualificationInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class Customerprofessionshow extends Component
{
    use Toast,WithFileUploads;
    public $uuid;
    public  $breadcrumbs=[]; 
    protected $customerprofessionrepo;
    protected $qualificationcategoryrepo;
    protected $qualificationlevelrepo;
    public $step = 1;
    public $document_id;
    public $uploadmodal = false;
    public $file;
    public $customerprofession_id;
    public $verified = false;
    public $qualification_id;
    public $qualificationmodal = false;
    public $qualificationid;
    public $qualificationcategory_id; 
    public $qualificationlevel_id;
    public $institution;
    public $year;
    public $paymentfile;
    public $currency_id;
    public $qualificationfile;
    public $customerprofessionqualification_id;
    protected $invoicerepo;
    public $invoices;
    public $paymentmodal = false;
    public $invoice =null;
    public $exchangerate=null;
    public $walletbalance=null;
    public $invoice_id;
    public $totalpable;
    public $attachmodal = false;
    public $proofofpayments;
    protected $currencyrepo;
    protected $exchangeraterepo;
    protected $suspenserepo;
    protected $qualificationrepo;
    public function boot(icustomerprofessionInterface $customerprofessionrepo,iqualificationInterface $qualificationrepo,isuspenseInterface $suspenserepo,iqualificationcategoryInterface $qualificationcategoryrepo,iqualificationlevelInterface $qualificationlevelrepo,invoiceInterface $invoicerepo,icurrencyInterface $currencyrepo,iexchangerateInterface $exchangeraterepo)
    {
        $this->customerprofessionrepo = $customerprofessionrepo;
        $this->qualificationrepo = $qualificationrepo;
        $this->suspenserepo = $suspenserepo;
        $this->qualificationcategoryrepo = $qualificationcategoryrepo;
        $this->qualificationlevelrepo = $qualificationlevelrepo;
        $this->invoicerepo = $invoicerepo;
        $this->currencyrepo = $currencyrepo;
        $this->exchangeraterepo = $exchangeraterepo;
    }

  
    public function mount($uuid)
    {
        $this->uuid = $uuid;
        $this->invoices = [];
        $this->proofofpayments = new Collection();
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
    public function getcustomerprofession(){
       $payload= $this->customerprofessionrepo->getbyuuid($this->uuid);
       $this->customerprofession_id = $payload["customerprofession"]["id"];
   
       return $payload;
    }
    public function getqualifications(){
        return $this->qualificationrepo->getQualificationByProfessionId($this->customerprofession_id);
    }
    public function getqualificationcategories(){
        return $this->qualificationcategoryrepo->getAll();
    }
    public function getqualificationlevels(){
        return $this->qualificationlevelrepo->getAll();
    }
    public function getcurrencies(){
        return $this->currencyrepo->getAll("active");
    }

    public function openuploadmodal($document_id){
        $this->document_id = $document_id;
        $this->uploadmodal = true;
    }

    public function nextstep($step){
        $this->step = $step;
        if($this->step == 3){
           $data =$this->invoicerepo->getcustomerprofessioninvoices($this->customerprofession_id);
           if(count($data) == 0){
           $response= $this->customerprofessionrepo->generatepractitionerinvoice($this->customerprofession_id);
         
        
              $this->invoices = $this->invoicerepo->getcustomerprofessioninvoices($this->customerprofession_id);
       
           }else{
            $this->invoices = $data;
           }
        }
    }

    public function prevstep($step){
        $this->step = $step;
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

    public  function savequalification(){
        $this->validate([
            "qualificationid"=>"required",
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
        $this->reset(['qualificationid','qualificationcategory_id','qualificationlevel_id','institution','year','qualificationfile','customerprofessionqualification_id']);
       
  
    }
    public function updatedCurrencyId(){
       $this->exchangerate = $this->exchangeraterepo->getlatestrate($this->currency_id);
       $settlementsplit = $this->invoice->settlementsplit;
       if($settlementsplit != null){
        if($settlementsplit->currency_id != $this->currency_id){
            if($settlementsplit->percentage == 100){
                $this->totalpable = $this->invoice->amount;
            }else{
                $allowedpercentage = 100-$settlementsplit->percentage;
                $this->totalpable = ($this->invoice->amount * $this->exchangerate->rate)*($allowedpercentage/100);
            }
        }
       }else if($this->invoice->currency_id != $this->currency_id){
        $this->totalpable = $this->invoice->amount * $this->exchangerate->rate;
       }else{
        $this->totalpable = $this->invoice->amount;
       }
       $this->walletbalance = $this->suspenserepo->getbalance($this->invoice->customer_id, $this->currency_id)["balance"];
       
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
            "qualification_id"=>$this->qualificationid,
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
        $this->qualificationid = $qualification->qualification_id;
        $this->qualificationcategory_id = $qualification->qualificationcategory_id;
        $this->qualificationlevel_id = $qualification->qualificationlevel_id;
        $this->institution = $qualification->institution;
        $this->year = $qualification->year;
        $this->qualificationfile = $qualification->file;
        $this->qualificationmodal = true;
    }
    public function openpaymentmodal($invoice_id){
      
        $this->invoice_id = $invoice_id;
        $this->invoice = $this->invoicerepo->getInvoice($invoice_id);
        $totalpaid = $this->computetotalpaid($this->invoice->receipts);
        $this->totalpable = $this->invoice->amount - $totalpaid;
        $this->paymentmodal = true;
    }

    public function computetotalpaid($receipts){
       
        $totalpaid = 0;
        if(count($receipts) > 0){
        foreach($receipts as $receipt){
                $totalpaid += $receipt->amount/$receipt->exchangerate->rate;
            }
            return $totalpaid;
        }
        return 0;
    }
    public function getattachpayments(){
      $this->proofofpayments = $this->invoicerepo->getinvoiceproof($this->invoice_id);
      $this->attachmodal = true;
    }
    public function Updatedpaymentfile(){
        $this->addattachpayment();
    }
    public function addattachpayment(){
      $this->validate([
        "paymentfile"=>"required"
      ]);
      $path = $this->paymentfile->store('documents','public');
    $response =  $this->invoicerepo->createinvoiceproof([
        "invoice_id"=>$this->invoice_id,
        "file"=>$path,     
      ]);
      if($response["status"] == "success"){
        $this->reset('paymentfile');
        $this->proofofpayments = $this->invoicerepo->getinvoiceproof($this->invoice_id);
        $this->success($response["message"]);
      }else{
        $this->error($response["message"]);
      }
    }
    public function deleteattachpayment($id){
      $response = $this->invoicerepo->deleteinvoiceproof($id);
      if($response["status"] == "success"){
        $this->proofofpayments = $this->invoicerepo->getinvoiceproof($this->invoice_id);
        $this->success($response["message"]);
      }else{
        $this->error($response["message"]);
      }
    }
    public function submitforverification(){
      $response = $this->invoicerepo->submitforverification($this->invoice_id);
      if($response["status"] == "success"){
        $this->success($response["message"]);
      }else{
        $this->error($response["message"]);
      }
    }
    
        
    public function render() 
    {
        return view('livewire.admin.customerprofessionshow',[
            "customerprofession"=>$this->getcustomerprofession()["customerprofession"],
            "uploaddocuments"=>$this->getcustomerprofession()["uploaddocuments"],
            "categories"=>$this->getqualificationcategories(),
            "levels"=>$this->getqualificationlevels(),
            "currencies"=>$this->getcurrencies(),
            "qualifications"=>$this->getqualifications(),
        ]);
    }
} 
