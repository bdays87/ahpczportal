<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicationsessions', function (Blueprint $table) {
            $table->dropColumn(['renewal_window_start_month', 'renewal_window_end_month']);
        });

        Schema::table('applicationsessions', function (Blueprint $table) {
            $table->date('renewal_window_start_date')->nullable()->after('year');
            $table->date('renewal_window_end_date')->nullable()->after('renewal_window_start_date');
        });
    }

    public function down(): void
    {
        Schema::table('applicationsessions', function (Blueprint $table) {
            $table->dropColumn(['renewal_window_start_date', 'renewal_window_end_date']);
        });

        Schema::table('applicationsessions', function (Blueprint $table) {
            $table->tinyInteger('renewal_window_start_month')->default(10)->after('year');
            $table->tinyInteger('renewal_window_end_month')->default(12)->after('renewal_window_start_month');
        });
    }
};
