<?php

namespace App\implementations;

use App\Interfaces\ibanktransactionInterface;
use App\Interfaces\isuspenseInterface;
use App\Models\Bankaccount;
use App\Models\Banktransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class _banktranactionRepository implements ibanktransactionInterface
{
    /**
     * Create a new class instance.
     */
    protected $modal;
    protected $bankaccount;
    protected $suspenserepo;
    public function __construct(Banktransaction $modal,Bankaccount $bankaccount,isuspenseInterface $suspenserepo)
    {
        $this->modal = $modal;
        $this->bankaccount = $bankaccount;
        $this->suspenserepo = $suspenserepo;
    }
    public function getAll($search){
        return $this->modal->with('currency','customer','bank')->when($search, function ($query) use ($search) {
            return $query->where('description', 'like', '%' . $search . '%')
                         ->orWhere('statement_reference', 'like', '%' . $search . '%')
                         ->orWhere('source_reference', 'like', '%' . $search . '%');
        })->paginate(50);
    }
    public function search($search){
        return $this->modal->with('currency','customer','bank')->when($search, function ($query) use ($search) {
            return $query->where('description', 'like', '%' . $search . '%')
                         ->orWhere('statement_reference', 'like', '%' . $search . '%')
                         ->orWhere('source_reference', 'like', '%' . $search . '%');
        })->get();
    }
    public function get($id){
        return $this->modal->where('id',$id)->first();
    }
    public function create($data){
        try{
        $check = $this->modal->where('statement_reference',$data['statement_reference'])->where('source_reference',$data['source_reference'])->first();
        if($check){
            return ['status'=>'error','message'=>'Transaction already exists'];
        }
        $bankaccount = $this->bankaccount->where('account_number',$data['account_number'])->first();
        if(!$bankaccount){
            return ['status'=>'error','message'=>'Bank account not found'];
        }
        $data['currency_id'] = $bankaccount->currency_id;
        $data['amount'] = str_replace(',', '', $data['amount']);
        $this->modal->create($data);
        return ['status'=>'success','message'=>'Transaction created successfully'];
    }catch(\Exception $e){
        return ['status'=>'error','message'=>$e->getMessage()];
    }
}
    public function update($id, $data){
        try{
            $check = $this->modal->where('statement_reference',$data['statement_reference'])->where('source_reference',$data['source_reference'])->where('id','!=',$id)->where("status",'PENDING')->first();
            if($check){
                return ['status'=>'error','message'=>'Transaction already exists'];
            }
            $bankaccount = $this->bankaccount->where('account_number',$data['account_number'])->first();
            if(!$bankaccount){
                return ['status'=>'error','message'=>'Bank account not found'];
            }
            $data['currency_id'] = $bankaccount->currency_id;
            $data['amount'] = str_replace(',', '', $data['amount']);
            $this->modal->where('id',$id)->update($data);
            return ['status'=>'success','message'=>'Transaction updated successfully'];
        }catch(\Exception $e){
            return ['status'=>'error','message'=>$e->getMessage()];
    }
    }
    public function delete($id){
        try{
            $check = $this->modal->where('id',$id)->first();
            if(!$check){
                return ['status'=>'error','message'=>'Transaction not found'];
            }
            if($check->status != 'PENDING'){
                return ['status'=>'error','message'=>'Transaction cannot be deleted'];
            }
            $this->modal->where('id',$id)->delete();
            return ['status'=>'success','message'=>'Transaction deleted successfully'];
        }catch(\Exception $e){
            return ['status'=>'error','message'=>$e->getMessage()];
        }
    }
    public function claim($id,$proofofpayment_id,$customer_id){
        try{
            $check = $this->modal->where('id',$id)->first();
            if(!$check){
                return ['status'=>'error','message'=>'Transaction not found'];
            }
            if($check->status != 'PENDING'){
                return ['status'=>'error','message'=>'Transaction cannot be claimed'];
            }
            $this->modal->where('id',$id)->update(['status'=>'CLAIMED','proofofpayment_id'=>$proofofpayment_id,'customer_id'=>$customer_id]);
            $response = $this->suspenserepo->createSuspense([
                'customer_id'=>$customer_id,
                'currency_id'=>$check->currency_id,
                'amount'=>$check->amount,
                'source'=>"bank",
                'uuid'=>Str::uuid()->toString(),
                'source_id'=>$check->id,
                'status'=>'PENDING',
                'createdby'=>Auth::user()->id
            ]);
            return ['status'=>'success','message'=>'Transaction claimed successfully'];
        }catch(\Exception $e){
            return ['status'=>'error','message'=>$e->getMessage()];
        }
    }
    public function importdata($data){
        try{
             if(count($data)>0){
                foreach ($data as $key => $value) {
                   $check = $this->modal->where('statement_reference',$value['statement_reference'])->where('source_reference',$value['source_reference'])->first();
                   if($check){
                       continue;
                   }
                   $bankaccount = $this->bankaccount->where('account_number',$value['accountnumber'])->first();
                   $value['currency_id'] = $bankaccount->currency_id;
                   $value['bank_id'] = $bankaccount->bank_id;
                   $value['amount'] = str_replace(',', '', $value['amount']);
                   $this->modal->create($value);
                }
            }
            return ['status'=>'success','message'=>'Transaction imported successfully'];
        }catch(\Exception $e){
            return ['status'=>'error','message'=>$e->getMessage()];
        }
    }
}
