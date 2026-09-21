<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts_payable_payments', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('payment_number')->unique();   // e.g. "APY-2026-0001"
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('accounting_period_id')->nullable()->constrained('accounting_periods');
            $table->foreignId('currency_id')->constrained('currencies');
            $table->decimal('exchange_rate', 15, 6)->default(1.000000);
            $table->date('payment_date');
            // CASH, BANK_TRANSFER, CHEQUE, EFT, MOBILE_MONEY
            $table->string('payment_method')->default('BANK_TRANSFER');
            $table->decimal('amount', 15, 2);
            $table->string('reference')->nullable();       // cheque/transfer reference
            $table->foreignId('bank_account_id')->nullable()->constrained('bankaccounts');
            $table->foreignId('ap_account_id')->nullable()->constrained('chart_of_accounts');
            $table->foreignId('bank_gl_account_id')->nullable()->constrained('chart_of_accounts');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            // DRAFT, POSTED, CANCELLED
            $table->string('status')->default('DRAFT');
            $table->text('notes')->nullable();
            $table->foreignId('createdby')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts_payable_payments');
    }
};
