<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Profession;
use App\Models\Tire;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profession_tires', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Profession::class)->constrained();
            $table->foreignIdFor(Tire::class)->constrained();
            $table->integer('required_cdp')->default(0);
            $table->integer('minimum_cdp')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profession_tires');
    }
};
