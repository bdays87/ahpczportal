<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Allocation table linking AP payments to AP invoices (many-to-many with amounts)
        Schema::create('ap_invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ap_invoice_id')->constrained('accounts_payable_invoices')->cascadeOnDelete();
            $table->foreignId('ap_payment_id')->constrained('accounts_payable_payments')->cascadeOnDelete();
            $table->decimal('amount_allocated', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ap_invoice_payments');
    }
};
