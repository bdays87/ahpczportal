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
        Schema::create('smsbroadcasts', function (Blueprint $table) {
            $table->id();
            $table->string('campaign_name');
            $table->text('message');
            $table->json('filters')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->integer('credits_used')->default(0);
            $table->string('status')->default('DRAFT'); // DRAFT, SENDING, SENT, FAILED
            $table->unsignedBigInteger('createdby');
            $table->timestamps();

            $table->foreign('createdby')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smsbroadcasts');
    }
};
