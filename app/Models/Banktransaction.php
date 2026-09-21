<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banktransaction extends Model
{
    protected $fillable = [
        'currency_id',
        'bank_id',
        'customer_id',
        'statement_reference',
        'account_number',
        'source_reference',
        'description',
        'transaction_date',
        'amount',
        'status',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];
    
    public function bank(){
        return $this->belongsTo(Bank::class);
    }
    
    public function currency(){
        return $this->belongsTo(Currency::class);
    }
    
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
}
