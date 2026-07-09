<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('smsbroadcastrecipients', function (Blueprint $table) {
            $table->string('provider_message_id')->nullable()->after('phone');
            $table->string('delivered_at')->nullable()->after('sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('smsbroadcastrecipients', function (Blueprint $table) {
            $table->dropColumn(['provider_message_id', 'delivered_at']);
        });
    }
};
