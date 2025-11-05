<?php

namespace App\implementations;

use App\Interfaces\isuspenseInterface;
use App\Models\Currency;
use App\Models\Suspense;

class _suspenseRepository implements isuspenseInterface
{
    /**
     * Create a new class instance.
     */
    protected $suspense;
    protected $currency;
    public function __construct(Suspense $suspense, Currency $currency)
    {
        $this->suspense = $suspense;
        $this->currency = $currency;
    }
    public function getstatement($customer_id)
    {
        return $this->suspense->with('receipts','currency')->where('customer_id', $customer_id)->get();
    }
    public function getbalances($customer_id)
    {
        $currencylist = $this->currency->where('status', 'active')->get();
        $balance =[];
        foreach ($currencylist as $currency) {
            $balance[] = [
                'currency' => $currency->name,
                'currency_id' => $currency->id,
                'balance' => $this->computeBalance($customer_id, $currency->id)
            ];
        }
        return $balance;
    }
    public function getbalance($customer_id, $currency_id)
    {
        $currency = $this->currency->where('id', $currency_id)->first();
        $balance = $this->computeBalance($customer_id, $currency_id);
        return ['currency' => $currency->name, 'balance' => $balance];
    }
    public function createSuspense(array $data)
    {
        try{
        $check = $this->suspense->where('customer_id', $data['customer_id'])
                                 ->where('source', $data['source'])
                                 ->where('source_id', $data['source_id'])
                                 ->first();
        if($check != null){
            return ['status' => 'error', 'message' => 'Suspense already exists'];
        }
        $this->suspense->create($data);
        return ['status' => 'success', 'message' => 'Suspense created successfully'];
    }catch(\Exception $e){
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
    }
    public function deleteSuspense($id)
    {
        $suspense = $this->suspense->with('receipts','currency')->where('status', 'PENDING')->where('id', $id)->first();
        if($suspense == null){
            return ['status' => 'error', 'message' => 'Suspense not found'];
        }
        if($suspense->receipts->count() > 0){
            return ['status' => 'error', 'message' => 'Cannot delete suspense with receipts'];
        }
        $suspense->update(['status' => 'blocked']);
        return ['status' => 'success', 'message' => 'Suspense blocked successfully'];
    }

    public function  computeBalance($customer_id, $currency_id)
    {
        $suspenselist = $this->suspense->with('receipts','currency')->where('customer_id', $customer_id)->where('currency_id', $currency_id)->where('status', 'PENDING')->get();
        $summedreceipts= $suspenselist->map (function($suspense){
            return $suspense->receipts?->sum('amount')??0;
        })->sum();
       
        $summedsuspense= $suspenselist->sum('amount')??0;
        $balance = ($summedsuspense) - $summedreceipts; 
        return $balance;
    }
}
