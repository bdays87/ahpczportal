<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('emailbroadcasts', function (Blueprint $table) {
            $table->string('provider')->nullable()->default('default')->after('message');
        });
    }

    public function down(): void
    {
        Schema::table('emailbroadcasts', function (Blueprint $table) {
            $table->dropColumn('provider');
        });
    }
};
