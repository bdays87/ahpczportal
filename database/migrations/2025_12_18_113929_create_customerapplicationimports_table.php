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
        Schema::create('customerapplicationimports', function (Blueprint $table) {
            $table->id();
            $table->string('regnumber')->nullable();
            $table->string('prefix')->nullable();
            $table->string('applicationtype')->nullable();
            $table->string('registertype')->nullable();
            $table->string('certificatenumber')->nullable();
            $table->string('registrationdate')->nullable();
            $table->string('certificateexpirydate')->nullable();
            $table->integer('year')->nullable();
            $table->string('status')->default('PENDING');
            $table->string('processed')->default('N');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customerapplicationimports');
    }
};
