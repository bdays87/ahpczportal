<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    /** Every invoice number is this prefix followed by a running number: 1, 2, 3, ... */
    public const NUMBER_PREFIX = 'INV20261114';

    public static function numberFor($sequence): string
    {
        return self::NUMBER_PREFIX.$sequence;
    }

    /** Take the next number from the counter. The row lock keeps simultaneous invoices from sharing a number. */
    public static function nextSequence(): int
    {
        return DB::transaction(function () {
            $row = DB::table('invoice_sequences')->where('name', 'invoice')->lockForUpdate()->first();
            if (! $row) {
                DB::table('invoice_sequences')->insert(['name' => 'invoice', 'last_number' => 0, 'created_at' => now(), 'updated_at' => now()]);
                $row = (object) ['last_number' => 0];
            }
            $next = (int) $row->last_number + 1;
            DB::table('invoice_sequences')->where('name', 'invoice')->update(['last_number' => $next, 'updated_at' => now()]);

            return $next;
        });
    }

    protected static function booted()
    {
        // Whatever number the caller supplied is replaced right after creation with the next in sequence.
        static::created(function (Invoice $invoice) {
            $invoice->invoice_number = self::numberFor(self::nextSequence());
            $invoice->saveQuietly();
        });
    }

    public function currency(){
        return $this->belongsTo(Currency::class);
    }
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function settlementsplit(){
        return $this->belongsTo(Settlementsplit::class,'settlementsplit_id','id');
    }
    public function proofofpayment(){
        return $this->hasMany(Proofofpayment::class);
    }
    public function receipts(){
        return $this->hasMany(Receipt::class);
    }
}
