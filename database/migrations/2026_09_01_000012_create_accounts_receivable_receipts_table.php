<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts_receivable_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('receipt_number')->unique();    // e.g. "ARR-2026-0001"
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->string('customer_name')->nullable();
            $table->foreignId('accounting_period_id')->nullable()->constrained('accounting_periods');
            $table->foreignId('currency_id')->constrained('currencies');
            $table->decimal('exchange_rate', 15, 6)->default(1.000000);
            $table->date('receipt_date');
            $table->string('payment_method')->default('CASH'); // CASH, BANK_TRANSFER, CHEQUE, EFT, PAYNOW
            $table->decimal('amount', 15, 2);
            $table->string('reference')->nullable();
            $table->foreignId('bank_account_id')->nullable()->constrained('bankaccounts');
            $table->foreignId('ar_account_id')->nullable()->constrained('chart_of_accounts');
            $table->foreignId('cash_gl_account_id')->nullable()->constrained('chart_of_accounts');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->string('status')->default('DRAFT'); // DRAFT, POSTED, CANCELLED
            $table->text('notes')->nullable();
            $table->foreignId('createdby')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts_receivable_receipts');
    }
};
