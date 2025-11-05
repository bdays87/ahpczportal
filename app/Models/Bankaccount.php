<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bankaccount extends Model
{
 public function bank()
 {
    return $this->belongsTo(Bank::class);
 }
 public function currency()
 {
    return $this->belongsTo(Currency::class);
 }
}
