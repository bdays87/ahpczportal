<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');                  // e.g. "VAT Standard", "WHT"
            $table->string('code')->unique();         // e.g. "VAT15", "WHT20"
            $table->string('tax_type');              // VAT, WITHHOLDING, INCOME, EXEMPT
            $table->decimal('rate', 8, 4);           // e.g. 15.0000 for 15%
            $table->foreignId('sales_account_id')->nullable()->constrained('chart_of_accounts');
            $table->foreignId('purchase_account_id')->nullable()->constrained('chart_of_accounts');
            $table->string('status')->default('active');
            $table->foreignId('createdby')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_rates');
    }
};
