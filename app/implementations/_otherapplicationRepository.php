<?php

namespace App\implementations;

use App\Interfaces\iotherapplicationInterface;
use App\Models\Otherapplication;
use App\Models\Otherapplicationdocument;
use Illuminate\Support\Str;
use App\Interfaces\invoiceInterface;
use App\Interfaces\igeneralutilsInterface;
use App\Interfaces\iotherserviceInterface;
use Illuminate\Support\Facades\Auth;
use App\Notifications\OtherapplicationDecisionNotification;
class _otherapplicationRepository implements iotherapplicationInterface
{
    /**
     * Create a new class instance.
     */
    protected $otherapplication;
    protected $otherapplicationdocument;
    protected $otherservice;
    protected $invoicerepo;
    protected $generalutils;
    public function __construct(Otherapplication $otherapplication,Otherapplicationdocument $otherapplicationdocument,invoiceInterface $invoicerepo,igeneralutilsInterface $generalutils,iotherserviceInterface $otherservice)
    {
        $this->otherapplication = $otherapplication;
        $this->otherapplicationdocument = $otherapplicationdocument;
        $this->invoicerepo = $invoicerepo;
        $this->generalutils = $generalutils;
        $this->otherservice = $otherservice;
    }

    public  function getbycustomer($customer_id,$year){
        return $this->otherapplication
            ->with('customer', 'otherservice', 'documents', 'approvedby')
            ->where('customer_id', $customer_id)
            ->where('period', '>=', $year)           
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    public  function getbyid($id){
        return $this->otherapplication
            ->find($id);
    }
    public  function create($data){
        try{
            $check = $this->otherapplication->where('customer_id', $data['customer_id'])->where('otherservice_id', $data['otherservice_id'])->where('period', $data['period'])->first();
            if($check){
                return ["status"=>"error","message"=>"Other application already exists"];
            }
            $data['uuid'] = Str::uuid()->toString();
           $otherapplication = $this->otherapplication->create($data);
           $otherservice = $this->otherservice->get($data['otherservice_id']);
            $invoice_number = $this->generalutils->generateinvoice($otherapplication->id);
            $this->invoicerepo->createotherapplicationinvoice([
                'customer_id' => $data['customer_id'],
                'source_id' => $otherapplication->id,
                'invoice_number' => $invoice_number,
                'uuid' => Str::uuid()->toString(),
                'amount' => $otherservice->amount,
                'currency_id' => $otherservice->currency_id,
                'source' => 'otherapplication',
                'description' => 'Other Application',
                'createdby' => Auth::user()->id,
                'year' => $data['period'],
                'status' => 'PENDING',
            ]);
            return ["status"=>"success","message"=>"Other application created successfully","uuid"=>$data['uuid']];
        }catch(\Exception $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }

    }
    public function makedecision($uuid,$status,$comment=null){
        try{
        $otherapplication = $this->otherapplication->with('customer', 'otherservice', 'customerprofession.profession')->where('uuid', $uuid)->first();
        if(!$otherapplication){
            return ["status"=>"error","message"=>"Other application not found"];
        }
        if($status == "APPROVED"){
            if($otherapplication->otherservice->requireapproval == "YES"){
                $expirydate = "LIFETIME";
                $certificatenumber ="";
                if($otherapplication->otherservice->expiretype == "ANNUAL"){
                    $expirydate = $otherapplication->period . '-12-31';
                    $certificatenumber = $this->generalutils->generatecertificatenumber($otherapplication->period, $otherapplication->customerprofession->profession->prefix, $otherapplication->id);
          
                }
                $registrationdate = date('Y-m-d');
               $otherapplication->update(['status' => $status, 'certificate_number' => $certificatenumber, 'certificate_expiry_date' => $expirydate, 'registration_date' => $registrationdate, 'approvedby' => Auth::user()->id]);
            }
        }else{
            $otherapplication->update(['status' => $status, 'approvedby' => Auth::user()->id, 'comment' => $comment]);
        }
        $user = $otherapplication->customer?->customeruser?->user;
        if($user){
            $user->notify(new OtherapplicationDecisionNotification($otherapplication->customer, $otherapplication->otherservice, $status));
        }
        return ["status"=>"success","message"=>"Other application decision made successfully"];
        }catch(\Exception $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public function getvalidinstitutions($search=null){
        return $this->otherapplication                 
                  ->with('customerprofession.profession','customer')
                  ->where('status', 'APPROVED')
                  ->where('tradename', '!=', null)
                  ->where('period', '>=', date('Y'))
                  ->when($search, function($query) use ($search){
                    return $query->whereHas('customer', function($q) use ($search){
                        $q->where('name', 'like', '%'.$search.'%')->orWhere('surname', 'like', '%'.$search.'%');
                    });
        })->paginate(10);
    }
    public  function update($id, $data){
        try{
            $check = $this->otherapplication->where('customer_id', $data['customer_id'])->where('otherservice_id', $data['otherservice_id'])->where('period', $data['period'])->where('id', '!=', $id)->first();
            if($check){
                return ["status"=>"error","message"=>"Other application already exists"];
            }
            $check = $this->otherapplication->where('id', $id)->first();
            $this->otherapplication->where('id', $id)->update($data);
            return ["status"=>"success","message"=>"Other application updated successfully",'uuid'=>$check->uuid];
        }catch(\Exception $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public  function delete($id){
        try{
            $this->otherapplication->where('id', $id)->delete();
            return ["status"=>"success","message"=>"Other application deleted successfully"];
        }catch(\Exception $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public  function getdocuments($id){
        return $this->otherapplicationdocument
            ->with('otherservicedocument', 'document')
            ->where('otherapplication_id', $id)
            ->get();
    }
    public  function createdocument($data){
        try{
            $check = $this->otherapplicationdocument->where('otherapplication_id', $data['otherapplication_id'])->where('otherservicedocument_id', $data['otherservicedocument_id'])->first();
            if($check){
                return ["status"=>"error","message"=>"Document already exists"];
            }
            $this->otherapplicationdocument->create($data);
            return ["status"=>"success","message"=>"Document created successfully"];
        }catch(\Exception $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public  function deletedocument($id){
        try{
            $this->otherapplicationdocument->where('id', $id)->delete();
            return ["status"=>"success","message"=>"Document deleted successfully"];
        }catch(\Exception $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public  function verifydocument($id,$data){
        try{
            $this->otherapplicationdocument->where('id', $id)->update($data);
            return ["status"=>"success","message"=>"Document verified successfully"];
        }catch(\Exception $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public function getbyuuid($uuid){

        $otherapplication = $this->otherapplication->with('customer', 'otherservice', 'documents','customerprofession.profession', 'approvedby', 'invoice')->where('uuid', $uuid)->first();
        if(!$otherapplication){
            return ["data"=>null,"invoice"=>null];
        }
        $requireddocuments = $this->otherservice->getdocuments($otherapplication->otherservice_id);
        $uploaddocuments = [];
        foreach($requireddocuments->documents as $requireddocument){
            $uploaddocuments[] = [
                'otherservicedocument_id'=>$requireddocuments->id,
                "document_id"=>$requireddocument->document_id,
                "document_name"=>$requireddocument->document->name,
                "id"=>$otherapplication->documents->where("otherservicedocument_id",$requireddocument->id)->first()?->id,
                "upload"=>$otherapplication->documents->where("otherservicedocument_id",$requireddocument->id)->count() > 0,
                "file"=>$otherapplication->documents->where("otherservicedocument_id",$requireddocument->id)->first()?->file
            ];
        }
        return ["data"=>$otherapplication,"uploaddocuments"=>$uploaddocuments,"invoice"=>$otherapplication->invoice];
    }

    public function getotherapplications($search,$status,$year){
        return $this->otherapplication
            ->with('customer', 'otherservice', 'documents', 'approvedby', 'invoice')
            ->where('status', $status)
            ->where('period', '>=', $year)
            ->when($search, function($query) use ($search){
                return $query->whereHas('customer', function($q) use ($search){
                    $q->where('name', 'like', '%'.$search.'%')->orWhere('surname', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }
}
