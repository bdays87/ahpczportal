<?php

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
        Schema::create('customer_sage_statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('sage_customer_code', 50)->nullable();
            
            // Statement amounts
            $table->decimal('current_balance', 15, 2)->default(0)
                  ->comment('Total outstanding balance');
            $table->decimal('current', 15, 2)->default(0)
                  ->comment('Current (not yet due)');
            $table->decimal('days_30', 15, 2)->default(0)
                  ->comment('1-30 days overdue');
            $table->decimal('days_60', 15, 2)->default(0)
                  ->comment('31-60 days overdue');
            $table->decimal('days_90_plus', 15, 2)->default(0)
                  ->comment('90+ days overdue');
            
            // Metadata
            $table->timestamp('as_of_date')->nullable()
                  ->comment('Statement date from Sage');
            
            $table->timestamps();
            
            // Indexes
            $table->unique('customer_id');
            $table->index('sage_customer_code');
            $table->index('as_of_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_sage_statements');
    }
};
