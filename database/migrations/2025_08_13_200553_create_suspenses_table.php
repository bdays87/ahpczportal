<?php

use App\Models\Currency;
use App\Models\Customer;
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
        Schema::create('suspenses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customer::class)->constrained();
            $table->foreignIdFor(Currency::class)->constrained();
            $table->string('uuid');
            $table->string('source');
            $table->integer('source_id');
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('PENDING');
            $table->foreignIdFor(User::class,'createdby')->constrained()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suspenses');
    }
};
