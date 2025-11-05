<?php

use App\Models\Currency;
use App\Models\Otherservice;
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
        Schema::create('otherservicefees', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Otherservice::class)->constrained();
            $table->foreignIdFor(Currency::class)->constrained();
            $table->foreignIdFor(Qualificationcategory::class)->constrained();
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otherservicefees');
    }
};
