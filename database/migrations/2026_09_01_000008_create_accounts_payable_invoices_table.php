<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts_payable_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('invoice_number')->unique();    // e.g. "API-2026-0001"
            $table->string('supplier_reference')->nullable(); // supplier's own invoice number
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('accounting_period_id')->nullable()->constrained('accounting_periods');
            $table->foreignId('currency_id')->constrained('currencies');
            $table->decimal('exchange_rate', 15, 6)->default(1.000000);
            $table->date('invoice_date');
            $table->date('due_date');
            $table->text('description')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('balance_due', 15, 2)->default(0);
            // DRAFT, POSTED, PARTIALLY_PAID, PAID, CANCELLED, OVERDUE
            $table->string('status')->default('DRAFT');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->foreignId('expense_account_id')->nullable()->constrained('chart_of_accounts');
            $table->foreignId('ap_account_id')->nullable()->constrained('chart_of_accounts');
            $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers');
            $table->foreignId('tax_rate_id')->nullable()->constrained('tax_rates');
            $table->text('notes')->nullable();
            $table->foreignId('createdby')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts_payable_invoices');
    }
};
