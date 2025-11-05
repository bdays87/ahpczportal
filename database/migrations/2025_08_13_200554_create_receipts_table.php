<?php

use App\Models\Currency;
use App\Models\Customer;
use App\Models\Exchangerate;
use App\Models\Invoice;
use App\Models\Suspense;
use App\Models\User;
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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customer::class)->constrained();
            $table->foreignIdFor(Currency::class)->constrained();
            $table->foreignIdFor(Suspense::class)->constrained();
            $table->foreignIdFor(Invoice::class)->constrained();
           $table->foreignIdFor(Exchangerate::class)->constrained()->nullable();
            $table->string('receipt_number');
            $table->decimal('amount', 10, 2);
            $table->foreignIdFor(User::class,'createdby')->constrained()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
