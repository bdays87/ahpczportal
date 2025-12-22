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
        Schema::create('customerregistrationimports', function (Blueprint $table) {
            $table->id();
            $table->string('regnumber');
            $table->string('prefix');
            $table->string('certificatenumber');
            $table->string('registrationdate');
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
        Schema::dropIfExists('customerregistrationimports');
    }
};
