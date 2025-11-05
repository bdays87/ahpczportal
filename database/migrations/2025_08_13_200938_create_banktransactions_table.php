<?php

use App\Models\Currency;
use App\Models\Customer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('banktransactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Currency::class)->constrained();
            $table->foreignIdFor(Customer::class)->constrained()->nullable();
            $table->string('bank');
            $table->string('statement_reference');
            $table->string('account_number');
            $table->string('source_reference')->nullable();
            $table->string('regnumber')->nullable();
            $table->string('description')->nullable();
            $table->string('transaction_date');
            $table->string('amount');
            $table->string('status')->default('PENDING');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banktransactions');
    }
};
