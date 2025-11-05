<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manualpayment extends Model
{
  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }
  public function currency()
  {
    return $this->belongsTo(Currency::class);
  }

  public function receiptby()
  {
    return $this->belongsTo(User::class,'receipted_by','id');
  }
}
