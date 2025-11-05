<?php

use App\Models\Currency;
use App\Models\Customertype;
use App\Models\Qualificationcategory;
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
        Schema::create('registrationfees', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customertype::class)->constrained();
            $table->foreignIdFor(Currency::class)->constrained();
            $table->foreignIdFor(Qualificationcategory::class)->constrained();
            $table->string('name');
            $table->string('generalledger')->nullable();
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrationfees');
    }
};
