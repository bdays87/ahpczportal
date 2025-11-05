<?php

use App\Models\Currency;
use App\Models\Qualificationcategory;
use App\Models\Registertype;
use App\Models\Tire;
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
        Schema::create('applicationfees', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Tire::class)->constrained();
            $table->foreignIdFor(Currency::class)->constrained();
            $table->foreignIdFor(Registertype::class)->constrained();
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
        Schema::dropIfExists('applicationfees');
    }
};
