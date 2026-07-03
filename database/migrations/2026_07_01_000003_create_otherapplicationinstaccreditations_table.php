<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Accreditations held by an institution, e.g.
     * "National Certification Programme" (level "Level 2"), "SADCAS", etc.
     */
    public function up(): void
    {
        Schema::create('otherapplicationinstaccreditations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('otherapplication_id');
            $table->string('name');
            $table->string('level')->nullable();
            $table->string('status')->default('ACTIVE');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otherapplicationinstaccreditations');
    }
};
