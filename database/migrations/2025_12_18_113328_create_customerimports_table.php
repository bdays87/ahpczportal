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
        Schema::create('customerimports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname');
            $table->string('regnumber');
            $table->string('gender');
            $table->string('email')->nullable();
            $table->string('nationality')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('customertype')->nullable();
            $table->string('employmentlocation')->nullable();
            $table->string('employmentstatus')->nullable();
            $table->string('processed')->default('N');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customerimports');
    }
};
