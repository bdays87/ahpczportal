<?php

namespace App\implementations;

use App\Interfaces\icustomerprofessionInterface;
use App\Interfaces\igeneralutilsInterface;
use App\Interfaces\invoiceInterface;
use App\Models\Customerapplication;
use App\Models\Customerprofession;
use App\Models\Customerprofessioncomment;
use App\Models\Customerprofessiondocument;
use App\Models\Customerprofessionqualification;
use App\Models\Customerprofessionqualificationassessment;
use App\Models\Customerregistration;
use App\Models\Documentrequirement;
use App\Models\Customerapplicationdocument;
use App\Models\Renewaldocument;
use App\Models\Invoice;
use App\Models\Qualificationcategory;
use App\Notifications\ApplicationApprovalNotification;
use App\Notifications\AssessmentApprovalnotification;
use App\Notifications\Defaultnotification;
use App\Notifications\RegistrationApprovalNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Mycdp;
use App\Models\Professiondocument;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class _customerprofessionRepository implements icustomerprofessionInterface
{
    /**
     * Create a new class instance.
     */
    protected $customerprofession;
    protected $documentrequirement;
    protected $professiondocument;
    protected $mycdp;
    protected $renewaldocument;
    protected $customerprofessiondocument;
    protected $customerprofessionqualification;
    protected $invoicerepo;
    protected $invoice;
    protected $qualificationcategory;
    protected $customerprofessioncomment;
    protected $customerprofessionregistration;
    protected $customerprofessionapplication;
    protected $customerprofessionqualificationassessment;
    protected $customerapplication;
    protected $customerapplicationdocument;
    protected $generalutils;
    public function __construct(Customerprofession $customerprofession,Professiondocument $professiondocument,Customerapplicationdocument $customerapplicationdocument,Renewaldocument $renewaldocument,Customerapplication $customerapplication,Mycdp $mycdp,Documentrequirement $documentrequirement,Customerprofessiondocument $customerprofessiondocument,Customerprofessionqualification $customerprofessionqualification,invoiceInterface $invoicerepo,Invoice $invoice,Qualificationcategory $qualificationcategory,Customerprofessioncomment $customerprofessioncomment,Customerregistration $customerprofessionregistration,Customerapplication $customerprofessionapplication,Customerprofessionqualificationassessment $customerprofessionqualificationassessment,igeneralutilsInterface $generalutils)
    {
        $this->customerprofession = $customerprofession;
        $this->customerapplication = $customerapplication;
        $this->documentrequirement = $documentrequirement;
        $this->customerprofessiondocument = $customerprofessiondocument;
        $this->customerprofessionqualification = $customerprofessionqualification;
        $this->invoicerepo = $invoicerepo;
        $this->invoice = $invoice;
        $this->renewaldocument = $renewaldocument;
        $this->professiondocument = $professiondocument;
        $this->qualificationcategory = $qualificationcategory;
        $this->customerapplicationdocument = $customerapplicationdocument;
        $this->customerprofessioncomment = $customerprofessioncomment;
        $this->customerprofessionregistration = $customerprofessionregistration;
        $this->customerprofessionapplication = $customerprofessionapplication;
        $this->customerprofessionqualificationassessment = $customerprofessionqualificationassessment;
        $this->generalutils = $generalutils;
        $this->mycdp = $mycdp;
    }

    public function getAll($status = "PENDING",$year = null)
    {
        return $this->customerprofession->with('customer','profession','customertype','employmentstatus','employmentlocation','registertype','documents','qualifications','registration','applications')->when($status,function($query)use($status){
            $query->where('status',$status);
        })->when($year,function($query)use($year){
            $query->where('year',$year);
        })->paginate(500);
    }

    public function get($id)
    {
        return $this->customerprofession->with('customer','applications','registration','profession','customertype','employmentstatus','employmentlocation','registertype')->find($id);
    }
    public function getcustomerstudent($uuid){
        $customerprofession = $this->customerprofession->with('documents.document','studentqualifications.qualificationlevel','studentqualifications.documents.document','placements','registration','customer')->where("uuid",$uuid)->first();
        $documentrequirements = $this->documentrequirement->with('document')->where("tire_id",$customerprofession->profession->tire_id)->where("customertype_id",$customerprofession->customertype_id)->get();
        $uploaddocuments = [];
        foreach ($documentrequirements as $documentrequirement) {
            $upload = false;
            if($customerprofession->documents->where("document_id",$documentrequirement->document_id)->count() > 0){
                $upload = true;
            }
            $uploaddocuments[] = [
                "document_id"=>$documentrequirement->document_id,
                "document_name"=>$documentrequirement->document->name,
                "upload"=>$upload
            ];
        }
        $invoice = $this->invoice->with("currency")->where("source_id",$customerprofession->id)->where("description","Registration")->where("year",date("Y"))->first();
        return [
            "customerprofession"=>$customerprofession,
            "uploaddocuments"=>$uploaddocuments,
            "invoice"=>$invoice,
        ];
    }

    public function create($data)
    {
        try {
            $check = $this->customerprofession->where('customer_id',$data['customer_id'])->where('profession_id',$data['profession_id'])->where("status","!=","REJECTED")->first();
            if($check){
                return ["status"=>"error","message"=>"Customer already has this profession"];
            }

            $data["uuid"] = Str::uuid()->toString();
            $data["created_by"] = Auth::user()->id;
            $data["year"] = date("Y");            
            $this->customerprofession->create($data);
          
         
            return ["status"=>"success","message"=>"Customer profession created successfully","data"=>$data["uuid"]];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>$th->getMessage()];
        }
    } 
    public function getapplicationbyuuid($uuid){
        $customerapplication =  $this->customerapplication->with("customerprofession.customer","customerprofession.profession","documents.document")->where("uuid",$uuid)->first();
        if(!$customerapplication){
            return ["status"=>"error","message"=>"Customer application not found"];
        }
        $renewaldocuments = $this->renewaldocument->with("document")->where("applicationtype_id",$customerapplication->applicationtype_id)->where("tire_id",$customerapplication->customerprofession->profession->tire_id)->where("registertype_id",$customerapplication->customerprofession->registertype_id)->get();
        $uploaddocuments = [];
        foreach($renewaldocuments as $renewaldocument){
            $uploaddocuments[] = [
                "document_id"=>$renewaldocument->document_id,
                "document_name"=>$renewaldocument->document->name,
                'path'=>$customerapplication->documents->where("document_id",$renewaldocument->document_id)->first()?->file,
                "upload"=>$customerapplication->documents->where("document_id",$renewaldocument->document_id)->count() > 0
            ];
        }
        $invoice = $this->invoice->with("currency")->where("source_id",$customerapplication->id)->where("source","customerapplication")->first();
        return ["data"=>$customerapplication,"uploaddocuments"=>$uploaddocuments,"invoice"=>$invoice];
    }
    public function renew($id,$data){
        try {
            $customerprofession = $this->customerprofession->with("profession")->find($id);
            if(!$customerprofession){
                return ["status"=>"error","message"=>"Customer profession not found"];
            }
            $yearrange = [$data['year']-1,$data['year']];
            $mycdps = $this->mycdp->where('customerprofession_id',$customerprofession->id)->whereIn('year',$yearrange)->get();
            $totalpoints = 0;
            if($mycdps->count() > 0){
              $totalpoints = $mycdps->sum('points');
            }
            if($totalpoints < $customerprofession->profession->minimum_cdp){
                return ["status"=>"error","message"=>"Customer profession does not have enough points to renew, current points: ".$totalpoints." minimum required points: ".$customerprofession->profession->minimum_cdp    ];
            }
           return $this->invoicerepo->createrenewalinvoice(['customerprofession_id'=>$customerprofession->id,'year'=>$data['year'],'applicationtype_id'=>$data['applicationtype_id']]);
     
        }
        catch (\Throwable $th) {
            return ["status"=>"error","message"=>$th->getMessage()];
        }
    }

   public function generateregistrationinvoice($id){
    $customerprofession = $this->customerprofession->with("registration")->find($id);
    if(!$customerprofession){
        return ["status"=>"error","message"=>"Customer profession not found"];
    }
    $registrationinvoice = $this->invoicerepo->createInvoice(['description'=>'Registration','customerprofession_id'=>$customerprofession->id,'year'=>date("Y")]);
    if($registrationinvoice['status'] == "error"){
      $this->customerprofession->where("id",$customerprofession->id)->delete();
      return ["status"=>"error","message"=>$registrationinvoice['message']];
    }
    return ["status"=>"success","message"=>$registrationinvoice['message']];
   }
    public function generatepractitionerinvoice($id){
        $customerprofession = $this->customerprofession->with("registration","applications","qualificationassessments")->find($id);
      //  dd($customerprofession);
        if(!$customerprofession){
            return ["status"=>"error","message"=>"Customer profession not found"];
        }
        if($customerprofession->registration==null){
            
          $this->invoicerepo->createInvoice(['description'=>'Registration','customerprofession_id'=>$customerprofession->id,'year'=>date("Y")]);
      
    }
          
    if($customerprofession->applications->count()==0){
        $this->invoicerepo->createInvoice(['description'=>'New Application','customerprofession_id'=>$customerprofession->id,'year'=>date("Y")]);
    }
    if($customerprofession->qualificationassessments->count()>0){
        $this->invoicerepo->createInvoice(['description'=>'Qualification Assessment','customerprofession_id'=>$customerprofession->id,'year'=>date("Y")]);  
    }
    return ["status"=>"success","message"=>"Invoice generated successfully"];   
    }

    public function update($id,$data)
    {
        try {
            $profession = $this->customerprofession->find($id);
            if(!$profession){
                return ["status"=>"error","message"=>"Customer profession not found"];
            }
            if($profession->status != "PENDING"){
                return ["status"=>"error","message"=>"Customer profession cannot be updated"];
            }
            $profession->update($data);
            return ["status"=>"success","message"=>"Customer profession updated successfully"];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>$th->getMessage()];
        }
    }

    public function delete($id)
    {
      
        try {
            $profession = $this->customerprofession->with("documents","qualifications","registration","applications")->where("id",$id)->first();
            if(!$profession){
                return ["status"=>"error","message"=>"Customer profession not found"];
            }
            if($profession->status != "PENDING"){
                return ["status"=>"error","message"=>"Customer profession cannot be deleted"];
            }
            if($profession->documents->count() > 0){
                
            $profession->documents->each(function ($document) {
                Storage::delete($document->file);
                $document->delete();
            });
        }
        if($profession->qualifications->count() > 0){
            $profession->qualifications->each(function ($qualification) {
                $qualification->delete();
            });
        }
        if($profession->registration){
            $profession->registration->delete();
        }
            if($profession->applications->count() > 0){
            $profession->applications->each(function ($application) {
                $application->delete();
            });
            }
            $profession->delete();
            return ["status"=>"success","message"=>"Customer profession deleted successfully"];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>$th->getMessage()];
        }
    }
    public function getbyuuid($uuid){ 
        $customerprofession = $this->customerprofession->with('customer','profession.tires.tire','tire','qualifications.qualification','customertype','employmentstatus','employmentlocation','registertype','documents.document','qualifications.qualificationcategory','qualifications.qualificationlevel')->where("uuid",$uuid)->first();
         if(!$customerprofession){
            return [
                "customerprofession"=>null,
                "uploaddocuments"=>[]
            ];
        }
      //  $documentrequirements = $this->professiondocument->with('document')->where("profession_id",$customerprofession->profession_id)->where("customertype_id",$customerprofession->customertype_id)->get();
      $applicationtype_id = null;
      if($customerprofession->applications->count() > 0){
        $applicationtype_id = $customerprofession->applications->last()->applicationtype_id;
      }else{
        $applicationtype_id = 1;
      }
      $tire_id = null;
      if($customerprofession->tire_id){
        $tire_id = $customerprofession->tire_id;
      }else{
        $tire_id = $customerprofession->profession->tires->first()->tire_id;
      }
      
      $documentrequirements = $this->documentrequirement->with('document')->where("tire_id",$tire_id)
       ->where("customertype_id",$customerprofession->customertype_id)
       ->where("applicationtype_id",$applicationtype_id)->get();
       foreach($documentrequirements as $documentrequirement){
        $upload = false;
        if($customerprofession->documents->where("document_id",$documentrequirement->document_id)->count() > 0){
            $upload = true;
        }
        $uploaddocuments[] = [
            "document_id"=>$documentrequirement->document_id,
            "document_name"=>$documentrequirement->document->name,
            "upload"=>$upload
        ];
    }
        $uploaddocuments = [];
        foreach ($documentrequirements as $documentrequirement) {
            $upload = false;
            if($customerprofession->documents->where("document_id",$documentrequirement->document_id)->count() > 0){
                $upload = true;
            }
            $uploaddocuments[] = [
                "document_id"=>$documentrequirement->document_id,
                "document_name"=>$documentrequirement->document->name,
                "upload"=>$upload
            ];
        }
        return [
            "customerprofession"=>$customerprofession,
            "uploaddocuments"=>$uploaddocuments
        ];
    }
    public function uploadDocument(array $data){
        try {
            if($data["verified"]){
                $data["verifiedby"] = Auth::user()->id;
                unset($data["verified"]);
                $data["status"] = "VERIFIED";
            }else{
                unset($data["verified"]);
                $data["status"] = "PENDING";
            }
            $document = $this->customerprofessiondocument->create($data);
            return ["status"=>"success","message"=>"Document uploaded successfully","data"=>$document];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>$th->getMessage()];
        }
    }
    public function removedocument($document_id,$customerprofession_id){
        try {
            $document = $this->customerprofessiondocument->where("document_id",$document_id)->where("customerprofession_id",$customerprofession_id)->first();
            if(!$document){
                return ["status"=>"error","message"=>"Document not found"];
            }
            Storage::delete($document->file);
            $document->delete();
            return ["status"=>"success","message"=>"Document removed successfully"];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>$th->getMessage()];
        }
    }
    public function verifydocument($document_id,$customerprofession_id){
        try {
            $document = $this->customerprofessiondocument->where("document_id",$document_id)->where("customerprofession_id",$customerprofession_id)->first();
            if(!$document){
                return ["status"=>"error","message"=>"Document not found"];
            }
            $document->update(["verifiedby"=>Auth::user()->id,"verified"=>true,"status"=>"VERIFIED"]);
            return ["status"=>"success","message"=>"Document verified successfully"];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>$th->getMessage()];
        }
    }
    public function addqualification($data){
        try {

            $qualificationcategory = $this->qualificationcategory->where("id",$data["qualificationcategory_id"])->first();
            if(!$qualificationcategory){
                return ["status"=>"error","message"=>"Qualification category not found"];
            }
            if($qualificationcategory->requireapproval=="Y"){
               // $applicationinvoice = $this->invoicerepo->createInvoice(['description'=>'Qualification Assessment','customerprofession_id'=>$data['customerprofession_id'],'year'=>date("Y")]);
                $this->customerprofessionqualificationassessment->firstOrCreate([
                    "customerprofession_id"=>$data['customerprofession_id'],
                    "status"=>"PENDING"
                ]);
                /*if($applicationinvoice['status'] == "error"){
                    return ["status"=>"error","message"=>$applicationinvoice['message']];
                }*/
            }
                

            $qualification = $this->customerprofessionqualification->create($data);
            return ["status"=>"success","message"=>"Qualification added successfully","data"=>$qualification];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>$th->getMessage()];
        }
    }
    public function removequalification($id){
        try {
            $qualification = $this->customerprofessionqualification->with("qualificationcategory")->where("id",$id)->first();
            if(!$qualification){
                return ["status"=>"error","message"=>"Qualification not found"];
            }
            if($qualification->qualificationcategory->requireapproval=="Y"){
                $applicationinvoice = $this->invoice->with("receipts")->where(['description'=>'Qualification Assessment','source_id'=>$qualification->customerprofession_id])->first();
    
                if($applicationinvoice){
                    if($applicationinvoice->receipts->count() > 0){
                      return ["status"=>"error","message"=>"Invoice has receipts"];
                    }
                    if($applicationinvoice->status == "PAID"){
                        return ["status"=>"error","message"=>"Invoice is paid"];
                    }
                    $applicationinvoice->delete();
                }
            }
            Storage::delete($qualification->file);
            $qualification->delete();
            return ["status"=>"success","message"=>"Qualification removed successfully"];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>$th->getMessage()];
        }
    }
    public function updatequalification($id,$data){
        try {
            $qualification = $this->customerprofessionqualification->find($id);
            if(!$qualification){
                return ["status"=>"error","message"=>"Qualification not found"];
            }
            $qualification->update($data);
            return ["status"=>"success","message"=>"Qualification updated successfully"];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>$th->getMessage()];
        }
    }
    public function getqualification($id){
        return $this->customerprofessionqualification->find($id);
    }
    public function addcomment($data){
        try {
             $customerprofession = $this->customerprofession->with("customer.customeruser.user","registration","applications","profession")->find($data['customerprofession_id']);
             if(!$customerprofession){
                return ["status"=>"error","message"=>"Customer profession not found"];
             }
             $name = $customerprofession->customer->name." ".$customerprofession->customer->surname;
            
             if($data['status'] == "APPROVED"){
                if($data['commenttype'] == "Qualification Assessment"){
                $customerprofessionqualificationassessment = $this->customerprofessionqualificationassessment->where("customerprofession_id",$customerprofession->id)->where("status","PENDING")->first();
                if($customerprofessionqualificationassessment){
                    $customerprofessionqualificationassessment->update(["status"=>"APPROVED","comment"=>$data['comment'],"user_id"=>Auth::user()->id]);
                    $user = $customerprofession->customer->customeruser->user;
                    $customerprofession->update(["status"=>"PENDING"]);
                    $user->notify(new AssessmentApprovalnotification($data['status']));
                }
            }elseif($data['commenttype'] == "Registration"){
                $customerregistration = $this->customerprofessionregistration->where("customerprofession_id",$customerprofession->id)->first();
                if($customerregistration){
                    $certificatenumber = $this->generalutils->generatecertificatenumber($customerregistration->year,$customerprofession->profession->prefix,$customerregistration->id);
                    $customerregistration->update(["status"=>"APPROVED","registrationdate"=>date("Y-m-d"),"user_id"=>Auth::user()->id,"certificatenumber"=>$certificatenumber]);
                    $user = $customerprofession->customer->customeruser->user;
                    if($customerprofession->customertype_id == 3){
                        $customerprofession->update(["status"=>"APPROVED",'registertype_id'=>$data['registertype_id'],'tire_id'=>$data['tire_id']]);  
                    }else{
                    $customerprofession->update(["status"=>"PENDING",'registertype_id'=>$data['registertype_id'],'tire_id'=>$data['tire_id']]);
                    if($customerprofession->applications->count()==0){
        $this->invoicerepo->createInvoice(['description'=>'New Application','customerprofession_id'=>$customerprofession->id,'year'=>date("Y")]);
    }
                    }
                    $user->notify(new RegistrationApprovalNotification($customerprofession->customer,$customerprofession->profession,$data['status'],$data['comment']));
                }
                

            }elseif($data['commenttype'] == "Application"){
                $customerprofessionapplication = $this->customerprofessionapplication->where("customerprofession_id",$customerprofession->id)->first();
                if($customerprofessionapplication){
                    $certificatenumber = $this->generalutils->generatecertificatenumber($customerprofessionapplication->year,$customerprofession->profession->prefix,$customerprofessionapplication->id);
                    $customerprofessionapplication->update(["status"=>"APPROVED","approvedby"=>Auth::user()->id,"certificate_number"=>$certificatenumber,'registration_date'=>date("Y-m-d"),'certificate_expiry_date'=>date("Y")."-12-31"]);
                    $customerprofession->update(["status"=>"APPROVED"]);
                    $user = $customerprofession->customer->customeruser->user;
                    $user->notify(new ApplicationApprovalNotification($customerprofession->customer,$customerprofession->profession,$data['status'],$data['comment']));
                }
                
            }
               
             }else{
                if($data['commenttype'] == "Qualification Assessment"){
                    $customerprofessionqualificationassessment = $this->customerprofessionqualificationassessment->where("customerprofession_id",$customerprofession->id)->where("status","PENDING")->first();
                    if($customerprofessionqualificationassessment){
                        $customerprofessionqualificationassessment->update(["status"=>"REJECTED","comment"=>$data['comment'],"user_id"=>Auth::user()->id]);
                    }
                }
                $customerprofession->update(["status"=>"REJECTED"]);
                $customerprofession->registration->update(["status"=>"REJECTED"]);
                $customerprofession->applications->last()->update(["status"=>"REJECTED"]);
                $this->invoice->where("source","customerprofession")->where("source_id",$customerprofession->id)->where("status","PENDING")->update(["status"=>"CANCELLED"]);
             }
             unset($data['status']);
             $data['user_id'] = Auth::user()->id;
             unset($data['registertype_id']);
             unset($data['tire_id']);
             $customerprofession->comments()->create($data);
          //   $customerprofession->customer->customeruser->user->notify(new Defaultnotification($name,$data['commenttype'],$data['comment']));
          
            return ["status"=>"success","message"=>"Comment added successfully"];
        } catch (\Throwable $th) {
         
            return ["status"=>"error","message"=>$th->getMessage()];
        }

    }
public function getcomment($id){
    return $this->customerprofessioncomment->where("id",$id)->first();
}
public function removecomment($id){
    try {
        $comment = $this->customerprofessioncomment->where("id",$id)->first();
        if(!$comment){
            return ["status"=>"error","message"=>"Comment not found"];
        }
    }
    catch (\Throwable $th) {
        return ["status"=>"error","message"=>$th->getMessage()];
    }
}
public function updatecomment($id,$data){

    try {
        $comment = $this->customerprofessioncomment->with("customerprofession.customer")->where("id",$id)->first();
        if(!$comment){
            return ["status"=>"error","message"=>"Comment not found"];
        }
        $customerprofession = $comment->customerprofession;
        $name = $customerprofession->customer->name." ".$customerprofession->customer->surname;
        if($data['status'] == "APPROVED"){
           $customerprofession->update(["status"=>"PENDING"]);
        }else{
           $customerprofession->update(["status"=>"REJECTED"]);
           $this->invoice->where("source","customerprofession")->where("source_id",$customerprofession->id)->where("status","PENDING")->update(["status"=>"CANCELLED"]);
        }
        unset($data['status']);
        $data['user_id'] = Auth::user()->id;
        $comment->update($data);
        
      Notification::send($customerprofession->customer,new Defaultnotification($name,$data['commenttype'],$data['comment']));
       return ["status"=>"success","message"=>"Comment added successfully"];
   } catch (\Throwable $th) {
       return ["status"=>"error","message"=>$th->getMessage()];
   }


}

public function generateregistrationcertificate($id){
 
   return $this->customerprofessionregistration->with("customerprofession.customer","customerprofession.profession")->where("id", $id)->first();    
 
}
public function generatestudentcertificate($id){
   return $this->customerprofessionregistration->with("customerprofession.customer","customerprofession.profession.conditions","customerprofession.studentqualifications")->where("id", $id)->first();    
 
}
public function generatepractisingcertificate($id){
   return $this->customerprofessionapplication->with("customerprofession.customer","customerprofession.profession","customerprofession.registertype")->where("id",$id)->first();
    
}

public function uploadrenewaldocuments($data){
    try {
        $customerapplicationdocument = $this->customerapplicationdocument->create($data);
        return ["status"=>"success","message"=>"Document uploaded successfully"];
    } catch (\Throwable $th) {
        return ["status"=>"error","message"=>$th->getMessage()];
    }
}
public function removerenewaldocument($document_id,$customerapplication_id){
    try {
        $customerapplicationdocument = $this->customerapplicationdocument->where("document_id",$document_id)->where("customerapplication_id",$customerapplication_id)->first();
        if(!$customerapplicationdocument){
            return ["status"=>"error","message"=>"Document not found"];
        }
        Storage::delete($customerapplicationdocument->file);
        $customerapplicationdocument->delete();
        return ["status"=>"success","message"=>"Document removed successfully"];
    }
    catch (\Throwable $th) {
        return ["status"=>"error","message"=>$th->getMessage()];
    }
}

 
     
}
