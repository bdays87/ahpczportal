<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
  public function accounts()
  {
    return $this->hasMany(Bankaccount::class);
  }
}
