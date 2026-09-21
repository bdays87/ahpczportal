<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_rate_id')->constrained('tax_rates');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->string('source');           // AP_INVOICE, AR_INVOICE, MANUAL
            $table->unsignedBigInteger('source_id');
            $table->date('transaction_date');
            $table->decimal('taxable_amount', 15, 2);
            $table->decimal('tax_amount', 15, 2);
            $table->string('direction');        // INPUT (purchase), OUTPUT (sales)
            $table->foreignId('accounting_period_id')->nullable()->constrained('accounting_periods');
            $table->string('status')->default('POSTED');
            $table->foreignId('createdby')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_transactions');
    }
};
