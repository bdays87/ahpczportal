<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The bulk customer importer used to store the literal string
     * 'placeholder.jpg' in the profile column instead of leaving it null.
     * @fileurl() only falls back to the noimage placeholder for a null
     * value — a non-null string gets resolved against the storage disk and
     * 404s, which is why almost every imported customer's avatar showed as
     * a broken image. This clears those rows back to null so the intended
     * fallback picture shows instead. See _customerRepository::importcustomersexcel().
     */
    public function up(): void
    {
        DB::table('customers')->where('profile', 'placeholder.jpg')->update(['profile' => null]);
    }

    /**
     * Not reversible — the original value being restored (null) was already
     * effectively broken, so there is nothing meaningful to roll back to.
     */
    public function down(): void
    {
        //
    }
};
