<?php

namespace App\implementations;

use App\Interfaces\ipaymentchannelInterface;
use App\Interfaces\ipaynowInterface;
use App\Interfaces\isuspenseInterface;
use App\Models\Customer;
use App\Models\Paynowtransaction;
use App\Notifications\WallettopupNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Paynow\Payments\Paynow;

class _paynowRepository implements ipaynowInterface
{
    /**
     * Create a new class instance.
     */
    protected $paymentchannelrepository;
    protected $suspenserepository;
    protected $paynowtransaction;
    protected $customer;
    public function __construct(ipaymentchannelInterface $paymentchannelrepository,isuspenseInterface $suspenserepository,Paynowtransaction $paynowtransaction, Customer $customer)
    {
        $this->paymentchannelrepository = $paymentchannelrepository;
        $this->suspenserepository = $suspenserepository;
        $this->paynowtransaction = $paynowtransaction;
        $this->customer = $customer;
    }
    public function initiatetransaction(array $data)
    {
        try{
        $channel = $this->paymentchannelrepository->getchannelparameters('PAYNOW',$data['currency_id']);
        if($channel == null){
            return [
                'status'=>'error',
                'message'=>'PAYNOW Channel not found'
            ];
        }
   
        $integrationparameter = $channel->parameters->where('key', 'IntegrationId')->first();
        
        $integrationid = $integrationparameter->value??null;
        $integrationkeyparameter = $channel->parameters->where('key', 'Integrationkey')->first();
  
        $integrationkey = $integrationkeyparameter->value??null;
        if($integrationid == null || $integrationkey == null){
            return [
                'status'=>'error',
                'message'=>'PAYNOW Integration not found'
            ];
        }
        $uuid = Str::uuid()->toString();
        $paynow = new Paynow($integrationid, $integrationkey,config('paynowconfig.return_url')."/checktransaction/".$uuid,config('paynowconfig.result_url')."/checktransaction/".$uuid);
         $customer = $this->customer->where('id', $data['customer_id'])->first();
         if($customer == null){
            return [
                'status'=>'error',
                'message'=>'Customer not found'
            ];
         }
        $email = $customer->email;
        if(config('paynowconfig.testmode')){
            $email = config('paynowconfig.default_email');
        }
    
        $payment = $paynow->createPayment($uuid, $email);
        $payment->add( 'Practitioner Portal', $data['amount']);
        $response = $paynow->send($payment);
        if($response->success()){
            $link = $response->redirectUrl();
            $pollurl = $response->pollUrl();
            $this->paynowtransaction->create([
                'uuid'=>$uuid,
                'pollurl'=>$pollurl,
                'customer_id'=>$customer->id,
                'amount'=>$data['amount'],
                'currency_id'=>$data['currency_id'],
                'createdby'=>Auth::user()->id,
                'status'=>'pending'
            ]);
            return [
                'status'=>'success',
                'message'=>'Payment Initiated Successfully',
                'redirecturl'=>$link
            ];
            
        }else{
            return [
                'status'=>'error',
                'message'=>"Payment Initiation Failed"
            ];
        }
    }catch(\Exception $e){
        return [
            'status'=>'error',
            'message'=>$e->getMessage()
        ];
    }
       
    }
    public function gettransactions($customer_id){
        return $this->paynowtransaction->with('customer','currency')->where('customer_id', $customer_id)->get();
    }

    public function checktransaction($uuid){
        $transaction = $this->paynowtransaction->with('customer.customeruser.user','currency')->where('uuid', $uuid)->first();
        if($transaction == null){
            return [
                'status'=>'error',
                'message'=>'Transaction not found'
            ];
        }
        $channel = $this->paymentchannelrepository->getchannelparameters('PAYNOW',$transaction->currency_id);
        if($channel == null){
            return [
                'status'=>'error',
                'message'=>'PAYNOW Channel not found'
            ];
        }
        $integrationid = $channel->parameters->where('key', 'IntegrationId')->first()->value;
        $integrationkey = $channel->parameters->where('key', 'Integrationkey')->first()->value;
        $paynow = new Paynow($integrationid, $integrationkey,config('paynowconfig.return_url')."/checktransaction/".$transaction->uuid,config('paynowconfig.result_url')."/checktransaction/".$transaction->uuid);
        $response = $paynow->pollTransaction($transaction->pollurl);
     
        if($response->paid()){
            $transaction->update([
                'status'=>'PAID'
            ]);
           $response = $this->suspenserepository->createSuspense([
                'customer_id'=>$transaction->customer_id,
                'currency_id'=>$transaction->currency_id,
                'amount'=>$transaction->amount,
                'source'=>'PAYNOW',
                'uuid'=>Str::uuid()->toString(),
                'source_id'=>$transaction->id,
                'status'=>'PENDING',
                'createdby'=>Auth::user()->id
            ]);
            if($response['status']=='success'){
                $user = $transaction?->customer?->customeruser?->user;
                if($user){
                    $user->notify(new WallettopupNotification($user->name." ".$user->surname,$transaction->amount,$transaction->currency->name));
                }
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
        }else{
            return [
                'status'=>'error',
                'message'=>$response->status()
            ];
        }
    }
    public function checktransactionbyid($id){

        $transaction = $this->paynowtransaction->with('customer.customeruser.user','currency')->where('id', $id)->first();
        if($transaction == null){
            return [
                'status'=>'error',
                'message'=>'Transaction not found'
            ];
        }
        $channel = $this->paymentchannelrepository->getchannelparameters('PAYNOW',$transaction->currency_id);
        if($channel == null){
            return [
                'status'=>'error',
                'message'=>'PAYNOW Channel not found'
            ];
        }
        $integrationid = $channel->parameters->where('key', 'IntegrationId')->first()->value;
        $integrationkey = $channel->parameters->where('key', 'Integrationkey')->first()->value;
        $paynow = new Paynow($integrationid, $integrationkey,config('paynowconfig.return_url'),config('paynowconfig.result_url'));
        $response = $paynow->pollTransaction($transaction->pollurl);
        if($response->paid()){
            $transaction->update([
                'status'=>'PAID'
            ]);
           $response = $this->suspenserepository->createSuspense([
                'customer_id'=>$transaction->customer_id,
                'currency_id'=>$transaction->currency_id,
                'amount'=>$transaction->amount,
                'source'=>'PAYNOW',
                'uuid'=>Str::uuid()->toString(),
                'source_id'=>$transaction->id,
                'status'=>'PENDING',
                'createdby'=>Auth::user()->id
            ]);
            if($response['status']=='success'){
                $user = $transaction?->customer?->customeruser?->user;
                if($user){
                    $user->notify(new WallettopupNotification($user->name." ".$user->surname,$transaction->amount,$transaction->currency->name));
                }
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
        }else{
            $transaction->update([
                'status'=>$response->status()
            ]);
            return [
                'status'=>'error',
                'message'=>"Transaction Failed with status : ".$response->status()
            ];
        }

    }
}
