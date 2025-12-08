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
        Schema::create('otherapplications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->integer('customer_id');
            $table->integer('otherservice_id');
            $table->integer('customerprofession_id')->nullable();
            $table->string("tradename")->nullable();
            $table->string('period');
            $table->string('certificate_number')->nullable();
            $table->date('certificate_expiry_date')->nullable();
            $table->date('registration_date')->nullable();
            $table->integer('approvedby')->nullable();
            $table->string('status')->default('PENDING');
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otherapplications');
    }
};
