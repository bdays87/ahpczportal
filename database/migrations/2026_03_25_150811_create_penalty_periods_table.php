<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penalty_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');                      // e.g. "Normal Renewal Period"
            $table->string('start_date');                // e.g. "01-01" (MM-DD or full date string)
            $table->string('end_date');                  // e.g. "06-30"
            $table->integer('year');     
            $table->enum('status', ['Active', 'inactive'])->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penalty_periods');
    }
};
