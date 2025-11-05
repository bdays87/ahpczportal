<?php

namespace App\implementations;

use App\Interfaces\imanualpaymentInterface;
use App\Interfaces\isuspenseInterface;
use App\Models\Manualpayment;
use App\Models\Suspense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class _manualpaymentRepository implements imanualpaymentInterface
{
    /**
     * Create a new class instance.
     */
    protected $manualpayment;
    protected $suspenserepository;
    protected $suspense;
    public function __construct(Manualpayment $manualpayment,isuspenseInterface $suspenserepository,Suspense $suspense)
    {
        $this->manualpayment = $manualpayment;
        $this->suspenserepository = $suspenserepository;
        $this->suspense = $suspense;
    }

    public function create($data)
    {
        try {
            $data['year'] = date('Y');
            $data['receipted_by'] =Auth::user()->id;
            $manualpayment = $this->manualpayment->create($data);
            $response = $this->suspenserepository->createSuspense([
                'customer_id'=>$data['customer_id'],
                'currency_id'=>$data['currency_id'],
                'amount'=>$data['amount'],
                'source'=>$data['mode'],
                'uuid'=>Str::uuid()->toString(),
                'source_id'=>$manualpayment->id,
                'status'=>'PENDING',
                'createdby'=>Auth::user()->id
            ]);
            if($response['status']=='success'){
                return [
                    'status'=>'success',
                    'message'=>'Transaction Completed Successfully'
                ];
            }else{
                return [
                    'status'=>'error',
                    'message'=>$response['message']
                ];
            }
            return ["status"=>"success", "message"=>"Manualpayment created successfully"];
        } catch (\Throwable $th) {
            return ["status"=>"error", "message"=>$th->getMessage()];
        }
    }
    public function delete($id)
    {
        try {
            $manualpayment = $this->manualpayment->where('id', $id)->first();
            if($manualpayment == null){
                return ["status"=>"error", "message"=>"Manualpayment not found"];
            }
            $suspense = $this->suspense->with('receipts')->where('source_id', $id)->where('source', $manualpayment->mode)->first();
             if($suspense != null){
                if($suspense->status !="PENDING"){
                    return ["status"=>"error", "message"=>"Manualpayment cannot be deleted as it is associated with a suspense"];
                }
                if($suspense->receipts->count() > 0){
                    return ["status"=>"error", "message"=>"Manualpayment cannot be deleted as it is associated with a receipt"];
                }
                $suspense->delete();
             }
            $this->manualpayment->destroy($id);
            return ["status"=>"success", "message"=>"Manualpayment deleted successfully"];
        } catch (\Throwable $th) {
            return ["status"=>"error", "message"=>$th->getMessage()];
        }
    }
    public function getAll($year)
    {
        return $this->manualpayment->where('year', $year)->get();
    }
    public function get($id)
    {
        return $this->manualpayment->where('id', $id)->first();
    }
    public function getbycustomer($customer_id)
    {
        return $this->manualpayment->with('receiptby','currency')->where('customer_id', $customer_id)->get();
    }
}
