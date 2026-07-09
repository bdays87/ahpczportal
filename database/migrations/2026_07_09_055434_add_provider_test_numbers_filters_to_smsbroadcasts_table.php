<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('smsbroadcasts', function (Blueprint $table) {
            $table->string('provider')->default('esolutions')->after('status');
            $table->text('test_numbers')->nullable()->after('provider');
        });
    }

    public function down(): void
    {
        Schema::table('smsbroadcasts', function (Blueprint $table) {
            $table->dropColumn(['provider', 'test_numbers']);
        });
    }
};
