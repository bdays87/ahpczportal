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
        Schema::create('otherapplicationinstcustomers', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->integer('otherapplication_id');
            $table->string("employmenttype")->default('CONTRACT');
            $table->date('date_employed')->nullable();
            $table->string('status')->default('ACTIVE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otherapplicationinstcustomers');
    }
};
