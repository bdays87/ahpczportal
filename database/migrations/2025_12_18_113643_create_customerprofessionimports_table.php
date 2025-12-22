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
        Schema::create('customerprofessionimports', function (Blueprint $table) {
            $table->id();
            $table->string('regnumber');
             $table->string('prefix');
             $table->string('status')->default('ACTIVE');
             $table->string('processed')->default('N');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customerprofessionimports');
    }
};
