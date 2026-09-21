<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('code')->unique();           // e.g. "SUP001"
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('tax_number')->nullable();   // VAT / TIN number
            $table->foreignId('currency_id')->nullable()->constrained('currencies');
            $table->foreignId('ap_account_id')->nullable()->constrained('chart_of_accounts'); // default AP GL account
            $table->foreignId('tax_rate_id')->nullable()->constrained('tax_rates');
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->integer('payment_terms')->default(30); // days
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('createdby')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
