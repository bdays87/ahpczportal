<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * WhatsApp bot: which phone number belongs to which practitioner, where each
     * conversation is up to, and a log used to ignore messages Meta delivers twice.
     */
    public function up(): void
    {
        // A verified link between a WhatsApp number and a customer (one number = one customer).
        Schema::create('whatsapp_links', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 30)->unique();           // digits only, e.g. 263772936790
            $table->unsignedBigInteger('customer_id')->index();
            $table->timestamp('linked_at')->nullable();
            $table->timestamps();
        });

        // Conversation state + the pending verification code.
        Schema::create('whatsapp_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 30)->unique();
            $table->string('state', 40)->nullable();
            $table->json('data')->nullable();
            $table->string('code_hash')->nullable();         // hash of the 6-digit link code, never the code itself
            $table->timestamp('code_expires_at')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->string('wa_message_id')->nullable()->unique(); // Meta's id; stops double processing
            $table->string('phone', 30)->index();
            $table->string('direction', 3);                  // in | out
            $table->string('type', 30)->nullable();
            $table->text('body')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('whatsapp_sessions');
        Schema::dropIfExists('whatsapp_links');
    }
};
