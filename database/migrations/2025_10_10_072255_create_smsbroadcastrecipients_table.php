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
        Schema::create('smsbroadcastrecipients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('smsbroadcast_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('phone');
            $table->string('status')->default('PENDING'); // PENDING, SENT, FAILED
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->foreign('smsbroadcast_id')->references('id')->on('smsbroadcasts')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smsbroadcastrecipients');
    }
};
