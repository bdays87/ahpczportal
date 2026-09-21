<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ar_invoice_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ar_invoice_id')->constrained('accounts_receivable_invoices')->cascadeOnDelete();
            $table->foreignId('ar_receipt_id')->constrained('accounts_receivable_receipts')->cascadeOnDelete();
            $table->decimal('amount_allocated', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ar_invoice_receipts');
    }
};
