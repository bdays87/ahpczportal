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
    /**
     * Put a bank deposit into a customer's wallet (suspense account) with no proof of payment:
     * marks the transaction CLAIMED to that customer and creates the suspense credit.
     * Either both happen or neither does.
     */
    public function creditwallet($id, $customer_id){
        try{
            $customer = \App\Models\Customer::find($customer_id);
            if(!$customer){
                return ['status'=>'error','message'=>'Customer not found'];
            }

            \Illuminate\Support\Facades\DB::transaction(function () use ($id, $customer) {
                $transaction = $this->modal->where('id',$id)->lockForUpdate()->first();
                if(!$transaction){
                    throw new \Exception('Transaction not found');
                }
                if($transaction->status != 'PENDING'){
                    throw new \Exception('Transaction has already been claimed');
                }
                if((float) $transaction->amount <= 0){
                    throw new \Exception('Only a deposit (credit) can be put into a wallet');
                }

                $transaction->update(['status'=>'CLAIMED','customer_id'=>$customer->id]);

                $response = $this->suspenserepo->createSuspense([
                    'customer_id'=>$customer->id,
                    'currency_id'=>$transaction->currency_id,
                    'amount'=>$transaction->amount,
                    'source'=>'bank',
                    'uuid'=>Str::uuid()->toString(),
                    'source_id'=>$transaction->id,
                    'status'=>'PENDING',
                    'createdby'=>Auth::id()
                ]);
                if(($response['status'] ?? '') != 'success'){
                    throw new \Exception($response['message'] ?? 'Could not credit the wallet');
                }
            });

            return ['status'=>'success','message'=>'Wallet credited. The customer can now settle invoices from the portal.'];
        }catch(\Exception $e){
            return ['status'=>'error','message'=>$e->getMessage()];
        }
    }

    /**
     * Undo a claim: takes the money back out of the customer's wallet and makes the
     * transaction PENDING again. Refused once any of it has been used to pay an invoice.
     */
    public function unclaim($id){
        try{
            \Illuminate\Support\Facades\DB::transaction(function () use ($id) {
                $transaction = $this->modal->where('id',$id)->lockForUpdate()->first();
                if(!$transaction){
                    throw new \Exception('Transaction not found');
                }
                if($transaction->status != 'CLAIMED'){
                    throw new \Exception('Transaction is not claimed');
                }

                $suspenses = \App\Models\Suspense::with('receipts')->where('source','bank')->where('source_id',$transaction->id)->get();
                foreach($suspenses as $suspense){
                    if($suspense->receipts->count() > 0 || $suspense->status != 'PENDING'){
                        throw new \Exception('This money has already been used to pay an invoice, so it cannot be unclaimed');
                    }
                }
                foreach($suspenses as $suspense){
                    $suspense->delete();
                }

                $transaction->update(['status'=>'PENDING','customer_id'=>null,'proofofpayment_id'=>null]);
            });

            return ['status'=>'success','message'=>'Transaction unclaimed and removed from the customer wallet'];
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
