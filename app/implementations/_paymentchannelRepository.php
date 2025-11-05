<?php

namespace App\implementations;

use App\Interfaces\ipaymentchannelInterface;
use App\Models\Paymentchannel;
use App\Models\Paymentchannelparameter;

class _paymentchannelRepository implements ipaymentchannelInterface
{
    /**
     * Create a new class instance.
     */
    protected $paymentchannel;
    protected $paymentchannelparameter;
    public function __construct(Paymentchannel $paymentchannel, Paymentchannelparameter $paymentchannelparameter)
    {
        $this->paymentchannel = $paymentchannel;
        $this->paymentchannelparameter = $paymentchannelparameter;
    }
    public function getAll(){
        return $this->paymentchannel->all();
    }
    public function get($id){
        return $this->paymentchannel->find($id);
    }
    public function create($data){
        try {
            $this->paymentchannel->create($data);
            return [
                'status'=>'success',
                'message'=>'Payment Channel Created Successfully'
            ];
        } catch (\Throwable $th) {
            return [
                'status'=>'error',
                'message'=>$th->getMessage()
            ];
        }

    }
    public function update($id, $data){ 
        try {
            $this->paymentchannel->where('id', $id)->update($data);
            return [
                'status'=>'success',
                'message'=>'Payment Channel Updated Successfully'
            ];
        } catch (\Throwable $th) {
            return [
                'status'=>'error',
                'message'=>$th->getMessage()
            ];
        }

    }
    public function delete($id){
        try {
            $this->paymentchannel->delete($id);
            return [
                'status'=>'success',
                'message'=>'Payment Channel Deleted Successfully'
            ];
        } catch (\Throwable $th) {
            return [
                'status'=>'error',
                'message'=>$th->getMessage()
            ];
        }
    }

    public function getparameters($id){
     
            return $this->paymentchannel->with('parameters.currency')->where('id', $id)->first();
    
    }
    public function getparameter($id){
        
            return $this->paymentchannelparameter->find($id);
   
    }
    public function getparameterbycurrency($id,$currency_id){
        try {
            return $this->paymentchannelparameter->where('paymentchannel_id', $id)->where('currency_id', $currency_id)->first();
        } catch (\Throwable $th) {
            return [
                'status'=>'error',
                'message'=>$th->getMessage()
            ];
        }
    }
    public function createparameter($data){
        try {
         
            $check = $this->paymentchannelparameter->where('paymentchannel_id', $data['paymentchannel_id'])->where('currency_id', $data['currency_id'])->where('key', $data['key'])->first();

            if($check){
                return [
                    'status'=>'error',
                    'message'=>'Payment Channel Parameter Already Exists'
                ];
            }
            $this->paymentchannelparameter->create($data);
            return [
                'status'=>'success',
                'message'=>'Payment Channel Parameter Created Successfully'
            ];
        } catch (\Throwable $th) {
            return [
                'status'=>'error',
                'message'=>$th->getMessage()
            ];
        }

    }
    public function updateparameter($id, $data){ 
        try {
          
         
           $payload=  $this->paymentchannelparameter->where('id', $id)->first();
         
           $payload->update($data);
            return [
                'status'=>'success',
                'message'=>'Payment Channel Parameter Updated Successfully'
            ];
        } catch (\Throwable $th) {
            return [
                'status'=>'error',
                'message'=>$th->getMessage()
            ];
        }

    }
    public function deleteparameter($id){
        try {
            $this->paymentchannelparameter->where('id', $id)->delete();
            return [
                'status'=>'success',
                'message'=>'Payment Channel Parameter Deleted Successfully'
            ];
        } catch (\Throwable $th) {
            return [
                'status'=>'error',
                'message'=>$th->getMessage()
            ];
        }
    }

    public function getchannelparameters($channel,$currency_id){
        return $this->paymentchannel->with(['parameters'=>function($query)use($currency_id){
            $query->where('currency_id', $currency_id);
        }])->where('name', $channel)->first();
    }
}
