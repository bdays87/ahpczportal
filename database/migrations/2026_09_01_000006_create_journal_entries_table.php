<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('reference_number')->unique();  // e.g. "JNL-2026-0001"
            $table->date('entry_date');
            $table->string('description');
            $table->text('narration')->nullable();
            // MANUAL, AP_INVOICE, AP_PAYMENT, AR_INVOICE, AR_RECEIPT, BANK_RECONCILIATION, OPENING, CLOSING, REVERSAL
            $table->string('entry_type')->default('MANUAL');
            $table->string('source')->nullable();          // polymorphic source type
            $table->unsignedBigInteger('source_id')->nullable(); // polymorphic source id
            $table->foreignId('accounting_period_id')->nullable()->constrained('accounting_periods');
            $table->foreignId('currency_id')->constrained('currencies');
            $table->decimal('exchange_rate', 15, 6)->default(1.000000);
            $table->decimal('total_debit', 15, 2)->default(0);
            $table->decimal('total_credit', 15, 2)->default(0);
            // DRAFT, POSTED, REVERSED, CANCELLED
            $table->string('status')->default('DRAFT');
            $table->foreignId('reversed_by_entry_id')->nullable()->constrained('journal_entries');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('posted_by')->nullable()->constrained('users');
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('createdby')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
