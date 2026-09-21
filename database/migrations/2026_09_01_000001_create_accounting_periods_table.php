<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // e.g. "January 2026"
            $table->integer('year');
            $table->integer('month');            // 1–12
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('OPEN'); // OPEN, CLOSED, LOCKED
            $table->foreignId('closed_by')->nullable()->constrained('users');
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('createdby')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_periods');
    }
};
