<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('smsbroadcasts', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('smsbroadcasts', 'provider')) {
                $table->string('provider')->default('esolutions')->after('status');
            }
            
            if (!Schema::hasColumn('smsbroadcasts', 'test_numbers')) {
                $table->text('test_numbers')->nullable()->after('provider');
            }
        });
    }

    public function down(): void
    {
        Schema::table('smsbroadcasts', function (Blueprint $table) {
            // Check if columns exist before dropping them
            if (Schema::hasColumn('smsbroadcasts', 'provider')) {
                $table->dropColumn('provider');
            }
            
            if (Schema::hasColumn('smsbroadcasts', 'test_numbers')) {
                $table->dropColumn('test_numbers');
            }
        });
    }
};
